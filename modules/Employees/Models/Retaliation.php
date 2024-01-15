<?php

namespace Modules\Employees\Models;

use App\Models\BaseModel as Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Traits\TransformableTrait;
use Wildside\Userstamps\Userstamps;

class Retaliation extends Model
{
    use HasFactory, TransformableTrait;
    use SoftDeletes, Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'apply_salary_date',
        'increment_date',
        'previous_salary',
        'new_salary',
        'created_by',
    ];

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

    public function getOriginalAmountAttribute()
    {
        return $this->new_salary - $this->previous_salary ?? 0;
    }

    public function getOriginalMonthsAttribute()
    {
        return Carbon::parse($this->increment_date)->diffInMonths(Carbon::parse($this->apply_salary_date)) + 1 ?? 0;
    }

    public function getOriginalAmountOfMoneyAttribute()
    {
        //The number of working days remaining in the month is based on the increment date excluding weekends
        $workingDays = Carbon::parse($this->increment_date)->diffInDaysFiltered(function (Carbon $date) {
            return $date->isWeekday();
        }, Carbon::parse($this->increment_date)->endOfMonth()) - 1;

        return ($this->original_amount * $this->original_months - ($this->original_amount / 22 * $workingDays)) ?? 0;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withoutGlobalScope('branch');
    }
}
