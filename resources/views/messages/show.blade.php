@extends('layouts.app')

@section('content')
<div x-data="realtimeChatPage()" x-init="init()" class="h-screen flex flex-col bg-gray-50">

    <!-- Chat Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('chats.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                        @if(Auth::id() === $chat->buyer_id)
                        {{ substr($chat->seller->name, 0, 1) }}
                        @else
                        {{ substr($chat->buyer->name, 0, 1) }}
                        @endif
                    </div>
                    <!-- Online indicator -->
                    <div
                        x-show="isOnline"
                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                </div>
                <div>
                    <h1 class="font-bold text-gray-900">
                        @if(Auth::id() === $chat->buyer_id)
                        {{ $chat->seller->name }}
                        @else
                        {{ $chat->buyer->name }}
                        @endif
                    </h1>
                    <!-- Typing or online status -->
                    <p class="text-sm text-gray-600">
                        <span x-show="!isTyping">
                            @if(Auth::id() === $chat->buyer_id)
                            Seller
                            @else
                            Buyer
                            @endif
                        </span>
                        <span x-show="isTyping" class="text-blue-600 italic">typing...</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Chat Actions -->
        <div class="flex items-center gap-3">
            <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Chat Messages Area -->
    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" x-ref="messagesContainer">

        <!-- Loop through messages with unique keys -->
        <template x-for="(message, index) in messages" :key="`msg-${message.id}-${index}`">
            <div>
                <!-- Product Context Message -->
                <template x-if="message.type === 'product_context'">
                    <div class="flex justify-center">
                        <div class="max-w-md w-full bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700 mb-3" x-text="message.message"></p>

                            <template x-if="message.metadata_parsed">
                                <a :href="message.metadata_parsed.product_url || '#'" class="block bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                    <div class="flex gap-3 p-3">
                                        <template x-if="message.metadata_parsed.product_image">
                                            <img
                                                :src="`/storage/products/${message.metadata_parsed.product_image}`"
                                                :alt="message.metadata_parsed.product_title || 'Product'"
                                                class="w-16 h-16 object-cover rounded">
                                        </template>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 text-sm line-clamp-2" x-text="message.metadata_parsed.product_title || 'Product'"></h4>
                                            <p class="text-blue-600 font-bold text-sm mt-1" x-text="`Rp ${formatPrice(message.metadata_parsed.product_price || 0)}`"></p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </a>
                            </template>

                            <p class="text-xs text-gray-500 mt-2" x-text="message.formatted_time || ''"></p>
                        </div>
                    </div>
                </template>

                <!-- Regular Message -->
                <template x-if="message.type === 'text'">
                    <div>
                        <!-- My Message (Right) -->
                        <template x-if="message.user_id === currentUserId">
                            <div class="flex justify-end">
                                <div class="max-w-md">
                                    <div class="bg-blue-600 text-white rounded-lg rounded-tr-none px-4 py-2">
                                        <p class="text-sm whitespace-pre-wrap" x-text="message.message"></p>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 mt-1">
                                        <p class="text-xs text-gray-500" x-text="message.formatted_time || ''"></p>
                                        <!-- Read indicator -->
                                        <template x-if="message.read">
                                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                            </svg>
                                        </template>
                                        <template x-if="!message.read">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Their Message (Left) -->
                        <template x-if="message.user_id !== currentUserId">
                            <div class="flex justify-start">
                                <div class="max-w-md">
                                    <div class="bg-white border border-gray-200 rounded-lg rounded-tl-none px-4 py-2">
                                        <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="message.message"></p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" x-text="message.formatted_time || ''"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <!-- Loading Indicator -->
        <div x-show="isLoading" class="flex justify-center">
            <div class="animate-pulse flex gap-2">
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
            </div>
        </div>

    </div>

    <!-- Message Input -->
    <div class="bg-white border-t border-gray-200 px-6 py-4">
        <form @submit.prevent="sendMessage()" class="flex items-end gap-3">

            <!-- Attachment Button -->
            <button
                type="button"
                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </button>

            <!-- Message Input -->
            <div class="flex-1 relative">
                <textarea
                    x-model="newMessage"
                    @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                    @input="handleTyping(); autoResize($event.target)"
                    rows="1"
                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                    placeholder="Type a message... (Shift+Enter for new line)"
                    style="max-height: 120px;"></textarea>

                <!-- Emoji Button -->
                <button
                    type="button"
                    class="absolute right-3 bottom-3 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>

            <!-- Send Button -->
            <button
                type="submit"
                :disabled="!newMessage.trim() || isSending"
                class="p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed flex-shrink-0 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>

        <!-- Quick Replies -->
        <div class="flex flex-wrap gap-2 mt-3">
            <button
                @click="newMessage = 'Is this still available?'"
                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Is this still available?
            </button>
            <button
                @click="newMessage = 'Can you give me a discount?'"
                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Can you give me a discount?
            </button>
            <button
                @click="newMessage = 'Thank you!'"
                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Thank you!
            </button>
        </div>
    </div>

</div>

<script>
    function realtimeChatPage() {
        return {
            chatId: {{$chat -> id}},
            currentUserId: {{Auth::id()}},
            newMessage: '',
            isSending: false,
            isLoading: false,
            isTyping: false,
            isOnline: false,
            messages: [],
            typingTimeout: null,
            channel: null,

            init() {
                // Initialize messages from server data
                this.loadInitialMessages();
                this.scrollToBottom();
                this.setupReverb();
            },

            loadInitialMessages() {
                const serverMessages = @json($messages);

                this.messages = (serverMessages || []).map(msg => {
                    let metadataParsed = null;
                    if (msg.metadata) {
                        try {
                            metadataParsed = JSON.parse(msg.metadata);
                        } catch (e) {
                            metadataParsed = null;
                        }
                    }

                    return {
                        id: msg.id,
                        chat_id: msg.chat_id,
                        user_id: msg.user_id,
                        user_name: msg.user_name,
                        message: msg.message,
                        type: msg.type,
                        metadata: msg.metadata,
                        metadata_parsed: metadataParsed,
                        read: msg.read,
                        created_at: msg.created_at,
                        formatted_time: msg.formatted_time
                    };
                });
            },

            setupReverb() {
                // Join presence channel
                this.channel = window.Echo.join(`chat.${this.chatId}`)
                    .here((users) => {
                        // Users currently in room
                        this.isOnline = users.some(u => u.id !== this.currentUserId);
                    })
                    .joining((user) => {
                        // Someone joined
                        if (user.id !== this.currentUserId) {
                            this.isOnline = true;
                        }
                    })
                    .leaving((user) => {
                        // Someone left
                        if (user.id !== this.currentUserId) {
                            this.isOnline = false;
                        }
                    })
                    .listen('.message.sent', (e) => {
                        const exists = this.messages.some(m => m.id === e.id);
                        if (exists) {
                            console.log('Message already exists, skipping duplicate');
                            return; // Skip adding duplicate
                        }
                        // New message received
                        const newMessage = {
                            id: e.id,
                            chat_id: e.chat_id,
                            user_id: e.user_id,
                            user_name: e.user_name,
                            message: e.message,
                            type: e.type,
                            metadata: e.metadata,
                            metadata_parsed: e.metadata ? JSON.parse(e.metadata) : null,
                            read: e.read,
                            created_at: e.created_at,
                            formatted_time: e.formatted_time
                        };

                        this.messages.push(newMessage);

                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });

                        // Play sound notification
                        this.playNotificationSound();
                    })
                    .listen('.user.typing', (e) => {
                        // Someone is typing
                        if (e.user_id !== this.currentUserId) {
                            this.isTyping = e.is_typing;
                        }
                    })
                    .listen('.message.read', (e) => {
                        // Messages marked as read
                        if (e.user_id !== this.currentUserId) {
                            e.message_ids.forEach(msgId => {
                                const msg = this.messages.find(m => m.id === msgId);
                                if (msg) {
                                    msg.read = true;
                                }
                            });
                        }
                    });
            },

            scrollToBottom() {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            },

            autoResize(textarea) {
                textarea.style.height = 'auto';
                textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
            },

            handleTyping() {
                // Broadcast typing status
                if (this.typingTimeout) {
                    clearTimeout(this.typingTimeout);
                }

                // Send typing start
                fetch(`/chats/${this.chatId}/typing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        is_typing: true
                    })
                }).catch(err => console.error('Typing error:', err));

                // Stop typing after 3 seconds
                this.typingTimeout = setTimeout(() => {
                    fetch(`/chats/${this.chatId}/typing`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            is_typing: false
                        })
                    }).catch(err => console.error('Typing error:', err));
                }, 3000);
            },

            async sendMessage() {
                if (!this.newMessage.trim() || this.isSending) return;

                this.isSending = true;
                const messageText = this.newMessage.trim();
                this.newMessage = '';

                try {
                    const response = await fetch(`{{ route('chats.send', $chat->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message: messageText
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Message will be added via Reverb broadcast
                        // But add optimistically for instant feedback
                        // const optimisticMessage = {
                        //     id: data.message.id,
                        //     chat_id: this.chatId,
                        //     user_id: this.currentUserId,
                        //     user_name: '{{ Auth::user()->name }}',
                        //     message: messageText,
                        //     type: 'text',
                        //     metadata: null,
                        //     metadata_parsed: null,
                        //     read: false,
                        //     created_at: new Date().toISOString(),
                        //     formatted_time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false })
                        // };

                        // this.messages.push(optimisticMessage);

                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    } else {
                        alert('Failed to send message');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred');
                } finally {
                    this.isSending = false;
                }
            },

            formatPrice(price) {
                if (!price) return '0';
                return new Intl.NumberFormat('id-ID').format(price);
            },

            playNotificationSound() {
                // Optional: play notification sound
                // const audio = new Audio('/sounds/notification.mp3');
                // audio.play();
            },

            destroy() {
                if (this.channel) {
                    window.Echo.leave(`chat.${this.chatId}`);
                }
            }
        }
    }
</script>

<style>
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection