<?php

namespace Modules\Employees\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Roles\Models\Role;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wildside\Userstamps\Userstamps;

class Award extends BaseModel implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Userstamps;

    protected $fillable = [
        'title',
        'award_type',
        'type',
        'award_period',
        'total_amount',
    ];

    protected $table = 'awards';

    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->employee?->branch_id) {
            $builder->whereHas('employeeAwards', function (Builder $query) {
                $query->whereHas('employee', function (Builder $query) {
                    $query->where('branch_id', auth()->user()->employee->branch_id);
                });
            });
        }

        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder;
    }

    public function employeeAwards()
    {
        return $this->hasMany(EmployeeAward::class)
            ->whereHas('employee', function (Builder $query) {
                $query->where('branch_id', auth()->user()->branch_id);
            });
    }
}
