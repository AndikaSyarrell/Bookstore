{{-- resources/views/seller/orders/index.blade.php --}}
@extends('layouts.app')
@section('title','Pre-order')
@section('content')
<div x-data="sellerOrders()" class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Pesanan Saya</h1>
        <p class="text-gray-600">Kelola semua pesanan dari pembeli</p>
    </div>

    {{-- Filter & Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <button
            @click="filterStatus = 'all'"
            :class="filterStatus === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="p-4 rounded-lg border-2 transition"
            :class="filterStatus === 'all' ? 'border-blue-600' : 'border-gray-200'">
            <div class="text-2xl font-bold" x-text="getStatusCount('all')"></div>
            <div class="text-sm">Semua Pesanan</div>
        </button>

        <button
            @click="filterStatus = 'pending'"
            :class="filterStatus === 'pending' ? 'bg-orange-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="p-4 rounded-lg border-2 transition"
            :class="filterStatus === 'pending' ? 'border-orange-600' : 'border-gray-200'">
            <div class="text-2xl font-bold" x-text="getStatusCount('pending')"></div>
            <div class="text-sm">Pending</div>
        </button>

        <button
            @click="filterStatus = 'processed'"
            :class="filterStatus === 'processed' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="p-4 rounded-lg border-2 transition"
            :class="filterStatus === 'processed' ? 'border-blue-600' : 'border-gray-200'">
            <div class="text-2xl font-bold" x-text="getStatusCount('processed')"></div>
            <div class="text-sm">Diproses</div>
        </button>

        <button
            @click="filterStatus = 'on_shipment'"
            :class="filterStatus === 'on_shipment' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="p-4 rounded-lg border-2 transition"
            :class="filterStatus === 'on_shipment' ? 'border-purple-600' : 'border-gray-200'">
            <div class="text-2xl font-bold" x-text="getStatusCount('on_shipment')"></div>
            <div class="text-sm">Dikirim</div>
        </button>

        <button
            @click="filterStatus = 'finished'"
            :class="filterStatus === 'finished' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="p-4 rounded-lg border-2 transition"
            :class="filterStatus === 'finished' ? 'border-green-600' : 'border-gray-200'">
            <div class="text-2xl font-bold" x-text="getStatusCount('finished')"></div>
            <div class="text-sm">Selesai</div>
        </button>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="filterOrders()"
                    placeholder="Cari nomor pesanan atau nama pembeli..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select
                x-model="sortBy"
                @change="sortOrders()"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="amount_high">Nominal Tertinggi</option>
                <option value="amount_low">Nominal Terendah</option>
            </select>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembeli</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="order in filteredOrders" :key="order.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="order.product_image" class="w-12 h-12 object-cover rounded">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900" x-text="order.order_number"></div>
                                        <div class="text-sm text-gray-500" x-text="order.product_name"></div>
                                        <div class="text-xs text-gray-400">Qty: <span x-text="order.quantity"></span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900" x-text="order.buyer_name"></div>
                                <div class="text-sm text-gray-500" x-text="order.buyer_email"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900" x-text="formatDate(order.created_at)"></div>
                                <div class="text-xs text-gray-500" x-text="formatTime(order.created_at)"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900" x-text="formatPrice(order.total)"></div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                        'bg-red-100 text-red-800': order.status === 'declined',
                                        'bg-blue-100 text-blue-800': order.status === 'processed',
                                        'bg-purple-100 text-purple-800': order.status === 'on_shipment',
                                        'bg-green-100 text-green-800': order.status === 'finished'
                                    }"
                                    x-text="getStatusText(order.status)"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Quick Accept (Only for pending) --}}
                                    <button
                                        x-show="order.status === 'pending'"
                                        @click="quickAccept(order.id)"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                        title="Terima Pesanan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>

                                    {{-- Quick Decline (Only for pending) --}}
                                    <button
                                        x-show="order.status === 'pending'"
                                        @click="quickDecline(order.id)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Tolak Pesanan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>

                                    {{-- View Detail --}}
                                    <button
                                        @click="viewDetail(order)"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>

                                    {{-- Remove (Only for declined or finished) --}}
                                    <button
                                        x-show="order.status === 'declined' || order.status === 'finished'"
                                        @click="removeOrder(order.id)"
                                        class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Empty State --}}
        <div x-show="filteredOrders.length === 0" class="text-center py-16">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada pesanan</h3>
            <p class="mt-2 text-gray-500">Belum ada pesanan yang masuk</p>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div
        x-show="detailModalOpen"
        @click.self="detailModalOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div
            @click.stop
            class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Detail Pesanan</h3>
                <button
                    @click="detailModalOpen = false"
                    class="p-2 hover:bg-gray-100 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-6" x-show="selectedOrder">
                {{-- Order Info --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-3">Informasi Pesanan</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nomor Pesanan</p>
                            <p class="font-medium" x-text="selectedOrder?.order_number"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Pesanan</p>
                            <p class="font-medium" x-text="formatDate(selectedOrder?.created_at)"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': selectedOrder?.status === 'pending',
                                    'bg-red-100 text-red-800': selectedOrder?.status === 'declined',
                                    'bg-blue-100 text-blue-800': selectedOrder?.status === 'processed',
                                    'bg-purple-100 text-purple-800': selectedOrder?.status === 'on_shipment',
                                    'bg-green-100 text-green-800': selectedOrder?.status === 'finished'
                                }"
                                x-text="getStatusText(selectedOrder?.status)"></span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Metode Pembayaran</p>
                            <!-- Tombol Lihat Bukti Pembayaran -->
                            <button x-show="selectedOrder?.proof && selectedOrder.proof !== 'default.jpg'"
                                @click="showPaymentProof(selectedOrder)"
                                class="text-blue-600 hover:text-blue-800 p-1 rounded-full hover:bg-blue-50 transition duration-150"
                                title="Lihat bukti pembayaran">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            <!-- Badge jika ada bukti -->
                            <span x-show="selectedOrder?.proof && selectedOrder.proof !== 'default.jpg'"
                                class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">
                                Bukti ada
                            </span>

                            <!-- Badge jika tidak ada bukti -->
                            <span x-show="selectedOrder?.proof === 'default.jpg'"
                                class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                Belum upload
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Buyer Info --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-3">Informasi Pembeli</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama:</span>
                            <span class="font-medium" x-text="selectedOrder?.buyer_name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium" x-text="selectedOrder?.buyer_email"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Telepon:</span>
                            <span class="font-medium" x-text="selectedOrder?.buyer_phone"></span>
                        </div>
                        <div class="pt-2 border-t border-gray-200">
                            <p class="text-gray-600 text-sm mb-1">Alamat Pengiriman:</p>
                            <p class="font-medium text-sm" x-text="selectedOrder?.shipping_address"></p>
                        </div>
                    </div>
                </div>

                {{-- Product Info --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-3">Detail Produk</h4>
                    <div class="flex gap-4 bg-gray-50 rounded-lg p-4">
                        <img :src="selectedOrder?.product_image" class="w-20 h-20 object-cover rounded">
                        <div class="flex-1">
                            <h5 class="font-medium text-gray-900 mb-1" x-text="selectedOrder?.product_name"></h5>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Jumlah: <span x-text="selectedOrder?.quantity"></span></span>
                                <span class="font-semibold text-gray-900" x-text="formatPrice(selectedOrder?.price)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Price Breakdown --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-3">Rincian Harga</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal Produk</span>
                            <span x-text="formatPrice(selectedOrder?.subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span x-text="formatPrice(selectedOrder?.shipping_cost)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Biaya Admin</span>
                            <span x-text="formatPrice(selectedOrder?.admin_fee)"></span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-blue-600" x-text="formatPrice(selectedOrder?.total)"></span>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div x-show="selectedOrder?.notes">
                    <h4 class="font-semibold text-gray-800 mb-2">Catatan Pembeli</h4>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700" x-text="selectedOrder?.notes"></p>
                    </div>
                </div>

                {{-- Status Actions --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-3">Update Status</h4>
                    <div class="flex gap-2">
                        <button
                            x-show="selectedOrder?.status === 'pending'"
                            @click="updateStatus(selectedOrder?.id, 'processed')"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Proses Pesanan
                        </button>
                        <button
                            x-show="selectedOrder?.status === 'processed'"
                            @click="updateStatus(selectedOrder?.id, 'on_shipment')"
                            class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Kirim Pesanan
                        </button>
                        <button
                            x-show="selectedOrder?.status === 'on_shipment'"
                            @click="updateStatus(selectedOrder?.id, 'finished')"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Selesaikan Pesanan
                        </button>
                        <button
                            x-show="selectedOrder?.status === 'pending'"
                            @click="updateStatus(selectedOrder?.id, 'declined')"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Tolak Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sederhana Bukti Pembayaran -->
    <div x-show="showProofModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div @click="showProofModal = false"
                    class="absolute inset-0 bg-black opacity-50"></div>
            </div>

            <!-- Modal panel sederhana -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- Modal header -->
                <div class="bg-white px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            Bukti Pembayaran
                        </h3>
                        <p class="text-sm text-gray-500">
                            Pesanan #<span x-text="currentProof?.order_number"></span>
                        </p>
                    </div>
                    <button @click="showProofModal = false"
                        class="text-gray-400 hover:text-gray-500 p-1 rounded-full hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal body - hanya gambar -->
                <div class="bg-white p-4">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <img :src="getProofImage(currentProof?.proof)"
                            alt="Bukti Pembayaran"
                            class="w-full h-auto max-h-96 object-contain"
                            x-on:error="handleImageError"
                            x-ref="proofImage">
                    </div>

                    <!-- Loading indicator -->
                    <div x-show="!imageLoaded"
                        class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                        <span class="ml-3 text-gray-600">Memuat gambar...</span>
                    </div>

                    <!-- Error state -->
                    <div x-show="imageError"
                        x-cloak
                        class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Gambar tidak dapat dimuat</h3>
                        <p class="mt-1 text-sm text-gray-500">Bukti pembayaran tidak tersedia atau format tidak didukung</p>
                    </div>
                </div>

                <!-- Modal footer sederhana -->
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-between">
                    <div class="text-sm text-gray-500">
                        <span x-text="currentProof?.payment_method"></span>
                        •
                        <span x-text="formatDate(currentProof?.created_at, true)"></span>
                    </div>
                    <div class="flex space-x-2">
                        <a :href="getProofImage(currentProof?.proof)"
                            download
                            class="inline-flex items-center px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            title="Download">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                        <button @click="showProofModal = false"
                            class="inline-flex items-center px-3 py-1.5 text-sm border border-transparent rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function sellerOrders() {
        return {
            orders: [{
                    id: 1,
                    order_number: 'ORD-2026-001',
                    buyer_name: 'Ahmad Rizki',
                    buyer_email: 'ahmad@example.com',
                    buyer_phone: '081234567890',
                    product_name: 'Laptop Gaming ASUS ROG',
                    product_image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop',
                    quantity: 1,
                    price: 15000000,
                    subtotal: 15000000,
                    shipping_cost: 50000,
                    admin_fee: 5000,
                    total: 15055000,
                    status: 'pending',
                    proof: 'deasdfault.jpg',
                    payment_method: 'Transfer Bank',
                    shipping_address: 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10110',
                    notes: 'Mohon dikirim sebelum tanggal 30',
                    created_at: '2026-01-20T10:30:00'
                },
                {
                    id: 2,
                    order_number: 'ORD-2026-002',
                    buyer_name: 'Siti Nurhaliza',
                    buyer_email: 'siti@example.com',
                    buyer_phone: '082345678901',
                    product_name: 'Mouse Wireless Logitech',
                    product_image: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop',
                    quantity: 2,
                    price: 250000,
                    subtotal: 500000,
                    shipping_cost: 15000,
                    admin_fee: 2000,
                    total: 517000,
                    status: 'processed',
                    proof: 'default.jpg',
                    payment_method: 'E-Wallet',
                    shipping_address: 'Jl. Gatot Subroto No. 456, Bandung, Jawa Barat 40123',
                    notes: '',
                    created_at: '2026-01-19T14:20:00'
                },
                {
                    id: 3,
                    order_number: 'ORD-2026-003',
                    buyer_name: 'Budi Santoso',
                    buyer_email: 'budi@example.com',
                    buyer_phone: '083456789012',
                    product_name: 'Headset Gaming HyperX',
                    product_image: 'https://images.unsplash.com/photo-1599669454699-248893623440?w=400&h=300&fit=crop',
                    quantity: 1,
                    price: 1200000,
                    subtotal: 1200000,
                    shipping_cost: 25000,
                    admin_fee: 3000,
                    total: 1228000,
                    status: 'on_shipment',
                    proof: 'default.jpg',
                    payment_method: 'COD',
                    shipping_address: 'Jl. Ahmad Yani No. 789, Surabaya, Jawa Timur 60234',
                    notes: 'Hubungi sebelum dikirim',
                    created_at: '2026-01-18T09:15:00'
                },
                {
                    id: 4,
                    order_number: 'ORD-2026-004',
                    buyer_name: 'Dewi Lestari',
                    buyer_email: 'dewi@example.com',
                    buyer_phone: '084567890123',
                    product_name: 'Monitor LED 24 inch',
                    product_image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop',
                    quantity: 1,
                    price: 2500000,
                    subtotal: 2500000,
                    shipping_cost: 75000,
                    admin_fee: 5000,
                    total: 2580000,
                    status: 'finished',
                    proof: 'default.jpg',
                    payment_method: 'Transfer Bank',
                    shipping_address: 'Jl. Diponegoro No. 321, Yogyakarta, DIY 55223',
                    notes: '',
                    created_at: '2026-01-15T16:45:00'
                },
                {
                    id: 5,
                    order_number: 'ORD-2026-005',
                    buyer_name: 'Eko Prasetyo',
                    buyer_email: 'eko@example.com',
                    buyer_phone: '085678901234',
                    product_name: 'Webcam HD Logitech',
                    product_image: 'https://images.unsplash.com/photo-1625605320347-78e5531e3a47?w=400&h=300&fit=crop',
                    quantity: 3,
                    price: 750000,
                    subtotal: 2250000,
                    shipping_cost: 40000,
                    admin_fee: 5000,
                    total: 2295000,
                    status: 'declined',
                    proof: 'default.jpg',
                    payment_method: 'Transfer Bank',
                    shipping_address: 'Jl. Pahlawan No. 654, Semarang, Jawa Tengah 50132',
                    notes: 'Stok tidak tersedia',
                    created_at: '2026-01-14T11:00:00'
                }
            ],
            filteredOrders: [],
            filterStatus: 'all',
            searchQuery: '',
            sortBy: 'newest',
            detailModalOpen: false,
            selectedOrder: null,

            // Modal state
            showProofModal: false,
            currentProof: null,
            imageLoaded: false,
            imageError: false,

            init() {
                this.filteredOrders = [...this.orders];
            },

            getStatusCount(status) {
                if (status === 'all') return this.orders.length;
                return this.orders.filter(o => o.status === status).length;
            },
            // Tampilkan modal bukti pembayaran
            showPaymentProof(order) {
                this.currentProof = order;
                this.imageLoaded = false;
                this.imageError = false;
                this.showProofModal = true;

                // Tunggu sebentar agar modal muncul dulu, lalu load gambar
                this.$nextTick(() => {
                    const img = this.$refs.proofImage;
                    if (img && img.complete) {
                        this.imageLoaded = true;
                    }
                });
            },

            // Dapatkan URL gambar bukti
            getProofImage(proof) {
                if (!proof || proof === 'default.jpg') {
                    return 'https://via.placeholder.com/600x800?text=Tidak+Ada+Bukti';
                }

                // Jika proof adalah URL lengkap
                if (proof.startsWith('http')) {
                    return proof;
                }

                // Jika proof hanya nama file
                return `/storage/payment-proofs/${proof}`;
            },

            // Handle error saat gambar gagal load
            handleImageError() {
                this.imageError = true;
                this.imageLoaded = true;
            },

            getStatusText(status) {
                const statusMap = {
                    'pending': 'Pending',
                    'declined': 'Ditolak',
                    'processed': 'Diproses',
                    'on_shipment': 'Dikirim',
                    'finished': 'Selesai'
                };
                return statusMap[status] || status;
            },

            filterOrders() {
                let result = [...this.orders];

                // Filter by status
                if (this.filterStatus !== 'all') {
                    result = result.filter(o => o.status === this.filterStatus);
                }

                // Search
                if (this.searchQuery) {
                    result = result.filter(o =>
                        o.order_number.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        o.buyer_name.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }

                this.filteredOrders = result;
                this.sortOrders();
            },

            sortOrders() {
                switch (this.sortBy) {
                    case 'newest':
                        this.filteredOrders.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                        break;
                    case 'oldest':
                        this.filteredOrders.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                        break;
                    case 'amount_high':
                        this.filteredOrders.sort((a, b) => b.total - a.total);
                        break;
                    case 'amount_low':
                        this.filteredOrders.sort((a, b) => a.total - b.total);
                        break;
                }
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            formatPrice(price) {
                if (price == null) return 'Rp 0';
                return 'Rp ' + price.toLocaleString('id-ID');
            },

            viewDetail(order) {
                this.selectedOrder = order;
                this.detailModalOpen = true;
            },

            async quickAccept(orderId) {
                if (!confirm('Terima pesanan ini?')) return;

                await this.updateStatus(orderId, 'processed');
            },

            async quickDecline(orderId) {
                if (!confirm('Tolak pesanan ini? Tindakan ini tidak dapat dibatalkan.')) return;

                await this.updateStatus(orderId, 'declined');
            },

            async updateStatus(orderId, newStatus) {
                try {
                    // Simulate API call
                    await new Promise(resolve => setTimeout(resolve, 500));

                    // Update local data
                    const order = this.orders.find(o => o.id === orderId);
                    if (order) {
                        order.status = newStatus;
                        if (this.selectedOrder && this.selectedOrder.id === orderId) {
                            this.selectedOrder.status = newStatus;
                        }
                    }

                    this.filterOrders();

                    // In production:
                    // const response = await fetch(`/api/orders/${orderId}/status`, {
                    //     method: 'PUT',
                    //     headers: { 'Content-Type': 'application/json' },
                    //     body: JSON.stringify({ status: newStatus })
                    // });

                    alert('Status pesanan berhasil diupdate!');
                } catch (error) {
                    alert('Gagal update status: ' + error.message);
                }
            },

            async removeOrder(orderId) {
                if (!confirm('Hapus pesanan ini dari daftar?')) return;

                try {
                    // Simulate API call
                    await new Promise(resolve => setTimeout(resolve, 500));

                    // Remove from local data
                    this.orders = this.orders.filter(o => o.id !== orderId);
                    this.filterOrders();
                    this.detailModalOpen = false;

                    // In production:
                    // await fetch(`/api/orders/${orderId}`, { method: 'DELETE' });

                    alert('Pesanan berhasil dihapus!');
                } catch (error) {
                    alert('Gagal menghapus pesanan: ' + error.message);
                }
            }
        }
    }
</script>
@endsection