<?php

namespace Modules\Overtimes\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employees\Models\Employee;
use Modules\Payroll\Models\ExchangeRate;

class Overtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'overtime_date',
        'rates',
        'total_hrs',
        'total_amount',
    ];

    protected $casts = [
        'rates' => 'json',
    ];

    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->branch_id) {
            return $builder->whereHas('employee', function ($query) {
                $query->where('branch_id', auth()->user()->branch_id);
            });
        } else {
            return $builder;
        }
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function exchangeRates()
    {
        return $this->morphMany(ExchangeRate::class, 'exchangeable');
    }
}
