<?php

namespace Modules\Departments\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Modules\Branches\Models\Branch;
use Modules\Designations\Models\Designation;
use Modules\Employees\Models\Employee;
use Modules\Roles\Models\Role;
use Modules\Teams\Models\Team;
use Prettus\Repository\Traits\TransformableTrait;

class Department extends Model
{
    use HasFactory, NodeTrait, TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'manager_id',
        'branch_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => StatusDepartment::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('branch', function (Builder $builder) {
            if (!app()->runningInConsole()) {
                if (auth()->user()->branch_id) {
                    return $builder->where('departments.branch_id', auth()->user()->branch_id);
                } else {
                    return $builder;
                }
            }
        });
    }

    public function getStatusAttribute($value)
    {
        return $value == StatusDepartment::ACTIVE->value() ? 'active' : 'inactive';
    }

    // Specify parent id attribute mutator
    public function setParentAttribute($value)
    {
        $this->setParentIdAttribute($value);
    }

    public function scopeAllData(Builder $builder)
    {
        $key = $builder->getQuery()->from;

        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
