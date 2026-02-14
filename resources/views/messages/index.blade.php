@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
            <p class="text-gray-600 mt-2">Your conversations with {{ Auth::user()->role->name === 'buyer' ? 'sellers' : 'buyers' }}</p>
        </div>

        <!-- Chat List -->
        @if($chats->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
            @foreach($chats as $chat)
            @php
                $otherUser = Auth::id() === $chat->buyer_id ? $chat->seller : $chat->buyer;
                $lastMessage = $chat->messages->first();
            @endphp
            
            <a href="{{ route('messages.show', $chat->id) }}" class="block hover:bg-gray-50 transition-colors">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        
                        <!-- Avatar -->
                        <div class="relative flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($otherUser->name, 0, 1) }}
                            </div>
                            @if($chat->unread_count > 0)
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ $chat->unread_count > 9 ? '9+' : $chat->unread_count }}
                            </div>
                            @endif
                        </div>

                        <!-- Chat Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-1">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $otherUser->name }}</h3>
                                    <p class="text-sm text-gray-600">
                                        @if(Auth::user()->role->name === 'buyer')
                                        Seller
                                        @else
                                        Buyer
                                        @endif
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}
                                </span>
                            </div>

                            <!-- Last Message Preview -->
                            @if($lastMessage)
                            <div class="flex items-center gap-2 mt-2">
                                @if($lastMessage->user_id === Auth::id())
                                <span class="text-gray-500 text-sm">You:</span>
                                @endif
                                
                                @if($lastMessage->type === 'text')
                                <p class="text-sm text-gray-600 line-clamp-1 {{ $chat->unread_count > 0 && $lastMessage->user_id !== Auth::id() ? 'font-semibold' : '' }}">
                                    {{ $lastMessage->message }}
                                </p>
                                @elseif($lastMessage->type === 'product_context')
                                <p class="text-sm text-gray-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Sent a product
                                </p>
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Arrow -->
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>

                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No messages yet</h3>
            <p class="text-gray-600 mb-6">
                @if(Auth::user()->role->name === 'buyer')
                Start chatting with sellers by clicking "Chat Seller" on product pages
                @else
                You'll see messages from buyers here when they contact you
                @endif
            </p>
            @if(Auth::user()->role->name === 'buyer')
            <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Browse Products
            </a>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection