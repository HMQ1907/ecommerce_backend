<?php

namespace Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employees\Models\Employee;
use Modules\Payroll\Traits\HasExchangeRate;
use Modules\Roles\Models\Role;
use Prettus\Repository\Traits\TransformableTrait;
use Wildside\Userstamps\Userstamps;

class SalarySlip extends Model
{
    use HasExchangeRate;
    use HasFactory, TransformableTrait;
    use Userstamps;

    const GENERATED = 'generated';

    const REVIEW = 'review';

    const LOCKED = 'locked';

    const PAID = 'paid';

    protected $fillable = [
        'employee_id',
        'salary_from',
        'salary_to',
        'paid_on',
        'status',
        'salary_json',
        'extra_json',
        'net_salary',
        'gross_salary',
        'total',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('branch', function (Builder $builder) {
            if (auth()->user()->branch_id) {
                return $builder->whereHas('employee', function ($query) {
                    $query->where('employees.branch_id', auth()->user()->branch_id);
                });
            } else {
                return $builder;
            }
        });
    }

    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder;
    }

    protected $casts = [
        'salary_json' => 'json',
        'extra_json' => 'json',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withoutGlobalScope('active');
    }

    public function exchangeRates()
    {
        return $this->morphMany(ExchangeRate::class, 'exchangeable');
    }
}
