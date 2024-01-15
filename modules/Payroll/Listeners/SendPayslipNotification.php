<?php

namespace Modules\Payroll\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Payroll\Events\SendEmailPayslip;
use Modules\Payroll\Notifications\PayslipNotification;

class SendPayslipNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(SendEmailPayslip $event)
    {
        Notification::send($event->employee, new PayslipNotification($event->employee, $event->file, $event->month));
    }
}
