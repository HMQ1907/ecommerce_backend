<?php

namespace Modules\Employees\Listeners;

use Modules\Employees\Events\EmployeeCreated;
use Modules\Employees\Notifications\CreateEmployeeNotification;

class SendEmployeeCreatedNotification
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
    public function handle(EmployeeCreated $event)
    {
        $event->employee->user->notify(new CreateEmployeeNotification($event->employee, $event->password));
    }
}
