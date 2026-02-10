{{-- resources/views/partials/cart-button.blade.php --}}
<div x-data="cartButton()" class="relative">
    @csrf
    <!-- Cart Button -->
    <button
        @click="toggleCart()"
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition">
        <svg class="w-6 h-6" viewBox="0 0 24 24">
            <path stroke-width="2" fill="rgb(75, 85, 99)" d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1m-9-1a2 2 0 0 1 4 0v1h-4Zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2Z" />
        </svg>
        <span
            x-show="cartCount > 0"
            x-text="cartCount"
            class="absolute top-0 right-0 min-w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center px-1"></span>
    </button>

    <!-- Cart Dropdown -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="isOpen = false"
        class="absolute right-0 z-50 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200">
        <!-- Cart Header -->
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Shopping Cart</h3>
                <button
                    @click="isOpen = false"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="cartItems.length === 0">
                <div class="px-4 py-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-gray-500 text-sm">Your cart is empty</p>
                </div>
            </template>

            <template x-for="item in cartItems" :key="item.id">
                <div class="px-4 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <!-- Product Image -->
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-md overflow-hidden">
                            <img
                                :src="item.image || '/images/placeholder.png'"
                                :alt="item.name"
                                class="w-full h-full object-cover">
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></h4>
                            <p class="text-sm text-gray-500 mt-1">
                                <span x-text="formatCurrency(item.price)"></span> ×
                                <span x-text="item.quantity"></span>
                            </p>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-2 mt-2">
                                <button
                                    @click="decreaseQuantity(item.id)"
                                    class="w-6 h-6 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100 text-gray-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="text-sm font-medium text-gray-900 min-w-[20px] text-center" x-text="item.quantity"></span>
                                <button
                                    @click="increaseQuantity(item.id)"
                                    class="w-6 h-6 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100 text-gray-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Price & Remove -->
                        <div class="flex flex-col items-end gap-2">
                            <p class="text-sm font-semibold text-gray-900" x-text="formatCurrency(item.price * item.quantity)"></p>
                            <button
                                @click="removeFromCart(item.id)"
                                class="text-red-500 hover:text-red-700 transition-colors"
                                title="Remove item">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Cart Footer -->
        <div x-show="cartItems.length > 0" class="px-4 py-4 bg-gray-50 border-t border-gray-200">
            <!-- Subtotal -->
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-600">Subtotal:</span>
                <span class="text-lg font-bold text-gray-900" x-text="formatCurrency(cartTotal)"></span>
            </div>

            <!-- View Cart Link -->
            <a
                href="{{ route('cart') }}"
                class="block w-full mt-2 px-4 py-2 text-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                View Full Cart
            </a>
        </div>
    </div>
</div>

<script>
    function cartButton() {
        
        return {
            isOpen: false,
            cartItems: [],
            cartCount: 0,
            cartTotal: 0,

            

            init() {
                // Load cart from global store on init
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
                    this.cartCount = window.cartStore.count;
                    this.cartTotal = window.cartStore.total;
                }
            },

            toggleCart() {
                this.isOpen = !this.isOpen;
            },
            

            async fetchCart() {
                const csrf = document.querySelector('input[name="_token"]').value;
                try {
                    const response = await fetch('{{ route("cart.get") }}', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf
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

            async increaseQuantity(itemId) {
                try {
                    const response = await fetch(`{{ route('cart.update', ['id'=>'__ID__']) }}`.replace('__ID__',itemId), {
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
                    const response = await fetch(`{{ route('cart.update', ['id'=>'__ID__']) }}`.replace('__ID__',itemId), {
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

            async removeFromCart(itemId) {
                if (!confirm('Are you sure you want to remove this item?')) {
                    return;
                }
                const csrf = document.querySelector('input[name="_token"]').value;
                try {
                    const response = await fetch(`{{ route('cart.remove', ['id'=>'__ID__']) }}`.replace('__ID__',itemId), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.cartStore.setCart(data.items || []);
                    }
                } catch (error) {
                    console.error('Error removing from cart:', error);
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