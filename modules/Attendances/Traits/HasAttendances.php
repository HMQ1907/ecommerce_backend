<?php

namespace Modules\Attendances\Traits;

use Modules\Attendances\Models\Attendance;

trait HasAttendances
{
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
