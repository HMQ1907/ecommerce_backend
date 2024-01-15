<?php

namespace App\Notifications\Contracts;

use App\Notifications\Notification;

interface CanBeNotifiable
{
    /**
     * Send a notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance);

    /**
     * Return the notification icon as the creator.
     *
     * @return void
     */
    public function getNotificationIcon(Notification $notification);
}
