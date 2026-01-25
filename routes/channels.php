<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);
    return $chat && ($chat->buyer_id === $user->id || $chat->seller_id === $user->id);
});