<?php

namespace Modules\Users\Events;

use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use SerializesModels;

    public $user;

    public $targetId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $targetId)
    {
        $this->user = $user;
        $this->targetId = $targetId;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
