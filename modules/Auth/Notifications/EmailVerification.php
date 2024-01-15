<?php

namespace Modules\Auth\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class EmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $appUrl = config('app.client_url', config('app.url'));

        $url = URL::temporarySignedRoute(
            'verification.verify', now()->addMinutes(60), ['user' => $notifiable->id]
        );

        return str_replace(url('/api/auth'), $appUrl, $url);
    }
}
