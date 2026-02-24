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
use App\Models\Refund;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\Paginator;

class DashboardController extends Controller
{
    public function index()
{
    $role = Auth::user()?->role?->name;

    return match($role) {
        'master' => $this->masterDashboard(),
        'seller' => $this->sellerDashboard(),
        default => view('errors.index', ['message' => 'unauthorized']),
    };
}

    public function masterDashboard(){
         // Overall Statistics
        $stats = [
            // Users
            'total_users' => User::count(),
            'total_sellers' => User::whereHas('role', fn($q) => $q->where('name', 'seller'))->count(),
            'total_buyers' => User::whereHas('role', fn($q) => $q->where('name', 'buyer'))->count(),
            'new_users_month' => User::whereMonth('created_at', now()->month)->count(),
            
            // Products
            'total_products' => Product::count(),
            'active_products' => Product::where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'products_today' => Product::whereDate('created_at', today())->count(),
            
            // Orders
            'total_orders' => Order::count(),
            'pending_orders' => Order::whereIn('status', ['pending_payment', 'pending_verification'])->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'auto_cancelled')->count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            
            // Revenue
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'revenue_month' => Order::where('status', 'delivered')->whereMonth('created_at', now()->month)->sum('total_amount'),
            'revenue_today' => Order::where('status', 'delivered')->whereDate('created_at', today())->sum('total_amount'),
            'pending_revenue' => Order::whereIn('status', ['processing', 'shipped'])->sum('total_amount'),
            
            // Refunds
            'total_refunds' => Refund::count(),
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'approved_refunds' => Refund::where('status', 'approved')->count(),
            'refund_amount' => Refund::where('status', 'approved')->sum('refund_amount'),
            
            // Bank Accounts
            'total_bank_accounts' => BankAccount::count(),
            'verified_accounts' => BankAccount::where('is_verified', true)->count(),
            'unverified_accounts' => BankAccount::where('is_verified', false)->count(),
        ];

        // Recent Users
        $recentUsers = User::with('role')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent Orders
        $recentOrders = Order::with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top Sellers (by revenue)
        $topSellers = User::whereHas('role', fn($q) => $q->where('name', 'seller'))
            ->withCount(['sellerOrders as total_orders'])
            ->withSum(['sellerOrders as total_revenue' => function($q) {
                $q->where('status', 'delivered');
            }], 'total_amount')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Top Products (by sales)
        $topProducts = Product::with('seller')
            ->withCount(['orderDetails as total_sold' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->withSum(['orderDetails as total_revenue'], 'price')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Revenue Chart (Last 30 days)
        $revenueChart = Order::where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Orders by Status
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Users Growth (Last 12 months)
        $usersGrowth = User::where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Platform Performance
        $performance = [
            'avg_order_value' => $stats['completed_orders'] > 0 
                ? $stats['total_revenue'] / $stats['completed_orders'] 
                : 0,
            'conversion_rate' => $stats['total_orders'] > 0 
                ? ($stats['completed_orders'] / $stats['total_orders']) * 100 
                : 0,
            'avg_products_per_seller' => $stats['total_sellers'] > 0 
                ? $stats['total_products'] / $stats['total_sellers'] 
                : 0,
            'seller_with_bank' => User::whereHas('role', fn($q) => $q->where('name', 'seller'))
                ->whereHas('bankAccounts')
                ->count(),
        ];

        // Pending Actions
        $pendingActions = [
            'pending_verifications' => Order::where('status', 'pending_verification')->count(),
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'unverified_banks' => BankAccount::where('is_verified', false)->count(),
            'low_stock_products' => Product::whereBetween('stock', [1, 5])->count(),
        ];

        return view('dashboard.index', compact(
            'stats',
            'recentUsers',
            'recentOrders',
            'topSellers',
            'topProducts',
            'revenueChart',
            'ordersByStatus',
            'usersGrowth',
            'performance',
            'pendingActions'
        ));
    }

    public function sellerDashboard()
    { {
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
                ->withCount(['orderDetails as total_sold' => function ($query) {
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

            return view('dashboard.seller.dashboard', compact(
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

    public function showUsers(Request $request)
    {
        $query = User::with('role');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('id', $request->role);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return view('dashboard.user.index', compact('users', 'roles'));
    }

    public function showProfile()
    {
        return view('dashboard.profile.index');
    }
}
