@extends('layouts.app')

@section('content')
<div x-data="notificationsPage()" x-init="init()" class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-2">Stay updated with your orders and messages</p>
            </div>
            <div class="flex items-center gap-3">
                <button 
                    @click="markAllAsRead()"
                    class="px-4 py-2 text-sm text-blue-600 hover:text-blue-700 font-medium"
                >
                    Mark all as read
                </button>
                <button 
                    @click="deleteAllRead()"
                    class="px-4 py-2 text-sm text-red-600 hover:text-red-700 font-medium"
                >
                    Clear read
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button 
                        @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        All Notifications
                    </button>
                    <button 
                        @click="activeTab = 'unread'"
                        :class="activeTab === 'unread' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        Unread
                        <span x-show="unreadCount > 0" class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full" x-text="unreadCount"></span>
                    </button>
                    <button 
                        @click="activeTab = 'orders'"
                        :class="activeTab === 'orders' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        Orders
                    </button>
                    <button 
                        @click="activeTab = 'messages'"
                        :class="activeTab === 'messages' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition-colors"
                    >
                        Messages
                    </button>
                </nav>
            </div>
        </div>

        <!-- Notifications List -->
        @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
            <a 
                href="{{ $notification->action_url ?? '#' }}"
                @click="markAsRead({{ $notification->id }})"
                class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow {{ !$notification->read ? 'bg-blue-50 border-blue-200' : '' }}"
            >
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="w-12 h-12 rounded-full bg-{{ $notification->color }}-100 flex items-center justify-center flex-shrink-0">
                        @if($notification->icon === 'shopping-cart')
                        <svg class="w-6 h-6 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @elseif($notification->icon === 'package')
                        <svg class="w-6 h-6 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        @elseif($notification->icon === 'check-circle')
                        <svg class="w-6 h-6 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @elseif($notification->icon === 'message-circle')
                        <svg class="w-6 h-6 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        @elseif($notification->icon === 'truck')
                        <svg class="w-6 h-6 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                        @else
                        <svg class="w-6 h-6 text-{{ $notification->color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                            <span class="text-xs text-gray-500 flex-shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700">{{ $notification->message }}</p>
                        
                        <!-- Type Badge -->
                        <div class="mt-3">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Unread Indicator -->
                    @if(!$notification->read)
                    <div class="w-3 h-3 bg-blue-600 rounded-full flex-shrink-0"></div>
                    @endif

                    <!-- Delete Button -->
                    <button 
                        @click.prevent="deleteNotification({{ $notification->id }})"
                        class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 flex-shrink-0"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No notifications yet</h3>
            <p class="text-gray-600">We'll notify you when something important happens</p>
        </div>
        @endif

    </div>
</div>

<script>
function notificationsPage() {
    return {
        activeTab: 'all',
        unreadCount: {{ $notifications->where('read', false)->count() }},

        init() {
            // Any initialization
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
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        async markAllAsRead() {
            if (!confirm('Mark all notifications as read?')) return;

            try {
                await fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                window.location.reload();
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        async deleteNotification(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                window.location.reload();
            } catch (error) {
                console.error('Failed to delete notification:', error);
            }
        },

        async deleteAllRead() {
            if (!confirm('Delete all read notifications?')) return;

            try {
                await fetch('{{ route("notifications.delete-all-read") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                window.location.reload();
            } catch (error) {
                console.error('Failed to delete read notifications:', error);
            }
        }
    }
}
</script>
@endsection