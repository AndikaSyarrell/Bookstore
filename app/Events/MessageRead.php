<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatId;
    public $userId;
    public $messageIds;

    public function __construct($chatId, $userId, array $messageIds)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->messageIds = $messageIds;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('chat.' . $this->chatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'message_ids' => $this->messageIds,
        ];
    }
}