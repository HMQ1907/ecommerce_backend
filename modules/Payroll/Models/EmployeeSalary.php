<?php

namespace Modules\Payroll\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employees\Models\Employee;
use Prettus\Repository\Traits\TransformableTrait;
use Wildside\Userstamps\Userstamps;

class EmployeeSalary extends BaseModel
{
    use HasFactory, TransformableTrait;
    use Userstamps;

    protected $guarded = ['id'];

    protected $dates = ['date'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('branch', function (Builder $builder) {
            if (!app()->runningInConsole()) {
                if (auth()->user()->branch_id) {
                    return $builder->whereHas('employee', function ($query) {
                        $query->where('employees.branch_id', auth()->user()->branch_id);
                    });
                } else {
                    return $builder;
                }
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function variableSalaries()
    {
        return $this->hasMany(EmployeeVariableSalary::class, 'salary_id');
    }

    public function scopeSalaryDateBetween(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('date', [Carbon::make($from)->startOfDay(), Carbon::make($to)->endOfDay()]);
    }
}
