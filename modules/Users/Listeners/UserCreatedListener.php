<?php

namespace Modules\Users\Listeners;

use App\Models\User;
use Modules\Roles\Models\Role;

class UserCreatedListener
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->user->account_type == User::TYPE_EMPLOYEE) {

            $event->user->assignRole(Role::EMPLOYEE);
            $event->user->syncPermissions($event->user->getAllPermissions()->pluck('id')->toArray());
        }
    }
}
