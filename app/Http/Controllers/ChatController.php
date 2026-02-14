<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Start or continue chat with seller (with product context)
     */
    public function startChat(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $productId = $request->get('product_id');

        // Validate seller exists
        $seller = User::findOrFail($sellerId);

        // Check if chat already exists
        $chat = Chat::where('buyer_id', Auth::id())
            ->where('seller_id', $sellerId)
            ->first();

        if (!$chat) {
            // Create new chat
            $chat = Chat::create([
                'buyer_id' => Auth::id(),
                'seller_id' => $sellerId,
                'last_message_at' => now()
            ]);
        }

        // If product context provided, send initial message
        if ($productId) {
            $product = Product::find($productId);

            if ($product) {
                // Check if product context message already sent
                $existingProductMessage = Message::where('chat_id', $chat->id)
                    ->where('type', 'product_context')
                    ->where('metadata->product_id', $productId)
                    ->exists();

                if (!$existingProductMessage) {
                    // Send product context message
                    $message = Message::create([
                        'chat_id' => $chat->id,
                        'user_id' => Auth::id(),
                        'message' => "Hi! I'm interested in this product:",
                        'type' => 'product_context',
                        'metadata' => json_encode([
                            'product_id' => $product->id,
                            'product_title' => $product->title,
                            'product_price' => $product->price,
                            'product_image' => $product->img,
                            'product_url' => route('products.show', $product->id)
                        ]),
                        'read' => false
                    ]);

                    $chat->update(['last_message_at' => now()]);

                    // Broadcast the message
                    broadcast(new MessageSent($message))->toOthers();
                }
            }
        }

        // Redirect to chat page
        return redirect()->route('messages.show', $chat->id);
    }

    /**
     * Display chat page
     */
    public function show($id)
    {
        $chat = Chat::with(['buyer', 'seller'])
            ->where(function ($query) {
                $query->where('buyer_id', Auth::id())
                    ->orWhere('seller_id', Auth::id());
            })
            ->findOrFail($id);

        // Get messages
        $messages = Message::where('chat_id', $chat->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $messagesFormatted = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'chat_id' => $msg->chat_id,
                'user_id' => $msg->user_id,
                'user_name' => $msg->user->name,
                'message' => $msg->message,
                'type' => $msg->type,
                'metadata_parsed' => json_decode($msg->metadata),
                'read' => $msg->read,
                'created_at' => $msg->created_at->toISOString(),
                'formatted_time' => $msg->created_at->format('H:i'),
            ];
        });

        // Mark messages as read and broadcast
        $unreadMessages = Message::where('chat_id', $chat->id)
            ->where('user_id', '!=', Auth::id())
            ->where('read', false)
            ->pluck('id')
            ->toArray();

        if (!empty($unreadMessages)) {
            Message::whereIn('id', $unreadMessages)->update(['read' => true]);

            // Broadcast read event
            broadcast(new MessageRead($chat->id, Auth::id(), $unreadMessages))->toOthers();
        }

        return view('messages.show', compact('chat', 'messages'));
    }

    /**
     * List all chats
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role->name === 'buyer') {
            $chats = Chat::where('buyer_id', $user->id)
                ->with(['seller', 'messages' => function ($query) {
                    $query->latest()->limit(1);
                }])
                ->withCount(['messages as unread_count' => function ($query) {
                    $query->where('user_id', '!=', Auth::id())
                        ->where('read', false);
                }])
                ->orderBy('last_message_at', 'desc')
                ->get();
        } else {
            $chats = Chat::where('seller_id', $user->id)
                ->with(['buyer', 'messages' => function ($query) {
                    $query->latest()->limit(1);
                }])
                ->withCount(['messages as unread_count' => function ($query) {
                    $query->where('user_id', '!=', Auth::id())
                        ->where('read', false);
                }])
                ->orderBy('last_message_at', 'desc')
                ->get();
        }

        return view('messages.index', compact('chats'));
    }

    /**
     * Send message with broadcasting
     */
    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $chat = Chat::where(function ($query) {
            $query->where('buyer_id', Auth::id())
                ->orWhere('seller_id', Auth::id());
        })
            ->findOrFail($chatId);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'type' => 'text',
            'read' => false
        ]);

        $chat->update(['last_message_at' => now()]);

        // Broadcast the message to other users
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('user')
        ]);
    }

    /**
     * Broadcast typing status
     */
    public function typing(Request $request, $chatId)
    {
        $request->validate([
            'is_typing' => 'required|boolean'
        ]);

        $chat = Chat::where(function ($query) {
            $query->where('buyer_id', Auth::id())
                ->orWhere('seller_id', Auth::id());
        })
            ->findOrFail($chatId);

        // Broadcast typing event
        broadcast(new UserTyping(
            $chat->id,
            Auth::id(),
            Auth::user()->name,
            $request->is_typing
        ))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead($chatId)
    {
        $chat = Chat::where(function ($query) {
            $query->where('buyer_id', Auth::id())
                ->orWhere('seller_id', Auth::id());
        })
            ->findOrFail($chatId);

        $messageIds = Message::where('chat_id', $chat->id)
            ->where('user_id', '!=', Auth::id())
            ->where('read', false)
            ->pluck('id')
            ->toArray();

        if (!empty($messageIds)) {
            Message::whereIn('id', $messageIds)->update(['read' => true]);

            // Broadcast read event
            broadcast(new MessageRead($chat->id, Auth::id(), $messageIds))->toOthers();
        }

        return response()->json(['success' => true]);
    }
}
