<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MessageController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $chats = Chat::where('buyer_id', Auth::id())
            ->orWhere('seller_id', Auth::id())
            ->with(['buyer', 'seller', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($chat) {
                $otherUser = $chat->getOtherUser(Auth::id());
                return [
                    'id' => $chat->id,
                    'name' => $otherUser->name,
                    'avatar' => $otherUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->name),
                    'lastMessage' => $chat->messages->first()?->message ?? '',
                    'lastMessageTime' => $chat->last_message_at?->diffForHumans() ?? '',
                    'unreadCount' => $chat->messages()
                        ->where('user_id', '!=', Auth::id())
                        ->where('read', false)
                        ->count(),
                    'online' => $otherUser->isOnline ?? false,
                ];
            });

        return view('messages.index', compact('chats'));
    }

    public function getMessages(Chat $chat)
    {

        $this->authorize('view', $chat);

        $messages = $chat->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->user_id === Auth::id() ? 'me' : 'them',
                    'text' => $message->message,
                    'type' => $message->type,
                    'product' => $message->type === 'product' ? $message->metadata : null,
                    'image' => $message->type === 'image' ? ($message->metadata['url'] ?? null) : null, // ✅ Safe access
                    'time' => $message->created_at->format('H:i'),
                    'read' => $message->read,
                ];
            });

        // Mark as read
        $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json($messages);
    }

    public function store(Request $request, Chat $chat)
    {
        $this->authorize('sendMessage', $chat);

        $validated = $request->validate([
            'message' => 'required|string',
            'type' => 'nullable|in:text,product,image',
            'metadata' => 'nullable|array',
        ]);

        $message = $chat->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'text',
            'metadata' => $validated['metadata'] ?? null,
        ]);

        $chat->update(['last_message_at' => now()]);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id' => $message->id,
            'sender' => 'me',
            'text' => $message->message,
            'type' => $message->type,
            'product' => $message->type === 'product' ? $message->metadata : null,
            'image' => $message->type === 'image' ? $message->metadata['url'] : null,
            'time' => $message->created_at->format('H:i'),
            'read' => false,
        ]);
    }
}
