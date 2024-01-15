<?php

namespace Modules\Attendances\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Attendances\Models\Attendance;

class CheckInCreated
{
    use SerializesModels;

    public $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
