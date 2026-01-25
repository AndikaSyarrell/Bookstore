@extends('layouts.app')
@section('title', 'Buyer')

@section('content')
<div x-data="buyerDashboard()" class="min-h-screen bg-gray-50 rounded-lg">
    <div class="container mx-auto px-4 py-8">
        {{-- Categories --}}
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Kategori Populer</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <template x-for="category in categories" :key="category.id">
                    <button 
                        @click="filterByCategory(category.name)"
                        class="flex flex-col items-center p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition group"
                    >
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700" x-text="category.name"></span>
                        <span class="text-xs text-gray-500" x-text="`${category.count} produk`"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Featured Products --}}
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Produk Unggulan</h2>
                <a href="/products" class="text-blue-600 hover:underline font-medium">Lihat Semua</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <template x-for="product in featuredProducts" :key="product.id">
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition group">
                        <div class="relative overflow-hidden rounded-t-lg">
                            <img 
                                :src="product.image" 
                                :alt="product.name"
                                class="w-full h-48 object-cover group-hover:scale-110 transition duration-300"
                            >
                            <div x-show="product.discount" class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                                <span x-text="`-${product.discount}%`"></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2" x-text="product.name"></h3>
                            <div class="mb-3">
                                <div x-show="product.discount" class="text-xs text-gray-500 line-through" x-text="formatPrice(product.original_price)"></div>
                                <div class="text-lg font-bold text-blue-600" x-text="formatPrice(product.price)"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                <span>⭐ <span x-text="product.rating"></span></span>
                                <span x-text="`${product.sold} terjual`"></span>
                            </div>
                            <button 
                                @click="addToCart(product)"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                            >
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- New Arrivals --}}
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Produk Terbaru</h2>
                <a href="/products?sort=newest" class="text-blue-600 hover:underline font-medium">Lihat Semua</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <template x-for="product in newProducts" :key="product.id">
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition group">
                        <div class="relative overflow-hidden rounded-t-lg">
                            <img 
                                :src="product.image" 
                                :alt="product.name"
                                class="w-full h-48 object-cover group-hover:scale-110 transition duration-300"
                            >
                            <div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                Baru
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2" x-text="product.name"></h3>
                            <div class="text-lg font-bold text-blue-600 mb-3" x-text="formatPrice(product.price)"></div>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                <span>⭐ <span x-text="product.rating"></span></span>
                                <span>Stok: <span x-text="product.stock"></span></span>
                            </div>
                            <button 
                                @click="addToCart(product)"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                            >
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function buyerDashboard() {
    return {
        searchQuery: '',
        categories: [
            { id: 1, name: 'Elektronik', count: 120 },
            { id: 2, name: 'Fashion', count: 85 },
            { id: 3, name: 'Makanan', count: 45 },
            { id: 4, name: 'Kesehatan', count: 67 },
            { id: 5, name: 'Rumah', count: 92 },
            { id: 6, name: 'Olahraga', count: 54 }
        ],
        featuredProducts: [
            {
                id: 1,
                name: 'Laptop Gaming ASUS ROG Strix',
                image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop',
                price: 12750000,
                original_price: 15000000,
                discount: 15,
                rating: 4.8,
                sold: 234
            },
            {
                id: 2,
                name: 'iPhone 15 Pro Max 256GB',
                image: 'https://images.unsplash.com/photo-1592286927505-2177fef2361d?w=400&h=300&fit=crop',
                price: 18500000,
                original_price: 19999000,
                discount: 8,
                rating: 4.9,
                sold: 567
            },
            {
                id: 3,
                name: 'Samsung Galaxy S24 Ultra',
                image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&h=300&fit=crop',
                price: 16200000,
                original_price: 18000000,
                discount: 10,
                rating: 4.7,
                sold: 432
            },
            {
                id: 4,
                name: 'Sony WH-1000XM5 Headphones',
                image: 'https://images.unsplash.com/photo-1599669454699-248893623440?w=400&h=300&fit=crop',
                price: 4250000,
                original_price: 5000000,
                discount: 15,
                rating: 4.9,
                sold: 892
            },
            {
                id: 5,
                name: 'iPad Pro 12.9" M2 Chip',
                image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop',
                price: 14500000,
                original_price: 16999000,
                discount: 12,
                rating: 4.8,
                sold: 345
            }
        ],
        newProducts: [
            {
                id: 6,
                name: 'Mechanical Keyboard RGB Gaming',
                image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=300&fit=crop',
                price: 850000,
                rating: 4.6,
                stock: 45
            },
            {
                id: 7,
                name: 'Monitor 4K UHD 27 inch',
                image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop',
                price: 3200000,
                rating: 4.7,
                stock: 28
            },
            {
                id: 8,
                name: 'Wireless Gaming Mouse',
                image: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop',
                price: 650000,
                rating: 4.5,
                stock: 67
            },
            {
                id: 9,
                name: 'USB-C Hub Multiport Adapter',
                image: 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&h=300&fit=crop',
                price: 450000,
                rating: 4.4,
                stock: 89
            }
        ],

        formatPrice(price) {
            return 'Rp ' + price.toLocaleString('id-ID');
        },

        searchProducts() {
            if (this.searchQuery.trim()) {
                window.location.href = `/products?search=${encodeURIComponent(this.searchQuery)}`;
            }
        },

        filterByCategory(category) {
            window.location.href = `/products?category=${encodeURIComponent(category)}`;
        },

        addToCart(product) {
            // Dispatch custom event to update cart
            window.dispatchEvent(new CustomEvent('add-to-cart', { 
                detail: { product } 
            }));
            alert(`${product.name} ditambahkan ke keranjang!`);
        }
    }
}
</script>
@endsection