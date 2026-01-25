{{-- resources/views/messages/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="chatApp()" class="h-[calc(100vh-4rem)] flex bg-gray-50">
    {{-- Sidebar - Chat List --}}
    <div class="w-full md:w-80 lg:w-96 bg-white border-r border-gray-200 flex flex-col" :class="selectedChat ? 'hidden md:flex' : 'flex'">
        {{-- Sidebar Header --}}
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pesan</h2>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input="filterChats()"
                    placeholder="Cari chat..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Chat List --}}
        <div class="flex-1 overflow-y-auto">
            <template x-for="chat in filteredChats" :key="chat.id">
                <div
                    @click="selectChat(chat)"
                    class="p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition"
                    :class="selectedChat?.id === chat.id ? 'bg-blue-50 border-l-4 border-blue-600' : ''">
                    <div class="flex gap-3">
                        {{-- Avatar --}}
                        <div class="relative">
                            <img :src="chat.avatar" class="w-12 h-12 rounded-full object-cover">
                            <div
                                x-show="chat.online"
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>

                        {{-- Chat Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-1">
                                <h3 class="text-sm font-semibold text-gray-900 truncate" x-text="chat.name"></h3>
                                <span class="text-xs text-gray-500 ml-2" x-text="chat.lastMessageTime"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600 truncate flex-1" x-text="chat.lastMessage"></p>
                                <span
                                    x-show="chat.unreadCount > 0"
                                    class="ml-2 min-w-5 h-5 bg-blue-600 text-white text-xs rounded-full flex items-center justify-center px-1"
                                    x-text="chat.unreadCount"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Main Chat Area --}}
    <div class="flex-1 flex flex-col bg-white" :class="!selectedChat ? 'hidden md:flex' : 'flex'">
        <template x-if="!selectedChat">
            {{-- Empty State --}}
            <div class="flex-1 flex items-center justify-center text-gray-500">
                <div class="text-center">
                    <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Chat</h3>
                    <p class="text-gray-500">Pilih percakapan dari daftar untuk mulai chat</p>
                </div>
            </div>
        </template>

        <template x-if="selectedChat">
            <div class="flex-1 flex flex-col h-full">
                {{-- Chat Header --}}
                <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-3">
                        <button
                            @click="selectedChat = null"
                            class="md:hidden p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="relative">
                            <img :src="selectedChat.avatar" class="w-10 h-10 rounded-full">
                            <div
                                x-show="selectedChat.online"
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900" x-text="selectedChat.name"></h3>
                            <p class="text-xs text-gray-500" x-text="selectedChat.online ? 'Online' : 'Offline'"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div
                    x-ref="messagesContainer"
                    class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                    <template x-for="message in messages" :key="message.id">
                        <div
                            class="flex"
                            :class="message.sender === 'me' ? 'justify-end' : 'justify-start'">
                            <div
                                class="max-w-xs lg:max-w-md"
                                :class="message.sender === 'me' ? 'order-2' : 'order-1'">
                                {{-- Message Bubble --}}
                                <div
                                    class="rounded-lg p-3 shadow-sm"
                                    :class="message.sender === 'me' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none'">
                                    {{-- Product Card (if attached) --}}
                                    <div x-show="message.product" class="mb-2 p-2 rounded" :class="message.sender === 'me' ? 'bg-blue-700' : 'bg-gray-50'">
                                        <div class="flex gap-2 items-center">
                                            <img :src="message.product?.image" class="w-12 h-12 object-cover rounded">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium truncate" x-text="message.product?.name"></p>
                                                <p class="text-xs font-semibold" x-text="message.product?.price"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Image (if attached) --}}
                                    <img
                                        x-show="message.image"
                                        :src="message.image"
                                        class="rounded mb-2 max-w-full cursor-pointer"
                                        @click="viewImage(message.image)">

                                    {{-- Message Text --}}
                                    <p class="text-sm whitespace-pre-wrap break-words" x-text="message.text"></p>

                                    {{-- Timestamp --}}
                                    <div class="flex items-center justify-end gap-1 mt-1">
                                        <span
                                            class="text-xs"
                                            :class="message.sender === 'me' ? 'text-blue-100' : 'text-gray-500'"
                                            x-text="message.time"></span>
                                        <template x-if="message.sender === 'me'">
                                            <svg
                                                class="w-4 h-4"
                                                :class="message.read ? 'text-blue-200' : 'text-blue-300'"
                                                fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Typing Indicator --}}
                    <div x-show="isTyping" class="flex justify-start">
                        <div class="bg-white rounded-lg p-3 shadow-sm rounded-bl-none">
                            <div class="flex gap-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Input Area --}}
                <div class="p-4 border-t border-gray-200 bg-white">
                    {{-- Quick Actions --}}
                    <div class="flex gap-2 mb-3 overflow-x-auto pb-2">
                        <button
                            @click="sendQuickMessage('Halo, saya tertarik dengan produk ini')"
                            class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition whitespace-nowrap">
                            👋 Saya tertarik
                        </button>
                        <button
                            @click="sendQuickMessage('Apakah produk masih tersedia?')"
                            class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition whitespace-nowrap">
                            ✅ Masih ada?
                        </button>
                        <button
                            @click="sendQuickMessage('Bisa nego harga?')"
                            class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition whitespace-nowrap">
                            💰 Nego?
                        </button>
                    </div>

                    {{-- Message Input --}}
                    <div class="flex items-end gap-2">
                        <button
                            @click="showAttachmentOptions = !showAttachmentOptions"
                            class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>

                            {{-- Attachment Options --}}
                            <div
                                x-show="showAttachmentOptions"
                                @click.away="showAttachmentOptions = false"
                                class="absolute bottom-full left-0 mb-2 bg-white rounded-lg shadow-xl border border-gray-200 py-2 w-48">
                                <button
                                    @click="attachImage()"
                                    class="w-full px-4 py-2 text-left hover:bg-gray-50 flex items-center gap-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm">Gambar</span>
                                </button>
                                <button
                                    @click="attachProduct()"
                                    class="w-full px-4 py-2 text-left hover:bg-gray-50 flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <span class="text-sm">Produk</span>
                                </button>
                            </div>
                        </button>

                        <div class="flex-1">
                            <textarea
                                x-model="newMessage"
                                @keydown.enter.prevent="sendMessage()"
                                @input="adjustTextareaHeight($event)"
                                rows="1"
                                placeholder="Ketik pesan..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent max-h-32"></textarea>
                        </div>

                        <button
                            @click="sendMessage()"
                            :disabled="!newMessage.trim()"
                            class="p-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function chatApp() {
        return {
            chats: [{
                    id: 1,
                    name: 'Toko Elektronik Jakarta',
                    avatar: 'https://ui-avatars.com/api/?name=Toko+Elektronik&background=3b82f6&color=fff',
                    lastMessage: 'Baik, pesanan sudah kami proses',
                    lastMessageTime: '10:30',
                    unreadCount: 0,
                    online: true
                },
                {
                    id: 2,
                    name: 'Fashion Store Bandung',
                    avatar: 'https://ui-avatars.com/api/?name=Fashion+Store&background=ec4899&color=fff',
                    lastMessage: 'Oke siap, terima kasih',
                    lastMessageTime: 'Kemarin',
                    unreadCount: 2,
                    online: false
                },
                {
                    id: 3,
                    name: 'Gadget Shop Surabaya',
                    avatar: 'https://ui-avatars.com/api/?name=Gadget+Shop&background=10b981&color=fff',
                    lastMessage: 'Produk ready stok kak',
                    lastMessageTime: '2 hari lalu',
                    unreadCount: 1,
                    online: true
                },
                {
                    id: 4,
                    name: 'Home Decor Store',
                    avatar: 'https://ui-avatars.com/api/?name=Home+Decor&background=f59e0b&color=fff',
                    lastMessage: 'Kami akan kirim hari ini',
                    lastMessageTime: '3 hari lalu',
                    unreadCount: 0,
                    online: false
                }
            ],
            messages: [{
                    id: 1,
                    sender: 'them',
                    text: 'Halo, selamat datang di Toko Elektronik Jakarta! Ada yang bisa kami bantu?',
                    time: '09:00',
                    read: true
                },
                {
                    id: 2,
                    sender: 'me',
                    text: 'Halo, saya mau tanya tentang laptop gaming yang di display',
                    time: '09:05',
                    read: true
                },
                {
                    id: 3,
                    sender: 'them',
                    product: {
                        name: 'Laptop Gaming ASUS ROG',
                        price: 'Rp 15.000.000',
                        image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=100&h=100&fit=crop'
                    },
                    text: 'Ini produknya kak, masih ready stok',
                    time: '09:06',
                    read: true
                },
                {
                    id: 4,
                    sender: 'me',
                    text: 'Bisa nego harga ga?',
                    time: '09:10',
                    read: true
                },
                {
                    id: 5,
                    sender: 'them',
                    text: 'Untuk harga sudah nett kak, tapi kami kasih bonus mouse gaming dan tas laptop',
                    time: '09:12',
                    read: true
                },
                {
                    id: 6,
                    sender: 'me',
                    text: 'Oke deal, saya order sekarang ya',
                    time: '10:15',
                    read: true
                },
                {
                    id: 7,
                    sender: 'them',
                    text: 'Baik, pesanan sudah kami proses. Terima kasih sudah berbelanja!',
                    time: '10:30',
                    read: true
                }
            ],
            filteredChats: [],
            selectedChat: null,
            searchQuery: '',
            newMessage: '',
            isTyping: false,
            showAttachmentOptions: false,

            init() {
                this.filteredChats = [...this.chats];
                // Auto select first chat on desktop
                if (window.innerWidth >= 768 && this.chats.length > 0) {
                    this.selectChat(this.chats[0]);
                }
            },

            filterChats() {
                if (!this.searchQuery.trim()) {
                    this.filteredChats = [...this.chats];
                    return;
                }
                this.filteredChats = this.chats.filter(chat =>
                    chat.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            },

            selectChat(chat) {
                this.selectedChat = chat;
                chat.unreadCount = 0;
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },

            sendMessage() {
                if (!this.newMessage.trim()) return;

                const message = {
                    id: this.messages.length + 1,
                    sender: 'me',
                    text: this.newMessage.trim(),
                    time: new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                    read: false
                };

                this.messages.push(message);
                this.newMessage = '';

                // Update last message in chat list
                if (this.selectedChat) {
                    this.selectedChat.lastMessage = message.text;
                    this.selectedChat.lastMessageTime = message.time;
                }

                this.$nextTick(() => {
                    this.scrollToBottom();
                });

                // Simulate typing and response
                setTimeout(() => {
                    this.isTyping = true;
                    setTimeout(() => {
                        this.isTyping = false;
                        this.receiveMessage('Terima kasih pesannya! Kami akan segera merespons.');
                    }, 2000);
                }, 1000);
            },

            sendQuickMessage(text) {
                this.newMessage = text;
                this.sendMessage();
            },

            receiveMessage(text) {
                const message = {
                    id: this.messages.length + 1,
                    sender: 'them',
                    text: text,
                    time: new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                    read: true
                };

                this.messages.push(message);

                if (this.selectedChat) {
                    this.selectedChat.lastMessage = message.text;
                    this.selectedChat.lastMessageTime = message.time;
                }

                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },

            scrollToBottom() {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            },

            adjustTextareaHeight(event) {
                event.target.style.height = 'auto';
                event.target.style.height = Math.min(event.target.scrollHeight, 128) + 'px';
            },

            attachImage() {
                this.showAttachmentOptions = false;
                // Simulate image attachment
                alert('Fitur upload gambar - Implementasi dengan input file');
            },

            attachProduct() {
                this.showAttachmentOptions = false;
                // Simulate product attachment
                alert('Fitur kirim produk - Implementasi dengan product picker');
            },

            viewImage(imageSrc) {
                // Open image in lightbox or new tab
                window.open(imageSrc, '_blank');
            }
        }
    }
</script>
@endsection