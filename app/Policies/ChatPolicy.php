<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    public function view(User $user, Chat $chat): bool
    {
    //     dd([
    //     'ID_User_Login' => $user->id,
    //     'Buyer_ID_di_Chat' => $chat->buyer_id,
    //     'Seller_ID_di_Chat' => $chat->seller_id,
    //     'Hasil_Perbandingan' => ($chat->seller_id === $user->id)
    // ]);
        return $chat->buyer_id === $user->id || $chat->seller_id === $user->id;
    }

    public function sendMessage(User $user, Chat $chat): bool
    {
        return $chat->buyer_id === $user->id || $chat->seller_id === $user->id;
    }
}
    // Register in AuthServiceProvider
// protected $policies = [
//     Chat::class => ChatPolicy::class,
// ];