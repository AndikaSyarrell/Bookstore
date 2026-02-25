@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12" x-data="orderDetail()" x-init="init()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex justify-between">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    @if(Auth::user()->role->name === 'buyer')
                    My Orders
                    @else
                    Sales Orders
                    @endif
                </h1>
                <p class="text-gray-600 mt-2">Track and manage your orders</p>
            </div>
            <div class="">
                <button
                    @click="openModal()"
                    class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Order Summary</span>
                </button>
            </div>
        </div>

        <!-- Orders List -->
        @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h3>
                            @php
                            $statusColors = [
                            'pending_payment' => 'bg-yellow-100 text-yellow-800',
                            'pending_verification' => 'bg-blue-100 text-blue-800',
                            'payment_rejected' => 'bg-red-100 text-red-800',
                            'processing' => 'bg-indigo-100 text-indigo-800',
                            'shipped' => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                            $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        @if(Auth::user()->role->name === 'buyer')
                        <p class="text-sm text-gray-600">Seller: {{ $order->seller->name }}</p>
                        @else
                        <p class="text-sm text-gray-600">Buyer: {{ $order->buyer->name }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">{{ $order->orderDetails->count() }} {{ Str::plural('item', $order->orderDetails->count()) }}</p>
                    </div>
                </div>

                <!-- Products Preview -->
                <div class="border-t border-gray-200 pt-4 mb-4">
                    <div class="flex gap-3">
                        @foreach($order->orderDetails->take(3) as $detail)
                        <img
                            src="{{ $detail->product->img ? asset('storage/products/' . $detail->product->img) : 'https://via.placeholder.com/60' }}"
                            alt="{{ $detail->product->title }}"
                            class="w-16 h-16 object-cover rounded border">
                        @endforeach
                        @if($order->orderDetails->count() > 3)
                        <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded border text-sm font-semibold text-gray-600">
                            +{{ $order->orderDetails->count() - 3 }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <a
                        href="{{ route('order.show', $order->id) }}"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        View Details
                    </a>

                    @if(Auth::user()->role->name === 'buyer')
                    @if($order->status === 'pending_payment')
                    <a
                        href="{{ route('order.show', $order->id) }}"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Pay Now
                    </a>
                    @endif
                    @endif

                    @if($order->payment)
                    <span class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg">
                        Payment: {{ ucfirst($order->payment->status) }}
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-600 mb-6">
                @if(Auth::user()->role->name === 'buyer')
                Start shopping to see your orders here
                @else
                Orders from buyers will appear here
                @endif
            </p>
            @if(Auth::user()->role->name === 'buyer')
            <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Browse Products
            </a>
            @endif
        </div>
        @endif

        <!-- Modal -->
    <div 
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="showModal = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="showModal = false"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.stop
                class="relative bg-white rounded-2xl max-w-4xl w-full shadow-2xl"
            >
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white">Order Summary</h3>
                        <button @click="showModal = false" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6">
                    
                    <!-- Settings Form -->
                    <div class="space-y-5 mb-6">
                        
                        <!-- Period Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Time Period</label>
                            <select 
                                x-model="settings.period"
                                @change="periodChanged()"
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            >
                                <option value="7days">Last 7 Days</option>
                                <option value="30days">Last 30 Days</option>
                                <option value="3months">Last 3 Months</option>
                                <option value="6months">Last 6 Months</option>
                                <option value="1year">Last Year</option>
                                <option value="all">All Time</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>

                        <!-- Custom Date Range (show if custom selected) -->
                        <div x-show="settings.period === 'custom'" class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">From Date</label>
                                <input 
                                    type="date" 
                                    x-model="settings.date_from"
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">To Date</label>
                                <input 
                                    type="date" 
                                    x-model="settings.date_to"
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Order Status</label>
                            <select 
                                x-model="settings.status"
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            >
                                <option value="all">All Status</option>
                                <option value="pending_payment">Pending Payment</option>
                                <option value="pending_verification">Pending Verification</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>

                    </div>

                    <!-- Generate Button -->
                    <button 
                        @click="generateSummary()"
                        :disabled="isLoading"
                        class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition-colors"
                    >
                        <span x-show="!isLoading">Generate Summary</span>
                        <span x-show="isLoading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating...
                        </span>
                    </button>

                    <!-- Summary Results (show after generate) -->
                    <div x-show="summaryData" x-transition class="mt-6">
                        
                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                                <p class="text-sm text-blue-700 mb-1">Total Orders</p>
                                <p class="text-2xl font-bold text-blue-900" x-text="summaryData?.stats?.total_orders || 0"></p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                                <p class="text-sm text-green-700 mb-1">Total Spent</p>
                                <p class="text-2xl font-bold text-green-900" x-text="'Rp ' + formatNumber(summaryData?.stats?.total_spent || 0)"></p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                                <p class="text-sm text-purple-700 mb-1">Items Purchased</p>
                                <p class="text-2xl font-bold text-purple-900" x-text="summaryData?.stats?.total_items || 0"></p>
                            </div>
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                                <p class="text-sm text-orange-700 mb-1">Avg Order Value</p>
                                <p class="text-2xl font-bold text-orange-900" x-text="'Rp ' + formatNumber(summaryData?.stats?.average_order_value || 0)"></p>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Completed</p>
                                    <p class="text-lg font-bold text-green-600" x-text="summaryData?.stats?.completed_orders || 0"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Pending</p>
                                    <p class="text-lg font-bold text-yellow-600" x-text="summaryData?.stats?.pending_orders || 0"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Cancelled</p>
                                    <p class="text-lg font-bold text-red-600" x-text="summaryData?.stats?.cancelled_orders || 0"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Download PDF Button -->
                        <button 
                            @click="downloadPdf()"
                            class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 flex items-center justify-center gap-2 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download PDF Report
                        </button>

                    </div>

                </div>

            </div>
        </div>
    </div>

    </div>
</div>

<script>
function orderDetail() {
    return {
        showModal: false,
        isLoading: false,
        summaryData: null,
        
        settings: {
            period: '30days',
            status: 'all',
            date_from: '',
            date_to: '',
        },

        init() {
            // Initialize
        },

        openModal() {
            this.showModal = true;
            this.summaryData = null;
        },

        periodChanged() {
            if (this.settings.period !== 'custom') {
                this.settings.date_from = '';
                this.settings.date_to = '';
            }
        },

        async generateSummary() {
            // Validate custom dates
            if (this.settings.period === 'custom') {
                if (!this.settings.date_from || !this.settings.date_to) {
                    alert('Please select both from and to dates for custom range');
                    return;
                }
            }

            this.isLoading = true;

            try {
                const response = await fetch(`{{route('orders.summary')}}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.settings)
                });

                const data = await response.json();

                if (data.success) {
                    this.summaryData = data;
                } else {
                    alert('Failed to generate summary');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while generating summary');
            } finally {
                this.isLoading = false;
            }
        },

        downloadPdf() {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{route("orders.summary.pdf")}}';
            form.target = '_blank';

            // CSRF Token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);

            // Add settings as form data
            Object.keys(this.settings).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = this.settings[key];
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        },

        formatNumber(num) {
            return Math.round(num).toLocaleString('id-ID');
        }
    }
}
</script>
@endsection