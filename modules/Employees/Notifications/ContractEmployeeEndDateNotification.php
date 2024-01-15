<?php

namespace Modules\Employees\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class ContractEmployeeEndDateNotification extends Notification
{
    protected $acceptedVia = ['mail', 'database'];

    protected $time;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($time)
    {
        parent::__construct();
        $this->time = $time;
    }

    /**
     * Notification subject.
     *
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return Lang::get('Your contract has :time left to expire! Please contact management for assistance.', [
            'time' => $this->time,
        ]);
    }

    /**
     * Notification's text content.
     *
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return __('Your contract has :time left to expire! Please contact management for assistance.', [
            'time' => $this->time,
        ]);
    }

    /**
     * Achievable action trough the notification.
     *
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        //
    }

    /**
     * Notification's email content.
     *
     * @return MailMessage
     */
    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail)
    {
        return $mail
            ->success()
            ->line(new HtmlString($this->getContent($notifiable)));
    }

    /**
     * Icon retrievement.
     *
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function getIcon($notifiable)
    {
        //
    }
}
