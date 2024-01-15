<?php

namespace Modules\Users\Events;

use Illuminate\Queue\SerializesModels;

class PasswordUpdated
{
    use SerializesModels;

    public $user;

    public $password;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
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
