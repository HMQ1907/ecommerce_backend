<?php

namespace Modules\Employees\Events;

use Illuminate\Queue\SerializesModels;

class EmployeeUpdated
{
    use SerializesModels;

    public $employee;

    public $password;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($employee, $password)
    {
        $this->employee = $employee;
        $this->password = $password;
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
