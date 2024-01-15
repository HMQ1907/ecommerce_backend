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
        if ($event->user->account_type == User::TYPE_CUSTOMER) {

            $event->user->assignRole(Role::CUSTOMER);
            $event->user->syncPermissions($event->user->getAllPermissions()->pluck('id')->toArray());
        }
    }
}
