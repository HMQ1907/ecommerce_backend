<?php

namespace Modules\Comments\Listeners;

use Modules\Comments\Events\CommentCreated;
use Modules\Comments\Notifications\CommentCreatedNotification;

class SendCommentCreatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(CommentCreated $event)
    {
        $event->comment->commenter->notify(new CommentCreatedNotification($event->comment));
    }
}
