<?php

namespace Modules\Attendances\Models;

use App\Models\BaseModel as Model;
use App\Settings\AttendanceSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employees\Models\Employee;
use Modules\Roles\Models\Role;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wildside\Userstamps\Userstamps;

class Attendance extends Model implements HasMedia, Transformable
{
    use HasFactory, TransformableTrait;
    use InteractsWithMedia;
    use SoftDeletes, Userstamps;

    protected $fillable = [
        'employee_id',
        'date',
        'is_late',
        'is_early',
        'clock_in',
        'clock_out',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_out_latitude',
        'clock_out_longitude',
    ];

    protected $casts = [
        'is_late' => 'boolean',
        'is_early' => 'boolean',
    ];

    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder->orWhere(function ($query) {
            return $query->ownership();
        });
    }

    public function scopeOwnership(Builder $builder)
    {
        return $builder->where($builder->getQuery()->from.'.created_by', auth()->id())
            ->orWhere('employee_id', auth()->user()->employee->id);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->getMedia('*')->map(fn ($media) => $media->getUrl());
    }

    public function totalTime()
    {
        $date = Carbon::parse($this->date);

        $clockIn = $this->clock_in;
        $clockOut = $this->clock_out;

        if (today()->equalTo($date) && empty($clockOut)) {
            return 0;
        }

        $attendanceSettings = app(AttendanceSettings::class);

        if (empty($clockOut)) {
            $clockOut = Carbon::createFromFormat('H:i', $attendanceSettings->work_time['end']);
        }

        return Carbon::parse($clockIn)->diffInMinutes($clockOut);
    }
}
