<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Presence channel for chat rooms
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Check if user is part of this chat
    $chat = Chat::find($chatId);
    
    if (!$chat) {
        return false;
    }
    
    // User must be either buyer or seller of this chat
    if ($user->id === $chat->buyer_id || $user->id === $chat->seller_id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role->name ?? 'user',
        ];
    }
    
    return false;
});

// Private channel for user notifications
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    // User can only listen to their own notifications
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('online-users', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name
    ];
});