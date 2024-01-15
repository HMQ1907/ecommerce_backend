<?php

namespace Modules\Employees\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;
use Modules\Employees\Models\Employee;

class CreateEmployeeNotification extends Notification
{
    protected $acceptedVia = ['mail', 'database'];

    protected $employee;

    protected $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, $password)
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
        return Lang::get('Bạn nhận được thông báo về một nhân viên mới');
    }

    /**
     * Notification's text content.
     *
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return Lang::get('<strong>:name</strong> đã được tạo', [
            'name' => $this->employee->full_name,
        ]);
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
            ->line(__('Employee: '.$this->employee->full_name))
            ->line(__('Email: '.$this->employee->user->email))
            ->line(__('Password: '.$this->password));

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
