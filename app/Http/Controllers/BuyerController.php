<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\Paginator;

class BuyerController extends Controller
{

    public function index()
    {
        if (Auth::check() && Auth::user()->role->name === 'buyer') {
            // Ambil data categories (max 6)
            $categories = Category::withCount(['products' => function ($query) {
                $query->where('stock', '>', 0);
            }])
                ->limit(6)
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'count' => $category->products_count,
                        'slug' => $category->slug,
                    ];
                });

            // 2. GET FEATURED PRODUCTS (max 6)
            // Rekomendasi query untuk featured products
            $featuredProducts = Product::query()
                ->where('stock', '>', 0)
                ->with(['seller', 'category'])
                ->withSum('orderDetails as sold', 'quantity')
                ->orderByDesc('sold')
                ->limit(6)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->title, // 'title' di database = 'name' di frontend
                        'image' => $product->img ? asset('storage/products/' . $product->img) : null,
                        'price' => (float) $product->price,
                        'sold' => (int) $product->sold,
                        // data tambahan jika diperlukan:
                        'author' => $product->author,
                        'stock' => $product->stock,
                    ];
                });

            return view('buyer.index', compact('categories', 'featuredProducts'));
        } else {
            return view('errors.index', ['message' => 'unauthorized']);
        }
    }


    public function bookDetails()
    {
        return view('buyer.books.index');
    }

    public function showCart()
    {
        return view('buyer.cart.index');
    }
    public function showCheckout()
    {
        return view('buyer.cart.checkout');
    }

    public function showPayment()
    {
        return view('buyer.cart.payment');
    }

    public function showTracks()
    {
        return view('buyer.package.index');
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

    public function showProfile()
    {
        return view('dashboard.profile.index');
    }

    public function showBookDetails()
    {
        return view('buyer.books.index');
    }
}
