@extends('layouts.app')

@section('content')
<div x-data="homePage()">
    
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="container mx-auto px-4 py-20 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Logo/Title -->
                <div class="mb-6">
                    <h1 class="text-5xl md:text-6xl font-bold mb-4">📚 BookStore</h1>
                    <p class="text-xl md:text-2xl text-blue-100">Discover Your Next Great Read</p>
                </div>

                <!-- Search Bar -->
                <div class="mt-10 max-w-2xl mx-auto">
                    <form action="{{ route('home.search') }}" method="GET" class="relative">
                        <input 
                            type="text" 
                            name="q"
                            placeholder="Search for books, authors, categories..." 
                            class="w-full px-6 py-4 pr-32 text-gray-900 rounded-full text-lg focus:outline-none focus:ring-4 focus:ring-white/30 shadow-2xl"
                        >
                        <button 
                            type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-6 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-full hover:from-orange-600 hover:to-red-600 transition-all shadow-lg"
                        >
                            Search
                        </button>
                    </form>
                </div>

                <!-- Stats -->
                <div class="mt-12 grid grid-cols-3 gap-8 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['total_products']) }}+</div>
                        <div class="text-blue-200 text-sm mt-1">Books Available</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['total_sellers']) }}+</div>
                        <div class="text-blue-200 text-sm mt-1">Trusted Sellers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['total_categories']) }}+</div>
                        <div class="text-blue-200 text-sm mt-1">Categories</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <div class="bg-gray-50">
        <div class="container mx-auto px-4 py-12">

            <!-- Categories Section -->
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Browse by Category</h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($categories->take(12) as $category)
                    <a href="{{ route('home.category', $category->slug) }}" 
                       class="group bg-white rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all border-2 border-transparent hover:border-blue-500">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center group-hover:from-blue-500 group-hover:to-indigo-500 transition-all">
                            <span class="text-2xl group-hover:scale-110 transition-transform">📖</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $category->products_count }} books</p>
                    </a>
                    @endforeach
                </div>
            </section>

            <!-- Featured Products -->
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">New Arrivals</h2>
                        <p class="text-gray-600 mt-1">Discover our latest additions</p>
                    </div>
                    <a href="{{ route('home.search') }}" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2">
                        View All
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                    @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>

            <!-- Popular Products -->
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Popular Books</h2>
                        <p class="text-gray-600 mt-1">Most loved by our readers</p>
                    </div>
                    <a href="{{ route('home.search', ['sort' => 'popular']) }}" class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2">
                        View All
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($popularProducts as $product)
                    @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>

            <!-- Featured Sellers -->
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Featured Sellers</h2>
                        <p class="text-gray-600 mt-1">Shop from our top sellers</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($featuredSellers as $seller)
                    <a href="{{ route('home.search', ['seller' => $seller->id]) }}" 
                       class="bg-white rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all border border-gray-200">
                        @if($seller->img)
                        <img src="{{ asset('storage/profile/' . $seller->img) }}" alt="{{ $seller->name }}" class="w-20 h-20 rounded-full mx-auto mb-3 object-cover border-4 border-blue-100">
                        @else
                        <div class="w-20 h-20 rounded-full mx-auto mb-3 bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-blue-100">
                            {{ substr($seller->name, 0, 1) }}
                        </div>
                        @endif
                        <h3 class="font-semibold text-gray-900 truncate">{{ $seller->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $seller->products_count }} books</p>
                    </a>
                    @endforeach
                </div>
            </section>

            <!-- Features Section -->
            <section class="mb-16">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-200">
                        <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Secure Payment</h3>
                        <p class="text-gray-600">Direct bank transfer with verified sellers</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-8 border border-green-200">
                        <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Easy Checkout</h3>
                        <p class="text-gray-600">Simple and fast ordering process</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-200">
                        <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Chat with Sellers</h3>
                        <p class="text-gray-600">Real-time communication support</p>
                    </div>
                </div>
            </section>

        </div>
    </div>

</div>

<script>
function homePage() {
    return {
        // Add any JavaScript functionality here
    }
}
</script>
@endsection