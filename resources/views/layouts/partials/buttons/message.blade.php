<div x-data="messageButton()">
    <button 
        @click="messageOpen = !messageOpen"
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <span 
            x-show="unreadCount > 0"
            x-text="unreadCount"
            class="absolute top-0 right-0 min-w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center px-1"
        ></span>
    </button>

    {{-- Messages Dropdown --}}
    <div 
        x-show="messageOpen"
        @click.away="messageOpen = false"
        x-transition
        class="absolute right-0 mr-9 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;"
    >
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Pesan</h3>
            <a href="/messages" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
        </div>
        
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-200">
            <template x-if="messages.length === 0">
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <p>Tidak ada pesan</p>
                </div>
            </template>

            <template x-for="message in messages" :key="message.id">
                <a 
                    :href="`/messages/${message.id}`"
                    class="block p-4 hover:bg-gray-50 transition"
                    :class="!message.read ? 'bg-blue-50' : ''"
                >
                    <div class="flex gap-3">
                        <img :src="message.sender_avatar" class="w-10 h-10 rounded-full">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-medium text-gray-900 truncate" x-text="message.sender_name"></h4>
                                <span class="text-xs text-gray-500" x-text="message.time"></span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2" x-text="message.preview"></p>
                        </div>
                        <div x-show="!message.read" class="w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                    </div>
                </a>
            </template>
        </div>
    </div>

    <script>
    function messageButton() {
        return {
            messageOpen: false,
            unreadCount: 3,
            messages: [
                {
                    id: 1,
                    sender_name: 'Toko Elektronik Jakarta',
                    sender_avatar: 'https://ui-avatars.com/api/?name=Toko+Elektronik',
                    preview: 'Pesanan Anda sudah kami proses dan akan segera dikirim',
                    time: '5 menit',
                    read: false
                },
                {
                    id: 2,
                    sender_name: 'Fashion Store Bandung',
                    sender_avatar: 'https://ui-avatars.com/api/?name=Fashion+Store',
                    preview: 'Terima kasih sudah berbelanja di toko kami',
                    time: '1 jam',
                    read: false
                },
                {
                    id: 3,
                    sender_name: 'Gadget Shop',
                    sender_avatar: 'https://ui-avatars.com/api/?name=Gadget+Shop',
                    preview: 'Produk yang Anda tanyakan sudah tersedia kembali',
                    time: '3 jam',
                    read: false
                },
                {
                    id: 4,
                    sender_name: 'Admin Support',
                    sender_avatar: 'https://ui-avatars.com/api/?name=Admin',
                    preview: 'Selamat datang! Ada yang bisa kami bantu?',
                    time: '1 hari',
                    read: true
                }
            ]
        }
    }
    </script>
</div>