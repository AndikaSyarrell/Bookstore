<!-- Notification Bell Component -->
<!-- Add this to your navbar in layouts/app.blade.php -->

<div 
    x-data="notificationBell()" 
    x-init="init()"
    class="relative"
    @click.away="showDropdown = false"
>
    <!-- Bell Icon Button -->
    <button 
        @click="toggleDropdown()"
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
    >
        <!-- Bell Icon -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        <!-- Unread Badge -->
        <span 
            x-show="unreadCount > 0"
            x-text="unreadCount > 99 ? '99+' : unreadCount"
            class="absolute -top-1 -right-1 px-1.5 py-0.5 bg-red-600 text-white text-xs font-bold rounded-full min-w-[20px] text-center"
        ></span>

        <!-- Pulse Animation -->
        <span 
            x-show="unreadCount > 0"
            class="absolute top-0 right-0 w-3 h-3"
        >
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div 
        x-show="showDropdown"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Notifications</h3>
            <div class="flex items-center gap-2">
                <button 
                    @click="markAllAsRead()"
                    x-show="unreadCount > 0"
                    class="text-xs text-blue-600 hover:text-blue-700"
                >
                    Mark all read
                </button>
                <a href="{{ route('notifications.index') }}" class="text-xs text-gray-600 hover:text-gray-900">
                    View all
                </a>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <a 
                    :href="notification.action_url || '#'"
                    @click="markAsRead(notification.id)"
                    class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0"
                    :class="!notification.read ? 'bg-blue-50' : ''"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div 
                            class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                            :class="`bg-${notification.color}-100`"
                        >
                            <svg class="w-5 h-5" :class="`text-${notification.color}-600`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <!-- Dynamic icon based on type -->
                                <template x-if="notification.icon === 'shopping-cart'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </template>
                                <template x-if="notification.icon === 'package'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </template>
                                <template x-if="notification.icon === 'check-circle'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </template>
                                <template x-if="notification.icon === 'message-circle'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </template>
                                <template x-if="notification.icon === 'truck'">
                                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </template>
                                <!-- Default bell icon -->
                                <template x-if="!['shopping-cart', 'package', 'check-circle', 'message-circle', 'truck'].includes(notification.icon)">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </template>
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                            <p class="text-sm text-gray-600 line-clamp-2 mt-1" x-text="notification.message"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="notification.time_ago"></p>
                        </div>

                        <!-- Unread Indicator -->
                        <div 
                            x-show="!notification.read"
                            class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0"
                        ></div>
                    </div>
                </a>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                View all notifications →
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        showDropdown: false,
        notifications: [],
        unreadCount: 0,
        channel: null,

        init() {
            this.loadNotifications();
            this.setupReverb();
        },

        async loadNotifications() {
            try {
                const response = await fetch('{{ route("notifications.recent") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('Failed to load notifications:', error);
            }
        },

        setupReverb() {
            // Listen to private notification channel
            this.channel = window.Echo.private(`notifications.{{ Auth::id() }}`)
                .listen('.notification.new', (e) => {
                    // Add new notification to top
                    this.notifications.unshift(e);
                    
                    // Update unread count
                    if (!e.read) {
                        this.unreadCount++;
                    }

                    // Keep only 10 notifications
                    if (this.notifications.length > 10) {
                        this.notifications.pop();
                    }

                    // Show browser notification
                    this.showBrowserNotification(e);

                    // Play sound
                    this.playNotificationSound();
                });
        },

        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            
            if (this.showDropdown) {
                this.loadNotifications();
            }
        },

        async markAsRead(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                // Update local state
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification && !notification.read) {
                    notification.read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                // Update local state
                this.notifications.forEach(n => n.read = true);
                this.unreadCount = 0;
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        showBrowserNotification(notification) {
            // Request permission if needed
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(notification.title, {
                    body: notification.message,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                });
            } else if ('Notification' in window && Notification.permission !== 'denied') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        new Notification(notification.title, {
                            body: notification.message,
                            icon: '/favicon.ico',
                        });
                    }
                });
            }
        },

        playNotificationSound() {
            // Optional: play notification sound
            // const audio = new Audio('/sounds/notification.mp3');
            // audio.play();
        },

        destroy() {
            if (this.channel) {
                window.Echo.leave(`notifications.{{ Auth::id() }}`);
            }
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>