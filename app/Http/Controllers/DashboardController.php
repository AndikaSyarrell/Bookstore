<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Product;
use App\Models\User;
use App\Models\Chat;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
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

    public function sellerDashboard()
    {
        {
        $sellerId = Auth::id();

        // Statistics
        $stats = [
            // Products
            'total_products' => Product::where('seller_id', $sellerId)->count(),
            'active_products' => Product::where('seller_id', $sellerId)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('seller_id', $sellerId)->where('stock', 0)->count(),
            'low_stock' => Product::where('seller_id', $sellerId)->whereBetween('stock', [1, 5])->count(),

            // Orders
            'total_orders' => Order::where('seller_id', $sellerId)->count(),
            'pending_orders' => Order::where('seller_id', $sellerId)
                ->whereIn('status', ['pending_payment', 'pending_verification'])
                ->count(),
            'processing_orders' => Order::where('seller_id', $sellerId)
                ->where('status', 'processing')
                ->count(),
            'completed_orders' => Order::where('seller_id', $sellerId)
                ->where('status', 'delivered')
                ->count(),

            // Revenue
            'total_revenue' => Order::where('seller_id', $sellerId)
                ->where('status', 'delivered')
                ->sum('total_amount'),
            'pending_revenue' => Order::where('seller_id', $sellerId)
                ->whereIn('status', ['pending_verification', 'processing', 'shipped'])
                ->sum('total_amount'),
            'monthly_revenue' => Order::where('seller_id', $sellerId)
                ->where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
        ];

        // Recent Orders
        $recentOrders = Order::where('seller_id', $sellerId)
            ->with(['buyer', 'orderDetails'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top Selling Products
        $topProducts = Product::where('seller_id', $sellerId)
            ->withCount(['orderDetails as total_sold' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Low Stock Products
        $lowStockProducts = Product::where('seller_id', $sellerId)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        // Revenue Chart Data (Last 7 days)
        $revenueData = Order::where('seller_id', $sellerId)
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Orders by Status
        $ordersByStatus = Order::where('seller_id', $sellerId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('dashboard.index', compact(
            'stats',
            'recentOrders',
            'topProducts',
            'lowStockProducts',
            'revenueData',
            'ordersByStatus'
        ));
    }

    }

    public function showProducts()
    {
        $products = Product::where('seller_id', '=', Auth::user()->id)->paginate(5);
        return view('dashboard.products.index', ['products' => $products]);
    }

    public function showOrders()
    {
        $sellerId = Auth::id();

        // Get stats
        $stats = [
            'pending_verification' => Order::where('seller_id', $sellerId)
                ->where('status', 'pending_verification')
                ->count(),
            'processing' => Order::where('seller_id', $sellerId)
                ->where('status', 'processing')
                ->count(),
            'shipped' => Order::where('seller_id', $sellerId)
                ->where('status', 'shipped')
                ->count(),
            'delivered' => Order::where('seller_id', $sellerId)
                ->where('status', 'delivered')
                ->count(),
            'pending_refund' => Order::where('seller_id', $sellerId)
                ->where('status', 'refund_pending')
                ->count(),
        ];

        // Get orders by status
        $pendingOrders = Order::where('seller_id', $sellerId)
            ->where('status', 'pending_verification')
            ->with(['buyer', 'payment', 'orderDetails.product'])
            ->orderBy('created_at', 'asc')
            ->get();

        $processingOrders = Order::where('seller_id', $sellerId)
            ->where('status', 'processing')
            ->with(['buyer', 'orderDetails.product'])
            ->orderBy('created_at', 'asc')
            ->get();

        $shippedOrders = Order::where('seller_id', $sellerId)
            ->where('status', 'shipped')
            ->with(['buyer', 'orderDetails.product'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        $refundRequests = Order::where('seller_id', $sellerId)
            ->where('status', 'refund_pending')
            ->with(['buyer', 'refund', 'orderDetails.product'])
            ->orderBy('created_at', 'asc')
            ->get();
            // dd($refundRequests);

        $allOrders = Order::where('seller_id', $sellerId)
            ->with(['buyer', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.preorder.index', compact(
            'stats',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'refundRequests',
            'allOrders'
        ));
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
}
