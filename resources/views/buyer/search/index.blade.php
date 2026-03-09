@extends('layouts.app')

@section('content')
<div x-data="searchResults()" class="min-h-screen bg-gray-50">
    
    <div class="container mx-auto px-4 py-8">
        
        <!-- Search Summary Header -->
        <div class="mb-8">
            @if(!empty($filters['query']))
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    Search Results for "<span class="text-blue-600">{{ $filters['query'] }}</span>"
                </h1>
            @else
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    All Books
                </h1>
            @endif
            
            <div class="flex flex-wrap items-center gap-4 text-gray-600">
                <p class="text-lg">
                    <span class="font-semibold text-gray-900">{{ number_format($totalResults) }}</span> 
                    {{ Str::plural('result', $totalResults) }} found
                </p>
                
                @if($filters['category'] || $filters['min_price'] || $filters['max_price'])
                <div class="h-4 w-px bg-gray-300"></div>
                <p class="text-sm">Active filters applied</p>
                @endif
            </div>
        </div>

        <!-- Active Filters Tags -->
        @if($filters['query'] || $filters['category'] || $filters['min_price'] || $filters['max_price'])
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700">Filters:</span>
            
            @if($filters['query'])
            <a href="{{ route('home.search') }}" 
               class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium hover:bg-blue-200 transition-colors">
                <span>Search: "{{ $filters['query'] }}"</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
            @endif
            
            @if($filters['category'])
            @php
                $selectedCategory = $categories->firstWhere('id', $filters['category']);
            @endphp
            @if($selectedCategory)
            <a href="{{ route('home.search', array_filter(['q' => $filters['query']])) }}" 
               class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium hover:bg-purple-200 transition-colors">
                <span>{{ $selectedCategory->name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
            @endif
            @endif
            
            @if($filters['min_price'] || $filters['max_price'])
            <a href="{{ route('home.search', array_filter(['q' => $filters['query'], 'category' => $filters['category']])) }}" 
               class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium hover:bg-green-200 transition-colors">
                <span>
                    Price: 
                    @if($filters['min_price'] && $filters['max_price'])
                        Rp {{ number_format($filters['min_price'], 0, ',', '.') }} - Rp {{ number_format($filters['max_price'], 0, ',', '.') }}
                    @elseif($filters['min_price'])
                        > Rp {{ number_format($filters['min_price'], 0, ',', '.') }}
                    @else
                        < Rp {{ number_format($filters['max_price'], 0, ',', '.') }}
                    @endif
                </span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
            @endif
            
            <a href="{{ route('home.search') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                Clear all
            </a>
        </div>
        @endif

        <!-- Sort & View Options -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <!-- Sort Dropdown -->
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <form method="GET" action="{{ route('home.search') }}">
                    <input type="hidden" name="q" value="{{ $filters['query'] }}">
                    <input type="hidden" name="category" value="{{ $filters['category'] }}">
                    <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                    <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
                    
                    <select name="sort" onchange="this.form.submit()" 
                            class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="newest" {{ $filters['sort'] == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="popular" {{ $filters['sort'] == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="price_low" {{ $filters['sort'] == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ $filters['sort'] == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ $filters['sort'] == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                        <option value="name_desc" {{ $filters['sort'] == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                    </select>
                </form>
            </div>

            <!-- View Toggle (Grid/List) -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">View:</span>
                <button 
                    @click="viewMode = 'grid'" 
                    :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    class="p-2 rounded-lg border-2 border-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button 
                    @click="viewMode = 'list'" 
                    :class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    class="p-2 rounded-lg border-2 border-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

        </div>

        <!-- Products Results -->
        @if($products->count() > 0)
            
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
                @foreach($products as $product)
                @include('layouts.partials.components.product-card', ['product' => $product])
                @endforeach
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'" class="space-y-4 mb-8" style="display: none;">
                @foreach($products as $product)
                <a href="#" 
                   class="block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-gray-200 hover:border-blue-500 group">
                    <div class="flex gap-6 p-6">
                        <!-- Image -->
                        <div class="w-32 h-40 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                            @if($product->img)
                            <img src="{{ asset('storage/products/' . $product->img) }}" 
                                 alt="{{ $product->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="mb-2">
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">
                                {{ $product->title }}
                            </h3>
                            <p class="text-gray-600 mb-3">by {{ $product->author }}</p>
                            
                            @if($product->description)
                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">{{ $product->description }}</p>
                            @endif

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-blue-600">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-2">
                                        @if($product->seller->img)
                                        <img src="{{ asset('storage/profile/' . $product->seller->img) }}" 
                                             alt="{{ $product->seller->name }}" 
                                             class="w-5 h-5 rounded-full object-cover">
                                        @else
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($product->seller->name, 0, 1) }}
                                        </div>
                                        @endif
                                        <span class="text-sm text-gray-600">{{ $product->seller->name }}</span>
                                    </div>
                                </div>

                                @if($product->stock > 0)
                                <div class="text-right">
                                    <p class="text-sm text-green-600 font-semibold">In Stock</p>
                                    <p class="text-xs text-gray-500">{{ $product->stock }} available</p>
                                </div>
                                @else
                                <div class="text-right">
                                    <p class="text-sm text-red-600 font-semibold">Out of Stock</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            @endif

        @else
            <!-- No Results -->
            <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-300">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 mb-6">
                    @if(!empty($filters['query']))
                        We couldn't find any books matching "{{ $filters['query'] }}"
                    @else
                        Try adjusting your filters
                    @endif
                </p>
                <div class="flex gap-3 justify-center">
                    <a href="{{ route('home.search') }}" 
                       class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Clear Filters
                    </a>
                    <a href="{{ route('home') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                        Back to Home
                    </a>
                </div>
            </div>
        @endif

    </div>

</div>

<script>
function searchResults() {
    return {
        viewMode: 'grid',
        
        init() {
            // Load view preference from localStorage
            const savedView = localStorage.getItem('searchViewMode');
            if (savedView) {
                this.viewMode = savedView;
            }
        },
        
        // Watch viewMode changes
        watch: {
            viewMode(value) {
                localStorage.setItem('searchViewMode', value);
            }
        }
    }
}
</script>
@endsection