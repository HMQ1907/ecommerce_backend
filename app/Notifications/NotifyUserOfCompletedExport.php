<?php

namespace App\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class NotifyUserOfCompletedExport extends Notification implements ShouldQueue
{
    use Queueable;

    protected $acceptedVia = ['database'];

    protected $filePath;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        parent::__construct();
        $this->filePath = $filePath;
    }

    /**
     * Notification subject.
     *
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return Lang::get('Export file success. Click to download');
    }

    /**
     * Notification's text content.
     *
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return Lang::get('Export file success. Click to download');
    }

    /**
     * Achievable action trough the notification.
     *
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        $appUrl = config('app.api_url', config('app.url'));
        $url = sprintf('%s/storage/%s', $appUrl, $this->filePath);

        return [
            'name' => Lang::get('Download'),
            'url' => $url,
            'navigation' => 'export',
            'path' => $this->filePath,
        ];
    }

    /**
     * Notification's email content.
     *
     * @return void
     */
    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail)
    {
        //
    }

    /**
     * Icon retirement.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function getIcon($notifiable)
    {
        //
    }
}
