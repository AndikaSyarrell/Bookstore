{{-- resources/views/seller/orders/index.blade.php --}}
@extends('layouts.app')
@section('title','Pre-order')

@section('content')
<div x-data="sellerApproval()" x-init="init()" class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header with Tabs -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Management</h1>
            <p class="text-gray-600">Manage and approve customer orders</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending Verification</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_verification'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Processing</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['processing'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Shipped</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['shipped'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['delivered'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button 
                        @click="activeTab = 'pending'"
                        :class="activeTab === 'pending' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        Pending Verification
                        @if(($stats['pending_verification'] ?? 0) > 0)
                        <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">{{ $stats['pending_verification'] }}</span>
                        @endif
                    </button>
                    <button 
                        @click="activeTab = 'processing'"
                        :class="activeTab === 'processing' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        Processing Orders
                    </button>
                    <button 
                        @click="activeTab = 'shipped'"
                        :class="activeTab === 'shipped' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        Shipped Orders
                    </button>
                    <button 
                        @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        All Orders
                    </button>
                </nav>
            </div>
        </div>

        <!-- Pending Verification Tab -->
        <div x-show="activeTab === 'pending'" class="space-y-4">
            @forelse($pendingOrders as $order)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Order Header -->
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600">Buyer: <span class="font-medium">{{ $order->buyer->name }}</span></p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                            Pending Verification
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Payment Proof -->
                @if($order->payment && $order->payment->proof_image)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Payment Proof</h4>
                    <div class="flex gap-4">
                        <a href="{{ asset('storage/payment-proofs/' . $order->payment->proof_image) }}" target="_blank" class="block">
                            <img 
                                src="{{ asset('storage/payment-proofs/' . $order->payment->proof_image) }}" 
                                alt="Payment Proof" 
                                class="w-48 h-auto rounded border hover:opacity-90 transition-opacity cursor-pointer"
                            >
                        </a>
                        <div class="flex-1">
                            <div class="text-sm text-gray-700 space-y-2">
                                <p><strong>Uploaded:</strong> {{ $order->payment->updated_at->format('d M Y, H:i') }}</p>
                                <p><strong>Amount:</strong> Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p>
                                @if($order->payment->note)
                                <p><strong>Buyer Notes:</strong></p>
                                <p class="text-gray-600 italic">{{ $order->payment->note }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Order Items Preview -->
                <div class="border-t border-gray-200 pt-4 mb-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Order Items ({{ $order->orderDetails->count() }} items)</h4>
                    <div class="flex gap-3 flex-wrap">
                        @foreach($order->orderDetails->take(5) as $detail)
                        <div class="flex items-center gap-2 bg-gray-50 rounded px-3 py-2">
                            <img 
                                src="{{ $detail->product->img ? asset('storage/products/' . $detail->product->img) : 'https://via.placeholder.com/40' }}" 
                                alt="{{ $detail->product->title }}"
                                class="w-10 h-10 object-cover rounded"
                            >
                            <div class="text-xs">
                                <p class="font-medium text-gray-900 line-clamp-1">{{ $detail->product->title }}</p>
                                <p class="text-gray-600">{{ $detail->quantity }}x</p>
                            </div>
                        </div>
                        @endforeach
                        @if($order->orderDetails->count() > 5)
                        <div class="flex items-center justify-center bg-gray-100 rounded px-3 py-2 text-sm font-semibold text-gray-600">
                            +{{ $order->orderDetails->count() - 5 }} more
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button 
                        @click="approvePayment({{ $order->id }})"
                        class="flex-1 px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve Payment
                    </button>
                    <button 
                        @click="rejectPayment({{ $order->id }})"
                        class="flex-1 px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reject Payment
                    </button>
                    <a 
                        href="{{ route('order.show-seller', $order->id) }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        View Detail
                    </a>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-600">No pending payment verification</p>
            </div>
            @endforelse
        </div>

        <!-- Processing Orders Tab -->
        <div x-show="activeTab === 'processing'" class="space-y-4">
            @forelse($processingOrders as $order)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600">Buyer: <span class="font-medium">{{ $order->buyer->name }}</span></p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                            Processing
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="border-t border-gray-200 pt-4 mb-4">
                    <div class="flex gap-3 flex-wrap">
                        @foreach($order->orderDetails->take(5) as $detail)
                        <div class="flex items-center gap-2 bg-gray-50 rounded px-3 py-2">
                            <img 
                                src="{{ $detail->product->img ? asset('storage/products/' . $detail->product->img) : 'https://via.placeholder.com/40' }}" 
                                alt="{{ $detail->product->title }}"
                                class="w-10 h-10 object-cover rounded"
                            >
                            <div class="text-xs">
                                <p class="font-medium text-gray-900">{{ $detail->product->title }}</p>
                                <p class="text-gray-600">{{ $detail->quantity }}x</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3">
                    <button 
                        @click="updateStatus({{ $order->id }}, 'shipped')"
                        class="flex-1 px-4 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors"
                    >
                        Mark as Shipped
                    </button>
                    <a 
                        href="{{ route('order.show-seller', $order->id) }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50"
                    >
                        View Detail
                    </a>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-600">No orders being processed</p>
            </div>
            @endforelse
        </div>

        <!-- Shipped Orders Tab -->
        <div x-show="activeTab === 'shipped'" class="space-y-4">
            @forelse($shippedOrders as $order)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600">Buyer: <span class="font-medium">{{ $order->buyer->name }}</span></p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-semibold rounded-full">
                            Shipped
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button 
                        @click="updateStatus({{ $order->id }}, 'delivered')"
                        class="flex-1 px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors"
                    >
                        Mark as Delivered
                    </button>
                    <a 
                        href="{{ route('order.show-seller', $order->id) }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50"
                    >
                        View Detail
                    </a>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-600">No shipped orders</p>
            </div>
            @endforelse
        </div>

        <!-- All Orders Tab -->
        <div x-show="activeTab === 'all'" class="space-y-4">
            @forelse($allOrders as $order)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600">Buyer: {{ $order->buyer->name }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $statusColors = [
                                'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                'pending_verification' => 'bg-blue-100 text-blue-800',
                                'processing' => 'bg-indigo-100 text-indigo-800',
                                'shipped' => 'bg-purple-100 text-purple-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                        @endphp
                        <span class="px-3 py-1 {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }} text-sm font-semibold rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('order.show-seller', $order->id) }}"
                    class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        View Details →
                    </a>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-600">No orders found</p>
            </div>
            @endforelse

            <!-- Pagination -->
            @if($allOrders->hasPages())
            <div class="mt-6">
                {{ $allOrders->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

<script>
function sellerApproval() {
    return {
        activeTab: 'pending',

        init() {
            // Set active tab based on URL hash
            const hash = window.location.hash.substring(1);
            if (['pending', 'processing', 'shipped', 'all'].includes(hash)) {
                this.activeTab = hash;
            }
        },

        async approvePayment(orderId) {
            const notes = prompt('Verification notes (optional):');
            
            try {
                const response = await fetch(`/home/orders/${orderId}/verify-payment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        action: 'approve',
                        notes: notes 
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Payment approved! Order moved to processing.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to approve payment');
                }
            } catch (error) {
                alert('An error occurred');
                console.error(error);
            }
        },

        async rejectPayment(orderId) {
            const reason = prompt('Reason for rejection (required):');
            
            if (!reason || reason.trim() === '') {
                alert('Please provide a reason for rejection');
                return;
            }

            if (!confirm('Are you sure you want to reject this payment?')) {
                return;
            }

            try {
                const response = await fetch(`/orders/${orderId}/verify-payment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        action: 'reject',
                        notes: reason 
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Payment rejected. Buyer will be notified.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to reject payment');
                }
            } catch (error) {
                alert('An error occurred');
                console.error(error);
            }
        },

        async updateStatus(orderId, status) {
            const statusMessages = {
                'shipped': 'Mark this order as shipped?',
                'delivered': 'Mark this order as delivered?'
            };

            if (!confirm(statusMessages[status] || 'Update order status?')) {
                return;
            }

            const notes = prompt('Notes (optional):');

            try {
                const response = await fetch(`/orders/${orderId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        status: status,
                        notes: notes 
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Order status updated successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update status');
                }
            } catch (error) {
                alert('An error occurred');
                console.error(error);
            }
        }
    }
}
</script>
@endsection