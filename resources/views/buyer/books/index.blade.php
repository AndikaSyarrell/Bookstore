@extends('layouts.app')
@section('title', 'Book Details')
@section('content')
<div x-data="bookDetail()" class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>

        <!-- Main Content -->
        <div x-show="!loading" x-cloak>
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="#" class="text-sm font-medium text-gray-700 hover:text-blue-600">Beranda</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600">Katalog Buku</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500">Detail Buku</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Book Detail Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="md:flex">
                    <!-- Book Cover -->
                    <div class="md:w-1/3 p-8">
                        <div class="relative">
                            <img
                                :src="book.cover_image || 'https://via.placeholder.com/400x500?text=Cover+Buku'"
                                :alt="book.title"
                                class="w-full rounded-lg shadow-md">
                            <!-- Badge Stok -->
                            <div class="absolute top-4 right-4">
                                <span x-show="book.stock > 0"
                                    class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    Tersedia
                                </span>
                                <span x-show="book.stock <= 0"
                                    class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    Habis
                                </span>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mt-6">
                            <div class="flex items-center">
                                <div class="flex">
                                    <template x-for="i in 5" :key="i">
                                        <svg
                                            :class="i <= Math.round(book.rating) ? 'text-yellow-400' : 'text-gray-300'"
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                </div>
                                <span class="ml-2 text-gray-600" x-text="`${book.rating} (${book.review_count} ulasan)`"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Book Info -->
                    <div class="md:w-2/3 p-8">
                        <!-- Title & Author -->
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900" x-text="book.title"></h1>
                            <p class="mt-2 text-lg text-gray-600" x-text="`Oleh: ${book.author}`"></p>
                        </div>

                        <!-- Price -->
                        <div class="mt-6">
                            <div class="flex items-center">
                                <span class="text-3xl font-bold text-blue-600" x-text="formatCurrency(book.price)"></span>
                                <span x-show="book.discount > 0" class="ml-4">
                                    <span class="text-lg text-gray-500 line-through" x-text="formatCurrency(book.original_price)"></span>
                                    <span class="ml-2 bg-red-100 text-red-800 text-sm font-semibold px-2 py-1 rounded">
                                        <span x-text="`-${book.discount}%`"></span>
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- Stock & Sales -->
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Stok Tersedia</p>
                                <p class="text-lg font-semibold" x-text="book.stock"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Terjual</p>
                                <p class="text-lg font-semibold" x-text="book.sold_count"></p>
                            </div>
                        </div>

                        <!-- Category & Publisher -->
                        <div class="mt-6">
                            <div class="flex flex-wrap gap-2">
                                <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                                    <span x-text="book.category"></span>
                                </span>
                                <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                                    <span x-text="book.publisher"></span>
                                </span>
                                <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                                    <span x-text="book.publication_year"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-900">Deskripsi Buku</h3>
                            <div class="mt-3 text-gray-700" x-html="book.description"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 border-t pt-6">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Quantity Selector -->
                                <div class="flex items-center" x-show="book.stock > 0">
                                    <span class="mr-4 font-medium">Jumlah:</span>
                                    <div class="flex items-center border rounded-lg">
                                        <button
                                            @click="decreaseQuantity"
                                            :disabled="quantity <= 1"
                                            :class="quantity <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                            class="px-3 py-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <input
                                            type="number"
                                            x-model="quantity"
                                            min="1"
                                            :max="book.stock"
                                            class="w-16 text-center border-l border-r focus:outline-none">
                                        <button
                                            @click="increaseQuantity"
                                            :disabled="quantity >= book.stock"
                                            :class="quantity >= book.stock ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                            class="px-3 py-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                    <span class="ml-4 text-sm text-gray-500">
                                        Stok: <span x-text="book.stock"></span>
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-4">
                                    <!-- Add to Cart Button -->
                                    <button
                                        x-show="book.stock > 0"
                                        @click="addToCart"
                                        :disabled="isAddingToCart"
                                        :class="isAddingToCart ? 'opacity-75 cursor-not-allowed' : 'hover:bg-blue-700'"
                                        class="flex-1 bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                                        <svg x-show="isAddingToCart" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="isAddingToCart ? 'Menambahkan...' : 'Tambah ke Keranjang'"></span>
                                    </button>

                                    <!-- Buy Now Button -->
                                    <button
                                        x-show="book.stock > 0"
                                        @click="buyNow"
                                        class="flex-1 bg-green-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-green-700 transition duration-200">
                                        Beli Sekarang
                                    </button>

                                    <!-- Out of Stock Button -->
                                    <button
                                        x-show="book.stock <= 0"
                                        disabled
                                        class="flex-1 bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg cursor-not-allowed">
                                        Stok Habis
                                    </button>

                                    <button
                                        @click="openChat"
                                        class="px-4 py-3 bg-green-50 text-green-700 border border-green-200 rounded-lg hover:bg-green-100 transition duration-200 flex items-center justify-center group"
                                        :disabled="isChatLoading"
                                        :class="isChatLoading ? 'opacity-75 cursor-not-allowed' : ''">
                                        <svg
                                            x-show="!isChatLoading"
                                            class="w-5 h-5 mr-2 text-green-600 group-hover:text-green-800"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <svg
                                            x-show="isChatLoading"
                                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-green-600"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="isChatLoading ? 'Membuka Chat...' : 'Chat Penjual'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <!-- <div class="mt-8 grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Detail Buku</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ISBN</span>
                            <span class="font-medium" x-text="book.isbn"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Halaman</span>
                            <span class="font-medium" x-text="`${book.pages} halaman`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Bahasa</span>
                            <span class="font-medium" x-text="book.language"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Berat</span>
                            <span class="font-medium" x-text="`${book.weight} gram`"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dimensi</span>
                            <span class="font-medium" x-text="book.dimensions"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Ulasan Pembeli</h3>
                        <a href="#reviews" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Lihat Semua
                        </a>
                    </div>
                    
                    <div x-show="reviews.length === 0" class="text-center py-8">
                        <p class="text-gray-500">Belum ada ulasan untuk buku ini</p>
                    </div>
                    
                    <div x-show="reviews.length > 0">
                        <div class="space-y-4">
                            <template x-for="review in reviews.slice(0, 3)" :key="review.id">
                                <div class="border-b pb-4">
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400">
                                            <template x-for="i in 5" :key="i">
                                                <svg 
                                                    :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                                                    class="w-4 h-4" 
                                                    fill="currentColor" 
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </template>
                                        </div>
                                        <span class="ml-2 font-medium text-sm" x-text="review.user_name"></span>
                                        <span class="ml-auto text-xs text-gray-500" x-text="formatDate(review.created_at)"></span>
                                    </div>
                                    <p class="mt-2 text-gray-700 text-sm" x-text="review.comment"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Related Books -->
            <div x-show="relatedBooks.length > 0" class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Buku Serupa</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="relatedBook in relatedBooks" :key="relatedBook.id">
                        <a :href="`/books/${relatedBook.id}`" class="bg-white rounded-lg shadow hover:shadow-lg transition duration-200 overflow-hidden">
                            <div class="p-4">
                                <img
                                    :src="relatedBook.cover_image || 'https://via.placeholder.com/300x400?text=Cover+Buku'"
                                    :alt="relatedBook.title"
                                    class="w-full h-48 object-cover rounded">
                                <h3 class="mt-4 font-semibold text-gray-900 truncate" x-text="relatedBook.title"></h3>
                                <p class="text-sm text-gray-600 truncate" x-text="relatedBook.author"></p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="font-bold text-blue-600" x-text="formatCurrency(relatedBook.price)"></span>
                                    <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">
                                        <span x-text="relatedBook.category"></span>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div
        x-show="showToast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-4 right-4 z-50">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span x-text="toastMessage"></span>
            </div>
        </div>
    </div>

    <!-- Modal Chat (tambahkan di bagian bawah sebelum penutup div) -->
    <template x-if="showChatModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Chat dengan Penjual
                                </h3>
                                <div class="mt-4">
                                    <div class="flex items-center mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="relative">
                                                <img class="h-12 w-12 rounded-full" src="https://via.placeholder.com/48?text=TS" alt="Seller">
                                                <span x-show="sellerInfo.online" class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400"></span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-semibold text-gray-900" x-text="sellerInfo.name"></h4>
                                            <div class="flex items-center mt-1">
                                                <div class="flex text-yellow-400">
                                                    <template x-for="i in 5" :key="i">
                                                        <svg
                                                            :class="i <= Math.round(sellerInfo.rating) ? 'text-yellow-400' : 'text-gray-300'"
                                                            class="w-4 h-4"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    </template>
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600" x-text="sellerInfo.rating"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Initial message -->
                                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Pesan otomatis:</p>
                                        <p class="text-gray-800">Halo, saya tertarik dengan buku "<span x-text="book.title" class="font-medium"></span>". 
        Apakah masih tersedia?</p>
                                    </div>

                                    <!-- Quick actions -->
                                    <div class="space-y-2">
                                        <button @click="sendQuickMessage('Apakah buku ini original?')" class="w-full text-left px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                                            Apakah buku ini original?
                                        </button>
                                        <button @click="sendQuickMessage('Boleh minta foto kondisi buku?')" class="w-full text-left px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                                            Boleh minta foto kondisi buku?
                                        </button>
                                        <button @click="sendQuickMessage('Ada diskon untuk pembelian lebih dari 1?')" class="w-full text-left px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                                            Ada diskon untuk pembelian lebih dari 1?
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            @click="redirectToChatPage"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Mulai Chat
                        </button>
                        <button
                            @click="showChatModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function bookDetail() {
        return {
            loading: true,
            book: {},
            relatedBooks: [],
            reviews: [],
            quantity: 1,
            isAddingToCart: false,
            showToast: false,
            toastMessage: '',
            // Di dalam data() function Alpine.js
            isChatLoading: false,
            sellerInfo: {
                name: "Toko Buku Sejahtera",
                online: true,
                response_time: "5 menit",
                rating: 4.8
            },

            // Tambahkan di dalam data() function
            showChatModal: false,

            

            init() {
                // Simulasi pengambilan data dari API
                this.fetchBookData();
            },

            fetchBookData() {
                // Data contoh - ganti dengan data dari API
                setTimeout(() => {
                    this.book = {
                        id: 1,
                        title: "Laut Bercerita",
                        author: "Leila S. Chudori",
                        description: "Novel ini mengisahkan tentang perjalanan hidup seorang aktivis yang hilang pada masa 1998. Buku ini merupakan karya terbaru dari penulis bestseller Leila S. Chudori.",
                        price: 95000,
                        original_price: 120000,
                        discount: 20,
                        cover_image: "https://via.placeholder.com/400x500?text=Laut+Bercerita",
                        stock: 15,
                        sold_count: 254,
                        rating: 4.7,
                        review_count: 128,
                        category: "Fiksi Sastra",
                        publisher: "Kepustakaan Populer Gramedia",
                        publication_year: "2021",
                        isbn: "9786024246945",
                        pages: 392,
                        language: "Indonesia",
                        weight: 450,
                        dimensions: "14x21 cm"
                    };

                    this.relatedBooks = [{
                            id: 2,
                            title: "Pulang",
                            author: "Leila S. Chudori",
                            price: 85000,
                            cover_image: "https://via.placeholder.com/300x400?text=Pulang",
                            category: "Fiksi Sastra"
                        },
                        {
                            id: 3,
                            title: "Ronggeng Dukuh Paruk",
                            author: "Ahmad Tohari",
                            price: 75000,
                            cover_image: "https://via.placeholder.com/300x400?text=Ronggeng+Dukuh+Paruk",
                            category: "Fiksi Sastra"
                        },
                        {
                            id: 4,
                            title: "Bumi Manusia",
                            author: "Pramoedya Ananta Toer",
                            price: 90000,
                            cover_image: "https://via.placeholder.com/300x400?text=Bumi+Manusia",
                            category: "Fiksi Sejarah"
                        },
                        {
                            id: 5,
                            title: "Negeri 5 Menara",
                            author: "Ahmad Fuadi",
                            price: 80000,
                            cover_image: "https://via.placeholder.com/300x400?text=Negeri+5+Menara",
                            category: "Fiksi Inspiratif"
                        }
                    ];

                    this.reviews = [{
                            id: 1,
                            user_name: "Rizki Pratama",
                            rating: 5,
                            comment: "Buku yang sangat mengharukan dan penuh makna. Recommended banget!",
                            created_at: "2024-03-15"
                        },
                        {
                            id: 2,
                            user_name: "Sari Dewi",
                            rating: 4,
                            comment: "Ceritanya bagus, tapi beberapa bagian agak lambat.",
                            created_at: "2024-03-10"
                        }
                    ];

                    this.loading = false;
                }, 1000);
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            },

            formatDate(dateString) {
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            },

            increaseQuantity() {
                if (this.quantity < this.book.stock) {
                    this.quantity++;
                }
            },

            decreaseQuantity() {
                if (this.quantity > 1) {
                    this.quantity--;
                }
            },

            addToCart() {
                this.isAddingToCart = true;

                // Simulasi API call
                setTimeout(() => {
                    this.showToastMessage(`${this.quantity} buku "${this.book.title}" berhasil ditambahkan ke keranjang`);
                    this.isAddingToCart = false;

                    // Reset quantity
                    this.quantity = 1;
                }, 1500);
            },

            buyNow() {
                // Redirect ke halaman checkout dengan buku ini
                window.location.href = `/checkout?book_id=${this.book.id}&quantity=${this.quantity}`;
            },


            showToastMessage(message) {
                this.toastMessage = message;
                this.showToast = true;

                setTimeout(() => {
                    this.showToast = false;
                }, 3000);
            },

            
            // Method tambahan
            sendQuickMessage(message) {
                // Logic untuk mengirim pesan cepat
                console.log('Mengirim pesan:', message);
                // Redirect ke halaman chat dengan pesan
                window.location.href = `/chat/seller/${this.book.seller_id}?book_id=${this.book.id}&message=${encodeURIComponent(message)}`;
            },

            redirectToChatPage() {
                // Redirect ke halaman chat
                window.location.href = `/chat/seller/${this.book.seller_id}?book_id=${this.book.id}`;
            },

            // Methods untuk chat
            openChat() {
                this.isChatLoading = true;

                // Simulasi loading sebelum membuka chat
                setTimeout(() => {
                    // Logika untuk membuka chat
                    this.openChatModal();
                    this.isChatLoading = false;
                }, 800);
            },

            openChatModal() {
                // Anda bisa menggunakan modal atau redirect ke halaman chat
                this.showChatModal = true;
                // Contoh dengan modal:
                this.$dispatch('open-chat-modal', {
                    sellerId: this.book.seller_id,
                    sellerName: this.sellerInfo.name,
                    bookTitle: this.book.title,
                    bookId: this.book.id
                });

                // Atau redirect ke halaman chat
                // window.location.href = `/chat/seller/${this.book.seller_id}?book_id=${this.book.id}`;
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
</style>
@endsection