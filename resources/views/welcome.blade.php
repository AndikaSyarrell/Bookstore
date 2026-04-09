<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}} - Toko Buku Online</title>
    @vite('resources/css/app.css')
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-2xl font-bold text-indigo-600">{{config('app.name')}}</h1>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="#home" class="text-gray-900 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Beranda</a>
                        <a href="#books" class="text-gray-500 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Buku</a>
                        <a href="#categories" class="text-gray-500 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Kategori</a>
                        <a href="#contact" class="text-gray-500 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Kontak</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-20 bg-gradient-to-r from-indigo-600 to-purple-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        Temukan Dunia Pengetahuan
                    </h1>
                    <p class="text-lg text-indigo-100 mb-8">
                        Koleksi ribuan buku terbaik dari berbagai genre. Dari fiksi hingga non-fiksi, semuanya tersedia untuk Anda.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#books" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Jelajahi Buku
                        </a>
                        <a href="{{ route('register') }}" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Books" class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books -->
    <section id="books" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Buku Terlaris</h2>
                <p class="text-gray-600">Koleksi buku terbaik pilihan pembaca</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Book Card 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Book" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">The Great Novel</h3>
                        <p class="text-gray-600 text-sm mb-2">John Doe</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                ★★★★★
                            </div>
                            <span class="text-sm text-gray-500 ml-2">(128)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-600 font-bold text-xl">Rp 125.000</span>
                            <button class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition text-sm">
                                Beli
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Book Card 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Book" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Mystery of the Night</h3>
                        <p class="text-gray-600 text-sm mb-2">Jane Smith</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                ★★★★☆
                            </div>
                            <span class="text-sm text-gray-500 ml-2">(95)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-600 font-bold text-xl">Rp 98.000</span>
                            <button class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition text-sm">
                                Beli
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Book Card 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="https://images.unsplash.com/photo-1495446815901-a7297e633e8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Book" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Science Explorer</h3>
                        <p class="text-gray-600 text-sm mb-2">Dr. Albert Wong</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                ★★★★★
                            </div>
                            <span class="text-sm text-gray-500 ml-2">(204)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-600 font-bold text-xl">Rp 150.000</span>
                            <button class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition text-sm">
                                Beli
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Book Card 4 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Book" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Cooking Masterclass</h3>
                        <p class="text-gray-600 text-sm mb-2">Chef Gordon</p>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                ★★★★☆
                            </div>
                            <span class="text-sm text-gray-500 ml-2">(67)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-600 font-bold text-xl">Rp 175.000</span>
                            <button class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition text-sm">
                                Beli
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section id="categories" class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Kategori Buku</h2>
                <p class="text-gray-600">Temukan buku favorit Anda berdasarkan kategori</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="text-4xl mb-3"></div>
                    <h3 class="font-semibold text-gray-900">Fiksi</h3>
                    <p class="text-sm text-gray-600">248 buku</p>
                </div>
                <div class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="text-4xl mb-3"></div>
                    <h3 class="font-semibold text-gray-900">Sains</h3>
                    <p class="text-sm text-gray-600">156 buku</p>
                </div>
                <div class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="text-4xl mb-3"></div>
                    <h3 class="font-semibold text-gray-900">Teknologi</h3>
                    <p class="text-sm text-gray-600">98 buku</p>
                </div>
                <div class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition cursor-pointer">
                    <div class="text-4xl mb-3"></div>
                    <h3 class="font-semibold text-gray-900">Seni</h3>
                    <p class="text-sm text-gray-600">67 buku</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{config('app.name')}}</h3>
                    <p class="text-gray-400">Toko buku online terpercaya sejak 2024</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#home" class="hover:text-white">Beranda</a></li>
                        <li><a href="#books" class="hover:text-white">Buku</a></li>
                        <li><a href="#categories" class="hover:text-white">Kategori</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">Facebook</a>
                        <a href="#" class="text-gray-400 hover:text-white">Instagram</a>
                        <a href="#" class="text-gray-400 hover:text-white">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 {{config('app.name')}}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>