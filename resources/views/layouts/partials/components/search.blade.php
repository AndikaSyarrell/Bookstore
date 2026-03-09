<div x-data="globalSearch()" @keydown.escape.window="showResults = false" class="relative">

    <!-- Search Input -->
    <div class="relative">
        <input
            type="text"
            x-model="query"
            @input="debounceSearch()"
            @focus="showResults = true"
            @click.away="showResults = false"
            placeholder="Search books, authors, categories..."
            class="w-full px-4 py-2.5 pl-11 pr-10 bg-white border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-gray-400">
        <!-- Search Icon -->
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        <!-- Clear Button -->
        <button
            x-show="query.length > 0"
            @click="clearSearch()"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Loading Spinner -->
        <div
            x-show="isLoading"
            class="absolute right-3 top-1/2 -translate-y-1/2">
            <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Search Results Dropdown -->
    <div
        x-show="showResults && (results.products.length > 0 || results.categories.length > 0 || query.length >= 2)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-2xl border border-gray-200 max-h-[32rem] overflow-hidden"
        style="display: none;">

        <!-- Quick Categories (if query empty or short) -->
        <template x-if="query.length < 2 && results.categories.length > 0">
            <div class="p-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 px-2">Quick Browse</p>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="category in results.categories.slice(0, 6)" :key="category.id">
                        <a
                            :href="`/category/${category.slug}`"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group">
                            <span class="text-lg">📖</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate group-hover:text-blue-600" x-text="category.name"></p>
                                <p class="text-xs text-gray-500" x-text="category.products_count + ' books'"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </template>

        <!-- Search Results -->
        <template x-if="query.length >= 2">
            <div class="overflow-y-auto max-h-[28rem]">

                <!-- Products Results -->
                <template x-if="results.products.length > 0">
                    <div class="p-3 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 px-2">Books</p>
                        <div class="space-y-1">
                            <template x-for="product in results.products" :key="product.id">
                                <a
                                    :href="`/product/${product.id}`"
                                    class="flex items-start gap-3 px-2 py-2.5 rounded-lg hover:bg-blue-50 transition-colors group"
                                    @click="recordClick(product.id)">
                                    <!-- Image -->
                                    <div class="w-12 h-16 flex-shrink-0 rounded overflow-hidden bg-gray-100">
                                        <img
                                            :src="product.img ? `/storage/products/${product.img}` : '/images/book-placeholder.svg'"
                                            :alt="product.title"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 group-hover:text-blue-600 line-clamp-1" x-text="product.title"></p>
                                        <p class="text-sm text-gray-600 line-clamp-1" x-text="'by ' + product.author"></p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <p class="text-sm font-bold text-blue-600" x-text="'Rp ' + formatPrice(product.price)"></p>
                                            <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full" x-text="product.category_name"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <!-- View All Results -->
                        <a
                            :href="`{{ route('home.search') }}?q=${encodeURIComponent(query)}`"
                            class="block mt-2 px-2 py-2 text-center text-sm font-semibold text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                            View all <span x-text="results.total_count"></span> results →
                        </a>
                    </div>
                </template>

                <!-- No Results -->
                <template x-if="results.products.length === 0 && !isLoading">
                    <div class="p-8 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-900 font-medium mb-1">No results found</p>
                        <p class="text-sm text-gray-600">Try different keywords</p>
                    </div>
                </template>

            </div>
        </template>

        <!-- Recent Searches (if available) -->
        <template x-if="query.length === 0 && recentSearches.length > 0">
            <div class="p-3 border-t border-gray-100">
                <div class="flex items-center justify-between mb-2 px-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Recent Searches</p>
                    <button @click="clearRecent()" class="text-xs text-gray-500 hover:text-gray-700">Clear</button>
                </div>
                <div class="space-y-1">
                    <template x-for="search in recentSearches.slice(0, 5)" :key="search">
                        <button
                            @click="query = search; performSearch()"
                            class="w-full flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors text-left group">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-700 group-hover:text-gray-900" x-text="search"></span>
                        </button>
                    </template>
                </div>
            </div>
        </template>

    </div>

</div>

<script>
    function globalSearch() {
        return {
            query: '',
            showResults: false,
            isLoading: false,
            debounceTimer: null,

            results: {
                products: [],
                categories: [],
                total_count: 0
            },

            recentSearches: [],

            init() {
                // Load recent searches from localStorage
                const stored = localStorage.getItem('recentSearches');
                if (stored) {
                    this.recentSearches = JSON.parse(stored);
                }

                // Load initial categories
                this.loadCategories();
            },

            async loadCategories() {
                try {
                    const response = await fetch("{{ route('home.search.categories') }}");
                    const data = await response.json();
                    this.results.categories = data.categories || [];
                } catch (error) {
                    console.error('Error loading categories:', error);
                }
            },

            debounceSearch() {
                clearTimeout(this.debounceTimer);

                if (this.query.length < 2) {
                    this.results.products = [];
                    return;
                }

                this.isLoading = true;

                this.debounceTimer = setTimeout(() => {
                    this.performSearch();
                }, 300);
            },

            async performSearch() {
                if (this.query.length < 2) {
                    this.isLoading = false;
                    return;
                }

                try {
                    const response = await fetch(`{{ route('home.search.quick') }}?q=${encodeURIComponent(this.query)}`);
                    const data = await response.json();

                    this.results.products = data.products || [];
                    this.results.total_count = data.total_count || 0;
                    this.showResults = true;

                    // Save to recent searches
                    this.saveRecentSearch(this.query);
                } catch (error) {
                    console.error('Search error:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            clearSearch() {
                this.query = '';
                this.results.products = [];
                this.showResults = false;
            },

            saveRecentSearch(query) {
                // Remove if exists
                this.recentSearches = this.recentSearches.filter(s => s !== query);

                // Add to front
                this.recentSearches.unshift(query);

                // Keep only last 10
                this.recentSearches = this.recentSearches.slice(0, 10);

                // Save to localStorage
                localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
            },

            clearRecent() {
                this.recentSearches = [];
                localStorage.removeItem('recentSearches');
            },

            recordClick(productId) {
                // Optional: Track click for analytics
                fetch(`{{ route('home.search.track-click') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        query: this.query
                    })
                }).catch(() => {});
            },

            formatPrice(price) {
                return new Intl.NumberFormat('id-ID').format(price);
            }
        }
    }
</script>