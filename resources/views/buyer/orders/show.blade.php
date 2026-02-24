@extends('layouts.app')

@section('content')
<div x-data="orderDetail()" x-init="init()" class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
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
                    <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusColor }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex-1 text-center">
                        <div class="w-8 h-8 mx-auto rounded-full {{ in_array($order->status, ['pending_payment', 'pending_verification', 'payment_rejected', 'processing', 'shipped', 'delivered']) ? 'bg-blue-600' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-gray-600">Order Placed</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    <div class="flex-1 text-center">
                        <div class="w-8 h-8 mx-auto rounded-full {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-blue-600' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-gray-600">Processing</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    <div class="flex-1 text-center">
                        <div class="w-8 h-8 mx-auto rounded-full {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-blue-600' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-gray-600">Shipped</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $order->status === 'delivered' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    <div class="flex-1 text-center">
                        <div class="w-8 h-8 mx-auto rounded-full {{ $order->status === 'delivered' ? 'bg-blue-600' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-gray-600">Delivered</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Payment Section - Only show if pending_payment or pending_verification -->
                @if(Auth::id() === $order->buyer_id && in_array($order->status, ['pending_payment', 'pending_verification', 'payment_rejected']))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Information</h2>

                    @if($order->status === 'pending_payment' || $order->status === 'payment_rejected')

                    <!-- Bank Account Info -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Transfer Pembayaran ke Rekening Penjual</h3>

                        <!-- Auto-Cancel Countdown (if pending payment) -->
                        @if($order->status === 'pending_payment' && $order->auto_cancel_at)
                        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-yellow-800 mb-1">Payment Deadline</h3>
                                    <p class="text-sm text-yellow-700 mb-2">
                                        Complete payment before this order is automatically cancelled
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-2 bg-yellow-100 rounded-lg">
                                            <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <template x-if="!isExpired">
                                                <span class="font-mono font-bold text-yellow-900" x-text="timeLeft"></span>
                                            </template>
                                            <template x-if="isExpired">
                                                <span class="font-bold text-red-700">EXPIRED</span>
                                            </template>
                                        </div>
                                        <span class="text-xs text-yellow-600">
                                            {{ \Carbon\Carbon::parse($order->auto_cancel_at)->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($sellerBankAccounts && $sellerBankAccounts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                            @foreach($sellerBankAccounts as $account)
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-xl p-5 hover:shadow-lg transition-all relative">
                                <!-- Primary Badge -->
                                @if($account->is_primary)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded-full shadow-sm">
                                        PRIMARY
                                    </span>
                                </div>
                                @endif

                                <!-- Bank Info -->
                                <div class="mb-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-3xl">{{ $account->bank_logo }}</span>
                                        <h4 class="font-bold text-blue-900 text-lg">{{ $account->bank_name }}</h4>
                                    </div>

                                    @if($account->is_verified)
                                    <div class="flex items-center gap-1 text-xs text-green-700 mb-3">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                        </svg>
                                        <span class="font-semibold">Verified</span>
                                    </div>
                                    @endif
                                </div>

                                <!-- Account Details -->
                                <div class="space-y-2 text-sm bg-white bg-opacity-50 rounded-lg p-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-blue-700 font-medium">Account Number:</span>
                                        <button
                                            class="flex items-center gap-1 font-mono font-bold text-blue-900 hover:text-blue-600 transition-colors"
                                            title="Click to copy">
                                            {{ $account->account_number }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-blue-700 font-medium">Account Name:</span>
                                        <span class="font-semibold text-blue-900 text-right">{{ $account->account_holder_name }}</span>
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="mt-4 pt-3 border-t-2 border-blue-300">
                                    <div class="flex justify-between items-center">
                                        <span class="text-blue-700 font-medium text-sm">Transfer Amount:</span>
                                        <div class="text-right">
                                            <button
                                                class="font-bold text-blue-900 text-lg hover:text-blue-600 transition-colors flex items-center gap-1"
                                                title="Click to copy">
                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Copy All Button -->
                                <button
                                    class="w-full mt-3 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Copy All Details
                                </button>
                            </div>
                            @endforeach
                        </div>

                        <!-- Payment Instructions -->
                        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-yellow-900 mb-1">Payment Instructions:</h4>
                                    <ul class="text-sm text-yellow-800 space-y-1">
                                        <li>• Transfer <strong>exact amount</strong> (Rp {{ number_format($order->total_amount, 0, ',', '.') }}) to one of the accounts above</li>
                                        <li>• You can choose any of the {{ $sellerBankAccounts->count() }} available accounts</li>
                                        <li>• Save your payment receipt/proof</li>
                                        <li>• Upload the proof after transfer is complete</li>
                                        <li>• Payment will be verified by seller within 24 hours</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- No Bank Accounts -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                            <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="font-bold text-red-900 mb-2">No Bank Account Available</h3>
                            <p class="text-sm text-red-700">Seller has not added bank account yet. Please contact seller.</p>
                        </div>
                        @endif
                    </div>


                    @if($order->status === 'payment_rejected')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-red-800">
                            <strong>Payment Rejected:</strong> {{ $order->payment->note ?? 'Please re-upload valid payment proof' }}
                        </p>
                    </div>
                    @endif

                    <!-- Upload Form -->
                    <form @submit.prevent="uploadProof()" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Proof *</label>
                            <input
                                type="file"
                                @change="handleFileSelect($event)"
                                accept="image/jpeg,image/png,image/jpg"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG (max 2MB)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                            <textarea
                                x-model="proofNotes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., Transferred from BCA account ending 1234"></textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="isUploading || !proofFile"
                            class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition-colors">
                            <span x-show="!isUploading">Upload Payment Proof</span>
                            <span x-show="isUploading">Uploading...</span>
                        </button>
                    </form>
                    @elseif($order->status === 'pending_verification')
                    <!-- Waiting Verification -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-900 mb-1">Payment Proof Uploaded</h4>
                                <p class="text-sm text-yellow-800">Waiting for seller verification. This usually takes 1-24 hours.</p>
                                @if($order->payment->proof_image)
                                <a href="{{ asset('storage/payment-proofs/' . $order->payment->proof_image) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-700 mt-2 inline-block">
                                    View Uploaded Proof →
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Seller: Payment Verification -->
                @if(Auth::id() === $order->seller_id && $order->status === 'pending_verification' || 'pending_refund')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Verification</h2>

                    @if($order->payment->proof_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/payment-proofs/' . $order->payment->proof_image) }}" alt="Payment Proof" class="max-w-full h-auto rounded-lg border">
                    </div>
                    @endif

                    @if($order->payment->note)
                    <div class="mb-4 p-3 bg-gray-50 rounded">
                        <p class="text-sm text-gray-700"><strong>Buyer Notes:</strong> {{ $order->payment->note }}</p>
                    </div>
                    @endif

                    @if($order->status === 'pending_verification')
                    <div class="flex gap-3">
                        <button
                            @click="verifyPayment('approve')"
                            class="flex-1 px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">
                            Approve Payment
                        </button>
                        <button
                            @click="verifyPayment('reject')"
                            class="flex-1 px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">
                            Reject Payment
                        </button>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Order Items</h2>
                    <div class="space-y-4">
                        @foreach($order->orderDetails as $detail)
                        <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                            <img
                                src="{{ $detail->product->img ? asset('storage/products/' . $detail->product->img) : 'https://via.placeholder.com/100' }}"
                                alt="{{ $detail->product->title }}"
                                class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $detail->product->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $detail->product->author }}</p>
                                <p class="text-sm text-gray-600 mt-1">Qty: {{ $detail->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">Rp {{ number_format($detail->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Shipping Address</h2>
                    @php $address = json_decode($order->shipping_address); @endphp
                    <div class="space-y-2 text-sm text-gray-700">
                        <p class="font-semibold text-gray-900">{{ $address->name }}</p>
                        <p>{{ $address->phone }}</p>
                        <p>{{ $address->address }}</p>
                        <p>{{ $address->city }}, {{ $address->postal_code }}</p>
                    </div>
                </div>

                <!-- Refund Status (if refund exists) -->
                @if($order->refund)
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" @click="showRefundApprovalModal = true">
                                    <h3 class="font-semibold text-blue-800">Refund Request</h3>
                                </button>
                                <span class="px-3 py-1 bg-{{ $order->refund->status_color }}-100 text-{{ $order->refund->status_color }}-800 text-xs font-semibold rounded-full">
                                    {{ ucfirst($order->refund->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-blue-700 mb-1">
                                Refund #{{ $order->refund->refund_number }}
                            </p>
                            <p class="text-sm text-blue-600">
                                Amount: Rp {{ number_format($order->refund->refund_amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-blue-600 mt-2">
                                Reason: {{ $order->refund->reason_label }}
                            </p>
                            @if($order->refund->reason_detail)
                            <p class="text-xs text-blue-600 mt-1">
                                {{ $order->refund->reason_detail }}
                            </p>
                            @endif
                            @if($order->refund->admin_notes)
                            <div class="mt-2 p-2 bg-blue-100 rounded text-xs text-blue-700">
                                <strong>Admin Note:</strong> {{ $order->refund->admin_notes }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax:</span>
                            <span class="font-medium">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="font-medium">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between">
                            <span class="font-semibold text-gray-900">Total:</span>
                            <span class="font-bold text-blue-600 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-3 my-4">
                        @if(Auth::id() === $order->buyer_id && in_array($order->status, ['pending_payment']))
                        <button
                            @click="cancelOrder()"
                            class="w-full px-4 py-2 border border-red-600 text-red-600 font-medium rounded-lg hover:bg-red-50">
                            Cancel Order
                        </button>
                        @endif

                        @if(Auth::id() === $order->buyer_id && in_array($order->status, ['pending_verification', 'proccessing']))
                        <button
                            @click="showRefundModal = true"
                            class="w-full px-4 py-2 border border-red-600 text-red-600 font-medium rounded-lg hover:bg-red-50">
                            Cancel and Ask for refund
                        </button>
                        @endif

                        <a href="{{ auth()->user()->role->name === 'seller' ? route('preorders') : route('order.index')}}" class="block w-full px-4 py-2 text-center border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                            Back to Orders
                        </a>
                    </div>

                    @if(Auth::id() === $order->seller_id && $order->status === 'processing')
                        <!-- Upload Resi Button -->
                    <button
                        @click="openShippingModal()"
                        class="w-full px-4 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload Shipping Receipt
                    </button>
                    @endif

                    <!-- Seller Info -->
                    <div class="my-3 py-3 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Seller Information</h3>
                        <p class="text-sm text-gray-700">{{ $order->seller->name }}</p>
                        <p class="text-sm text-gray-600">{{ $order->seller->email }}</p>
                    </div>

                    
                </div>
                
            </div>


        </div>

        @if(Auth::id() === $order->seller_id && $order->status === 'processing')
        <!-- Upload Resi Modal -->
        <div
            x-show="showShippingModal"
            x-cloak
            @keydown.escape.window="closeShippingModal()"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            style="display: none;">
            <div
                @click.away="closeShippingModal()"
                class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                    <h2 class="text-2xl font-bold text-gray-900">Upload Shipping Receipt (Resi)</h2>
                    <button @click="closeShippingModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="uploadResi()" class="px-6 py-6">
                    <div class="space-y-4">

                        <!-- Courier/Carrier -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Courier/Carrier <span class="text-red-500">*</span>
                            </label>
                            <select
                                x-model="shippingForm.carrier"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Courier</option>
                                <option value="JNE">JNE</option>
                                <option value="J&T Express">J&T Express</option>
                                <option value="SiCepat">SiCepat</option>
                                <option value="Anteraja">Anteraja</option>
                                <option value="ID Express">ID Express</option>
                                <option value="Ninja Xpress">Ninja Xpress</option>
                                <option value="Shopee Express">Shopee Express</option>
                                <option value="GoSend">GoSend</option>
                                <option value="GrabExpress">GrabExpress</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Tracking Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tracking Number/Resi
                            </label>
                            <input
                                type="text"
                                x-model="shippingForm.tracking_number"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., JNE123456789">
                            <p class="text-xs text-gray-500 mt-1">Optional if not available yet</p>
                        </div>

                        <!-- Tracking URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tracking URL (optional)
                            </label>
                            <input
                                type="url"
                                x-model="shippingForm.tracking_url"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="https://tracking.courier.com/...">
                        </div>

                        <!-- Receipt Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Receipt/Resi Image <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="file"
                                @change="handleReceiptFileSelect($event)"
                                accept="image/jpeg,image/png,image/jpg,application/pdf"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, or PDF (max 3MB)</p>

                            <!-- Preview -->
                            <div x-show="receiptPreviewUrl" class="mt-3">
                                <img :src="receiptPreviewUrl" alt="Preview" class="max-w-full h-auto rounded border">
                            </div>
                        </div>

                        <!-- Estimated Delivery -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Estimated Delivery Date
                            </label>
                            <input
                                type="date"
                                x-model="shippingForm.estimated_delivery"
                                :min="new Date().toISOString().split('T')[0]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Shipping Notes (optional)
                            </label>
                            <textarea
                                x-model="shippingForm.notes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., Package is fragile, handle with care"></textarea>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-1">Important:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Upload clear photo of shipping receipt (resi)</li>
                                        <li>Make sure tracking number is visible</li>
                                        <li>Order status will change to "Shipped" after upload</li>
                                        <li>Buyer will be notified via email</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex gap-3 mt-6 pt-6 border-t">
                        <button
                            type="button"
                            @click="closeShippingModal()"
                            class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="isUploadingReceipt || !receiptFile"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400">
                            <span x-show="!isUploadingReceipt">Upload & Mark as Shipped</span>
                            <span x-show="isUploadingReceipt">Uploading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Shipment Info Display (For Buyer and Seller when shipped) -->
        @if(in_array($order->status, ['shipped', 'delivered']))
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Shipping Information</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Courier:</span>
                    <span class="font-medium">{{ $order->shipment->carrier }}</span>
                </div>

                @if($order->shipment->tracking_number)
                <div class="flex justify-between">
                    <span class="text-gray-600">Tracking Number:</span>
                    <span class="font-medium">{{ $order->shipment->tracking_number }}</span>
                </div>
                @endif

                @if($order->shipment->tracking_url)
                <div class="flex justify-between">
                    <span class="text-gray-600">Track Package:</span>
                    <a href="{{ $order->shipment->tracking_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-medium">
                        Track Now →
                    </a>
                </div>
                @endif

                <div class="flex justify-between">
                    <span class="text-gray-600">Shipped Date:</span>
                    <span class="font-medium">{{ $order->shipment->shipped_date ? $order->shipment->shipped_date->format('d M Y') : '-' }}</span>
                </div>

                @if($order->shipment->estimated_delivery)
                <div class="flex justify-between">
                    <span class="text-gray-600">Estimated Delivery:</span>
                    <span class="font-medium">{{ $order->shipment->estimated_delivery->format('d M Y') }}</span>
                </div>
                @endif

                @if($order->shipment->delivery_date)
                <div class="flex justify-between">
                    <span class="text-gray-600">Delivered:</span>
                    <span class="font-medium text-green-600">{{ $order->shipment->delivery_date->format('d M Y') }}</span>
                </div>
                @endif

                @if($order->shipment->notes)
                <div class="pt-3 border-t">
                    <p class="text-gray-600 mb-1">Notes:</p>
                    <p class="text-gray-900">{{ $order->shipment->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Receipt Image -->
            @if($order->shipment->receipt_image)
            <div class="mt-4 pt-4 border-t">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Shipping Receipt:</h3>
                <a href="{{ asset('storage/shipment-receipts/' . $order->shipment->receipt_image) }}" target="_blank">
                    <img
                        src="{{ asset('storage/shipment-receipts/' . $order->shipment->receipt_image) }}"
                        alt="Shipping Receipt"
                        class="max-w-full h-auto rounded border hover:opacity-90 transition-opacity cursor-pointer">
                </a>
            </div>
            @endif

            <!-- Confirm Delivery Button (Buyer Only) -->
            @if(Auth::id() === $order->buyer_id && $order->status === 'shipped')
            <div class="mt-4 pt-4 border-t">
                <button
                    @click="confirmDelivery()"
                    class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    Confirm Order Received
                </button>
                <p class="text-xs text-gray-500 text-center mt-2">
                    Order will auto-complete in 7 days if not confirmed
                </p>
            </div>
            @endif
        </div>
        @endif

        <!-- Refund Request Modal -->
        <div
            x-show="showRefundModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-50" @click="showRefundModal = false"></div>

                <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Request Refund</h3>

                    <form @submit.prevent="submitRefund()">
                        <div class="space-y-5">

                            <!-- Reason -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Reason for Refund *</label>
                                <select
                                    x-model="refundData.reason"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all">
                                    <option value="">Select reason...</option>
                                    <option value="buyer_cancel">I changed my mind</option>
                                    <option value="payment_expired">Payment deadline too short</option>
                                    <option value="stock_unavailable">Product not available</option>
                                    <option value="product_defect">Product is defective</option>
                                    <option value="wrong_item">Wrong item received</option>
                                    <option value="other">Other reason</option>
                                </select>
                            </div>

                            <!-- Detail -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Additional Details</label>
                                <textarea
                                    x-model="refundData.reason_detail"
                                    rows="3"
                                    maxlength="500"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                    placeholder="Explain why you want to cancel this order..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">Max 500 characters</p>
                            </div>

                            <!-- Bank Account Section -->
                            <div class="border-t-2 border-gray-200 pt-5">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Your Bank Account for Refund
                                </h4>

                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                                    <p class="text-sm text-blue-800">
                                        <strong>Note:</strong> Please provide your bank account details where you want to receive the refund. The seller will transfer the money to this account after approval.
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <!-- Bank Name -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Bank Name *</label>
                                        <select
                                            x-model="refundData.bank_name"
                                            required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all">
                                            <option value="">Select your bank...</option>
                                            <option value="BCA">Bank Central Asia (BCA)</option>
                                            <option value="Mandiri">Bank Mandiri</option>
                                            <option value="BNI">Bank Negara Indonesia (BNI)</option>
                                            <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                                            <option value="CIMB Niaga">CIMB Niaga</option>
                                            <option value="Permata">Bank Permata</option>
                                            <option value="Danamon">Bank Danamon</option>
                                            <option value="BTN">Bank Tabungan Negara (BTN)</option>
                                            <option value="OCBC NISP">OCBC NISP</option>
                                            <option value="Maybank">Maybank Indonesia</option>
                                            <option value="Panin">Bank Panin</option>
                                            <option value="BTPN">Bank BTPN</option>
                                            <option value="Jenius">Jenius (BTPN)</option>
                                            <option value="Jago">Bank Jago</option>
                                            <option value="Seabank">SeaBank</option>
                                            <option value="Blu">Blu (BCA Digital)</option>
                                            <option value="Other">Other Bank</option>
                                        </select>
                                    </div>

                                    <!-- Account Number -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Account Number *</label>
                                        <input
                                            type="text"
                                            x-model="refundData.bank_account_number"
                                            required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                            placeholder="1234567890">
                                    </div>

                                    <!-- Account Holder Name -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-2">Account Holder Name *</label>
                                        <input
                                            type="text"
                                            x-model="refundData.bank_account_name"
                                            required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                                            placeholder="As per bank records">
                                        <p class="text-xs text-gray-500 mt-1">Name must match your bank account</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div class="text-sm text-yellow-800">
                                        <strong class="block mb-1">Important:</strong>
                                        <ul class="space-y-1 list-disc list-inside">
                                            <li>Your refund request will be reviewed by the seller</li>
                                            <li>Make sure your bank account details are correct</li>
                                            <li>Refund will be processed within 1-3 business days after approval</li>
                                            <li>Stock will be returned after approval</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3 mt-8">
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="flex-1 px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 disabled:bg-gray-400 transition-colors">
                                <span x-show="!isSubmitting">Submit Refund Request</span>
                                <span x-show="isSubmitting">Submitting...</span>
                            </button>
                            <button
                                type="button"
                                @click="showRefundModal = false"
                                class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- refund approval modal -->
         @if(isset($refund))
        <div
            x-show="showRefundApprovalModal"
            x-cloak
            style="display:none;"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold">Refund Details</h2>
                        <p class="text-sm text-gray-500">{{ $refund->refund_number }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-lg
        bg-{{ $refund->status_color }}-100
        text-{{ $refund->status_color }}-800">
                        {{ ucfirst($refund->status) }}
                    </span>
                </div>

                <!-- Refund Info -->
                <div class="border rounded-xl p-4 space-y-2 text-sm mb-4">
                    <div class="flex justify-between">
                        <span>Order</span>
                        <a href="{{ route('order.show',$refund->order_id) }}"
                            class="font-semibold text-blue-600">
                            #{{ $refund->order->order_number }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span>Amount</span>
                        <span class="font-bold text-green-600">
                            Rp {{ number_format($refund->refund_amount,0,',','.') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Reason</span>
                        <span class="font-semibold">
                            {{ $refund->reason_label }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Date</span>
                        <span>
                            {{ $refund->created_at->format('d M Y H:i') }}
                        </span>
                    </div>
                    @if($refund->reason_detail)
                    <div class="pt-2 border-t">
                        <p class="text-gray-600 text-xs mb-1">Details</p>
                        <p class="bg-gray-50 p-2 rounded text-xs">
                            {{ $refund->reason_detail }}
                        </p>
                    </div>
                    @endif
                </div>


                <!-- Bank Info -->
                @if($refund->hasBuyerBankDetails())
                <div class="border rounded-xl p-4 mb-4 text-sm space-y-2">
                    <h4 class="font-semibold">Bank Account</h4>
                    <div class="flex justify-between">
                        <span>Bank</span>
                        <span class="font-semibold">
                            {{ $refund->bank_name }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>No</span>
                        <span class="font-mono">
                            {{ $refund->bank_account_number }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Name</span>
                        <span>
                            {{ $refund->bank_account_name }}
                        </span>
                    </div>
                </div>
                @endif


                <!-- Buyer -->
                <div class="border rounded-xl p-4 mb-4 text-sm">
                    <h4 class="font-semibold mb-2">Buyer</h4>
                    <p>{{ $refund->user->name }}</p>
                    <p class="text-gray-600 text-xs">
                        {{ $refund->user->email }}
                    </p>
                </div>


                <!-- Upload Proof -->
                @if($refund->status=='pending' && $refund->hasBuyerBankDetails())
                <form @submit.prevent="approveRefund({{ $refund->id }})" class="space-y-3">
                    <input type="file"
                        @change="handleProofUpload($event)"
                        required
                        class="text-sm">
                    <textarea
                        x-model="approvalData.notes"
                        rows="2"
                        placeholder="Notes (optional)"
                        class="w-full border rounded-lg p-2 text-sm">
                    </textarea>
                    <div class="flex gap-2">
                        <button type="submit"
                            :disabled="isSubmitting"
                            class="flex-1 bg-green-600 text-white py-2 rounded-lg">
                            Approve
                        </button>
                        <button type="button"
                            @click="rejectRefund({{ $refund->id }})"
                            class="flex-1 bg-red-600 text-white py-2 rounded-lg">
                            Reject
                        </button>
                    </div>
                </form>
                @endif


                <!-- Approved Proof -->
                @if($refund->status=='approved' && $refund->refund_proof)
                <div class="mt-4">
                    <h4 class="font-semibold mb-2 text-sm">
                        Transfer Proof
                    </h4>
                    <img src="{{ asset('storage/refunds/'.$refund->refund_proof) }}"
                        class="rounded-lg border">
                </div>
                @endif


                <!-- Close -->
                <button
                    type="button"
                    @click="showRefundApprovalModal=false"
                    class="w-full mt-4 border py-2 rounded-lg">
                    Close
                </button>
            </div>
        </div>
        @endif

    </div>
</div>

<script>
    function countdown(deadline) {
        return {
            timeLeft: '',
            isExpired: false,
            interval: null,


            startCountdown() {
                this.updateTime();
                this.interval = setInterval(() => {
                    this.updateTime();
                }, 1000);
            },

            updateTime() {
                const now = new Date().getTime();
                const end = new Date(deadline).getTime();
                const distance = end - now;

                if (distance < 0) {
                    this.isExpired = true;
                    this.timeLeft = 'EXPIRED';
                    clearInterval(this.interval);

                    // Optional: reload page to show expired state
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                this.timeLeft = `${hours}h ${minutes}m ${seconds}s`;
            }
        }
    }

    function orderDetail() {
        return {
            // Existing payment proof data
            proofFile: null,
            proofNotes: '',
            isUploading: false,
            orderId: '{{$order -> id}}',

            //refund related
            showRefundModal: false,
            showRefundApprovalModal: false,
            isSubmitting: false,
            refundData: {
                reason: '',
                reason_detail: '',
                bank_name: '',
                bank_account_number: '',
                bank_account_name: '',
            },

            proofRefundFile: null,
            proofPreview: null,

            approvalData: {
                notes: ''
            },

            rejectionData: {
                notes: ''
            },


            // Resi upload data - INTEGRATED
            showShippingModal: false,
            shippingForm: {
                carrier: '',
                tracking_number: '',
                tracking_url: '',
                estimated_delivery: '',
                notes: ''
            },
            receiptFile: null,
            receiptPreviewUrl: null,
            isUploadingReceipt: false,

            init() {
                // Any initialization if needed
            },

            //refund
            async submitRefund() {
                // Validate bank details
                if (!this.refundData.bank_name || !this.refundData.bank_account_number || !this.refundData.bank_account_name) {
                    alert('Please fill in all bank account details');
                    return;
                }

                if (!this.refundData.reason) {
                    alert('Please select a reason');
                    return;
                }

                this.isSubmitting = true;

                try {
                    const response = await fetch(`{{route('buyer.refund.request', $order->id)}}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.refundData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Refund request submitted successfully');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to submit refund request');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred');
                } finally {
                    this.isSubmitting = false;
                }
            },

            // ===== PAYMENT PROOF METHODS (existing) =====

            handleFileSelect(event) {
                this.proofFile = event.target.files[0];
            },

            async uploadProof() {
                if (!this.proofFile) return;

                this.isUploading = true;
                const formData = new FormData();
                formData.append('proof_image', this.proofFile);
                formData.append('notes', this.proofNotes);

                try {
                    const response = await fetch(`{{ route('order.upload-proof', $order->id) }}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Payment proof uploaded successfully! Waiting for seller verification.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to upload proof');
                    }
                } catch (error) {
                    alert('An error occurred');
                } finally {
                    this.isUploading = false;
                }
            },

            handleProofUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.proofRefundFile = file;

                // Preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.proofPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },



            async approveRefund(id) {
                if (!this.proofRefundFile) {
                    alert('Please upload transfer proof');
                    return;
                }

                if (!confirm('Are you sure you want to approve this refund? Make sure you have transferred the money to buyer.')) {
                    return;
                }

                this.isSubmitting = true;

                try {
                    const formData = new FormData();
                    formData.append('refund_proof', this.proofRefundFile);
                    formData.append('notes', this.approvalData.notes);

                    const response = await fetch(`{{ route('seller.refund.approve', 'id') }}`.replace('id', id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Refund approved successfully!');
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Error Details:', {
                        message: error.message,
                        stack: error.stack,
                        name: error.name,
                        timestamp: new Date().toISOString()
                    });

                    // Tampilkan error yang lebih spesifik ke user
                    let errorMessage = 'An error occurred';

                    if (error instanceof TypeError) {
                        errorMessage = 'There was a problem with the data format';
                    } else if (error instanceof SyntaxError) {
                        errorMessage = 'There was a problem parsing the response';
                    } else if (error.message.includes('Failed to fetch')) {
                        errorMessage = 'Network connection failed. Please check your internet connection';
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    alert(errorMessage);
                } finally {
                    this.isSubmitting = false;
                }
            },

            async rejectRefund(id) {
                if (!this.approvalData.notes) {
                    alert('Please provide a rejection reason');
                    return;
                }

                if (!confirm('Are you sure you want to reject this refund?')) {
                    return;
                }

                this.isSubmitting = true;

                try {
                    const response = await fetch(`{{ route('seller.refund.reject', '__ID__') }}`.replace('__ID__', id), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.approvalData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Refund rejected');
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred');
                } finally {
                    this.isSubmitting = false;
                    this.showRejectModal = false;
                }
            },

            copyToClipboard(text) {
                const cleanText = text.toString().replace(/\./g, '');
                navigator.clipboard.writeText(cleanText).then(() => {
                    this.showToast('Copied to clipboard!');
                });
            },

            copyBankDetails() {
                const text = `
Transfer Details
━━━━━━━━━━━━━━━━━━━━━
Bank: {{ $refund->bank_name ?? '-'}}
Account: {{ $refund->bank_account_number ?? '-'}}
Name: {{ $refund->bank_account_name ?? '-'}}
Amount: Rp {{ isset($refund->refund_amount) ? number_format($refund->refund_amount, 0, ',', '.') : '-' }}
━━━━━━━━━━━━━━━━━━━━━
            `.trim();

                navigator.clipboard.writeText(text).then(() => {
                    this.showToast('All details copied!');
                });
            },

            showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            },

            // ===== SHIPPING RESI METHODS (new - integrated) =====

            openShippingModal() {
                this.showShippingModal = true;
                document.body.style.overflow = 'hidden';
            },

            closeShippingModal() {
                this.showShippingModal = false;
                document.body.style.overflow = 'auto';
                this.resetShippingForm();
            },

            resetShippingForm() {
                this.shippingForm = {
                    carrier: '',
                    tracking_number: '',
                    tracking_url: '',
                    estimated_delivery: '',
                    notes: ''
                };
                this.receiptFile = null;
                this.receiptPreviewUrl = null;
            },

            handleReceiptFileSelect(event) {
                this.receiptFile = event.target.files[0];

                // Create preview for images
                if (this.receiptFile && this.receiptFile.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.receiptPreviewUrl = e.target.result;
                    };
                    reader.readAsDataURL(this.receiptFile);
                } else {
                    this.receiptPreviewUrl = null;
                }
            },

            async uploadResi() {
                if (!this.receiptFile) {
                    alert('Please select a receipt image');
                    return;
                }

                if (!this.shippingForm.carrier) {
                    alert('Please select a courier');
                    return;
                }

                this.isUploadingReceipt = true;
                const formData = new FormData();

                formData.append('receipt_image', this.receiptFile);
                formData.append('carrier', this.shippingForm.carrier);
                formData.append('tracking_number', this.shippingForm.tracking_number || '');
                formData.append('tracking_url', this.shippingForm.tracking_url || '');
                formData.append('estimated_delivery', this.shippingForm.estimated_delivery || '');
                formData.append('notes', this.shippingForm.notes || '');

                try {
                    const response = await fetch(`{{ route('order.upload-receipt', $order->id) }}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Shipping receipt uploaded successfully! Order marked as shipped.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to upload receipt');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while uploading receipt');
                } finally {
                    this.isUploadingReceipt = false;
                }
            },

            // ===== PAYMENT VERIFICATION (seller) =====

            async verifyPayment(action) {
                const notes = prompt(action === 'reject' ? 'Reason for rejection:' : 'Verification notes (optional):');

                if (action === 'reject' && !notes) {
                    alert('Please provide a reason for rejection');
                    return;
                }

                try {
                    const response = await fetch(`{{ route('order.verify-payment', $order->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            action,
                            notes
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('An error occurred');
                }
            },

            // ===== DELIVERY CONFIRMATION (buyer) =====

            async confirmDelivery() {
                if (!confirm('Confirm that you have received this order?')) return;

                try {
                    const response = await fetch(`{{ route('order.confirm-delivery', $order->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Delivery confirmed! Thank you for shopping with us.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to confirm delivery');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred');
                }
            },

            // ===== CANCEL ORDER =====

            async cancelOrder() {
                if (!confirm('Are you sure you want to cancel this order?')) return;

                try {
                    const response = await fetch(`{{ route('order.cancel', $order->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('An error occurred');
                }
            }
        }
    }
</script>


<style>
    [x-cloak] {
        display: none !important;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>
@endsection