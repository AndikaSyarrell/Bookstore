<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class UserOnlineStatus implements ShouldBroadcastNow
{
    use SerializesModels;

    public $user;
    public $status;

    public function __construct($user, $status)
    {
        $this->user = $user;
        $this->status = $status; // online / offline
    }

    public function broadcastOn()
    {
        return new PresenceChannel('online-users');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'status' => $this->status
        ];
    }
}