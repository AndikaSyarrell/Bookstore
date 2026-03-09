<!-- Product Card Component -->
<a href="{{ route('products.show', $product->id) }}" class="group block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-blue-500">
    
    <!-- Product Image -->
    <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
        @if($product->img)
        <img src="{{ asset('storage/products/' . $product->img) }}" 
             alt="{{ $product->title }}" 
             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
            <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        @endif

        <!-- Stock Badge -->
        @if($product->stock < 5 && $product->stock > 0)
        <div class="absolute top-2 left-2">
            <span class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">
                Only {{ $product->stock }} left
            </span>
        </div>
        @endif

        <!-- New Badge -->
        @if($product->created_at->diffInDays(now()) < 7)
        <div class="absolute top-2 right-2">
            <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                New
            </span>
        </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="p-4">
        
        <!-- Category -->
        <div class="mb-2">
            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">
                {{ $product->category->name ?? 'Uncategorized' }}
            </span>
        </div>

        <!-- Title -->
        <h3 class="font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors min-h-[3rem]">
            {{ $product->title }}
        </h3>

        <!-- Author -->
        <p class="text-sm text-gray-600 mb-3">
            by {{ $product->author }}
        </p>

        <!-- Price & Seller -->
        <div class="flex items-end justify-between">
            <div>
                <p class="text-2xl font-bold text-blue-600">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Seller Info -->
        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
            @if($product->seller->img)
            <img src="{{ asset('storage/profile/' . $product->seller->img) }}" 
                 alt="{{ $product->seller->name }}" 
                 class="w-6 h-6 rounded-full object-cover">
            @else
            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                {{ substr($product->seller->name, 0, 1) }}
            </div>
            @endif
            <span class="text-xs text-gray-600 truncate">{{ $product->seller->name }}</span>
        </div>

    </div>

</a>