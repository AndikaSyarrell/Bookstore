@extends('layouts.app')
@section('title', 'Buyer')

@section('content')
<div x-data="productSelection()" class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Pilih Produk</h1>
        <p class="text-gray-600">Temukan produk terbaik untuk kebutuhan Anda</p>
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <template x-for="product in filteredProducts" :key="product.id">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300">
                {{-- Product Image --}}
                <div class="relative">
                    <img 
                        :src="product.image" 
                        :alt="product.name"
                        class="w-full h-48 object-cover rounded-t-lg"
                    >
                    <span 
                        x-show="product.stock < 10 && product.stock > 0"
                        class="absolute top-2 right-2 bg-orange-500 text-white text-xs px-2 py-1 rounded"
                    >
                        Stok Terbatas
                    </span>
                    <span 
                        x-show="product.stock === 0"
                        class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded"
                    >
                        Habis
                    </span>
                </div>

                {{-- Product Info --}}
                <div class="p-4">
                    <div class="mb-2">
                        <span 
                            class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded"
                            x-text="product.category"
                        ></span>
                    </div>
                    
                    <h3 
                        class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2"
                        x-text="product.name"
                    ></h3>
                    
                    <p 
                        class="text-sm text-gray-600 mb-3 line-clamp-2"
                        x-text="product.description"
                    ></p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-2xl font-bold text-gray-800" x-text="formatPrice(product.price)"></p>
                            <p class="text-xs text-gray-500">Stok: <span x-text="product.stock"></span></p>
                        </div>
                    </div>

                    {{-- Quantity & Add to Cart --}}
                    <div class="flex items-center gap-2">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button 
                                @click="decreaseQty(product.id)"
                                class="px-3 py-1 hover:bg-gray-100"
                                :disabled="product.stock === 0"
                            >
                                -
                            </button>
                            <input 
                                type="number" 
                                :value="getQuantity(product.id)"
                                @input="updateQty(product.id, $event.target.value)"
                                min="1"
                                :max="product.stock"
                                class="w-12 text-center border-x border-gray-300 py-1"
                                :disabled="product.stock === 0"
                            >
                            <button 
                                @click="increaseQty(product.id)"
                                class="px-3 py-1 hover:bg-gray-100"
                                :disabled="product.stock === 0"
                            >
                                +
                            </button>
                        </div>
                        
                        <button 
                            @click="addToCart(product)"
                            :disabled="product.stock === 0"
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                        >
                            <span x-show="product.stock > 0">Tambah</span>
                            <span x-show="product.stock === 0">Habis</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Empty State --}}
    <div x-show="filteredProducts.length === 0" class="text-center py-16">
        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Produk tidak ditemukan</h3>
        <p class="mt-2 text-gray-500">Coba ubah filter atau kata kunci pencarian</p>
    </div>

    {{-- Cart Summary (Sticky Bottom) --}}
    <div 
        x-show="cart.length > 0"
        class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 p-4"
    >
        <div class="container mx-auto flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">
                    <span x-text="cart.length"></span> produk dipilih
                </p>
                <p class="text-xl font-bold text-gray-800" x-text="formatPrice(cartTotal())"></p>
            </div>
            <button 
                @click="checkout()"
                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold"
            >
                Lanjut ke Checkout
            </button>
        </div>
    </div>

    {{-- Cart Sidebar --}}
    <!-- <div x-show="cartOpen"
        @click.self="cartOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 transition-opacity"
        style="display: none;">
        <div
            @click.stop
            class="fixed right-0 top-0 h-full w-full md:w-96 bg-white shadow-xl overflow-hidden flex flex-col"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full">
            {{-- Cart Header --}}
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">
                    Keranjang Belanja
                    <span class="text-sm font-normal text-gray-500" x-show="cart.length > 0">
                        (<span x-text="cart.length"></span> item)
                    </span>
                </h2>
                <button
                    @click="cartOpen = false"
                    class="p-2 hover:bg-gray-100 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4">
                {{-- Empty Cart --}}
                <div x-show="cart.length === 0" class="text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Keranjang Kosong</h3>
                    <p class="mt-2 text-gray-500">Belum ada produk yang ditambahkan</p>
                </div>

                {{-- Cart Items List --}}
                <div class="space-y-4">
                    <template x-for="item in cart" :key="item.id">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex gap-4">
                                <img
                                    :src="item.image"
                                    :alt="item.name"
                                    class="w-20 h-20 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1" x-text="item.name"></h3>
                                    <p class="text-sm text-gray-600 mb-2" x-text="formatPrice(item.price)"></p>

                                    {{-- Cart Item Quantity --}}
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                            <button
                                                @click="decreaseCartQty(item.id)"
                                                class="px-2 py-1 hover:bg-gray-100 text-sm">
                                                -
                                            </button>
                                            <span class="px-3 py-1 text-sm border-x border-gray-300" x-text="item.quantity"></span>
                                            <button
                                                @click="increaseCartQty(item.id)"
                                                class="px-2 py-1 hover:bg-gray-100 text-sm"
                                                :disabled="item.quantity >= item.stock">
                                                +
                                            </button>
                                        </div>

                                        <button
                                            @click="removeFromCart(item.id)"
                                            class="text-red-500 hover:text-red-700 text-sm">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <p class="text-right font-semibold text-gray-800">
                                    Subtotal: <span x-text="formatPrice(item.price * item.quantity)"></span>
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Cart Footer --}}
            <div x-show="cart.length > 0" class="border-t border-gray-200 p-4 bg-gray-50">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span x-text="formatPrice(cartTotal())"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Ongkir</span>
                        <span class="text-green-600">Gratis</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t border-gray-300">
                        <span>Total</span>
                        <span x-text="formatPrice(cartTotal())"></span>
                    </div>
                </div>

                <button
                    @click="checkout()"
                    class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                    Checkout
                </button>
            </div>
        </div>
    </div> -->
</div>

<script>
function productSelection() {
    return {
        products: [
            {
                id: 1,
                name: 'Laptop Gaming ASUS ROG',
                description: 'Laptop gaming dengan performa tinggi, layar 15.6 inch, RAM 16GB',
                price: 15000000,
                stock: 5,
                category: 'Elektronik',
                image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop'
            },
            {
                id: 2,
                name: 'Mouse Wireless Logitech',
                description: 'Mouse wireless ergonomis dengan baterai tahan lama',
                price: 250000,
                stock: 25,
                category: 'Elektronik',
                image: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop'
            },
            {
                id: 3,
                name: 'Mechanical Keyboard RGB',
                description: 'Keyboard mechanical dengan lampu RGB customizable',
                price: 850000,
                stock: 0,
                category: 'Elektronik',
                image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=300&fit=crop'
            },
            {
                id: 4,
                name: 'Monitor LED 24 inch',
                description: 'Monitor LED Full HD dengan refresh rate 144Hz',
                price: 2500000,
                stock: 8,
                category: 'Elektronik',
                image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop'
            },
            {
                id: 5,
                name: 'Headset Gaming HyperX',
                description: 'Headset gaming dengan surround sound 7.1',
                price: 1200000,
                stock: 15,
                category: 'Elektronik',
                image: 'https://images.unsplash.com/photo-1599669454699-248893623440?w=400&h=300&fit=crop'
            },
            {
                id: 6,
                name: 'Webcam HD Logitech',
                description: 'Webcam 1080p untuk video call dan streaming',
                price: 750000,
                stock: 12,
                category: 'Elektronik',
                image: 'https://images.unsplash.com/photo-1625605320347-78e5531e3a47?w=400&h=300&fit=crop'
            }
        ],
        filteredProducts: [],
        cart: [],
        cartOpen: false,
        quantities: {},
        search: '',
        selectedCategory: '',
        sortBy: 'name',
        categories: [],

        init() {
            this.filteredProducts = [...this.products];
            this.categories = [...new Set(this.products.map(p => p.category))];
            this.products.forEach(p => {
                this.quantities[p.id] = 1;
            });
        },

        filterProducts() {
            let result = [...this.products];

            if (this.search) {
                result = result.filter(p => 
                    p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    p.description.toLowerCase().includes(this.search.toLowerCase())
                );
            }

            if (this.selectedCategory) {
                result = result.filter(p => p.category === this.selectedCategory);
            }

            this.filteredProducts = result;
            this.sortProducts();
        },

        sortProducts() {
            switch(this.sortBy) {
                case 'name':
                    this.filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'price_asc':
                    this.filteredProducts.sort((a, b) => a.price - b.price);
                    break;
                case 'price_desc':
                    this.filteredProducts.sort((a, b) => b.price - a.price);
                    break;
                case 'newest':
                    this.filteredProducts.sort((a, b) => b.id - a.id);
                    break;
            }
        },

        getQuantity(productId) {
            return this.quantities[productId] || 1;
        },

        updateQty(productId, value) {
            const product = this.products.find(p => p.id === productId);
            let qty = parseInt(value) || 1;
            qty = Math.max(1, Math.min(qty, product.stock));
            this.quantities[productId] = qty;
        },

        increaseQty(productId) {
            const product = this.products.find(p => p.id === productId);
            if (this.quantities[productId] < product.stock) {
                this.quantities[productId]++;
            }
        },

        decreaseQty(productId) {
            if (this.quantities[productId] > 1) {
                this.quantities[productId]--;
            }
        },

        addToCart(product) {
            const qty = this.quantities[product.id];
            const existing = this.cart.find(item => item.id === product.id);

            if (existing) {
                const newQty = existing.quantity + qty;
                if (newQty <= product.stock) {
                    existing.quantity = newQty;
                } else {
                    alert(`Stok tidak mencukupi. Maksimal ${product.stock} item`);
                    return;
                }
            } else {
                this.cart.push({
                    ...product,
                    quantity: qty
                });
            }

            // this.cartOpen = true;
        },

        // increaseCartQty(productId) {
        //     const item = this.cart.find(i => i.id === productId);
        //     if (item && item.quantity < item.stock) {
        //         item.quantity++;
        //     }
        // },

        // decreaseCartQty(productId) {
        //     const item = this.cart.find(i => i.id === productId);
        //     if (item && item.quantity > 1) {
        //         item.quantity--;
        //     }
        // },

        removeFromCart(productId) {
            this.cart = this.cart.filter(item => item.id !== productId);
        },

        cartTotal() {
            return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        formatPrice(price) {
            return 'Rp ' + price.toLocaleString('id-ID');
        },

        // checkout() {
        //     window.location.href = '/buyer/checkout';
        // }
    }
}
</script>
@endsection