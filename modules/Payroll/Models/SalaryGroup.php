<?php

namespace Modules\Payroll\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(SalaryComponent::class, 'salary_group_components');
    }

    public function employee(): HasMany
    {
        return $this->hasMany(EmployeeSalaryGroup::class, 'salary_group_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeSalaryGroup::class, 'employee_salary_groups', 'salary_group_id', 'employee_id');
    }
}
