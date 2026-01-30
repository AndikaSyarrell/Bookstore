@extends('layouts.app')

@section('content')
<div x-data="paymentGateway()" x-init="initPayment()" class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Lakukan Pembayaran</h1>
            <p class="text-gray-600">Selesaikan pembayaran untuk pesanan #<span x-text="orderId"></span></p>
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Status Pembayaran</h2>
                <span class="px-3 py-1 rounded-full text-sm font-medium" 
                      :class="{
                        'bg-yellow-100 text-yellow-800': paymentStatus === 'pending',
                        'bg-blue-100 text-blue-800': paymentStatus === 'processing',
                        'bg-green-100 text-green-800': paymentStatus === 'completed',
                        'bg-red-100 text-red-800': paymentStatus === 'failed'
                      }"
                      x-text="paymentStatusText">
                </span>
            </div>

            <div class="relative">
                <!-- Timeline Progress -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             :class="{
                                'bg-blue-600 text-white': paymentStatus !== 'pending',
                                'bg-gray-200 text-gray-600': paymentStatus === 'pending'
                             }">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="mt-2 text-sm font-medium" :class="paymentStatus !== 'pending' ? 'text-blue-600' : 'text-gray-500'">Pesanan</span>
                    </div>
                    
                    <div class="flex-1 h-1 mx-4" :class="paymentStatus !== 'pending' ? 'bg-blue-600' : 'bg-gray-200'"></div>
                    
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             :class="{
                                'bg-blue-600 text-white': paymentStatus === 'processing' || paymentStatus === 'completed',
                                'bg-gray-200 text-gray-600': paymentStatus === 'pending'
                             }">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="mt-2 text-sm font-medium" 
                              :class="paymentStatus === 'processing' || paymentStatus === 'completed' ? 'text-blue-600' : 'text-gray-500'">
                            Bayar
                        </span>
                    </div>
                    
                    <div class="flex-1 h-1 mx-4" :class="paymentStatus === 'completed' ? 'bg-blue-600' : 'bg-gray-200'"></div>
                    
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             :class="{
                                'bg-blue-600 text-white': paymentStatus === 'completed',
                                'bg-gray-200 text-gray-600': paymentStatus !== 'completed'
                             }">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="mt-2 text-sm font-medium" :class="paymentStatus === 'completed' ? 'text-blue-600' : 'text-gray-500'">Selesai</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Left Column - Payment Methods -->
            <div class="md:col-span-2 space-y-6">
                <!-- Transfer Bank -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Transfer Bank</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 border rounded-lg hover:border-blue-500 cursor-pointer transition duration-200"
                                 @click="selectPaymentMethod('bca')"
                                 :class="selectedMethod === 'bca' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                                        BCA
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Bank BCA</h4>
                                        <p class="text-sm text-gray-600">Transfer Virtual Account</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4 border rounded-lg hover:border-blue-500 cursor-pointer transition duration-200"
                                 @click="selectPaymentMethod('mandiri')"
                                 :class="selectedMethod === 'mandiri' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                                        M
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Bank Mandiri</h4>
                                        <p class="text-sm text-gray-600">Transfer Virtual Account</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Virtual Account Info -->
                        <div x-show="selectedMethod && virtualAccountNumber" x-cloak class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="font-bold text-blue-800 mb-2">Instruksi Pembayaran:</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-700">Nomor Virtual Account:</span>
                                    <div class="flex items-center">
                                        <span class="font-mono font-bold text-lg text-blue-800" x-text="virtualAccountNumber"></span>
                                        <button @click="copyToClipboard(virtualAccountNumber)" class="ml-2 text-blue-600 hover:text-blue-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-700">Jumlah Pembayaran:</span>
                                    <span class="font-bold text-blue-800" x-text="formatCurrency(amount)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-700">Batas Waktu:</span>
                                    <span class="font-bold text-blue-800" x-text="formatTime(expiryTime)"></span>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-blue-200">
                                <h5 class="font-semibold text-blue-800 mb-2">Cara Pembayaran:</h5>
                                <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                                    <li>Login ke internet/mobile banking bank pilihan Anda</li>
                                    <li>Pilih menu "Transfer" atau "Bayar"</li>
                                    <li>Masukkan nomor Virtual Account di atas</li>
                                    <li>Konfirmasi dan selesaikan pembayaran</li>
                                    <li>Simpan bukti transfer</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manual Transfer -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Transfer Manual</h3>
                    <p class="text-gray-600 mb-4">Transfer ke rekening berikut dan upload bukti transfer:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                                    BCA
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Bank BCA</h4>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-gray-500">No. Rekening:</span>
                                    <p class="font-mono font-bold">1234567890</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Atas Nama:</span>
                                    <p class="font-semibold">TOKO BUKU ONLINE</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Cabang:</span>
                                    <p>Jakarta Pusat</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                                    M
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Bank Mandiri</h4>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-gray-500">No. Rekening:</span>
                                    <p class="font-mono font-bold">0987654321</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Atas Nama:</span>
                                    <p class="font-semibold">TOKO BUKU ONLINE</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Cabang:</span>
                                    <p>Jakarta Selatan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Bukti Transfer -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition duration-150">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload file</span>
                                        <input type="file" @change="handleFileUpload" class="sr-only" accept="image/*,.pdf">
                                    </label>
                                    <p class="pl-1">atau drag & drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, PDF sampai 5MB</p>
                            </div>
                        </div>
                        
                        <!-- Preview File -->
                        <template x-show="proofFile" x-cloak class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-green-800" x-text="proofFile.name"></p>
                                        <p class="text-sm text-green-600" x-text="formatFileSize(proofFile.size)"></p>
                                    </div>
                                </div>
                                <button @click="removeFile" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Konfirmasi Manual -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea 
                            x-model="manualNote"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: Sudah transfer tanggal 25 Desember 2023, nama pengirim: Budi Santoso"
                        ></textarea>
                    </div>

                    <button 
                        @click="confirmManualPayment"
                        :disabled="!proofFile || isProcessing"
                        :class="!proofFile || isProcessing ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                        class="w-full mt-4 bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200"
                    >
                        <span x-text="isProcessing ? 'Mengirim Konfirmasi...' : 'Konfirmasi Pembayaran'"></span>
                    </button>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Detail Pesanan</h2>
                    
                    <!-- Order Items -->
                     <template x-for="item in orderItems" :key="item.id">
                         <div class="space-y-4 mb-6 max-h-64 overflow-y-auto">
                             <div class="flex items-center" >
                                 <div class="flex-shrink-0 h-12 w-12">
                                     <img :src="item.image" :alt="item.name" class="h-12 w-12 object-cover rounded">
                                 </div>
                                 <div class="ml-4 flex-1">
                                     <h4 class="text-sm font-medium text-gray-900" x-text="item.name"></h4>
                                     <div class="flex justify-between items-center mt-1">
                                         <span class="text-sm text-gray-600" x-text="`${item.quantity} x ${formatCurrency(item.price)}`"></span>
                                         <span class="text-sm font-medium" x-text="formatCurrency(item.subtotal)"></span>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </template>

                    <!-- Order Summary -->
                    <div class="space-y-3 border-t border-b py-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span x-text="formatCurrency(orderSummary.subtotal)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pengiriman</span>
                            <span x-text="formatCurrency(orderSummary.shipping)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Diskon</span>
                            <span class="text-green-600" x-text="`-${formatCurrency(orderSummary.discount)}`"></span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="py-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-blue-600" x-text="formatCurrency(amount)"></span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1" x-text="`Order ID: #${orderId}`"></p>
                    </div>

                    <!-- Countdown Timer -->
                    <div x-show="paymentStatus === 'pending' && expiryTime" x-cloak 
                         class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Selesaikan dalam:</p>
                                <div class="flex items-center mt-1">
                                    <div class="text-2xl font-bold text-yellow-800" x-text="countdown"></div>
                                </div>
                            </div>
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Virtual Account Payment Button -->
                    <button x-show="selectedMethod && virtualAccountNumber && paymentStatus === 'pending'" x-cloak
                            @click="simulatePayment"
                            class="w-full mt-6 bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200"
                    >
                        Simulasi Pembayaran (Demo)
                    </button>

                    <!-- Status Info -->
                    <div x-show="paymentStatus === 'processing'" x-cloak 
                         class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-blue-800">Menunggu verifikasi pembayaran</p>
                                <p class="text-sm text-blue-600">Pembayaran Anda sedang diproses</p>
                            </div>
                        </div>
                    </div>

                    <!-- Success Info -->
                    <div x-show="paymentStatus === 'completed'" x-cloak 
                         class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-bold text-green-800">Pembayaran Berhasil!</p>
                                <p class="text-sm text-green-600">Pesanan Anda akan segera diproses</p>
                            </div>
                        </div>
                        <button @click="viewOrder" class="w-full mt-4 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200">
                            Lihat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>Untuk bantuan, hubungi customer service di <a href="mailto:cs@bookstore.com" class="text-blue-600 hover:text-blue-800">cs@bookstore.com</a> atau WA: 0812-3456-7890</p>
        </div>
    </div>
</div>

<script>
function paymentGateway() {
    return {
        // State
        orderId: '',
        amount: 0,
        paymentStatus: 'pending', // pending, processing, completed, failed
        selectedMethod: null,
        virtualAccountNumber: '',
        expiryTime: null,
        countdown: '59:59',
        proofFile: null,
        manualNote: '',
        isProcessing: false,
        countdownInterval: null,
        
        // Order data
        orderItems: [],
        orderSummary: {},
        
        // Computed
        get paymentStatusText() {
            const texts = {
                'pending': 'Menunggu Pembayaran',
                'processing': 'Sedang Diproses',
                'completed': 'Berhasil',
                'failed': 'Gagal'
            };
            return texts[this.paymentStatus] || 'Unknown';
        },
        
        // Methods
        initPayment() {
            // Generate order ID dari URL atau random
            const urlParams = new URLSearchParams(window.location.search);
            this.orderId = urlParams.get('order_id') || this.generateOrderId();
            this.amount = parseInt(urlParams.get('amount')) || 275000;
            
            // Set expiry time 24 jam dari sekarang
            const expiry = new Date();
            expiry.setHours(expiry.getHours() + 24);
            this.expiryTime = expiry;
            
            // Load order data
            this.loadOrderData();
            
            // Start countdown
            this.startCountdown();
            
            // Check payment status (simulasi)
            this.checkPaymentStatus();
        },
        
        generateOrderId() {
            const timestamp = Date.now().toString().slice(-6);
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `ORD${timestamp}${random}`;
        },
        
        loadOrderData() {
            // Contoh data pesanan
            this.orderItems = [
                {
                    id: 1,
                    name: "Laut Bercerita",
                    price: 95000,
                    quantity: 2,
                    subtotal: 190000,
                    image: "https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&h=300&fit=crop"
                },
                {
                    id: 2,
                    name: "Pulang",
                    price: 85000,
                    quantity: 1,
                    subtotal: 85000,
                    image: "https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&h=300&fit=crop"
                }
            ];
            
            this.orderSummary = {
                subtotal: 275000,
                shipping: 15000,
                discount: 10000,
                total: 280000
            };
        },
        
        startCountdown() {
            this.updateCountdown();
            this.countdownInterval = setInterval(() => {
                this.updateCountdown();
            }, 1000);
        },
        
        updateCountdown() {
            if (!this.expiryTime) return;
            
            const now = new Date();
            const expiry = new Date(this.expiryTime);
            const diff = expiry - now;
            
            if (diff <= 0) {
                this.countdown = "00:00";
                if (this.paymentStatus === 'pending') {
                    this.paymentStatus = 'failed';
                }
                clearInterval(this.countdownInterval);
                return;
            }
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            this.countdown = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        },
        
        selectPaymentMethod(method) {
            this.selectedMethod = method;
            
            // Generate virtual account number
            if (method === 'bca') {
                this.virtualAccountNumber = `8808${Date.now().toString().slice(-8)}`;
            } else if (method === 'mandiri') {
                this.virtualAccountNumber = `8899${Date.now().toString().slice(-8)}`;
            }
        },
        
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.showToast('Nomor VA berhasil disalin!');
            });
        },
        
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    this.showToast('File terlalu besar. Maksimal 5MB', 'error');
                    return;
                }
                this.proofFile = file;
            }
        },
        
        removeFile() {
            this.proofFile = null;
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        confirmManualPayment() {
            if (!this.proofFile) {
                this.showToast('Silakan upload bukti transfer', 'error');
                return;
            }
            
            this.isProcessing = true;
            
            // Simulasi API call
            setTimeout(() => {
                this.paymentStatus = 'processing';
                this.isProcessing = false;
                this.showToast('Konfirmasi pembayaran berhasil dikirim!');
                
                // Simulasi admin verification
                setTimeout(() => {
                    this.paymentStatus = 'completed';
                    this.showToast('Pembayaran berhasil diverifikasi!', 'success');
                }, 5000);
            }, 2000);
        },
        
        simulatePayment() {
            this.paymentStatus = 'processing';
            this.showToast('Pembayaran sedang diproses...');
            
            // Simulasi delay pembayaran
            setTimeout(() => {
                this.paymentStatus = 'completed';
                this.showToast('Pembayaran berhasil!', 'success');
                clearInterval(this.countdownInterval);
            }, 3000);
        },
        
        checkPaymentStatus() {
            // Simulasi cek status pembayaran dari backend
            // Di real implementation, ini akan polling ke API
            console.log('Checking payment status...');
        },
        
        viewOrder() {
            window.location.href = `/orders/${this.orderId}`;
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },
        
        formatTime(date) {
            return new Date(date).toLocaleString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'success' ? 'bg-green-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            toast.textContent = message;
            toast.style.zIndex = '1000';
            
            document.body.appendChild(toast);
            
            // Remove after 3 seconds
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

.sticky {
    position: sticky;
    top: 1.5rem;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection