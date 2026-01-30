@extends('layouts.app')

@section('title', "Cart")
@section('content')
<div x-data="cart()" x-init="init()" class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
            <p class="text-gray-600 mt-2">Berikut adalah item yang telah Anda tambahkan ke keranjang</p>
        </div>

        <!-- Empty State -->
        <div x-show="items.length === 0" x-cloak class="text-center py-16 bg-white rounded-xl shadow">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Keranjang kosong</h3>
            <p class="mt-1 text-gray-500">Tambahkan buku favorit Anda ke keranjang untuk mulai berbelanja</p>
            <div class="mt-6">
                <a href="#" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Jelajahi Katalog
                </a>
            </div>
        </div>

        <!-- Cart with Items -->
        <div x-show="items.length > 0" x-cloak>
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <!-- Left Column - Cart Items -->
                <div class="lg:col-span-8">
                    <!-- Cart Header with Select All -->
                    <div class="bg-white rounded-xl shadow p-6 mb-6">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       @change="toggleSelectAll"
                                       :checked="allItemsSelected"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm font-medium text-gray-900">Pilih Semua (<span x-text="selectedItemsCount"></span> item)</span>
                            </label>
                            <button @click="removeSelectedItems" 
                                    :disabled="selectedItemsCount === 0"
                                    :class="selectedItemsCount === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:text-red-700'"
                                    class="text-red-600 text-sm font-medium">
                                Hapus Terpilih
                            </button>
                        </div>
                    </div>

                    <!-- Cart Items List -->
                    <div class="space-y-4">
                        <template x-for="item in items" :key="item.id">
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <div class="p-6">
                                    <div class="flex">
                                        <!-- Checkbox -->
                                        <div class="flex items-start mr-4">
                                            <input type="checkbox" 
                                                   x-model="item.selected"
                                                   @change="updateSelectedItems"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                                        </div>

                                        <!-- Book Image -->
                                        <div class="flex-shrink-0">
                                            <img :src="item.cover_image" 
                                                 :alt="item.title"
                                                 class="h-32 w-24 object-cover rounded-lg">
                                        </div>

                                        <!-- Book Info -->
                                        <div class="ml-6 flex-1">
                                            <div class="flex justify-between">
                                                <div>
                                                    <h3 class="text-lg font-medium text-gray-900" x-text="item.title"></h3>
                                                    <p class="text-gray-600 mt-1" x-text="item.author"></p>
                                                    <div class="flex items-center mt-2">
                                                        <div class="flex text-yellow-400">
                                                            <template x-for="i in 5" :key="i">
                                                                <svg 
                                                                    :class="i <= Math.round(item.rating) ? 'text-yellow-400' : 'text-gray-300'"
                                                                    class="w-4 h-4" 
                                                                    fill="currentColor" 
                                                                    viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            </template>
                                                        </div>
                                                        <span class="ml-2 text-sm text-gray-600" x-text="item.rating"></span>
                                                    </div>
                                                </div>

                                                <!-- Price -->
                                                <div class="text-right">
                                                    <div class="text-2xl font-bold text-blue-600" x-text="formatCurrency(item.price * item.quantity)"></div>
                                                    <div class="text-sm text-gray-500 mt-1" x-text="`${formatCurrency(item.price)} per item`"></div>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex items-center justify-between mt-6">
                                                <!-- Quantity Control -->
                                                <div class="flex items-center">
                                                    <button @click="decreaseQuantity(item.id)"
                                                            :disabled="item.quantity <= 1"
                                                            :class="item.quantity <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                                            class="w-10 h-10 border rounded-l-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                        </svg>
                                                    </button>
                                                    <input type="number"
                                                           x-model="item.quantity"
                                                           @change="updateQuantity(item.id)"
                                                           min="1"
                                                           :max="item.stock"
                                                           class="w-16 h-10 text-center border-t border-b focus:outline-none">
                                                    <button @click="increaseQuantity(item.id)"
                                                            :disabled="item.quantity >= item.stock"
                                                            :class="item.quantity >= item.stock ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                                            class="w-10 h-10 border rounded-r-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </button>
                                                    <span class="ml-4 text-sm text-gray-500">
                                                        Stok: <span x-text="item.stock"></span>
                                                    </span>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="flex items-center space-x-4">
                                                    <button @click="saveForLater(item.id)"
                                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                        Simpan Nanti
                                                    </button>
                                                    <button @click="removeItem(item.id)"
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Saved for Later -->
                    <div x-show="savedItems.length > 0" x-cloak class="mt-12">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Disimpan untuk Nanti</h2>
                        <div class="space-y-4">
                            <template x-for="item in savedItems" :key="item.id">
                                <div class="bg-white rounded-xl shadow p-6">
                                    <div class="flex">
                                        <img :src="item.cover_image" 
                                             :alt="item.title"
                                             class="h-20 w-16 object-cover rounded">
                                        <div class="ml-6 flex-1">
                                            <h3 class="font-medium text-gray-900" x-text="item.title"></h3>
                                            <p class="text-gray-600 text-sm mt-1" x-text="item.author"></p>
                                            <div class="flex items-center justify-between mt-4">
                                                <span class="font-bold text-blue-600" x-text="formatCurrency(item.price)"></span>
                                                <div class="flex space-x-3">
                                                    <button @click="moveToCart(item.id)"
                                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                        Pindah ke Keranjang
                                                    </button>
                                                    <button @click="removeSavedItem(item.id)"
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:col-span-4 mt-8 lg:mt-0">
                    <div class="bg-white rounded-xl shadow p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Belanja</h2>
                        
                        <!-- Summary Details -->
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Item</span>
                                <span x-text="totalItems"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Harga</span>
                                <span class="font-medium" x-text="formatCurrency(subtotal)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Diskon</span>
                                <span class="font-medium text-green-600" x-text="`-${formatCurrency(discount)}`"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Biaya Layanan</span>
                                <span class="font-medium" x-text="formatCurrency(serviceFee)"></span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t my-6"></div>

                        <!-- Total -->
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-blue-600" x-text="formatCurrency(total)"></span>
                        </div>

                        <!-- Checkout Button -->
                        <button @click="proceedToCheckout"
                                :disabled="selectedItemsCount === 0 || isProcessing"
                                :class="selectedItemsCount === 0 || isProcessing ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                                class="w-full bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg x-show="isProcessing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isProcessing ? 'Memproses...' : `Checkout (${selectedItemsCount} item)`"></span>
                        </button>

                        <!-- Continue Shopping -->
                        <div class="mt-6 text-center">
                            <a href="#" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                ← Lanjutkan Berbelanja
                            </a>
                        </div>

                        <!-- Security Info -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Pembelian aman & terjamin
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Books -->
        <div x-show="items.length > 0" x-cloak class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Rekomendasi untuk Anda</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="book in recommendedBooks" :key="book.id">
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition duration-200 overflow-hidden">
                        <div class="p-4">
                            <img :src="book.cover_image" 
                                 :alt="book.title"
                                 class="w-full h-48 object-cover rounded">
                            <h3 class="mt-4 font-semibold text-gray-900 truncate" x-text="book.title"></h3>
                            <p class="text-sm text-gray-600 truncate" x-text="book.author"></p>
                            <div class="mt-2 flex justify-between items-center">
                                <span class="font-bold text-blue-600" x-text="formatCurrency(book.price)"></span>
                                <button @click="addToCart(book)"
                                        class="text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded-lg transition duration-200">
                                    + Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-4 right-4 z-50"
         x-cloak>
        <div :class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'"
             class="text-white px-6 py-3 rounded-lg shadow-lg">
            <div class="flex items-center">
                <template x-if="toastType === 'success'">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="toastType === 'error'">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <span x-text="toastMessage"></span>
            </div>
        </div>
    </div>
</div>

<script>
function cart() {
    return {
        // State
        items: [],
        savedItems: [],
        recommendedBooks: [],
        isProcessing: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        
        // Computed properties
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        
        get discount() {
            // Contoh diskon 10% jika total > 100000
            return this.subtotal > 100000 ? this.subtotal * 0.1 : 0;
        },
        
        get serviceFee() {
            return 2000; // Biaya tetap
        },
        
        get total() {
            return this.subtotal - this.discount + this.serviceFee;
        },
        
        get totalItems() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },
        
        get selectedItemsCount() {
            return this.items.filter(item => item.selected).length;
        },
        
        get allItemsSelected() {
            return this.items.length > 0 && this.items.every(item => item.selected);
        },
        
        get selectedItems() {
            return this.items.filter(item => item.selected);
        },
        
        // Methods
        init() {
            this.loadCartItems();
            this.loadRecommendedBooks();
            
            // Load saved items from localStorage
            const saved = localStorage.getItem('savedForLater');
            if (saved) {
                this.savedItems = JSON.parse(saved);
            }
        },
        
        loadCartItems() {
            // Load from localStorage atau API
            const cartData = localStorage.getItem('cart');
            if (cartData) {
                this.items = [
                    {
                        id: 1,
                        title: "Laut Bercerita",
                        author: "Leila S. Chudori",
                        price: 95000,
                        quantity: 2,
                        stock: 10,
                        rating: 4.7,
                        cover_image: "#",
                        selected: true
                    },
                    {
                        id: 2,
                        title: "Pulang",
                        author: "Leila S. Chudori",
                        price: 85000,
                        quantity: 1,
                        stock: 5,
                        rating: 4.5,
                        cover_image: "#",
                        selected: true
                    }
                ];
                this.saveCartToStorage();

                // this.items = JSON.parse(cartData).map(item => ({
                //     ...item,
                //     selected: true // Default semua terpilih
                // }));
            } else {
                // Contoh data jika localStorage kosong
                this.items = [
                    {
                        id: 1,
                        title: "Laut Bercerita",
                        author: "Leila S. Chudori",
                        price: 95000,
                        quantity: 2,
                        stock: 10,
                        rating: 4.7,
                        cover_image: "#",
                        selected: true
                    },
                    {
                        id: 2,
                        title: "Pulang",
                        author: "Leila S. Chudori",
                        price: 85000,
                        quantity: 1,
                        stock: 5,
                        rating: 4.5,
                        cover_image: "#",
                        selected: true
                    }
                ];
                this.saveCartToStorage();
            }
        },
        
        loadRecommendedBooks() {
            // Contoh data buku rekomendasi
            this.recommendedBooks = [
                {
                    id: 3,
                    title: "Ronggeng Dukuh Paruk",
                    author: "Ahmad Tohari",
                    price: 75000,
                    cover_image: "#"
                },
                {
                    id: 4,
                    title: "Bumi Manusia",
                    author: "Pramoedya Ananta Toer",
                    price: 90000,
                    cover_image: "#"
                },
                {
                    id: 5,
                    title: "Negeri 5 Menara",
                    author: "Ahmad Fuadi",
                    price: 80000,
                    cover_image: "#"
                },
                {
                    id: 6,
                    title: "Sang Pemimpi",
                    author: "Andrea Hirata",
                    price: 70000,
                    cover_image: "#"
                }
            ];
        },
        
        saveCartToStorage() {
            localStorage.setItem('cart', JSON.stringify(this.items));
        },
        
        saveSavedItemsToStorage() {
            localStorage.setItem('savedForLater', JSON.stringify(this.savedItems));
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },
        
        increaseQuantity(itemId) {
            const item = this.items.find(i => i.id === itemId);
            if (item && item.quantity < item.stock) {
                item.quantity++;
                this.updateQuantity(itemId);
            }
        },
        
        decreaseQuantity(itemId) {
            const item = this.items.find(i => i.id === itemId);
            if (item && item.quantity > 1) {
                item.quantity--;
                this.updateQuantity(itemId);
            }
        },
        
        updateQuantity(itemId) {
            const item = this.items.find(i => i.id === itemId);
            if (item) {
                // Validasi stok
                if (item.quantity > item.stock) {
                    item.quantity = item.stock;
                    this.showToastMessage(`Stok hanya tersedia ${item.stock} item`, 'error');
                }
                
                // Simpan ke localStorage
                this.saveCartToStorage();
                
                // Show success message
                this.showToastMessage(`Jumlah ${item.title} diperbarui`, 'success');
            }
        },
        
        removeItem(itemId) {
            const item = this.items.find(i => i.id === itemId);
            if (item) {
                this.items = this.items.filter(i => i.id !== itemId);
                this.saveCartToStorage();
                this.showToastMessage(`${item.title} dihapus dari keranjang`, 'success');
            }
        },
        
        removeSelectedItems() {
            const selectedTitles = this.selectedItems.map(item => item.title);
            this.items = this.items.filter(item => !item.selected);
            this.saveCartToStorage();
            
            if (selectedTitles.length > 0) {
                this.showToastMessage(`${selectedTitles.length} item dihapus dari keranjang`, 'success');
            }
        },
        
        saveForLater(itemId) {
            const itemIndex = this.items.findIndex(i => i.id === itemId);
            if (itemIndex !== -1) {
                const item = this.items[itemIndex];
                this.savedItems.push({...item, savedAt: new Date().toISOString()});
                this.items.splice(itemIndex, 1);
                
                this.saveCartToStorage();
                this.saveSavedItemsToStorage();
                this.showToastMessage(`${item.title} disimpan untuk nanti`, 'success');
            }
        },
        
        moveToCart(itemId) {
            const itemIndex = this.savedItems.findIndex(i => i.id === itemId);
            if (itemIndex !== -1) {
                const item = this.savedItems[itemIndex];
                this.items.push({
                    ...item,
                    quantity: 1,
                    selected: true
                });
                this.savedItems.splice(itemIndex, 1);
                
                this.saveCartToStorage();
                this.saveSavedItemsToStorage();
                this.showToastMessage(`${item.title} dipindah ke keranjang`, 'success');
            }
        },
        
        removeSavedItem(itemId) {
            const item = this.savedItems.find(i => i.id === itemId);
            if (item) {
                this.savedItems = this.savedItems.filter(i => i.id !== itemId);
                this.saveSavedItemsToStorage();
                this.showToastMessage(`${item.title} dihapus`, 'success');
            }
        },
        
        toggleSelectAll() {
            const selectAll = !this.allItemsSelected;
            this.items.forEach(item => item.selected = selectAll);
        },
        
        updateSelectedItems() {
            // Method ini dipanggil saat checkbox individual diubah
            // Tidak perlu melakukan apa-apa khusus karena sudah reactive
        },
        
        addToCart(book) {
            // Cek apakah buku sudah ada di keranjang
            const existingItem = this.items.find(item => item.id === book.id);
            
            if (existingItem) {
                if (existingItem.quantity < existingItem.stock || 10) {
                    existingItem.quantity++;
                    this.showToastMessage(`Jumlah ${book.title} ditambah`, 'success');
                } else {
                    this.showToastMessage(`Stok ${book.title} tidak mencukupi`, 'error');
                }
            } else {
                this.items.push({
                    ...book,
                    quantity: 1,
                    stock: 10, // Default stock
                    rating: 4.5, // Default rating
                    selected: true
                });
                this.showToastMessage(`${book.title} ditambahkan ke keranjang`, 'success');
            }
            
            this.saveCartToStorage();
        },
        
        proceedToCheckout() {
            if (this.selectedItemsCount === 0) {
                this.showToastMessage('Pilih minimal 1 item untuk checkout', 'error');
                return;
            }
            
            this.isProcessing = true;
            
            // Siapkan data untuk checkout
            const checkoutData = {
                items: this.selectedItems,
                subtotal: this.subtotal,
                discount: this.discount,
                serviceFee: this.serviceFee,
                total: this.total
            };
            
            // Simpan data checkout ke localStorage atau session
            localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
            
            // Simulasi API call
            setTimeout(() => {
                // Redirect ke halaman checkout
                window.location.href = '/checkout';
                this.isProcessing = false;
            }, 1500);
        },
        
        showToastMessage(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }
}
</script>

<style>
[x-cloak] {
    display: none !important;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

.sticky {
    position: sticky;
    top: 1.5rem;
}

/* Custom scrollbar untuk recommended books */
.grid::-webkit-scrollbar {
    height: 6px;
}

.grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.grid::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.grid::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection