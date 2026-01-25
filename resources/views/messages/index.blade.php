{{-- resources/views/messages/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="chatApp(@js($chats))" class="h-[calc(100vh-4rem)] flex bg-gray-50">
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
        <template x-if="selectedChat">
            <div class="flex-1 flex flex-col h-full">
                {{-- Chat Header --}}
                <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-3">
                        <button @click="selectedChat = null" class="md:hidden p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="relative">
                            <img :src="selectedChat.avatar" class="w-10 h-10 rounded-full">
                            <div x-show="selectedChat.online" class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900" x-text="selectedChat.name"></h3>
                            <p class="text-xs text-gray-500" x-text="selectedChat.online ? 'Online' : 'Offline'"></p>
                        </div>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                    <template x-for="message in messages" :key="message.id">
                        <div class="flex" :class="message.sender === 'me' ? 'justify-end' : 'justify-start'">
                            <div class="max-w-xs lg:max-w-md">
                                <div class="rounded-lg p-3 shadow-sm" :class="message.sender === 'me' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none'">
                                    {{-- Product Card --}}
                                    <div x-show="message.product" class="mb-2 p-2 rounded" :class="message.sender === 'me' ? 'bg-blue-700' : 'bg-gray-50'">
                                        <div class="flex gap-2 items-center">
                                            <img :src="message.product?.image" class="w-12 h-12 object-cover rounded">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium truncate" x-text="message.product?.name"></p>
                                                <p class="text-xs font-semibold" x-text="message.product?.price"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Image --}}
                                    <img x-show="message.image" :src="message.image" class="rounded mb-2 max-w-full cursor-pointer">

                                    {{-- Text --}}
                                    <p class="text-sm whitespace-pre-wrap break-words" x-text="message.text"></p>

                                    {{-- Timestamp --}}
                                    <div class="flex items-center justify-end gap-1 mt-1">
                                        <span class="text-xs" :class="message.sender === 'me' ? 'text-blue-100' : 'text-gray-500'" x-text="message.time"></span>
                                        <template x-if="message.sender === 'me'">
                                            <svg class="w-4 h-4" :class="message.read ? 'text-blue-200' : 'text-blue-300'" fill="currentColor" viewBox="0 0 20 20">
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
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <textarea
                                x-model="newMessage"
                                @keydown.enter.prevent="sendMessage()"
                                rows="1"
                                placeholder="Ketik pesan..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent max-h-32"></textarea>
                        </div>
                        <button
                            @click="sendMessage()"
                            :disabled="!newMessage.trim() || sending"
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
</div>

<script>
    function chatApp(initalChats) {
        const currentUserId = '{{ Auth::id() }}';

        return {
            chats: initalChats,
            messages: [],
            filteredChats: [],
            selectedChat: null,
            newMessage: '',
            sending: false,
            isTyping: false,
            searchQuery: '',
            channel: null,

            init() {
                console.log(typeof this.chats)
                console.log(this.chats)
                this.filteredChats = [...this.chats];
                // Auto select first chat on desktop
                if (this.chats.length > 0 && window.innerWidth >= 768) {
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

            async selectChat(chat) {
                this.selectedChat = chat;

                // Leave previous channel
                if (this.channel) {
                    Echo.leave(`chat.${this.channel.chatId}`);
                }

                // Load messages from server
                await this.loadMessages(chat.id);

                // Join Reverb channel for real-time updates
                this.channel = Echo.join(`chat.${chat.id}`)
                    .here((users) => {
                        console.log('Users currently in chat:', users);
                    })
                    .joining((user) => {
                        console.log('User joined:', user.name);
                        this.selectedChat.online = true;
                    })
                    .leaving((user) => {
                        console.log('User left:', user.name);
                        this.selectedChat.online = false;
                    })
                    .listen('MessageSent', (e) => {
                        const exists = this.messages.find(m => m.id === e.id);
                        if (exists) {
                            console.log('Message already exists, skipping...');
                            return; // SKIP duplicate
                        }

                        console.log('New message received:', e);

                        // Add message to messages array
                        this.messages.push({
                            id: e.id,
                            sender: e.user_id === currentUserId ? 'me' : 'them',
                            text: e.message,
                            type: e.type,
                            product: e.type === 'product' ? e.metadata : null,
                            image: e.type === 'image' ? e.metadata?.url : null,
                            time: new Date(e.created_at).toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit'
                            }),
                            read: false
                        });

                        // Update chat list
                        this.selectedChat.lastMessage = e.message;
                        this.selectedChat.lastMessageTime = 'Baru saja';

                        // Scroll to bottom
                        this.$nextTick(() => this.scrollToBottom());
                    });

                this.channel.chatId = chat.id;
                this.$nextTick(() => this.scrollToBottom());
            },

            async loadMessages(chatId) {
                try {

                    console.log('Loading messages for chat:', chatId);

                    const response = await fetch(`chats/receive/${chatId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`); // ✅ Status code
                    }

                    const data = await response.json();
                    console.log('Messages loaded:', data); // ✅ Log response

                    this.messages = data;

                    // ✅ Scroll to bottom after messages loaded
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                } catch (error) {
                    console.error('Error loading messages:', error);
                    alert('Gagal memuat pesan');
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || this.sending) return;

                this.sending = true;
                const messageText = this.newMessage.trim();
                this.newMessage = '';

                try {
                    const response = await fetch(`chats/sent/${this.selectedChat.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: messageText,
                            type: 'text'
                        })
                    });

                    if (!response.ok) throw new Error('Failed to send message');

                    const message = await response.json();

                    // Add message to local messages array
                    this.messages.push(message);

                    // Update chat list
                    this.selectedChat.lastMessage = message.text;
                    this.selectedChat.lastMessageTime = message.time;

                    // Scroll to bottom
                    this.$nextTick(() => this.scrollToBottom());
                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('Gagal mengirim pesan');
                    this.newMessage = messageText; // Restore message
                } finally {
                    this.sending = false;
                }
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