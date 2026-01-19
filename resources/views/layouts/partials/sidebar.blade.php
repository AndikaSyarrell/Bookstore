<aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white shadow-lg overflow-y-auto z-[10]">
    <nav class="p-4">
        <!-- Dashboard Section -->
        <div class="mb-6">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                Main Menu
            </h3>
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('dashboard') 
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            @if (auth()->check() && auth()->user()->role->name === 'master')
            <a href="{{ route('users') }}" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('users') || request()->routeIs('users.*')
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Users</span>
            </a>
            @elseif(auth()->check() && auth()->user()->role->name === 'seller')
            <a href="{{ route('users') }}" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('user') 
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Products</span>
            </a>
            @endif


            <!-- <a href="#" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('posts.*') 
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <span class="font-medium">Posts</span>
            </a> -->
            

        <!-- Content Section -->
        <div class="mb-6">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                Content
            </h3>

            @if(auth()->check() && auth()->user()->role->name === 'seller')
            <a href="{{ route('categories') }}" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('categories') 
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="font-medium">Preorder</span>
            </a>
            @elseif(auth()->check() && auth()->user()->role->name === 'master')
            <a href="{{ route('categories') }}" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('categories.*') || request()->routeIs('categories')
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="font-medium">Categories</span>
            </a>
            @endif
            
        </div>

        <!-- Settings Section -->
        <div class="mb-6">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                Settings
            </h3>
            
            <a href="#" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('settings.general') 
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="font-medium">General</span>
            </a>

            <a href="{{ route('profile') }}" 
               class="flex items-center px-4 py-3 mb-2 rounded-lg transition-colors duration-200
                      {{ request()->routeIs('settings.profile') 
                          ? 'bg-indigo-600 text-white shadow-md' 
                          : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="font-medium">Profile</span>
            </a>
        </div>

        <!-- Logout Button -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="flex items-center w-full px-4 py-3 rounded-lg transition-colors duration-200 text-red-600 hover:bg-red-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
