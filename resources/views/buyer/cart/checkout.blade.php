@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div x-data="checkout()" x-init="initCheckout()" class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="#" class="text-sm font-medium text-gray-700 hover:text-blue-600">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600">Keranjang</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-medium text-gray-500">Checkout</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Left Column - Form Pengiriman & Pembayaran -->
            <div class="lg:col-span-8">
                <div class="space-y-8">
                    <!-- Informasi Pengiriman -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Informasi Pengiriman</h2>
                            <button @click="openAddressModal" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                + Tambah Alamat Baru
                            </button>
                        </div>

                        <!-- Daftar Alamat -->
                        <div class="space-y-4 mb-6">
                            <template x-for="address in addresses" :key="address.id">
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                    <input 
                                        type="radio" 
                                        name="selected_address" 
                                        x-model="selectedAddressId"
                                        :value="address.id"
                                        class="mt-1 text-blue-600 focus:ring-blue-500"
                                    >
                                    <div class="ml-3 flex-1">
                                        <div class="flex justify-between">
                                            <span class="font-medium text-gray-900" x-text="address.name"></span>
                                            <span class="text-sm font-medium px-2 py-1 rounded" 
                                                  :class="address.is_primary ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
                                                  x-text="address.is_primary ? 'Utama' : ''">
                                            </span>
                                        </div>
                                        <p class="mt-1 text-gray-600" x-text="address.full_address"></p>
                                        <p class="mt-1 text-gray-600" x-text="`${address.city}, ${address.province} ${address.postal_code}`"></p>
                                        <p class="mt-1 text-gray-600" x-text="address.phone"></p>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <!-- Kurir Pengiriman -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Kurir</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="courier in couriers" :key="courier.id">
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input 
                                            type="radio" 
                                            name="selected_courier" 
                                            x-model="selectedCourier"
                                            :value="courier.id"
                                            class="text-blue-600 focus:ring-blue-500"
                                        >
                                        <div class="ml-3 flex-1">
                                            <div class="flex justify-between">
                                                <span class="font-medium text-gray-900" x-text="courier.name"></span>
                                                <span class="font-bold text-blue-600" 
                                                      x-text="formatCurrency(courier.cost)">
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1" x-text="courier.estimated_time"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Catatan Pesanan -->
                        <div>
                            <label for="order_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Pesanan (Opsional)
                            </label>
                            <textarea 
                                id="order_notes"
                                x-model="orderNotes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: Tolong dibungkus dengan rapi, atau instruksi khusus lainnya"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <!-- <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Metode Pembayaran</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-3">Transfer Bank</h3>
                                <div class="space-y-3">
                                    <template x-for="bank in banks" :key="bank.id">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input 
                                                type="radio" 
                                                name="payment_method" 
                                                x-model="selectedPayment"
                                                :value="'bank_' + bank.id"
                                                class="text-blue-600 focus:ring-blue-500"
                                            >
                                            <div class="ml-3">
                                                <span class="font-medium text-gray-900" x-text="bank.name"></span>
                                                <p class="text-sm text-gray-500" x-text="bank.account_number"></p>
                                                <p class="text-sm text-gray-500" x-text="bank.account_name"></p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-3">E-Wallet</h3>
                                <div class="space-y-3">
                                    <template x-for="wallet in ewallets" :key="wallet.id">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input 
                                                type="radio" 
                                                name="payment_method" 
                                                x-model="selectedPayment"
                                                :value="'ewallet_' + wallet.id"
                                                class="text-blue-600 focus:ring-blue-500"
                                            >
                                            <img :src="wallet.logo" :alt="wallet.name" class="ml-3 h-8 w-auto">
                                            <span class="ml-3 font-medium text-gray-900" x-text="wallet.name"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-3">QRIS</h3>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        x-model="selectedPayment"
                                        value="qris"
                                        class="text-blue-600 focus:ring-blue-500"
                                    >
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-900">QRIS</span>
                                        <p class="text-sm text-gray-500">Scan kode QR untuk pembayaran</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div x-show="selectedPayment.startsWith('bank_')" x-cloak class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                Setelah menekan tombol "Bayar Sekarang", Anda akan mendapatkan instruksi pembayaran melalui Virtual Account.
                                Silakan transfer sesuai nominal yang tertera.
                            </p>
                        </div>
                    </div> -->
                </div>
            </div>

            <!-- Right Column - Ringkasan Pesanan -->
            <div class="lg:col-span-4 mt-8 lg:mt-0">
                <div class="bg-white rounded-xl shadow p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>
                    
                    <!-- Daftar Produk -->
                    <div class="space-y-4 mb-6">
                        <template x-for="item in cartItems" :key="item.id">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <img :src="item.cover_image" :alt="item.title" class="h-16 w-16 object-cover rounded">
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-sm font-medium text-gray-900" x-text="item.title"></h4>
                                    <p class="text-sm text-gray-500" x-text="item.author"></p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-sm text-gray-600" x-text="`${item.quantity} x ${formatCurrency(item.price)}`"></span>
                                        <span class="font-medium text-gray-900" x-text="formatCurrency(item.subtotal)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Rincian Harga -->
                    <div class="space-y-3 border-t border-b py-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium" x-text="formatCurrency(summary.subtotal)"></span>
                        </div>
                        <!-- <div class="flex justify-between">
                            <span class="text-gray-600">Diskon</span>
                            <span class="font-medium text-green-600" x-text="`-${formatCurrency(summary.discount)}`"></span>
                        </div> -->
                        <div class="flex justify-between">
                            <span class="text-gray-600">Biaya Pengiriman</span>
                            <span class="font-medium" x-text="formatCurrency(summary.shipping_cost)"></span>
                        </div>
                        <div x-show="summary.voucher_discount > 0" class="flex justify-between">
                            <span class="text-gray-600">Voucher</span>
                            <span class="font-medium text-green-600" x-text="`-${formatCurrency(summary.voucher_discount)}`"></span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="py-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-blue-600" x-text="formatCurrency(summary.total)"></span>
                        </div>
                        <p class="text-sm text-gray-500">
                            Sudah termasuk PPN dan biaya layanan
                        </p>
                    </div>

                    <!-- Voucher Input -->
                    <!-- <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Voucher</label>
                        <div class="flex">
                            <input 
                                type="text" 
                                x-model="voucherCode"
                                placeholder="Masukkan kode voucher"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500"
                                :disabled="isApplyingVoucher"
                            >
                            <button 
                                @click="applyVoucher"
                                :disabled="!voucherCode || isApplyingVoucher"
                                :class="!voucherCode || isApplyingVoucher ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-lg transition duration-200"
                            >
                                <span x-text="isApplyingVoucher ? '...' : 'Pakai'"></span>
                            </button>
                        </div>
                        <p x-show="voucherError" x-cloak class="mt-2 text-sm text-red-600" x-text="voucherError"></p>
                        <p x-show="voucherSuccess" x-cloak class="mt-2 text-sm text-green-600" x-text="voucherSuccess"></p>
                    </div> -->

                    <!-- Syarat & Ketentuan -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                x-model="agreeToTerms"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <span class="ml-2 text-sm text-gray-600">
                                Saya setuju dengan 
                                <a href="#" class="text-blue-600 hover:text-blue-800">Syarat & Ketentuan</a>
                                dan 
                                <a href="#" class="text-blue-600 hover:text-blue-800">Kebijakan Privasi</a>
                            </span>
                        </label>
                    </div>

                    <!-- Tombol Bayar -->
                    <button 
                        @click="processPayment"
                        :disabled="!isCheckoutReady || isProcessing"
                        :class="!isCheckoutReady || isProcessing ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                        class="w-full bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition duration-200 flex items-center justify-center"
                    >
                        <svg x-show="isProcessing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isProcessing ? 'Memproses Pembayaran...' : `Bayar ${formatCurrency(summary.total)}`"></span>
                    </button>

                    <!-- Info Keamanan -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center text-sm text-gray-500">
                            Setelah menekan tombol "Bayar Sekarang", Anda akan mendapatkan instruksi pembayaran melalui Virtual Account.
                                Silakan transfer sesuai nominal yang tertera.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Alamat -->
    <div x-show="showAddressModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Tambah Alamat Baru
                            </h3>
                            
                            <form @submit.prevent="saveNewAddress" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                                        <input type="text" x-model="newAddress.name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                        <input type="tel" x-model="newAddress.phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                    <textarea x-model="newAddress.full_address" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                        <input type="text" x-model="newAddress.city" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                                        <input type="text" x-model="newAddress.province" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                                        <input type="text" x-model="newAddress.postal_code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="flex items-end">
                                        <label class="flex items-center">
                                            <input type="checkbox" x-model="newAddress.is_primary" class="h-4 w-4 text-blue-600">
                                            <span class="ml-2 text-sm text-gray-600">Jadikan alamat utama</span>
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="saveNewAddress" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Alamat
                    </button>
                    <button @click="showAddressModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkout() {
    return {
        // State
        cartItems: [],
        addresses: [],
        couriers: [],
        banks: [],
        ewallets: [],
        selectedAddressId: null,
        selectedCourier: 'jne',
        selectedPayment: 'bank_1',
        orderNotes: '',
        voucherCode: '',
        agreeToTerms: false,
        isProcessing: false,
        isApplyingVoucher: false,
        voucherError: '',
        voucherSuccess: '',
        showAddressModal: false,
        
        // New address form
        newAddress: {
            name: '',
            phone: '',
            full_address: '',
            city: '',
            province: '',
            postal_code: '',
            is_primary: false
        },
        
        // Summary
        summary: {
            subtotal: 0,
            discount: 0,
            shipping_cost: 0,
            voucher_discount: 0,
            total: 0
        },
        
        // Computed
        get isCheckoutReady() {
            return this.selectedAddressId && this.selectedCourier && this.selectedPayment && this.agreeToTerms;
        },
        
        // Methods
        initCheckout() {
            this.loadData();
            this.calculateSummary();
        },
        
        loadData() {
            // Load cart items (contoh data)
            this.cartItems = [
                {
                    id: 1,
                    title: "Laut Bercerita",
                    author: "Leila S. Chudori",
                    price: 95000,
                    quantity: 2,
                    subtotal: 190000,
                    cover_image: "https://via.placeholder.com/64x64?text=Buku+1"
                },
                {
                    id: 2,
                    title: "Pulang",
                    author: "Leila S. Chudori",
                    price: 85000,
                    quantity: 1,
                    subtotal: 85000,
                    cover_image: "https://via.placeholder.com/64x64?text=Buku+2"
                }
            ];
            
            // Load addresses
            this.addresses = [
                {
                    id: 1,
                    name: "Budi Santoso",
                    phone: "081234567890",
                    full_address: "Jl. Merdeka No. 123, RT 01/RW 02",
                    city: "Jakarta Pusat",
                    province: "DKI Jakarta",
                    postal_code: "10110",
                    is_primary: true
                },
                {
                    id: 2,
                    name: "Budi Santoso (Kantor)",
                    phone: "081234567891",
                    full_address: "Jl. Sudirman Kav. 1, Gedung Plaza",
                    city: "Jakarta Selatan",
                    province: "DKI Jakarta",
                    postal_code: "12190",
                    is_primary: false
                }
            ];
            
            // Load couriers
            this.couriers = [
                {
                    id: 'jne',
                    name: 'JNE REG',
                    cost: 15000,
                    estimated_time: '2-3 hari kerja'
                },
                {
                    id: 'tiki',
                    name: 'TIKI',
                    cost: 20000,
                    estimated_time: '1-2 hari kerja'
                },
                {
                    id: 'pos',
                    name: 'Pos Indonesia',
                    cost: 10000,
                    estimated_time: '3-5 hari kerja'
                },
                {
                    id: 'gosend',
                    name: 'GoSend Instant',
                    cost: 25000,
                    estimated_time: '1-3 jam'
                }
            ];
            
            // Load banks
            this.banks = [
                {
                    id: 1,
                    name: 'BCA',
                    account_number: '1234567890',
                    account_name: 'BOOKSTORE INDONESIA'
                },
                {
                    id: 2,
                    name: 'Mandiri',
                    account_number: '0987654321',
                    account_name: 'BOOKSTORE INDONESIA'
                },
                {
                    id: 3,
                    name: 'BNI',
                    account_number: '1122334455',
                    account_name: 'BOOKSTORE INDONESIA'
                }
            ];
            
            // Load e-wallets
            this.ewallets = [
                {
                    id: 1,
                    name: 'GoPay',
                    logo: 'https://via.placeholder.com/40x40/00AA13/FFFFFF?text=GP'
                },
                {
                    id: 2,
                    name: 'OVO',
                    logo: 'https://via.placeholder.com/40x40/4B2E9A/FFFFFF?text=OV'
                },
                {
                    id: 3,
                    name: 'Dana',
                    logo: 'https://via.placeholder.com/40x40/108EE9/FFFFFF?text=DN'
                }
            ];
            
            // Set default selections
            this.selectedAddressId = this.addresses[0]?.id || null;
        },
        
        calculateSummary() {
            const subtotal = this.cartItems.reduce((sum, item) => sum + item.subtotal, 0);
            const shippingCost = this.couriers.find(c => c.id === this.selectedCourier)?.cost || 0;
            
            this.summary = {
                subtotal: subtotal,
                discount: 10000, // Contoh diskon
                shipping_cost: shippingCost,
                voucher_discount: this.summary.voucher_discount,
                total: subtotal - 10000 + shippingCost - this.summary.voucher_discount
            };
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },
        
        openAddressModal() {
            this.showAddressModal = true;
            // Reset form
            this.newAddress = {
                name: '',
                phone: '',
                full_address: '',
                city: '',
                province: '',
                postal_code: '',
                is_primary: false
            };
        },
        
        saveNewAddress() {
            // Simulasi save address
            const newId = this.addresses.length + 1;
            this.addresses.push({
                id: newId,
                ...this.newAddress
            });
            
            // If marked as primary, unset others
            if (this.newAddress.is_primary) {
                this.addresses.forEach(addr => {
                    if (addr.id !== newId) addr.is_primary = false;
                });
            }
            
            this.selectedAddressId = newId;
            this.showAddressModal = false;
            
            // Show success message
            this.showToast('Alamat berhasil disimpan');
        },
        
        applyVoucher() {
            this.isApplyingVoucher = true;
            this.voucherError = '';
            this.voucherSuccess = '';
            
            // Simulasi API call
            setTimeout(() => {
                if (this.voucherCode.toUpperCase() === 'DISKON10') {
                    this.summary.voucher_discount = 10000;
                    this.calculateSummary();
                    this.voucherSuccess = 'Voucher berhasil diterapkan! Diskon Rp 10.000';
                } else if (this.voucherCode.toUpperCase() === 'WELCOME15') {
                    this.summary.voucher_discount = 15000;
                    this.calculateSummary();
                    this.voucherSuccess = 'Voucher berhasil diterapkan! Diskon Rp 15.000';
                } else {
                    this.voucherError = 'Kode voucher tidak valid atau sudah kadaluarsa';
                }
                this.isApplyingVoucher = false;
            }, 1000);
        },
        
        processPayment() {
            if (!this.isCheckoutReady) return;
            
            this.isProcessing = true;
            
            // Simulasi proses pembayaran
            setTimeout(() => {
                // Redirect ke halaman konfirmasi pembayaran
                // window.location.href = `/payment/confirmation?order_id=${orderId}`;
                
                // Untuk demo, tampilkan alert
                alert('Pembayaran berhasil diproses! Anda akan diarahkan ke halaman konfirmasi.');
                this.isProcessing = false;
            }, 2000);
        },
        
        showToast(message) {
            // Implement toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        }
    }
}
</script>

<style>
[x-cloak] {
    display: none !important;
}

input[type="radio"]:checked + div {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

/* Styling untuk sticky summary */
.sticky {
    position: sticky;
    top: 1.5rem;
}
</style>
@endsection