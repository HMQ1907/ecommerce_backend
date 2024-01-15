<?php

namespace Modules\Attendances\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Attendances\Models\Attendance;

class AttendanceTransformer extends TransformerAbstract
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Attendance $attendance)
    {
        return [
            'id' => $attendance->id,
            'employee_id' => $attendance->employee_id,
            'full_name' => $attendance->employee->full_name,
            'avatar_url' => $attendance->employee->avatar_url,
            'date' => $attendance->date,
            'is_late' => $attendance->is_late,
            'is_early' => $attendance->is_early,
            'clock_in' => $attendance->clock_in,
            'clock_out' => $attendance->clock_out,
            'image' => $attendance->image_url,
            'total' => $attendance->totalTime(),
        ];
    }
}
