@extends('layouts.app')

@section('title', "Cart")

@section('content')
<div x-data="cartPage()" x-init="init()" class="max-w-7xl mx-auto">

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
        <p class="mt-2 text-gray-600">Review and manage your cart items</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Cart Items Section -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Empty State -->
            <template x-if="cartItems.length === 0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
                    <p class="text-gray-600 mb-6">Start shopping to add items to your cart</p>
                    <a href="{{ route('homepage') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Browse Products
                    </a>
                </div>
            </template>

            <!-- Select All Checkbox -->
            <div x-show="cartItems.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <label class="flex items-center cursor-pointer group">
                    <input
                        type="checkbox"
                        :checked="isAllSelected"
                        @change="toggleSelectAll()"
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900">
                        Select All Items (<span x-text="cartItems.length"></span>)
                    </span>
                </label>
            </div>

            <!-- Cart Items List -->
            <template x-for="item in cartItems" :key="item.id">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex gap-4">

                        <!-- Checkbox -->
                        <div class="flex items-start pt-1">
                            <input
                                type="checkbox"
                                :checked="isItemSelected(item.id)"
                                @change="toggleSelectItem(item.id)"
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                        </div>

                        <!-- Product Image -->
                        <div class="flex-shrink-0 w-24 h-24 bg-gray-200 rounded-lg overflow-hidden">
                            <img
                                :src="item.image || 'https://via.placeholder.com/150'"
                                :alt="item.name"
                                class="w-full h-full object-cover">
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900" x-text="item.name"></h3>
                                    <p class="text-sm text-gray-500 mt-1">Product ID: <span x-text="item.product_id"></span></p>
                                    <p class="text-lg font-bold text-blue-600 mt-2" x-text="formatCurrency(item.price)"></p>
                                </div>

                                <!-- Remove Button -->
                                <button
                                    @click="removeFromCart(item.id)"
                                    class="text-red-500 hover:text-red-700 transition-colors p-2"
                                    title="Remove from cart">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-3 mt-4">
                                <span class="text-sm text-gray-600 font-medium">Quantity:</span>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="decreaseQuantity(item.id)"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input
                                        type="number"
                                        :value="item.quantity"
                                        @change="updateQuantity(item.id, $event.target.value)"
                                        min="1"
                                        max="99"
                                        class="w-16 text-center border border-gray-300 rounded-lg px-2 py-1 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button
                                        @click="increaseQuantity(item.id)"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Subtotal -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Subtotal:</span>
                                    <span class="text-lg font-bold text-gray-900" x-text="formatCurrency(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

        </div>

        <!-- Order Summary Sidebar (Sticky) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

                <!-- Selected Items Info -->
                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-blue-900 font-medium">Selected Items:</span>
                        <span class="text-lg font-bold text-blue-600" x-text="selectedItems.length"></span>
                    </div>
                </div>

                <!-- Summary Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-900" x-text="formatCurrency(selectedSubtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax (11%):</span>
                        <span class="font-medium text-gray-900" x-text="formatCurrency(tax)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="font-medium text-gray-900" x-text="formatCurrency(shipping)"></span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <span class="text-base font-semibold text-gray-900">Total:</span>
                        <span class="text-xl font-bold text-blue-600" x-text="formatCurrency(total)"></span>
                    </div>
                </div>

                <!-- Checkout Button -->
                <button
                    @click="openCheckoutModal()"
                    :disabled="selectedItems.length === 0"
                    :class="selectedItems.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                    class="w-full px-6 py-3 text-white font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span x-show="selectedItems.length === 0">Select Items to Checkout</span>
                    <span x-show="selectedItems.length > 0">Proceed to Checkout</span>
                </button>

                <!-- Continue Shopping -->
                <a
                    href="{{ route('homepage') }}"
                    class="block w-full mt-3 px-6 py-3 text-center text-sm font-medium text-blue-600 hover:text-blue-700 border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                    Continue Shopping
                </a>
            </div>
        </div>

    </div>

    <!-- Checkout Modal -->
    <div
        x-show="showCheckoutModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        @click.self="closeCheckoutModal()">
        <div
            x-show="showCheckoutModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                <h2 class="text-2xl font-bold text-gray-900">Checkout</h2>
                <button
                    @click="closeCheckoutModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">

                <!-- Selected Items -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items (<span x-text="selectedItems.length"></span>)</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <template x-for="item in getSelectedItems()" :key="item.id">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <img
                                    :src="item.image || 'https://via.placeholder.com/80'"
                                    :alt="item.name"
                                    class="w-16 h-16 object-cover rounded">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></h4>
                                    <p class="text-sm text-gray-600">
                                        <span x-text="formatCurrency(item.price)"></span> ×
                                        <span x-text="item.quantity"></span>
                                    </p>
                                </div>
                                <div class="text-sm font-semibold text-gray-900" x-text="formatCurrency(item.price * item.quantity)"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Shipping Address Form -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input
                                type="text"
                                x-model="shippingAddress.name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="John Doe"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input
                                type="tel"
                                x-model="shippingAddress.phone"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="08123456789"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Complete Address</label>
                            <textarea
                                x-model="shippingAddress.address"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Street address, building, apartment..."
                                required></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input
                                    type="text"
                                    x-model="shippingAddress.city"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Jakarta"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                <input
                                    type="text"
                                    x-model="shippingAddress.postal_code"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="12345"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Steps -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Langkah Pembayaran:</h4>
                    <ol class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                            <span>Order akan dibuat dan Anda akan diarahkan ke halaman detail order</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                            <span>Lihat informasi rekening bank penjual di halaman detail order</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">3</span>
                            <span>Transfer pembayaran sesuai nominal yang tertera</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">4</span>
                            <span>Upload bukti transfer untuk verifikasi</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">5</span>
                            <span>Penjual akan memverifikasi pembayaran dan memproses pesanan Anda</span>
                        </li>
                    </ol>
                </div>

                <!-- Warning -->
                <div class="mt-4 flex items-start gap-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-xs text-yellow-800">
                        <strong>Penting:</strong> Transfer hanya ke rekening yang tertera di halaman detail order.
                        Jangan transfer ke rekening lain untuk menghindari penipuan.
                    </p>
                </div>
                <!-- Order Summary in Modal -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Summary</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium text-gray-900" x-text="formatCurrency(selectedSubtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax (11%):</span>
                            <span class="font-medium text-gray-900" x-text="formatCurrency(tax)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="font-medium text-gray-900" x-text="formatCurrency(shipping)"></span>
                        </div>
                        <div class="border-t border-gray-300 pt-2 flex justify-between">
                            <span class="text-base font-bold text-gray-900">Total:</span>
                            <span class="text-xl font-bold text-blue-600" x-text="formatCurrency(total)"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex gap-3">
                <button
                    @click="closeCheckoutModal()"
                    class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                    Cancel
                </button>
                <button
                    @click="processCheckout()"
                    :disabled="isProcessing"
                    class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                    <span x-show="!isProcessing">Place Order</span>
                    <span x-show="isProcessing" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>

        </div>
    </div>

</div>

<script>
    window.shipping = @json($shipping);

    function cartPage() {
        return {
            cartItems: [],
            selectedItemIds: [],
            showCheckoutModal: false,
            isProcessing: false,
            shippingAddress: window.shipping || {
                name: '',
                phone: '',
                address: '',
                city: '',
                postal_code: ''
            },

            init() {
                // Load cart from global store
                this.loadCart();

                // Listen to cart updates
                window.addEventListener('cart-updated', () => {
                    this.loadCart();
                });

                // Fetch cart from server
                this.fetchCart();
            },

            loadCart() {
                if (window.cartStore) {
                    this.cartItems = window.cartStore.items;
                }
            },

            async fetchCart() {
                try {
                    const response = await fetch('{{ route("cart.get") }}', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').value
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.cartStore.setCart(data.items || []);
                    }
                } catch (error) {
                    console.error('Error fetching cart:', error);
                }
            },

            toggleSelectItem(itemId) {
                const index = this.selectedItemIds.indexOf(itemId);
                if (index > -1) {
                    this.selectedItemIds.splice(index, 1);
                } else {
                    this.selectedItemIds.push(itemId);
                }
            },

            isItemSelected(itemId) {
                return this.selectedItemIds.includes(itemId);
            },

            toggleSelectAll() {
                if (this.isAllSelected) {
                    this.selectedItemIds = [];
                } else {
                    this.selectedItemIds = this.cartItems.map(item => item.id);
                }
            },

            get isAllSelected() {
                return this.cartItems.length > 0 && this.selectedItemIds.length === this.cartItems.length;
            },

            get selectedItems() {
                return this.cartItems.filter(item => this.selectedItemIds.includes(item.id));
            },

            getSelectedItems() {
                return this.selectedItems;
            },

            get selectedSubtotal() {
                return this.selectedItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            get tax() {
                return this.selectedSubtotal * 0.11; // 11% tax
            },

            get shipping() {
                return this.selectedItems.length > 0 ? 15000 : 0; // Flat rate
            },

            get total() {
                return this.selectedSubtotal + this.tax + this.shipping;
            },

            async increaseQuantity(itemId) {
                try {
                    const response = await fetch(`{{ route('cart.update', ['id'=>'__ID__']) }}`.replace('__ID__', itemId), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            action: 'increase'
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.cartStore.setCart(data.items || []);
                    }
                } catch (error) {
                    console.error('Error updating cart:', error);
                }
            },

            async decreaseQuantity(itemId) {
                try {
                    const response = await fetch(`{{ route('cart.update', ['id'=>'__ID__']) }}`.replace('__ID__', itemId), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            action: 'decrease'
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.cartStore.setCart(data.items || []);
                    }
                } catch (error) {
                    console.error('Error updating cart:', error);
                }
            },

            async updateQuantity(itemId, quantity) {
                const qty = parseInt(quantity);
                if (qty < 1 || isNaN(qty)) return;

                try {
                    const response = await fetch(`{{ route('cart.update', ['id'=>'__ID__']) }}`.replace('__ID__', itemId), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            quantity: qty
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.cartStore.setCart(data.items || []);
                    }
                } catch (error) {
                    console.error('Error updating cart:', error);
                }
            },

            async removeFromCart(itemId) {
                if (!confirm('Are you sure you want to remove this item?')) return;

                try {
                    const response = await fetch(`{{ route('cart.remove', ['id'=>'__ID__']) }}`.replace('__ID__', itemId), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.cartStore.setCart(data.items || []);

                        // Remove from selected items if was selected
                        const index = this.selectedItemIds.indexOf(itemId);
                        if (index > -1) {
                            this.selectedItemIds.splice(index, 1);
                        }
                    }
                } catch (error) {
                    console.error('Error removing from cart:', error);
                }
            },

            openCheckoutModal() {
                if (this.selectedItems.length === 0) return;
                this.showCheckoutModal = true;
                document.body.style.overflow = 'hidden';
            },

            closeCheckoutModal() {
                this.showCheckoutModal = false;
                document.body.style.overflow = 'auto';
            },

            async processCheckout() {
                // Validate form
                if (!this.shippingAddress.name || !this.shippingAddress.phone ||
                    !this.shippingAddress.address || !this.shippingAddress.city ||
                    !this.shippingAddress.postal_code) {
                    alert('Please fill in all shipping address fields');
                    return;
                }


                this.isProcessing = true;

                try {
                    const response = await fetch('{{ route("checkout.process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            items: this.selectedItems.map(item => ({
                                cart_item_id: item.id,
                                product_id: item.product_id,
                                quantity: item.quantity,
                                price: item.price
                            })),
                            shipping_address: this.shippingAddress,
                            subtotal: this.selectedSubtotal,
                            tax: this.tax,
                            shipping: this.shipping,
                            total: this.total
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();

                        // Show success message
                        alert('Order placed successfully! Order ID: ' + data.order_id);

                        // Redirect to order detail or success page
                        window.location.href = '{{ url("orders") }}/' + data.order_id;
                    } else {
                        const error = await response.json();
                        alert('Error: ' + (error.message || 'Failed to process checkout'));
                    }
                } catch (error) {
                    console.error('Error processing checkout:', error);
                    alert('An error occurred while processing your order');
                } finally {
                    this.isProcessing = false;
                }
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            }
        }
    }
</script>
@endsection