{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-screen overflow-hidden bg-gray-50">
    <div x-data="forgotPassword()" class="flex h-full">
        @csrf
        {{-- Left Side - Background Image --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-blue-600 to-purple-700">
            <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1200&h=1600&fit=crop')"></div>

            <div class="relative z-10 flex flex-col justify-center items-start p-16 text-white">
                <div class="mb-8">
                    <h1 class="text-5xl font-bold mb-4">Lupa Password?</h1>
                    <p class="text-xl opacity-90">Jangan khawatir, kami akan mengirimkan link reset password ke email Anda</p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Reset via Email</h3>
                            <p class="opacity-80">Link reset akan dikirim ke email terdaftar Anda</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Aman & Terpercaya</h3>
                            <p class="opacity-80">Link reset hanya valid selama 60 menit</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Proses Cepat</h3>
                            <p class="opacity-80">Email akan dikirim dalam hitungan detik</p>
                        </div>
                    </div>
                </div>

                <div class="mt-auto">
                    <p class="text-sm opacity-70">&copy; 2026 Your Company. All rights reserved.</p>
                </div>
            </div>
        </div>

        {{-- Right Side - Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white overflow-y-auto">
            <div class="w-full max-w-md px-6 sm:px-8 py-8 sm:py-12">
                {{-- Logo --}}
                <div class="text-center mb-6 sm:mb-8">
                    <a href="/" class="inline-block mb-6">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Lupa Password</h2>
                    <p class="text-gray-600 mt-2 text-sm sm:text-base">Masukkan email Anda untuk menerima link reset password</p>
                </div>

                {{-- Success Message --}}
                <div
                    x-show="success"
                    x-transition
                    class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">Email Terkirim!</h3>
                            <p class="text-sm text-green-700 mt-1">
                                Kami telah mengirimkan link reset password ke email Anda. Silakan cek inbox atau folder spam.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Error Message --}}
                <div
                    x-show="error"
                    x-transition
                    class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Terjadi Kesalahan</h3>
                            <p class="text-sm text-red-700 mt-1" x-text="errorMessage"></p>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form @submit.prevent="submitForm()" class="space-y-5">
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input
                                type="email"
                                x-model="email"
                                @blur="validateEmail()"
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 text-sm sm:text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="emailError ? 'border-red-500' : 'border-gray-300'"
                                placeholder="email@example.com"
                                :disabled="loading || success">
                        </div>
                        <p x-show="emailError" class="mt-1 text-xs sm:text-sm text-red-500" x-text="emailError"></p>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        :disabled="loading || success"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-700 text-white py-2.5 sm:py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm sm:text-base">
                        <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Mengirim...' : success ? 'Email Terkirim' : 'Kirim Link Reset'"></span>
                    </button>

                    {{-- Resend Button (after success) --}}
                    <div x-show="success" x-transition class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Tidak menerima email?</p>
                        <button
                            type="button"
                            @click="resendEmail()"
                            :disabled="resendCooldown > 0"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium disabled:text-gray-400 disabled:cursor-not-allowed">
                            <span x-show="resendCooldown === 0">Kirim Ulang</span>
                            <span x-show="resendCooldown > 0" x-text="`Kirim Ulang (${resendCooldown}s)`"></span>
                        </button>
                    </div>
                </form>

                {{-- Back to Login --}}
                <div class="mt-6 text-center">
                    <a href="/login" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Login
                    </a>
                </div>

                {{-- Help Text --}}
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">💡 Tips</h3>
                    <ul class="text-xs text-blue-800 space-y-1">
                        <li>• Pastikan email yang dimasukkan sudah terdaftar</li>
                        <li>• Cek folder spam jika tidak menerima email</li>
                        <li>• Link reset berlaku selama 60 menit</li>
                        <li>• Hubungi admin jika masih bermasalah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function forgotPassword() {
            return {
                email: '',
                emailError: '',
                loading: false,
                success: false,
                error: false,
                errorMessage: '',
                resendCooldown: 0,
                resendInterval: null,

                validateEmail() {
                    this.emailError = '';

                    if (!this.email.trim()) {
                        this.emailError = 'Email wajib diisi';
                        return false;
                    }

                    if (!this.isValidEmail(this.email)) {
                        this.emailError = 'Format email tidak valid';
                        return false;
                    }

                    return true;
                },

                isValidEmail(email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                },

                async submitForm() {

                const csrf = document.querySelector('input[name="_token"]').value;
                        this.error = false;
                        this.errorMessage = '';

                        if (!this.validateEmail()) {
                            return;
                        }

                        this.loading = true;

                        try {
                            const response = await fetch('/forgot-password', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    email: this.email
                                })
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'Gagal mengirim email');
                            }

                            // Success
                            this.success = true;
                            this.startResendCooldown();

                        } catch (err) {
                            this.error = true;
                            this.errorMessage = err.message || 'Terjadi kesalahan. Silakan coba lagi.';
                        } finally {
                            this.loading = false;
                        }
                    },

                async resendEmail() {
                    this.loading = true;
                    this.error = false;
                    this.errorMessage = '';

                    try {
                        // Simulate API call
                        await new Promise(resolve => setTimeout(resolve, 1500));

                        // In production:
                        // const response = await fetch('/forgot-password', {
                        //     method: 'POST',
                        //     headers: {
                        //         'Content-Type': 'application/json',
                        //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        //     },
                        //     body: JSON.stringify({ email: this.email })
                        // });

                        this.startResendCooldown();

                    } catch (err) {
                        this.error = true;
                        this.errorMessage = 'Gagal mengirim ulang email';
                    } finally {
                        this.loading = false;
                    }
                },

                startResendCooldown() {
                    this.resendCooldown = 60;

                    if (this.resendInterval) {
                        clearInterval(this.resendInterval);
                    }

                    this.resendInterval = setInterval(() => {
                        this.resendCooldown--;

                        if (this.resendCooldown <= 0) {
                            clearInterval(this.resendInterval);
                            this.resendInterval = null;
                        }
                    }, 1000);
                }
            }
        }
    </script>
</body>

</html>