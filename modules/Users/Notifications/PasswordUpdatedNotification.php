<?php

namespace Modules\Users\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    protected $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via()
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $appUrl = config('app.client_url', config('app.url'));
        $url = $appUrl.'/users/'.$this->user->id.'/profile';

        return (new MailMessage())
            ->subject('Update password for employee')
            ->line('Hello'.' '.$notifiable->name)
            ->line(__('You receive a notification change password!'))
            ->line('Email: '.' '.$this->user->email)
            ->line('Password: '.' '.$this->password)
            ->action('View Employee', $url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toDatabase()
    {
        return [
            'id' => $this->user->employee->id,
            'title' => $this->user->employee->editor->employee->full_name.' updated '.$this->user->employee->full_name,
        ];
    }
}
