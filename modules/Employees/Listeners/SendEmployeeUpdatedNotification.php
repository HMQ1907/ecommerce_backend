<?php

namespace Modules\Employees\Listeners;

use Modules\Employees\Events\EmployeeUpdated;
use Modules\Employees\Notifications\UpdateEmployeeNotification;
use Modules\Employees\Repositories\EmployeeRepository;

class SendEmployeeUpdatedNotification
{
    protected $employeeRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(EmployeeUpdated $event)
    {
        $event->employee->user->notify(new UpdateEmployeeNotification($event->employee, $event->password));
    }
}
