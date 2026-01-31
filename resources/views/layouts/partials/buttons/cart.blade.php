<div x-data="cartButton()" @add-to-cart.window="handleAddToCart($event.detail)">
    <button 
        @click="cartOpen = !cartOpen"
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition"
    >
        <svg class="w-6 h-6" viewBox="0 0 24 24">
            <path stroke-width="2" fill="rgb(75, 85, 99)" d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1m-9-1a2 2 0 0 1 4 0v1h-4Zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2Z" />
        </svg>
        <span 
            x-show="cartCount > 0"
            x-text="cartCount"
            class="absolute top-0 right-0 min-w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center px-1"
        ></span>
    </button>

    {{-- Cart Dropdown --}}
    <div 
        x-show="cartOpen"
        @click.away="cartOpen = false"
        x-transition
        class="absolute right-0 mr-9 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;"
    >
        <div class="p-4 border-b border-gray-200">
            <a href="{{ route('cart') }}" class="font-semibold text-gray-800 hover:text-sky-500">Keranjang Belanja</a>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            <template x-if="cartItems.length === 0">
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p>Keranjang kosong</p>
                </div>
            </template>

            <template x-if="cartItems.length > 0">
                <div class="divide-y divide-gray-200">
                    <template x-for="item in cartItems" :key="item.id">
                        <div class="p-4 flex gap-3">
                            <img :src="item.image" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-800 line-clamp-2" x-text="item.name"></h4>
                                <p class="text-sm font-semibold text-blue-600" x-text="formatPrice(item.price)"></p>
                            </div>
                            <button 
                                @click="removeItem(item.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div x-show="cartItems.length > 0" class="p-4 border-t border-gray-200">
            <div class="flex justify-between mb-3">
                <span class="font-semibold">Total:</span>
                <span class="font-bold text-blue-600" x-text="formatPrice(cartTotal)"></span>
            </div>
            <a 
                href="/checkout"
                class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition font-medium"
            >
                Checkout
            </a>
        </div>
    </div>

    <script>
    function cartButton() {
        return {
            cartOpen: false,
            cartItems: [],
            cartCount: 0,
            cartTotal: 0,

            handleAddToCart(data) {
                const existing = this.cartItems.find(item => item.id === data.product.id);
                if (existing) {
                    existing.quantity = (existing.quantity || 1) + 1;
                } else {
                    this.cartItems.push({ ...data.product, quantity: 1 });
                }
                this.updateCart();
                this.cartOpen = true;
            },

            removeItem(id) {
                this.cartItems = this.cartItems.filter(item => item.id !== id);
                this.updateCart();
            },

            updateCart() {
                this.cartCount = this.cartItems.reduce((sum, item) => sum + (item.quantity || 1), 0);
                this.cartTotal = this.cartItems.reduce((sum, item) => sum + (item.price * (item.quantity || 1)), 0);
            },

            formatPrice(price) {
                return 'Rp ' + price.toLocaleString('id-ID');
            }
        }
    }
    </script>
</div>