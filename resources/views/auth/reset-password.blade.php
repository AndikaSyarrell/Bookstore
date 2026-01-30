<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div x-data="resetPassword('{{ $token }}', '{{ $email }}')" class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Reset Password</h2>
                <p class="text-gray-600 mt-2">Masukkan password baru untuk akun Anda</p>
            </div>

            <!-- Error Alert -->
            <div x-show="error" x-transition class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-700" x-text="errorMessage"></p>
            </div>

            <!-- Success Alert -->
            <div x-show="success" x-transition class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">Password berhasil direset! Redirecting...</p>
            </div>

            <form @submit.prevent="submitForm()" class="space-y-5">
                <input type="hidden" name="token" :value="token">
                <input type="hidden" name="email" :value="email">

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            x-model="password"
                            @input="checkPasswordStrength()"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="errors.password ? 'border-red-500' : 'border-gray-300'"
                            placeholder="Minimal 8 karakter"
                        >
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2"
                        >
                            <svg x-show="!showPassword" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <div x-show="password" class="mt-2">
                        <div class="flex gap-1">
                            <div class="h-1 flex-1 rounded" :class="passwordStrength >= 1 ? (passwordStrength === 1 ? 'bg-red-500' : passwordStrength === 2 ? 'bg-orange-500' : 'bg-green-500') : 'bg-gray-200'"></div>
                            <div class="h-1 flex-1 rounded" :class="passwordStrength >= 2 ? (passwordStrength === 2 ? 'bg-orange-500' : 'bg-green-500') : 'bg-gray-200'"></div>
                            <div class="h-1 flex-1 rounded" :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-200'"></div>
                        </div>
                        <p class="text-xs mt-1" :class="passwordStrength === 1 ? 'text-red-500' : passwordStrength === 2 ? 'text-orange-500' : 'text-green-500'" x-text="passwordStrengthText"></p>
                    </div>
                    <p x-show="errors.password" class="mt-1 text-sm text-red-500" x-text="errors.password"></p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input 
                        type="password"
                        x-model="password_confirmation"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="errors.password_confirmation ? 'border-red-500' : 'border-gray-300'"
                        placeholder="Ulangi password baru"
                    >
                    <p x-show="errors.password_confirmation" class="mt-1 text-sm text-red-500" x-text="errors.password_confirmation"></p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    :disabled="loading || success"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-800 transition disabled:opacity-50"
                >
                    <span x-text="loading ? 'Mereset Password...' : 'Reset Password'"></span>
                </button>
            </form>
        </div>
    </div>

    <script>
    function resetPassword(token, email) {
        return {
            token: token,
            email: email,
            password: '',
            password_confirmation: '',
            showPassword: false,
            passwordStrength: 0,
            passwordStrengthText: '',
            errors: {},
            error: false,
            errorMessage: '',
            success: false,
            loading: false,

            checkPasswordStrength() {
                const password = this.password;
                let strength = 0;

                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password) && /[^a-zA-Z0-9]/.test(password)) strength++;

                this.passwordStrength = strength;
                this.passwordStrengthText = ['', 'Lemah', 'Sedang', 'Kuat'][strength];
            },

            async submitForm() {
                this.errors = {};
                this.error = false;

                if (!this.password) {
                    this.errors.password = 'Password wajib diisi';
                    return;
                }

                if (this.password.length < 8) {
                    this.errors.password = 'Password minimal 8 karakter';
                    return;
                }

                if (this.password !== this.password_confirmation) {
                    this.errors.password_confirmation = 'Password tidak cocok';
                    return;
                }

                this.loading = true;

                try {
                    const response = await fetch('/reset-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            token: this.token,
                            email: this.email,
                            password: this.password,
                            password_confirmation: this.password_confirmation
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message);
                    }

                    this.success = true;
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);

                } catch (err) {
                    this.error = true;
                    this.errorMessage = err.message;
                } finally {
                    this.loading = false;
                }
            }
        }
    }
    </script>
</body>
</html>