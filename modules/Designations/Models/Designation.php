<?php

namespace Modules\Designations\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeTransfer;
use Modules\Roles\Models\Role;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder;
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function employeeTransfersFrom()
    {
        return $this->hasMany(EmployeeTransfer::class, 'from_designation_id');
    }

    public function employeeTransfersTo()
    {
        return $this->hasMany(EmployeeTransfer::class, 'to_designation_id');
    }
}
