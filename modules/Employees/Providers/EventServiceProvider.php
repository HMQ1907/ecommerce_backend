<?php

namespace Modules\Employees\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Employees\Events\EmployeeCreated;
use Modules\Employees\Events\EmployeeUpdated;
use Modules\Employees\Listeners\SendEmployeeCreatedNotification;
use Modules\Employees\Listeners\SendEmployeeUpdatedNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        EmployeeCreated::class => [
            SendEmployeeCreatedNotification::class,
        ],
        EmployeeUpdated::class => [
            SendEmployeeUpdatedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
