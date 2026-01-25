<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Contracts\Pagination\Paginator;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check() && \in_array(Auth::user()->role->name, ['master', 'seller'])) {
            return view('dashboard.index');
        } else {
            return view('errors.index', ['message' => 'unauthorized']);
        }
    }

    public function showProducts()
    {
        $products = Product::where('seller_id', '=', Auth::user()->id)->paginate(5);
        return view('dashboard.products.index', ['products' => $products]);
    }

    public function showOrders()
    {
        return view('dashboard.preorder.index');
    }

    public function showChats()
    {
        $chats = Chat::where('buyer_id', Auth::id())
            ->orWhere('seller_id', Auth::id())
            ->with(['buyer', 'seller'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($chat) {
                $otherUser = $chat->getOtherUser(Auth::id());
                $lastMessage = $chat->messages()->latest()->first(); // Query terpisah

                return [
                    'id' => $chat->id,
                    'name' => $otherUser->name,
                    'avatar' => $otherUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->name),
                    'lastMessage' => $lastMessage?->message ?? 'Belum ada pesan', //  Default text
                    'lastMessageTime' => $chat->last_message_at?->diffForHumans() ?? '',
                    'unreadCount' => $chat->messages()
                        ->where('user_id', '!=', Auth::id())
                        ->where('read', false)
                        ->count(),
                    'online' => false, // Will be updated by presence channel
                ];
            })
            ->toArray(); // Convert to array untuk JSON serialization

        return view('messages.index', compact('chats'));
    }

    public function showCategories()
    {
        $categories = Category::paginate(5);

        return view('dashboard.categories.index', ['categories' => $categories]);
    }

    public function showUsers()
    {
        $users = User::where('role_id', '!=', Auth::user()->role_id)->paginate(5);

        return view('dashboard.user.index', ['users' => $users]);
    }

    public function showProfile()
    {
        return view('dashboard.profile.index');
    }

    public function buyerPage()
    {
        return view('buyer.index');
    }
}
