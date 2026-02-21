@extends('layouts.app')

@section('content')
<div x-data="masterDashboard()" x-init="init()" class="min-h-screen bg from-gray-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Master Dashboard</h1>
                    <p class="text-gray-500">Complete platform overview and analytics</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-xl text-white">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-semibold">System Online</span>
                        </div>
                    </div>
                    <button class="p-3 bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-xl hover transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Pending Actions Alert -->
        @if($pendingActions['pending_verifications'] > 0 || $pendingActions['pending_refunds'] > 0 || $pendingActions['unverified_banks'] > 0)
        <div class="mb-6 bg-yellow-500 backdrop-blur-sm border border-yellow-400 rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-yellow-400 mb-2">Pending Actions Required</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm text-yellow-300">
                        @if($pendingActions['pending_verifications'] > 0)
                        <div>{{ $pendingActions['pending_verifications'] }} orders need verification</div>
                        @endif
                        @if($pendingActions['pending_refunds'] > 0)
                        <div>{{ $pendingActions['pending_refunds'] }} pending refunds</div>
                        @endif
                        @if($pendingActions['unverified_banks'] > 0)
                        <div>{{ $pendingActions['unverified_banks'] }} unverified bank accounts</div>
                        @endif
                        @if($pendingActions['low_stock_products'] > 0)
                        <div>{{ $pendingActions['low_stock_products'] }} low stock products</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
            
            <!-- Total Revenue -->
            <div class="bg-green-600 rounded-2xl shadow-2xl p-6 text-white transform hover:scale-105 transition-transform">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded-full">All Time</span>
                </div>
                <p class="text-3xl font-bold mb-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                <p class="text-sm opacity-90">Total Revenue</p>
                <p class="text-xs mt-2 opacity-75">+Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }} today</p>
            </div>

            <!-- Total Orders -->
            <div class="bg-blue-600 rounded-2xl shadow-2xl p-6 text-white transform hover:scale-105 transition-transform">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded-full">Orders</span>
                </div>
                <p class="text-3xl font-bold mb-1">{{ number_format($stats['total_orders']) }}</p>
                <p class="text-sm opacity-90">Total Orders</p>
                <p class="text-xs mt-2 opacity-75">{{ $stats['completed_orders'] }} completed</p>
            </div>

            <!-- Total Users -->
            <div class="bg-purple-600 rounded-2xl shadow-2xl p-6 text-white transform hover:scale-105 transition-transform">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded-full">Users</span>
                </div>
                <p class="text-3xl font-bold mb-1">{{ number_format($stats['total_users']) }}</p>
                <p class="text-sm opacity-90">Total Users</p>
                <p class="text-xs mt-2 opacity-75">{{ $stats['total_sellers'] }} sellers, {{ $stats['total_buyers'] }} buyers</p>
            </div>

            <!-- Total Products -->
            <div class="bg-orange-600 rounded-2xl shadow-2xl p-6 text-white transform hover:scale-105 transition-transform">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-xs bg-white px-2 py-1 rounded-full">Products</span>
                </div>
                <p class="text-3xl font-bold mb-1">{{ number_format($stats['total_products']) }}</p>
                <p class="text-sm opacity-90">Total Products</p>
                <p class="text-xs mt-2 opacity-75">{{ $stats['active_products'] }} active</p>
            </div>

        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-500 rounded-lg">
                        <svg class="w-5 h-5 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold ">{{ $stats['pending_orders'] }}</p>
                        <p class="text-xs text-gray-500">Pending Orders</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-500 rounded-lg">
                        <svg class="w-5 h-5 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold ">{{ $stats['pending_refunds'] }}</p>
                        <p class="text-xs text-gray-500">Pending Refunds</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg class="w-5 h-5 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold ">{{ number_format($performance['conversion_rate'], 1) }}%</p>
                        <p class="text-xs text-gray-500">Conversion Rate</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg class="w-5 h-5 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold ">{{ $stats['verified_accounts'] }}</p>
                        <p class="text-xs text-gray-500">Verified Banks</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-2xl border border-white border-opacity-20 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold ">Revenue Trend</h3>
                    <span class="text-sm text-gray-500">Last 30 days</span>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Orders by Status -->
            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-2xl border border-white border-opacity-20 p-6">
                <h3 class="text-xl font-bold  mb-6">Orders Status</h3>
                <div class="space-y-4">
                    @foreach(['pending_payment' => 'Pending Payment', 'pending_verification' => 'Verification', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'] as $status => $label)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-500">{{ $label }}</span>
                            <span class="text-sm font-bold ">{{ $ordersByStatus[$status] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['total_orders'] > 0 ? (($ordersByStatus[$status] ?? 0) / $stats['total_orders'] * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Data Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- Recent Users -->
            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-2xl border border-white border-opacity-20 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold ">Recent Users</h3>
                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300 font-medium">View All →</a>
                </div>

                <div class="space-y-3">
                    @foreach($recentUsers->take(5) as $user)
                    <div class="flex items-center gap-4 p-3 bg-white bg-opacity-5 rounded-xl hover transition-colors">
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center  font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold  truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 bg-purple-500 bg-opacity-30 text-purple-300 text-xs font-semibold rounded-full">
                                {{ ucfirst($user->role->name) }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-2xl border border-white border-opacity-20 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold ">Recent Orders</h3>
                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300 font-medium">View All →</a>
                </div>

                <div class="space-y-3">
                    @foreach($recentOrders->take(5) as $order)
                    <div class="p-3 bg-white bg-opacity-5 rounded-xl hover transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold  text-sm">#{{ $order->order_number }}</span>
                            <span class="px-2 py-1  text-xs font-semibold rounded-full">
                                @php
                                    $statusColors = [
                                        'delivered' => ['bg' => 'green', 'text' => 'green'],
                                        'cancelled' => ['bg' => 'red', 'text' => 'red'],
                                        'processed' => ['bg' => 'blue', 'text' => 'blue'],
                                        'refunded' => ['bg' => 'purple', 'text' => 'purple'],
                                        'pending_refund' => ['bg' => 'orange', 'text' => 'orange'],
                                        'pending_payment' => ['bg' => 'yellow', 'text' => 'yellow'],
                                        'pending_approval' => ['bg' => 'indigo', 'text' => 'indigo'],
                                        'shipped' => ['bg' => 'cyan', 'text' => 'cyan'],
                                    ];
                                    $colors = $statusColors[$order->status] ?? ['bg' => 'gray', 'text' => 'gray'];
                                @endphp
                                <span class="px-2 py-1 bg-{{ $colors['bg'] }}-500 bg-opacity-30  text-xs font-semibold rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-400">{{ $order->buyer->name }} → {{ $order->seller->name }}</span>
                            <span class="font-bold ">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Top Performers Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Top Sellers -->
            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-2xl border border-white border-opacity-20 p-6">
                <h3 class="text-xl font-bold  mb-6">Top Sellers</h3>
                <div class="space-y-3">
                    @foreach($topSellers->take(5) as $index => $seller)
                    <div class="flex items-center gap-4 p-3 bg-white bg-opacity-5 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg from-yellow-400 to-orange-500 flex items-center justify-center  font-bold text-sm">
                            #{{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold  truncate">{{ $seller->name }}</p>
                            <p class="text-xs text-gray-400">{{ $seller->total_orders }} orders</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-400">Rp {{ number_format($seller->total_revenue ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white shadow-sm border border-gray-100 backdrop-blur-sm rounded-2xl border border-white border-opacity-20 p-6">
                <h3 class="text-xl font-bold  mb-6">Top Products</h3>
                <div class="space-y-3">
                    @foreach($topProducts->take(5) as $index => $product)
                    <div class="flex items-center gap-4 p-3 bg-white bg-opacity-5 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg from-blue-400 to-purple-500 flex items-center justify-center  font-bold text-sm">
                            #{{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold  truncate">{{ $product->title }}</p>
                            <p class="text-xs text-gray-400">{{ $product->total_sold ?? 0 }} sold</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-400">Rp {{ number_format($product->total_revenue ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>

<script>
function masterDashboard() {
    return {
        revenueChart: null,

        init() {
            setTimeout(() => {
                this.initRevenueChart();
            }, 100);
        },

        initRevenueChart() {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;

            if (this.revenueChart) {
                this.revenueChart.destroy();
            }

            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            const revenueData = @json($revenueChart);
            
            const labels = revenueData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            
            const data = revenueData.map(item => item.revenue);

            this.revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue',
                        data: data,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + 'M';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000) + 'K';
                                    }
                                    return 'Rp ' + value;
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>
@endsection