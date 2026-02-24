@extends('layouts.app')

@section('content')
<div x-data="faqPage()" class="min-h-screen bg-gradient-to-br from-green-50 to-teal-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Buyer FAQ</h1>
            <p class="text-lg text-gray-600">Your guide to shopping on our platform</p>
        </div>

        <!-- Search -->
        <div class="mb-8">
            <div class="relative">
                <input 
                    type="text" 
                    x-model="searchQuery"
                    @input="filterFaqs()"
                    placeholder="Search for answers..." 
                    class="w-full px-5 py-4 pl-12 text-lg border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                >
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- No Results -->
        <div x-show="searchQuery && filteredFaqs.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-500 text-lg">No results found for "<span x-text="searchQuery"></span>"</p>
            <button @click="searchQuery = ''; filterFaqs()" class="mt-4 text-green-600 hover:text-green-700 font-medium">
                Clear search
            </button>
        </div>

        <!-- FAQ Categories -->
        <div class="space-y-6">
            @foreach($faqs as $categoryIndex => $category)
            <div x-show="!searchQuery || categoryVisible({{ $categoryIndex }})">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Category Header -->
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">{{ $category['category'] }}</h2>
                    </div>

                    <!-- Questions -->
                    <div class="divide-y divide-gray-200">
                        @foreach($category['items'] as $index => $item)
                        <div 
                            x-show="!searchQuery || itemVisible({{ $categoryIndex }}, {{ $index }})"
                            class="transition-all duration-200"
                        >
                            <!-- Question Button -->
                            <button 
                                @click="toggleItem({{ $categoryIndex }}, {{ $index }})"
                                class="w-full px-6 py-5 text-left hover:bg-gray-50 transition-colors flex items-start justify-between gap-4"
                            >
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 text-lg leading-relaxed">
                                        {{ $item['question'] }}
                                    </h3>
                                </div>
                                <svg 
                                    class="w-6 h-6 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                    :class="{ 'rotate-180': openItems.includes('{{ $categoryIndex }}-{{ $index }}') }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Answer -->
                            <div 
                                x-show="openItems.includes('{{ $categoryIndex }}-{{ $index }}')"
                                x-collapse
                                class="px-6 pb-5"
                            >
                                <div class="pl-4 border-l-4 border-green-500">
                                    <p class="text-gray-700 leading-relaxed">
                                        {{ $item['answer'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        <!-- Still Need Help -->
        <div class="mt-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl p-8 text-center">
            <h3 class="text-2xl font-bold text-white mb-3">Still need help?</h3>
            <p class="text-green-100 mb-6">Can't find the answer you're looking for? Browse our shop or contact support.</p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors">
                    Browse Shop
                </a>
                <a href="{{ route('buyer.orders') }}" class="inline-block px-6 py-3 bg-white/20 text-white font-semibold rounded-lg hover:bg-white/30 transition-colors">
                    My Orders
                </a>
            </div>
        </div>

    </div>
</div>

<script>
function faqPage() {
    return {
        searchQuery: '',
        openItems: [],
        filteredFaqs: @json($faqs),
        allFaqs: @json($faqs),

        toggleItem(categoryIndex, itemIndex) {
            const key = `${categoryIndex}-${itemIndex}`;
            const index = this.openItems.indexOf(key);
            
            if (index > -1) {
                this.openItems.splice(index, 1);
            } else {
                this.openItems.push(key);
            }
        },

        filterFaqs() {
            if (!this.searchQuery) {
                this.filteredFaqs = this.allFaqs;
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.filteredFaqs = this.allFaqs.map(category => {
                const filteredItems = category.items.filter(item => {
                    return item.question.toLowerCase().includes(query) ||
                           item.answer.toLowerCase().includes(query);
                });

                return {
                    ...category,
                    items: filteredItems
                };
            }).filter(category => category.items.length > 0);

            // Auto-open matching items
            if (this.searchQuery) {
                this.filteredFaqs.forEach((category, catIndex) => {
                    category.items.forEach((item, itemIndex) => {
                        const key = `${catIndex}-${itemIndex}`;
                        if (!this.openItems.includes(key)) {
                            this.openItems.push(key);
                        }
                    });
                });
            }
        },

        categoryVisible(categoryIndex) {
            if (!this.searchQuery) return true;
            return this.filteredFaqs.some((cat, idx) => idx === categoryIndex && cat.items.length > 0);
        },

        itemVisible(categoryIndex, itemIndex) {
            if (!this.searchQuery) return true;
            
            const category = this.filteredFaqs[categoryIndex];
            if (!category) return false;
            
            return category.items[itemIndex] !== undefined;
        }
    }
}
</script>
@endsection