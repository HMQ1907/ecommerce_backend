<?php

namespace Modules\Attendances\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;
use Modules\Attendances\Models\Attendance;

class CheckInNotification extends Notification
{
    protected $acceptedVia = ['mail', 'database'];

    protected $attendance;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Notification subject.
     *
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return Lang::get('Một nhân viên mới chấm công');
    }

    /**
     * Notification's text content.
     *
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return Lang::get('<strong>:name</strong> chấm công hôm nay vào lúc :clock_in', [
            'name' => $this->attendance->employee->full_name,
            'clock_in' => $this->attendance->clock_in,
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
        $url = sprintf('%s/hrm/attendances/%s', $appUrl, $this->attendance->id);

        return [
            'name' => Lang::get('View attendance'),
            'url' => $url,
            'navigation' => 'attendances',
            'id' => $this->attendance->id,
            'employee_id' => $this->attendance->employee_id,
            'date' => $this->attendance->date,
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
            ->line(__('Employee: '.$this->attendance->employee->full_name))
            ->line(__('Date: '.$this->attendance->date))
            ->line(__('Clock-in: '.$this->attendance->clock_in));

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
        return $this->attendance->employee->avatar_url;
    }
}
