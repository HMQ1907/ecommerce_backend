<?php

namespace Modules\Attendances\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Attendances\Events\CheckInCreated;
use Modules\Attendances\Notifications\CheckInNotification;
use Modules\Employees\Repositories\EmployeeRepository;

class SendCheckInCreatedNotification
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
    public function handle(CheckInCreated $event)
    {
        $managers = $this->employeeRepository->getManagers()->pluck('user');

        Notification::send($managers, new CheckInNotification($event->attendance));
    }
}
