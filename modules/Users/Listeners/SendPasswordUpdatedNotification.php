<?php

namespace Modules\Users\Listeners;

use Modules\Users\Notifications\PasswordUpdatedNotification;

class SendPasswordUpdatedNotification
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
        $event->user->notify(new PasswordUpdatedNotification($event->user, $event->password));
    }
}
