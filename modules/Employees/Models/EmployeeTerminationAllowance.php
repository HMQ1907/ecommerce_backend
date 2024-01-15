<?php

namespace Modules\Employees\Models;

use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Roles\Models\Role;

class EmployeeTerminationAllowance extends Model
{
    // Self modules
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'subject',
        'type',
        'notice_date',
        'termination_date',
        'terminated_by',
        'status',
        'description',
        'remaining_vacation_days',
    ];

    protected $table = 'employee_termination_allowances';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('branch', function (Builder $builder) {
            if (!app()->runningInConsole()) {
                if (auth()->user()->branch_id) {
                    return $builder->whereHas('employee', function ($query) {
                        $query->where('branch_id', auth()->user()->branch_id);
                    });
                } else {
                    return $builder;
                }
            }
        });
    }

    public function getVacationFundAttribute()
    {
        return optional($this->employee->currentSalary())->current_basic_salary / 22 * $this->remaining_vacation_days;
    }

    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->employee?->branch_id) {
            $builder->whereHas('employee', function ($query) {
                $query->where('branch_id', auth()->user()->employee->branch_id);
            });
        }

        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withoutGlobalScope('active');
    }

    public function terminatedByName()
    {
        return $this->belongsTo(Employee::class, 'terminated_by', 'id');
    }
}
