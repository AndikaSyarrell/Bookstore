@extends('layouts.app')
@section('title', 'Buyer')

@section('content')
<div class="min-h-screen bg-gray-50">
    
    <!-- Hero Section -->
    <!-- <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to Bookstore</h1>
                <p class="text-xl text-blue-100 mb-8">Discover your next favorite book</p>
                <div class="max-w-2xl mx-auto">
                    <form action="#" method="GET" class="flex gap-2">
                        <input 
                            type="text" 
                            name="search"
                            placeholder="Search for books, authors, or categories..." 
                            class="flex-1 px-6 py-4 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        >
                        <button 
                            type="submit"
                            class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors"
                        >
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section> -->

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-">
        
        <!-- Categories Section -->
        <section class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Browse by Category</h2>
                    <p class="text-gray-600 mt-2">Find books in your favorite genres</p>
                </div>
                <a 
                    href="#" 
                    class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2"
                >
                    View All
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @forelse($categories as $category)
                    <a 
                        href="#"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-blue-500 transition-all duration-200 group"
                    >
                        <div class="text-center">
                            <!-- Category Icon/Illustration -->
                            <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            
                            <!-- Category Name -->
                            <h3 class="text-sm font-semibold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">
                                {{ $category['name'] }}
                            </h3>
                            
                            <!-- Product Count -->
                            <p class="text-xs text-gray-500">
                                {{ $category['count'] }} {{ Str::plural('book', $category['count']) }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">No categories available</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Featured Products Section -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Best Sellers</h2>
                    <p class="text-gray-600 mt-2">Our most popular books</p>
                </div>
                <a 
                    href="#" 
                    class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2"
                >
                    View All
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                @forelse($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200 group">
                        <!-- Product Image -->
                        <a href="#" class="block">
                            <div class="aspect-[3/4] bg-gray-200 overflow-hidden">
                                @if($product['image'])
                                    <img 
                                        src="{{ $product['image'] }}" 
                                        alt="{{ $product['name'] }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                        <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <!-- Product Info -->
                        <div class="p-4">
                            <a href="#" class="block">
                                <h3 class="text-sm font-semibold text-gray-900 mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $product['name'] }}
                                </h3>
                            </a>
                            
                            @if(isset($product['author']))
                                <p class="text-xs text-gray-600 mb-2">{{ $product['author'] }}</p>
                            @endif

                            <!-- Price & Sold -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-lg font-bold text-blue-600">
                                    {{ 'Rp ' . number_format($product['price'], 0, ',', '.') }}
                                </span>
                                @if($product['sold'] > 0)
                                    <span class="text-xs text-gray-500">
                                        {{ number_format($product['sold']) }} sold
                                    </span>
                                @endif
                            </div>

                            <!-- Stock Badge -->
                            @if(isset($product['stock']))
                                <div class="mb-3">
                                    @if($product['stock'] > 10)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            In Stock
                                        </span>
                                    @elseif($product['stock'] > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Low Stock ({{ $product['stock'] }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Out of Stock
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Add to Cart Button -->
                            <button 
                                onclick="addToCart({{ $product['id'] }}, '{{ addslashes($product['name']) }}', {{ $product['price'] }}, '{{ $product['image'] }}')"
                                class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                {{ isset($product['stock']) && $product['stock'] <= 0 ? 'disabled' : '' }}
                            >
                                {{ isset($product['stock']) && $product['stock'] <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-gray-500">No featured products available</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="mt-16 bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 md:p-12 text-white text-center">
            <h2 class="text-3xl font-bold mb-4">Can't Find What You're Looking For?</h2>
            <p class="text-blue-100 mb-8 max-w-2xl mx-auto">
                Browse our complete collection of books or contact us for special requests
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a 
                    href="#" 
                    class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors"
                >
                    Browse All Books
                </a>
                <a 
                    href="#" 
                    class="px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition-colors"
                >
                    Contact Us
                </a>
            </div>
        </section>

    </div>
</div>
<script>
    // Add to cart function
    async function addToCart(productId, name, price, image) {
        try {
            const response = await fetch('{{ route("cart.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });

            if (response.ok) {
                const data = await response.json();
                
                // Update global cart store
                if (window.cartStore && data.cart_item) {
                    window.cartStore.addItem({
                        id: data.cart_item.id,
                        product_id: productId,
                        name: name,
                        price: price,
                        quantity: 1,
                        image: image
                    });
                }

                // Show success notification
                showNotification('Product added to cart!', 'success');
            } else {
                const error = await response.json();
                showNotification(error.message || 'Failed to add product to cart', 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showNotification('An error occurred', 'error');
        }
    }

    // Simple notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>
@endsection