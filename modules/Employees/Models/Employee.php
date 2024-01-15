<?php

namespace Modules\Employees\Models;

use App\Models\BaseModel as Model;
use App\Models\User;
use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Attendances\Models\Attendance;
use Modules\Attendances\Traits\HasAttendances;
use Modules\Branches\Models\Branch;
use Modules\Departments\Models\Department;
use Modules\Designations\Models\Designation;
use Modules\Overtimes\Models\Overtime;
use Modules\Payroll\Models\EmployeeSalary;
use Modules\Payroll\Models\EmployeeVariableSalary;
use Modules\Payroll\Models\SalarySlip;
use Modules\Roles\Models\Role;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Wildside\Userstamps\Userstamps;

class Employee extends Model implements CanBeNotifiable, HasMedia
{
    // Self modules
    use HasAttendances;
    use HasFactory, Notifiable, TransformableTrait;
    use HasRelationships;
    use InteractsWithMedia;
    use SoftDeletes, Userstamps;

    const CODE_PREFIX = 'L';

    const TYPE_STAFF = 'staff';

    const TYPE_CONTRACTOR = 'contractor';

    const TYPE_EXPAT = 'expat';

    const TYPE_REMOVAL = 'removal';

    const POSITION_TYPE_MANAGER = 'manager';

    const POSITION_TYPE_EMPLOYEE = 'employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'sort_order',
        'branch_id',
        'department_id',
        'designation_id',
        'employee_code',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'avatar',
        'email',
        'phone',
        'address',
        'date_to_company',
        'status',
        'type',
        'position_type',
        'allowance',
        'indicator',
        'is_insurance',
        'date_to_job',
        'job',
        'date_to_job_group',
        'date_of_engagement',
        'education',
        'jg',
        'actua_working_days',
        'created_by',
    ];

    protected $casts = [
        'is_insurance' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            return $builder->active();
        });

        static::addGlobalScope('branch', function (Builder $builder) {
            if (!app()->runningInConsole()) {
                if (auth()->user()->branch_id) {
                    return $builder->where('employees.branch_id', auth()->user()->branch_id);
                } else {
                    return $builder;
                }
            }
        });

        self::creating(function ($model) {
            if (empty($model->employee_code)) {
                $model->employee_code = IdGenerator::generate([
                    'table' => $model->getTable(),
                    'field' => 'employee_code',
                    'length' => 8,
                    'prefix' => sprintf('%s%s-', self::CODE_PREFIX, Carbon::parse($model->created_at)->format('Y')),
                    'reset_on_prefix_change' => true,
                ]);
            }
        });

        static::deleting(function ($model) {
            $model->terminationAllowances->each(function ($terminationAllowance) {
                $terminationAllowance->delete();
            });
        });

        static::deleting(function ($model) {
            $model->transfers->each(function ($transfer) {
                $transfer->delete();
            });
        });
    }

    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder->orWhere('id', auth()->user()->id);
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('type', '<>', self::TYPE_REMOVAL);
    }

    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return now()->diffInYears($this->date_of_birth);
        }

        return null;
    }

    public function getServiceAttribute()
    {
        if ($this->date_to_company) {
            return now()->diffInYears($this->date_to_company);
        }

        return 0;
    }

    public function getTitleAttribute()
    {
        if ($this->gender == 'male') {
            return 'Mr.';
        }

        return 'Ms.';
    }

    public function getRetiredAgeAttribute()
    {
        if ($this->gender == 'male') {
            return 60;
        }

        return 55;
    }

    public function getNormalRetirementDateAttribute()
    {
        if ($termination = $this->terminationAllowances()->first()) {
            return Carbon::parse($termination->termination_date)->toDateString();
        }

        if ($this->date_of_birth) {
            return Carbon::parse($this->date_of_birth)->addYears($this->retired_age)->toDateString();
        }

        return null;
    }

    public function getExpiryStatusAttribute()
    {
        $endDate = $this->normal_retirement_date;
        if ($this->type == self::TYPE_CONTRACTOR) {
            $endDate = optional($this->contracts()->latest()->first())->contract_to;
        }

        $nextDays = now()->addDays(3);
        if ($endDate) {
            if (Carbon::parse($endDate)->isPast()) {
                return 'expired';
            } elseif (Carbon::parse($endDate)->isBefore($nextDays)) {
                return 'nearing_expiry';
            }
        }

        return 'normal';
    }

    public function getRetirementRateAttribute()
    {
        if ($this->service >= 3 && $this->service < 15) {
            return 1.5;
        } elseif ($this->service >= 15 && $this->service < 35) {
            return 1.75;
        } elseif ($this->service >= 35) {
            return 2;
        } else {
            return 1;
        }
    }

    public function getRetirementFundAttribute()
    {
        if ($this->currentSalary()) {
            return $this->currentSalary()->current_basic_salary * $this->service * $this->retirement_rate;
        }

        return 0;
    }

    public function getTotalRetirementFundAttribute()
    {
        $terminationAllowance = $this->terminationAllowances()->first();

        if (!empty($terminationAllowance)) {
            return $terminationAllowance->vacation_fund + $this->retirement_fund;
        }

        return $this->retirement_fund;
    }

    public function scopeSearchName(Builder $query, $q)
    {
        return $query->where('first_name', 'LIKE', "%$q%")
            ->orWhere('last_name', 'LIKE', "%$q%")
            ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$q%");
    }

    public function scopeByRole(Builder $query, $role)
    {
        return $query->whereHas('user.roles', function ($query) use ($role) {
            return $query->where('name', $role);
        });
    }

    public function getFullNameAttribute()
    {
        return $this->title.' '.$this->first_name.' '.$this->last_name;
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getAvatarUrlAttribute()
    {
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function getBasicSalaryAttribute()
    {
        if ($this->currentSalary()) {
            return $this->currentSalary()->current_basic_salary;
        }

        return 0;
    }

    public function getCurrencyCodeAttribute()
    {
        if ($this->currentSalary()) {
            return $this->currentSalary()->currency_code;
        }

        return 'LAK';
    }

    public function getHousingAllowanceAttribute()
    {
        if ($this->currentSalary()) {
            foreach ($this->currentSalary()->variableSalaries as $variableSalary) {
                if ($variableSalary->variable_component_id == 1) {
                    return $variableSalary->variable_value;
                }
            }
        }

        return 0;
    }

    public function getPositionAllowanceAttribute()
    {
        if ($this->currentSalary()) {
            foreach ($this->currentSalary()->variableSalaries as $variableSalary) {
                if ($variableSalary->variable_component_id == 2) {
                    return $variableSalary->variable_value;
                }
            }
        }

        return 0;
    }

    public function getLastDayAttribute()
    {
        if ($this->terminationAllowances()->count() > 0) {
            return $this->terminationAllowances->last()->termination_date;
        }

        return null;
    }

    public function getRemarksAttribute()
    {
        if ($this->terminationAllowances()->count() > 0) {
            return $this->terminationAllowances->last()->remarks;
        }

        return null;
    }

    public function getBonusDobAttribute()
    {
        if ($this->employeeAwards()->count() > 0) {
            return $this->employeeAwards()->whereHas('award', function ($query) {
                return $query->where('type', 'birthday');
            })->latest()->first()->amount ?? 0;
        }

        return 0;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    public function managedDepartments()
    {
        return $this->departments()->where('manager_id', '=', $this->getKey());
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function variableSalaries()
    {
        return $this->hasManyThrough(
            EmployeeVariableSalary::class, EmployeeSalary::class,
            'employee_id', 'salary_id',
            'id'
        );
    }

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class);
    }

    public function getCustomFields()
    {
        return $this->customFields()->get()->flatMap(function ($e) {
            return [
                $e->title => $e->answers,
            ];
        });
    }

    public function currentSalary()
    {
        return $this->salaries()->latest()->first();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function terminationAllowances()
    {
        return $this->hasMany(EmployeeTerminationAllowance::class);
    }

    public function transfers()
    {
        return $this->hasMany(EmployeeTransfer::class);
    }

    public function employeeAwards()
    {
        return $this->hasMany(EmployeeAward::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function getNotificationIcon(Notification $notification)
    {
        return $this->avatar;
    }

    public function retaliations()
    {
        return $this->hasMany(Retaliation::class);
    }
}
