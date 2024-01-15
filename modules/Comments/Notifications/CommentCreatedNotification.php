<?php

namespace Modules\Comments\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class CommentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toDatabase()
    {
        return [
            'id' => $this->comment->id,
            'full_name' => $this->comment->commenter->employee->full_name,
            'message' => $this->comment->comment,
            'title' => __('comments::common.newComment'),
        ];
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function toFcm()
    {
        return FcmMessage::create()
            ->setData([
                'web_url' => config('app.client_url'),
                'id' => (string) $this->comment->id,
                'message' => (string) $this->comment->comment,
                'create_at' => (string) $this->comment->create_at,
                'type' => 'comment-notification',
            ])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle(config('app.name'))
                ->setBody($this->comment->commenter->name.' '.__('comment::common.newComment')))
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')));
    }
}
