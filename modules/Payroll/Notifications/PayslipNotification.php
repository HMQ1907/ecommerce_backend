<?php

namespace Modules\Payroll\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;

class PayslipNotification extends Notification
{
    protected $acceptedVia = ['mail'];

    protected $employee;

    protected $file;

    protected $month;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($employee, $file, $month)
    {
        $this->employee = $employee;
        $this->file = $file;
        $this->month = $month;
    }

    /**
     * Notification subject.
     *
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return Lang::get(__('payroll::common.payroll_slip').' '.$this->employee->full_name.' '.__('payroll::common.month').' '.Carbon::parse($this->month)->format('m-Y'));
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
            ->greeting(__('payroll::common.dear_sir_or_madam').' '.$this->employee->full_name)
            ->line(__('payroll::common.attachment_file').' '.Carbon::parse($this->month)->format('m-Y'))
            ->line(__('payroll::common.information_detail'))
            ->line(__('payroll::common.question'))
            ->salutation(__('payroll::common.best_regards'))
            ->attach($this->file, [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Notification's text content.
     *
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        //
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
