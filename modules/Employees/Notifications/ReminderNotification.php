<?php

namespace Modules\Employees\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use NotificationChannels\Fcm\FcmChannel;

class ReminderNotification extends Notification
{
    protected $acceptedVia = ['database', FcmChannel::class];

    protected $message;

    protected $auth;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, $auth)
    {
        $this->message = $message;
        $this->auth = $auth;
    }

    /**
     * Notification subject.
     *
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return Lang::get('Bạn nhận được tin nhắn từ :name', [
            'name' => $this->auth->employee->full_name,
        ]);
    }

    /**
     * Notification's text content.
     *
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return Lang::get($this->message);
    }

    /**
     * Notification's fcm data.
     *
     * @return array
     */
    protected function getData(CanBeNotifiable $notifiable)
    {
        return array_merge($this->getAction($notifiable), [
            'type' => 'reminder-notification',
            'web_url' => config('app.client_url'),
        ]);
    }

    /**
     * Icon retrievement.
     *
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function getIcon($notifiable)
    {
        return $this->auth->employee->avatar_url;
    }
}
