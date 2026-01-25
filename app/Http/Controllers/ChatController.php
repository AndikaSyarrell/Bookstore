<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\TypingIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // Dapatkan percakapan dengan user tertentu
    public function getConversation($userId)
    {
        $currentUserId = Auth::id();
        $otherUser = User::findOrFail($userId);
        
        $messages = Chat::where(function($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $currentUserId)
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($currentUserId, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $currentUserId);
        })->orderBy('created_at', 'asc')
          ->get();
        
        // Dalam real implementation, Anda perlu menyimpan encryption key dengan aman
        // Ini hanya contoh sederhana
        $encryptionKey = session('chat_key_' . min($currentUserId, $userId) . '_' . max($currentUserId, $userId));
        
        return response()->json([
            'messages' => $messages,
            'otherUser' => $otherUser,
            'encryptionKey' => $encryptionKey
        ]);
    }

    // Kirim pesan baru
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        // Generate encryption key dan IV
        $encryptionKey = Str::random(32);
        $iv = Str::random(16);
        
        // Enkripsi pesan
        $encryptedMessage = Chat::encryptMessage($request->message, $encryptionKey, $iv);
        
        // Generate message hash
        $messageHash = Chat::generateMessageHash($request->message, $sender->id, $receiver->id);
        
        // Simpan ke database
        $chat = Chat::create([
            'message_hash' => $messageHash,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'encrypted_message' => $encryptedMessage,
            'encryption_key_hash' => Hash::make($encryptionKey),
            'iv' => $iv,
            'sender_role' => $sender->role, // asumsi ada kolom role di users table
            'receiver_role' => $receiver->role,
            'is_read' => false
        ]);

        // Simpan encryption key di session (untuk demo)
        $conversationId = min($sender->id, $receiver->id) . '_' . max($sender->id, $receiver->id);
        session(['chat_key_' . $conversationId => $encryptionKey]);

        // Broadcast event
        broadcast(new MessageSent($chat, $encryptionKey));

        return response()->json(['success' => true, 'message' => $chat]);
    }

    // Update status typing
    public function typingStatus(Request $request)
    {
        $request->validate([
            'is_typing' => 'required|boolean',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $conversationId = min(Auth::id(), $request->receiver_id) . '.' . max(Auth::id(), $request->receiver_id);
        
        broadcast(new TypingIndicator(Auth::id(), $request->is_typing, $conversationId));

        return response()->json(['success' => true]);
    }

    // Tandai pesan sebagai sudah dibaca
    public function markAsRead($messageId)
    {
        $chat = Chat::findOrFail($messageId);
        
        if ($chat->receiver_id === Auth::id()) {
            $chat->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }
}