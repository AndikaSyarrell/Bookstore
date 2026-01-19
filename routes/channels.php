<?php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat-room', function ($user) {
    // Hanya user login yang bisa join. 
    // Data yang direturn akan diterima oleh user lain di frontend.
    return [
        'id' => $user->id,
        'name' => $user->name,
        'img' => $user->img
    ];
});