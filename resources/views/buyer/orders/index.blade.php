@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
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
                            class="w-16 h-16 object-cover rounded border"
                        >
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
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        View Details
                    </a>

                    @if(Auth::user()->role->name === 'buyer')
                        @if($order->status === 'pending_payment')
                        <a 
                            href="{{ route('order.show', $order->id) }}" 
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                        >
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
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

    </div>
</div>
@endsection