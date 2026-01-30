@extends('layouts.app')

@section('content')
<div x-data="orderStatus()" x-init="init()" class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Status Pesanan Saya</h1>
            <p class="text-gray-600 mt-2">Pantau semua pesanan Anda di satu tempat</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pesanan</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.total_orders"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Menunggu Diproses</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.pending"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Dalam Pengiriman</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.shipping"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Selesai</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.completed"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="searchOrders"
                            placeholder="Cari pesanan berdasarkan ID, nama produk..."
                            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <select x-model="filterStatus"
                        @change="filterOrders"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Pembayaran</option>
                        <option value="paid">Sudah Dibayar</option>
                        <option value="processing">Diproses</option>
                        <option value="shipping">Dikirim</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>

                    <select x-model="filterPeriod"
                        @change="filterOrders"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">Semua Waktu</option>
                        <option value="today">Hari Ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="last3months">3 Bulan Terakhir</option>
                    </select>

                    <button @click="resetFilters"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div x-show="isLoading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            <p class="mt-4 text-gray-600">Memuat data pesanan...</p>
        </div>

        <div x-show="!isLoading && filteredOrders.length > 0" x-cloak class="space-y-6">
            <template x-for="order in filteredOrders" :key="order.id">
                <!-- Order Card -->
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-200 overflow-hidden">
                    <!-- Order Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-4 mb-2">
                                    <span class="font-bold text-gray-900" x-text="`#${order.order_code}`"></span>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                        :class="getStatusClass(order.status)">
                                        <span x-text="getStatusText(order.status)"></span>
                                    </span>
                                    <span x-show="order.is_urgent" class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                        Urgent
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span x-text="formatDate(order.created_at)"></span>
                                    •
                                    <span x-text="`${order.items_count} item`"></span>
                                    •
                                    <span x-text="formatCurrency(order.total_amount)"></span>
                                </div>
                            </div>

                            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                                <!-- Chat Seller Button -->
                                <button @click="openChat(order.seller_id, order.order_code)"
                                    class="flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Chat Penjual
                                </button>

                                <button @click="toggleOrderDetails(order.id)"
                                    class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                    <span x-text="expandedOrders.includes(order.id) ? 'Sembunyikan' : 'Lihat Detail'"></span>
                                    <svg :class="expandedOrders.includes(order.id) ? 'rotate-180' : ''"
                                        class="w-5 h-5 ml-1 inline-block transition-transform duration-200"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Preview -->
                    <div class="p-6">
                        <div class="flex space-x-4 overflow-x-auto pb-2">
                            <template x-for="item in order.preview_items" :key="item.id">
                                <div class="flex-shrink-0 w-24">
                                    <div class="relative">
                                        <img :src="item.cover_image"
                                            :alt="item.title"
                                            class="h-32 w-full object-cover rounded-lg">
                                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                                            <span x-text="`${item.quantity}x`"></span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm font-medium text-gray-900 truncate" x-text="item.title"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="item.author"></p>
                                </div>
                            </template>

                            <div x-show="order.items_count > 3" class="flex-shrink-0 flex items-center justify-center w-24">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto">
                                        <span class="text-2xl font-bold text-gray-400" x-text="`+${order.items_count - 3}`"></span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">item lainnya</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expanded Details -->
                    <div x-show="expandedOrders.includes(order.id)"
                        x-collapse
                        class="border-t border-gray-200">
                        <div class="p-6">
                            <!-- Timeline -->
                            <div class="mb-8">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Status Pesanan</h4>
                                <div class="relative pl-8">
                                    <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                                    <template x-for="(timeline, index) in order.timeline" :key="index">
                                        <div class="relative mb-6 last:mb-0">
                                            <div class="absolute left-0 top-0 transform -translate-x-1/2 -translate-y-1/2">
                                                <div class="w-6 h-6 rounded-full border-4 border-white"
                                                    :class="timeline.completed ? 'bg-green-500' : 'bg-gray-300'"></div>
                                            </div>
                                            <div class="ml-6">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="font-medium text-gray-900" x-text="timeline.status"></p>
                                                        <p class="text-sm text-gray-500" x-text="timeline.description"></p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm text-gray-900" x-text="formatDate(timeline.date, true)"></p>
                                                        <p class="text-xs text-gray-500" x-text="timeline.time"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Shipping Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Alamat Pengiriman</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium" x-text="order.shipping_address?.recipient_name"></p>
                                        <p class="text-gray-600 mt-1" x-text="order.shipping_address?.phone"></p>
                                        <p class="text-gray-600 mt-1" x-text="order.shipping_address?.address"></p>
                                        <p class="text-gray-600" x-text="`${order.shipping_address?.city}, ${order.shipping_address?.province}`"></p>
                                        <p class="text-gray-600" x-text="`Kode Pos: ${order.shipping_address?.postal_code}`"></p>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Info Pengiriman</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="space-y-3">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Kurir</span>
                                                <span class="font-medium" x-text="order.shipping?.courier || 'Belum dipilih'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">No. Resi</span>
                                                <span class="font-medium" x-text="order.shipping?.tracking_number || 'Belum tersedia'"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Estimasi Tiba</span>
                                                <span class="font-medium" x-text="order.shipping?.estimated_delivery || 'Dalam proses'"></span>
                                            </div>
                                        </div>

                                        <button x-show="order.shipping?.tracking_number"
                                            @click="trackShipping(order.shipping.tracking_number, order.shipping.courier)"
                                            class="w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                            Lacak Pengiriman
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                                <button @click="viewOrderDetail(order.id)"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                                    Detail Lengkap
                                </button>

                                <button x-show="order.status === 'pending' || order.status === 'processing'"
                                    @click="cancelOrder(order.id)"
                                    class="px-4 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition duration-200">
                                    Batalkan Pesanan
                                </button>

                                <button x-show="order.status === 'completed'"
                                    @click="requestReturn(order.id)"
                                    class="px-4 py-2 border border-gray-600 text-gray-600 rounded-lg hover:bg-gray-50 transition duration-200">
                                    Ajukan Pengembalian
                                </button>

                                <button @click="downloadInvoice(order.order_code)"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
                                    Unduh Invoice
                                </button>

                                <button @click="reorderItems(order)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                    Pesan Ulang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Pagination -->
            <div x-show="pagination.total > pagination.per_page" class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span x-text="pagination.from"></span> - <span x-text="pagination.to"></span> dari <span x-text="pagination.total"></span> pesanan
                    </div>
                    <div class="flex space-x-2">
                        <button @click="prevPage"
                            :disabled="pagination.current_page === 1"
                            :class="pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            class="px-3 py-1 border border-gray-300 rounded-lg">
                            ← Sebelumnya
                        </button>
                        <div class="flex items-center space-x-1">
                            <template x-for="page in pagination.links" :key="page.label">
                                <button @click="goToPage(page.url)"
                                    :disabled="!page.url || page.active"
                                    :class="page.active ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                                    class="px-3 py-1 rounded-lg"
                                    x-text="page.label">
                                </button>
                            </template>
                        </div>
                        <button @click="nextPage"
                            :disabled="pagination.current_page === pagination.last_page"
                            :class="pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            class="px-3 py-1 border border-gray-300 rounded-lg">
                            Selanjutnya →
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="!isLoading && filteredOrders.length === 0" x-cloak class="text-center py-16 bg-white rounded-xl shadow">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada pesanan</h3>
            <p class="mt-1 text-gray-500" x-text="searchQuery || filterStatus || filterPeriod ? 'Tidak ada pesanan yang sesuai dengan filter' : 'Mulai berbelanja untuk melihat pesanan Anda di sini'"></p>
            <div class="mt-6">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Mulai Berbelanja
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div x-show="!isLoading && filteredOrders.length > 0" x-cloak class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Aktivitas Terbaru</h2>
            <div class="bg-white rounded-xl shadow">
                <div class="divide-y divide-gray-200">
                    <template x-for="activity in recentActivities" :key="activity.id">
                        <div class="p-6 hover:bg-gray-50 transition duration-150">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                        :class="{
                                            'bg-blue-100': activity.type === 'order',
                                            'bg-green-100': activity.type === 'shipping',
                                            'bg-yellow-100': activity.type === 'payment',
                                            'bg-purple-100': activity.type === 'status'
                                         }">
                                        <svg class="w-5 h-5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                            <path x-show="activity.type === 'order'"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />

                                            <path x-show="activity.type === 'shipping'"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />

                                            <path x-show="activity.type === 'payment'"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                                            <path x-show="activity.type === 'status'"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm text-gray-900">
                                        <span x-text="activity.message"></span>
                                        <span class="font-medium" x-text="`#${activity.order_code}`"></span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1" x-text="`${formatDate(activity.created_at, true)} • ${activity.time_ago}`"></p>
                                </div>
                                <button @click="viewOrderDetail(activity.order_id)"
                                    class="ml-4 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <!-- <div class="mt-12">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Butuh Bantuan dengan Pesanan Anda?</h3>
                        <p class="text-blue-100">Tim customer service kami siap membantu 24/7</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-4">
                        <button @click="openLiveChat"
                                class="px-6 py-3 bg-white text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition duration-200">
                            Chat Sekarang
                        </button>
                        <button @click="callCustomerService"
                                class="px-6 py-3 border-2 border-white text-white font-medium rounded-lg hover:bg-white hover:text-blue-600 transition duration-200">
                            Telepon Kami
                        </button>
                    </div>
                </div>
            </div>
        </div> -->
    </div>

    <!-- Order Detail Modal -->
    <div x-show="showOrderModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Detail Pesanan #<span x-text="selectedOrder?.order_code"></span>
                            </h3>

                            <!-- Order Items Table -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="item in selectedOrder?.items" :key="item.id">
                                            <tr>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <img :src="item.cover_image"
                                                            :alt="item.title"
                                                            class="h-16 w-12 object-cover rounded mr-4">
                                                        <div>
                                                            <div class="font-medium text-gray-900" x-text="item.title"></div>
                                                            <div class="text-sm text-gray-500" x-text="item.author"></div>
                                                            <div class="text-xs text-gray-400" x-text="`ISBN: ${item.isbn}`"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4" x-text="formatCurrency(item.price)"></td>
                                                <td class="px-6 py-4" x-text="item.quantity"></td>
                                                <td class="px-6 py-4 font-medium" x-text="formatCurrency(item.price * item.quantity)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="showOrderModal = false"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function orderStatus() {
        return {
            // State
            isLoading: true,
            orders: [],
            filteredOrders: [],
            searchQuery: '',
            filterStatus: '',
            filterPeriod: '',
            expandedOrders: [],
            showOrderModal: false,
            selectedOrder: null,
            recentActivities: [],

            // Stats
            stats: {
                total_orders: 0,
                pending: 0,
                shipping: 0,
                completed: 0
            },

            // Pagination
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 0,
                from: 0,
                to: 0,
                links: []
            },

            // Computed
            get hasFilters() {
                return this.searchQuery || this.filterStatus || this.filterPeriod !== 'all';
            },

            // Methods
            init() {
                this.loadOrders();
                this.loadRecentActivities();

                // Auto-refresh setiap 30 detik untuk update status
                setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        this.loadOrders(true); // Silent refresh
                    }
                }, 30000);
            },

            loadOrders(silent = false) {
                if (!silent) this.isLoading = true;

                // Simulasi API call
                setTimeout(() => {
                    // Contoh data pesanan
                    this.orders = [{
                            id: 1,
                            order_code: 'ORD2023123456',
                            status: 'shipping',
                            created_at: '2024-01-15T10:30:00',
                            total_amount: 285000,
                            items_count: 3,
                            seller_id: 1,
                            is_urgent: false,

                            shipping_address: {
                                recipient_name: 'Budi Santoso',
                                phone: '081234567890',
                                address: 'Jl. Merdeka No. 123, RT 01/RW 02',
                                city: 'Jakarta Pusat',
                                province: 'DKI Jakarta',
                                postal_code: '10110'
                            },

                            shipping: {
                                courier: 'JNE',
                                tracking_number: 'JNE1234567890',
                                estimated_delivery: '19-21 Jan 2024'
                            },

                            preview_items: [{
                                    id: 1,
                                    title: 'Laut Bercerita',
                                    author: 'Leila S. Chudori',
                                    cover_image: 'https://via.placeholder.com/96x128?text=B1',
                                    quantity: 2
                                },
                                {
                                    id: 2,
                                    title: 'Pulang',
                                    author: 'Leila S. Chudori',
                                    cover_image: 'https://via.placeholder.com/96x128?text=B2',
                                    quantity: 1
                                },
                                {
                                    id: 3,
                                    title: 'Ronggeng Dukuh Paruk',
                                    author: 'Ahmad Tohari',
                                    cover_image: 'https://via.placeholder.com/96x128?text=B3',
                                    quantity: 1
                                }
                            ],

                            items: [{
                                    id: 1,
                                    title: 'Laut Bercerita',
                                    author: 'Leila S. Chudori',
                                    isbn: '9786024246945',
                                    price: 95000,
                                    quantity: 2,
                                    cover_image: 'https://via.placeholder.com/96x128?text=B1'
                                },
                                {
                                    id: 2,
                                    title: 'Pulang',
                                    author: 'Leila S. Chudori',
                                    isbn: '9786024266660',
                                    price: 85000,
                                    quantity: 1,
                                    cover_image: 'https://via.placeholder.com/96x128?text=B2'
                                }
                            ],

                            timeline: [{
                                    status: 'Pesanan Dibuat',
                                    description: 'Pesanan berhasil dibuat',
                                    date: '2024-01-15T10:30:00',
                                    time: '10:30',
                                    completed: true
                                },
                                {
                                    status: 'Pembayaran Dikonfirmasi',
                                    description: 'Pembayaran berhasil diverifikasi',
                                    date: '2024-01-15T10:35:00',
                                    time: '10:35',
                                    completed: true
                                },
                                {
                                    status: 'Pesanan Diproses',
                                    description: 'Pesanan sedang disiapkan',
                                    date: '2024-01-15T14:20:00',
                                    time: '14:20',
                                    completed: true
                                },
                                {
                                    status: 'Pesanan Dikirim',
                                    description: 'Pesanan telah dikirim',
                                    date: '2024-01-16T09:15:00',
                                    time: '09:15',
                                    completed: true
                                },
                                {
                                    status: 'Dalam Pengiriman',
                                    description: 'Pesanan sedang dalam perjalanan',
                                    date: null,
                                    time: null,
                                    completed: false
                                }
                            ]
                        },
                        {
                            id: 2,
                            order_code: 'ORD2023123457',
                            status: 'processing',
                            created_at: '2024-01-18T14:20:00',
                            total_amount: 150000,
                            items_count: 1,
                            seller_id: 2,
                            is_urgent: true,

                            shipping_address: {
                                recipient_name: 'Budi Santoso',
                                phone: '081234567890',
                                address: 'Jl. Sudirman Kav. 1, Gedung Plaza',
                                city: 'Jakarta Selatan',
                                province: 'DKI Jakarta',
                                postal_code: '12190'
                            },

                            shipping: null,

                            preview_items: [{
                                id: 4,
                                title: 'Bumi Manusia',
                                author: 'Pramoedya Ananta Toer',
                                cover_image: 'https://via.placeholder.com/96x128?text=B4',
                                quantity: 1
                            }],

                            timeline: [{
                                    status: 'Pesanan Dibuat',
                                    description: 'Pesanan berhasil dibuat',
                                    date: '2024-01-18T14:20:00',
                                    time: '14:20',
                                    completed: true
                                },
                                {
                                    status: 'Pembayaran Dikonfirmasi',
                                    description: 'Pembayaran berhasil diverifikasi',
                                    date: '2024-01-18T14:25:00',
                                    time: '14:25',
                                    completed: true
                                },
                                {
                                    status: 'Pesanan Diproses',
                                    description: 'Pesanan sedang disiapkan',
                                    date: null,
                                    time: null,
                                    completed: false
                                }
                            ]
                        },
                        {
                            id: 3,
                            order_code: 'ORD2023123458',
                            status: 'completed',
                            created_at: '2024-01-10T09:15:00',
                            total_amount: 420000,
                            items_count: 4,
                            seller_id: 3,
                            is_urgent: false,

                            preview_items: [{
                                    id: 5,
                                    title: 'Negeri 5 Menara',
                                    author: 'Ahmad Fuadi',
                                    cover_image: 'https://via.placeholder.com/96x128?text=B5',
                                    quantity: 2
                                },
                                {
                                    id: 6,
                                    title: 'Sang Pemimpi',
                                    author: 'Andrea Hirata',
                                    cover_image: 'https://via.placeholder.com/96x128?text=B6',
                                    quantity: 2
                                }
                            ],

                            timeline: [{
                                    status: 'Pesanan Dibuat',
                                    description: 'Pesanan berhasil dibuat',
                                    date: '2024-01-10T09:15:00',
                                    time: '09:15',
                                    completed: true
                                },
                                {
                                    status: 'Pembayaran Dikonfirmasi',
                                    description: 'Pembayaran berhasil diverifikasi',
                                    date: '2024-01-10T09:20:00',
                                    time: '09:20',
                                    completed: true
                                },
                                {
                                    status: 'Pesanan Diproses',
                                    description: 'Pesanan sedang disiapkan',
                                    date: '2024-01-10T14:30:00',
                                    time: '14:30',
                                    completed: true
                                },
                                {
                                    status: 'Pesanan Dikirim',
                                    description: 'Pesanan telah dikirim',
                                    date: '2024-01-11T10:45:00',
                                    time: '10:45',
                                    completed: true
                                },
                                {
                                    status: 'Pesanan Selesai',
                                    description: 'Pesanan telah diterima',
                                    date: '2024-01-13T15:20:00',
                                    time: '15:20',
                                    completed: true
                                }
                            ]
                        }
                    ];

                    // Update stats
                    this.updateStats();

                    // Apply filters
                    this.filterOrders();

                    // Setup pagination
                    this.setupPagination();

                    this.isLoading = false;
                }, silent ? 500 : 1000);
            },

            loadRecentActivities() {
                // Contoh data aktivitas terbaru
                this.recentActivities = [{
                        id: 1,
                        type: 'shipping',
                        message: 'Pesanan telah dikirim',
                        order_code: 'ORD2023123456',
                        order_id: 1,
                        created_at: '2024-01-16T09:15:00',
                        time_ago: '2 hari yang lalu'
                    },
                    {
                        id: 2,
                        type: 'status',
                        message: 'Status pesanan diperbarui menjadi Diproses',
                        order_code: 'ORD2023123457',
                        order_id: 2,
                        created_at: '2024-01-18T14:25:00',
                        time_ago: 'Baru saja'
                    },
                    {
                        id: 3,
                        type: 'payment',
                        message: 'Pembayaran berhasil dikonfirmasi',
                        order_code: 'ORD2023123457',
                        order_id: 2,
                        created_at: '2024-01-18T14:20:00',
                        time_ago: '1 jam yang lalu'
                    },
                    {
                        id: 4,
                        type: 'order',
                        message: 'Pesanan baru dibuat',
                        order_code: 'ORD2023123457',
                        order_id: 2,
                        created_at: '2024-01-18T14:15:00',
                        time_ago: '2 jam yang lalu'
                    }
                ];
            },

            updateStats() {
                this.stats = {
                    total_orders: this.orders.length,
                    pending: this.orders.filter(o => o.status === 'pending' || o.status === 'processing').length,
                    shipping: this.orders.filter(o => o.status === 'shipping').length,
                    completed: this.orders.filter(o => o.status === 'completed').length
                };
            },

            filterOrders() {
                let filtered = [...this.orders];

                // Filter by status
                if (this.filterStatus) {
                    filtered = filtered.filter(order => order.status === this.filterStatus);
                }

                // Filter by period
                if (this.filterPeriod && this.filterPeriod !== 'all') {
                    const now = new Date();
                    let startDate = new Date();

                    switch (this.filterPeriod) {
                        case 'today':
                            startDate.setHours(0, 0, 0, 0);
                            break;
                        case 'week':
                            startDate.setDate(now.getDate() - 7);
                            break;
                        case 'month':
                            startDate.setMonth(now.getMonth() - 1);
                            break;
                        case 'last3months':
                            startDate.setMonth(now.getMonth() - 3);
                            break;
                    }

                    filtered = filtered.filter(order => {
                        const orderDate = new Date(order.created_at);
                        return orderDate >= startDate;
                    });
                }

                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(order => {
                        return order.order_code.toLowerCase().includes(query) ||
                            order.preview_items.some(item =>
                                item.title.toLowerCase().includes(query) ||
                                item.author.toLowerCase().includes(query)
                            );
                    });
                }

                this.filteredOrders = filtered;
                this.setupPagination();
            },

            searchOrders() {
                this.filterOrders();
            },

            resetFilters() {
                this.searchQuery = '';
                this.filterStatus = '';
                this.filterPeriod = 'all';
                this.filterOrders();
            },

            setupPagination() {
                const total = this.filteredOrders.length;
                const perPage = 5; // Untuk demo, tampilkan sedikit
                const pages = Math.ceil(total / perPage);

                // Hitung item yang ditampilkan
                const from = (this.pagination.current_page - 1) * perPage + 1;
                const to = Math.min(this.pagination.current_page * perPage, total);

                // Buat links pagination
                const links = [];
                for (let i = 1; i <= pages; i++) {
                    links.push({
                        label: i.toString(),
                        url: '#',
                        active: i === this.pagination.current_page
                    });
                }

                this.pagination = {
                    current_page: 1,
                    last_page: pages,
                    per_page: perPage,
                    total: total,
                    from: from,
                    to: to,
                    links: links
                };
            },

            prevPage() {
                if (this.pagination.current_page > 1) {
                    this.pagination.current_page--;
                    // Dalam implementasi real, ini akan fetch data baru dari API
                }
            },

            nextPage() {
                if (this.pagination.current_page < this.pagination.last_page) {
                    this.pagination.current_page++;
                    // Dalam implementasi real, ini akan fetch data baru dari API
                }
            },

            goToPage(pageUrl) {
                // Implementasi untuk pindah ke page tertentu
                // Dalam real app, ini akan fetch data dari API
            },

            toggleOrderDetails(orderId) {
                const index = this.expandedOrders.indexOf(orderId);
                if (index > -1) {
                    this.expandedOrders.splice(index, 1);
                } else {
                    this.expandedOrders.push(orderId);
                }
            },

            getStatusClass(status) {
                const classes = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'paid': 'bg-blue-100 text-blue-800',
                    'processing': 'bg-purple-100 text-purple-800',
                    'shipping': 'bg-indigo-100 text-indigo-800',
                    'completed': 'bg-green-100 text-green-800',
                    'cancelled': 'bg-red-100 text-red-800'
                };
                return classes[status] || 'bg-gray-100 text-gray-800';
            },

            getStatusText(status) {
                const texts = {
                    'pending': 'Menunggu Pembayaran',
                    'paid': 'Sudah Dibayar',
                    'processing': 'Diproses',
                    'shipping': 'Dalam Pengiriman',
                    'completed': 'Selesai',
                    'cancelled': 'Dibatalkan'
                };
                return texts[status] || status;
            },

            openChat(sellerId, orderCode) {
                const message = `Halo, saya ingin bertanya tentang pesanan #${orderCode}`;
                // Redirect ke halaman chat atau buka modal chat
                window.location.href = `/chat/seller/${sellerId}?message=${encodeURIComponent(message)}`;
            },

            trackShipping(trackingNumber, courier) {
                // Redirect ke halaman tracking kurir
                const courierUrls = {
                    'JNE': `https://www.jne.co.id/tracking?q=${trackingNumber}`,
                    'TIKI': `https://www.tiki.id/tracking?q=${trackingNumber}`,
                    'Pos Indonesia': `https://www.posindonesia.co.id/tracking?q=${trackingNumber}`,
                    'SiCepat': `https://www.sicepat.com/tracking?q=${trackingNumber}`,
                    'J&T': `https://www.jet.co.id/tracking?q=${trackingNumber}`
                };

                const url = courierUrls[courier] || `https://cekresi.com/?courier=${courier.toLowerCase()}&awb=${trackingNumber}`;
                window.open(url, '_blank');
            },

            viewOrderDetail(orderId) {
                const order = this.orders.find(o => o.id === orderId);
                if (order) {
                    this.selectedOrder = order;
                    this.showOrderModal = true;
                }
            },

            cancelOrder(orderId) {
                if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
                    // Simulasi API call
                    setTimeout(() => {
                        const order = this.orders.find(o => o.id === orderId);
                        if (order) {
                            order.status = 'cancelled';
                            this.updateStats();
                            this.filterOrders();
                            this.showToastMessage('Pesanan berhasil dibatalkan', 'success');
                        }
                    }, 1000);
                }
            },

            requestReturn(orderId) {
                alert('Fitur pengembalian akan segera tersedia. Silakan hubungi customer service untuk bantuan.');
            },

            downloadInvoice(orderCode) {
                // Simulasi download invoice
                const invoiceContent = `Invoice #${orderCode}`;
                const blob = new Blob([invoiceContent], {
                    type: 'text/plain'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `invoice-${orderCode}.txt`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                this.showToastMessage('Invoice berhasil diunduh', 'success');
            },

            reorderItems(order) {
                // Simpan items ke cart
                const cartItems = order.items.map(item => ({
                    id: item.id,
                    title: item.title,
                    author: item.author,
                    price: item.price,
                    quantity: item.quantity,
                    cover_image: item.cover_image
                }));

                localStorage.setItem('reorderCart', JSON.stringify(cartItems));
                this.showToastMessage('Item telah ditambahkan ke keranjang', 'success');

                // Redirect ke cart
                setTimeout(() => {
                    window.location.href = '/cart';
                }, 1500);
            },

            openLiveChat() {
                alert('Live chat akan segera dibuka');
            },

            callCustomerService() {
                window.location.href = 'tel:+6281234567890';
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            },

            formatDate(dateString, short = false) {
                if (!dateString) return '-';
                const date = new Date(dateString);

                if (short) {
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                }

                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            showToastMessage(message, type = 'success') {
                // Implementasi toast notification
                const toast = document.createElement('div');
                toast.className = `fixed bottom-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-3 rounded-lg shadow-lg`;
                toast.textContent = message;
                toast.style.zIndex = '1000';

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.transform = 'translateY(100px)';
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 3000);
            }
        }
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }

    /* Smooth collapse transition */
    [x-collapse] {
        transition: all 0.3s ease;
    }

    /* Custom scrollbar for preview items */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Timeline connector */
    .timeline-connector::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e5e7eb;
    }

    .timeline-connector:last-child::before {
        display: none;
    }
</style>
@endsection