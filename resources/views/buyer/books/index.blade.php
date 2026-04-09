@extends('layouts.app')

@section('title', 'Book Details')

@section('content')
<div x-data="productDetail()" x-init="init()" class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('homepage') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Products
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Product Image & Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Product Image -->
                    <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                        <img 
                            src="{{ $product->img ? asset('storage/products/' . $product->img) : 'https://via.placeholder.com/800x600?text=No+Image' }}" 
                            alt="{{ $product->title }}"
                            class="w-full h-96 object-cover"
                        >
                    </div>

                    <!-- Product Info -->
                    <div class="p-6">
                        <!-- Category Badge -->
                        <div class="mb-3">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                {{ $product->category->name }}
                            </span>
                        </div>

                        <!-- Title & Author -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->title }}</h1>
                        <p class="text-lg text-gray-600 mb-4">by {{ $product->author }}</p>

                        <!-- Price & Stock -->
                        <div class="flex items-center gap-6 mb-6 pb-6 border-b">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Price</p>
                                <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Stock</p>
                                @if($product->stock > 0)
                                <p class="text-lg font-semibold text-green-600">{{ $product->stock }} available</p>
                                @else
                                <p class="text-lg font-semibold text-red-600">Out of Stock</p>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 mb-3">Description</h2>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Product Details</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Category</p>
                                    <p class="font-medium text-gray-900">{{ $product->category->name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Author</p>
                                    <p class="font-medium text-gray-900">{{ $product->author }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Listed Date</p>
                                    <p class="font-medium text-gray-900">{{ $product->created_at->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Product ID</p>
                                    <p class="font-medium text-gray-900">#{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Purchase & Seller Info -->
            <div class="lg:col-span-1">
                <div class="sticky top-4 space-y-4">
                    
                    <!-- Purchase Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Purchase Options</h3>
                        
                        <!-- Quantity Selector -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center gap-3">
                                <button 
                                    @click="quantity = Math.max(1, quantity - 1)"
                                    class="w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-50 flex items-center justify-center"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input 
                                    type="number" 
                                    x-model.number="quantity"
                                    min="1"
                                    max="{{ $product->stock }}"
                                    class="w-20 text-center px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                                <button 
                                    @click="quantity = Math.min({{ $product->stock }}, quantity + 1)"
                                    :disabled="{{ $product->stock }} <= quantity"
                                    class="w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-50 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Max {{ $product->stock }} items</p>
                        </div>

                        <!-- Total Price -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Subtotal</span>
                                <span class="text-xl font-bold text-blue-600" x-text="formatPrice({{ $product->selling_price }} * quantity)"></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            @if($product->stock > 0)
                            <button 
                                @click="addToCart()"
                                :disabled="isAddingToCart"
                                class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span x-show="!isAddingToCart">Add to Cart</span>
                                <span x-show="isAddingToCart">Adding...</span>
                            </button>

                            <button 
                                @click="buyNow()"
                                class="w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Buy Now
                            </button>
                            @else
                            <button 
                                disabled
                                class="w-full px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg cursor-not-allowed"
                            >
                                Out of Stock
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Seller Info Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Seller Information</h3>
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($product->seller->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $product->seller->name }}</p>
                                <p class="text-sm text-gray-600">Verified Seller</p>
                            </div>
                        </div>

                        <!-- Contact Seller Button with Product Context -->
                        <a 
                            href="{{ route('chats.start', ['seller_id' => $product->seller_id, 'product_id' => $product->id]) }}"
                            class="w-full px-6 py-3 border-2 border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Chat Seller
                        </a>

                        <p class="text-xs text-gray-500 text-center mt-2">
                            Ask about this product
                        </p>
                    </div>

                    <!-- Share & Wishlist -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex gap-3">
                            <button class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                Share
                            </button>
                            <button class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Wishlist
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <a href="#" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <img 
                        src="{{ $related->img ? asset('storage/products/' . $related->img) : 'https://via.placeholder.com/300x400' }}" 
                        alt="{{ $related->title }}"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 line-clamp-2 mb-2">{{ $related->title }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $related->author }}</p>
                        <p class="text-lg font-bold text-blue-600">Rp {{ number_format($related->price, 0, ',', '.') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<script>
function productDetail() {
    return {
        quantity: 1,
        isAddingToCart: false,
        productId: {{ $product->id }},

        init() {
            // Any initialization
        },

        formatPrice(price) {
            return 'Rp ' + price.toLocaleString('id-ID');
        },

        async addToCart() {
            this.isAddingToCart = true;

            try {
                const response = await fetch('{{ route("cart.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        quantity: this.quantity
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update cart store
                    if (window.cartStore) {
                        window.cartStore.syncWithServer();
                    }

                    // Show notification
                    this.showNotification('Product added to cart!', 'success');

                    // Reset quantity
                    this.quantity = 1;
                } else {
                    this.showNotification(data.message || 'Failed to add to cart', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('An error occurred', 'error');
            } finally {
                this.isAddingToCart = false;
            }
        },

        buyNow() {
            // Add to cart then redirect to cart page
            this.addToCart().then(() => {
                setTimeout(() => {
                    window.location.href = '{{ route("cart") }}';
                }, 500);
            });
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-2xl text-white z-50 ${bgColor} transform transition-all duration-300`;
            notification.textContent = message;
            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateY(0)';
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }
}
</script>
@endsection