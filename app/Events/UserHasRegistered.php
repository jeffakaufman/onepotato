<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;
class UserHasRegistered extends Event
{
    use SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     *
     * @param \App\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        //
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
