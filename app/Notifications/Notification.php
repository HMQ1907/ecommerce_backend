<?php

namespace App\Notifications;

use App\Notifications\Contracts\CanBeNotifiable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

abstract class Notification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected $acceptedVia = [];

    protected $creator;

    protected $icon;

    /**
     * Notification's type declaration.
     */
    public function __construct(?string $icon = null, ?Model $creator = null)
    {
        $this->icon = $icon;
        $this->creator = ($creator ?? \Auth::user());
    }

    /**
     * Notification subject.
     *
     * @return string
     */
    abstract protected function getSubject(CanBeNotifiable $notifiable);

    /**
     * Notification's text content.
     *
     * @return string
     */
    abstract protected function getContent(CanBeNotifiable $notifiable);

    /**
     * Achievable action trough the notification.
     *
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        return [];
    }

    /**
     * Notification's fcm data.
     *
     * @return array
     */
    protected function getData(CanBeNotifiable $notifiable)
    {
        return [];
    }

    /**
     * Notification email content.
     *
     * @return MailMessage
     */
    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail)
    {
        $content = '<br />'.str_replace(PHP_EOL, '<br />', htmlentities($this->getContent($notifiable))).'<br />';

        return $mail
            ->line($notifiable->name)
            ->line(new HtmlString($content));
    }

    /**
     * List all notifications channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return $this->acceptedVia;
    }

    /**
     * Return the notification email representation.
     *
     * @return MailMessage
     */
    public function toMail(CanBeNotifiable $notifiable)
    {
        $action = $this->getAction($notifiable);
        $mail = $this->getMailBody(
            $notifiable,
            (new MailMessage)->subject($this->getSubject($notifiable))
        );

        if ($action && isset($action['name']) && isset($action['url'])) {
            $mail->action($action['name'], $action['url']);
        }

        return $mail;
    }

    /**
     * Return the notification fcm representation.
     *
     * @return FcmMessage
     */
    public function toFcm(CanBeNotifiable $notifiable)
    {
        return FcmMessage::create()
            ->setData($this->getData($notifiable))
            ->setNotification(
                \NotificationChannels\Fcm\Resources\Notification::create()
                    ->setTitle(config('app.name'))
                    ->setBody($this->getSubject($notifiable))
                    ->setImage($this->getIcon($notifiable))
            )
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios'))
            );
    }

    /**
     * Creator retrievement.
     */
    public function getCreator(Model $notifiable): Model
    {
        return $this->creator ?? $notifiable;
    }

    /**
     * Icon retrievement.
     *
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function getIcon($notifiable)
    {
        return $this->icon ?? $notifiable->getNotificationIcon($this);
    }

    /**
     * Return the notification under thr form of an array.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => $this->getSubject($notifiable),
            'content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
            'icon' => $this->getIcon($notifiable),
        ];
    }

    /**
     * Return the notification under the form of an array for the database.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $createdBy = $this->getCreator($notifiable);

        return array_merge($this->toArray($notifiable), [
            'created_by' => [
                'id' => $createdBy->getKey(),
                'type' => get_class($createdBy),
                'name' => $createdBy->name,
            ],
        ]);
    }
}
