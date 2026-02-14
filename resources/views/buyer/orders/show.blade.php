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
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-3">Transfer ke Rekening Penjual:</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-800">Bank:</span>
                                <span class="font-semibold text-blue-900">BCA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-800">Account Number:</span>
                                <span class="font-semibold text-blue-900">1234567890</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-800">Account Name:</span>
                                <span class="font-semibold text-blue-900">{{ $order->seller->name }}</span>
                            </div>
                            <div class="flex justify-between border-t border-blue-300 pt-2 mt-2">
                                <span class="text-blue-800">Amount:</span>
                                <span class="font-bold text-blue-900 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
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
                @if(Auth::id() === $order->seller_id && $order->status === 'pending_verification')
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
                    <div class="mt-6 space-y-3">
                        @if(Auth::id() === $order->buyer_id && in_array($order->status, ['pending_payment', 'payment_rejected']))
                        <button
                            @click="cancelOrder()"
                            class="w-full px-4 py-2 border border-red-600 text-red-600 font-medium rounded-lg hover:bg-red-50">
                            Cancel Order
                        </button>
                        @endif

                        <a href="{{ route('order.index') }}" class="block w-full px-4 py-2 text-center border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                            Back to Orders
                        </a>
                    </div>

                    <!-- Seller Info -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Seller Information</h3>
                        <p class="text-sm text-gray-700">{{ $order->seller->name }}</p>
                        <p class="text-sm text-gray-600">{{ $order->seller->email }}</p>
                    </div>
                </div>
            </div>

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


    </div>
</div>

<script>
    function orderDetail() {
        return {
            // Existing payment proof data
            proofFile: null,
            proofNotes: '',
            isUploading: false,
            orderId: {{$order->id}},

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
</style>
@endsection