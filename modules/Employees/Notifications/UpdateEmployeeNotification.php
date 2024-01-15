<?php

namespace Modules\Employees\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class UpdateEmployeeNotification extends Notification
{
    protected $acceptedVia = ['mail', 'database'];

    protected $employee;

    protected $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($employee, $password)
    {
        $this->employee = $employee;
        $this->password = $password;
    }

    /**
     * Notification subject.
     *
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return Lang::get('Bạn nhận được thông báo về một nhân viên cập nhật mới!');
    }

    /**
     * Notification's text content.
     *
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return __('Thông tin nhân viên đã thay đổi.');
    }

    /**
     * Achievable action trough the notification.
     *
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        $appUrl = config('app.client_url', config('app.url'));
        $url = sprintf('%s/hrm/employees/%s/profile', $appUrl, $this->employee->id);

        return [
            'name' => Lang::get('View profile'),
            'url' => $url,
            'navigation' => 'employees',
            'id' => $this->employee->id,
        ];
    }

    /**
     * Notification's email content.
     *
     * @return MailMessage
     */
    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail)
    {
        $mail = $mail
            ->success()
            ->line(new HtmlString($this->getContent($notifiable)))
            ->line(__('From: '.$this->employee->editor->employee->full_name))
            ->line(__('Password: '.$this->password))
            ->line(__('Updated at: '.$this->employee->updated_at));

        return $mail;
    }

    /**
     * Icon retrievement.
     *
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function getIcon($notifiable)
    {
        return $this->employee->avatar_url;
    }
}
