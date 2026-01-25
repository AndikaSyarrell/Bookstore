<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingIndicator implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $isTyping;
    public $conversationId;

    public function __construct($userId, $isTyping, $conversationId)
    {
        $this->userId = $userId;
        $this->isTyping = $isTyping;
        $this->conversationId = $conversationId;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('typing.' . $this->conversationId)
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}