<div x-data="notificationButton()">
    <button 
        @click="notificationOpen = !notificationOpen"
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <span 
            x-show="unreadCount > 0"
            x-text="unreadCount"
            class="absolute top-0 right-0 min-w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center px-1"
        ></span>
    </button>

    {{-- Notifications Dropdown --}}
    <div 
        x-show="notificationOpen"
        @click.away="notificationOpen = false"
        x-transition
        class="absolute right-0 mr-9 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;"
    >
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Notifikasi</h3>
            <button 
                @click="markAllAsRead()"
                class="text-sm text-blue-600 hover:underline"
            >
                Tandai Dibaca
            </button>
        </div>
        
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-200">
            <template x-if="notifications.length === 0">
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p>Tidak ada notifikasi</p>
                </div>
            </template>

            <template x-for="notif in notifications" :key="notif.id">
                <div 
                    class="p-4 hover:bg-gray-50 transition cursor-pointer"
                    :class="!notif.read ? 'bg-blue-50' : ''"
                    @click="markAsRead(notif.id)"
                >
                    <div class="flex gap-3">
                        <div 
                            class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                            :class="{
                                'bg-blue-100 text-blue-600': notif.type === 'order',
                                'bg-green-100 text-green-600': notif.type === 'payment',
                                'bg-purple-100 text-purple-600': notif.type === 'shipping',
                                'bg-yellow-100 text-yellow-600': notif.type === 'promo'
                            }"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-1">
                                <h4 class="text-sm font-medium text-gray-900" x-text="notif.title"></h4>
                                <span class="text-xs text-gray-500 ml-2" x-text="notif.time"></span>
                            </div>
                            <p class="text-sm text-gray-600" x-text="notif.message"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-3 border-t border-gray-200">
            <a 
                href="/notifications"
                class="block text-center text-sm text-blue-600 hover:underline font-medium"
            >
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>

    <script>
    function notificationButton() {
        return {
            notificationOpen: false,
            unreadCount: 4,
            notifications: [
                {
                    id: 1,
                    type: 'order',
                    title: 'Pesanan Dikonfirmasi',
                    message: 'Pesanan #ORD-2026-001 telah dikonfirmasi oleh penjual',
                    time: '10 menit',
                    read: false
                },
                {
                    id: 2,
                    type: 'shipping',
                    title: 'Pesanan Dikirim',
                    message: 'Pesanan Anda sedang dalam perjalanan. No Resi: JNE123456',
                    time: '2 jam',
                    read: false
                },
                {
                    id: 3,
                    type: 'payment',
                    title: 'Pembayaran Berhasil',
                    message: 'Pembayaran senilai Rp 850.000 telah diterima',
                    time: '5 jam',
                    read: false
                },
                {
                    id: 4,
                    type: 'promo',
                    title: 'Promo Spesial!',
                    message: 'Dapatkan diskon 50% untuk produk elektronik hari ini',
                    time: '1 hari',
                    read: false
                },
                {
                    id: 5,
                    type: 'order',
                    title: 'Pesanan Selesai',
                    message: 'Terima kasih! Jangan lupa beri ulasan untuk pesanan Anda',
                    time: '2 hari',
                    read: true
                }
            ],

            markAsRead(id) {
                const notif = this.notifications.find(n => n.id === id);
                if (notif && !notif.read) {
                    notif.read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            },

            markAllAsRead() {
                this.notifications.forEach(notif => notif.read = true);
                this.unreadCount = 0;
            }
        }
    }
    </script>
</div>