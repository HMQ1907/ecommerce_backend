<?php

namespace Modules\Payroll\Events;

use Illuminate\Queue\SerializesModels;

class SendEmailPayslip
{
    use SerializesModels;

    public $employee;

    public $file;

    public $month;

    public function __construct($employee, $file, $month)
    {
        $this->employee = $employee;
        $this->file = $file;
        $this->month = $month;
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
