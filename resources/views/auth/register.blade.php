{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-screen overflow-hidden  bg-repeat">
    <div x-data="registration()" class="flex h-full">
        {{-- Left Side - Background Image --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-[url('{{ asset('images/dummy5.jpg') }}')] bg-cover bg-center">
            <div class="relative z-10 flex flex-col justify-center items-start p-16 text-white">
                <div class="mb-8">
                    <h1 class="text-5xl font-bold mb-4">Selamat Datang!</h1>
                    <p class="text-xl opacity-90">Bergabunglah dengan ribuan pengguna lainnya</p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Akses Mudah</h3>
                            <p class="opacity-80">Kelola semua kebutuhan Anda dalam satu platform</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Keamanan Terjamin</h3>
                            <p class="opacity-80">Data Anda dilindungi dengan enkripsi tingkat tinggi</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-white bg-opacity-20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Performa Cepat</h3>
                            <p class="opacity-80">Pengalaman pengguna yang responsif dan efisien</p>
                        </div>
                    </div>
                </div>

                <div class="mt-auto">
                    <p class="text-sm opacity-70">&copy; 2026 Your Company. All rights reserved.</p>
                </div>
            </div>
        </div>

        {{-- Right Side - Registration Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white overflow-y-auto mb-10 lg:mb-0">
            <div class="w-full max-w-xl px-8 py-12 h-full">
                {{-- Logo --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800">Buat Akun</h2>
                    <p class="text-gray-600 mt-2">Isi formulir di bawah untuk memulai</p>
                </div>

                {{-- Registration Form --}}
                <form @submit.prevent="submitForm()" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Full Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                x-model="formData.name"
                                @blur="validateField('name')"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="errors.name ? 'border-red-500' : 'border-gray-300'"
                                placeholder="John Doe">
                            <p x-show="errors.name" class="mt-1 text-sm text-red-500" x-text="errors.name"></p>
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Daftar Sebagai
                            </label>
                            <select
                                x-model="formData.role"
                                @change="validateField('role')"
                                class="w-full px-4 bg-white py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="errors.role ? 'border-red-500' : 'border-gray-300'">
                                <option value="">Pilih Peran Akun Anda</option>
                                @foreach ($roles as $r)
                                <option value="{{ $r->id }}">{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                            <p x-show="errors.role" class="mt-1 text-sm text-red-500" x-text="errors.role"></p>
                        </div>
                    </div>

                    {{-- Email & no_telp --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input
                                type="email"
                                x-model="formData.email"
                                @blur="validateField('email')"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="errors.email ? 'border-red-500' : 'border-gray-300'"
                                placeholder="email@example.com">
                            <p x-show="errors.email" class="mt-1 text-sm text-red-500" x-text="errors.email"></p>
                        </div>

                        {{-- no_telp --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                No. Telepon
                            </label>
                            <input
                                type="tel"
                                x-model="formData.no_telp"
                                @blur="validateField('no_telp')"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="errors.no_telp ? 'border-red-500' : 'border-gray-300'"
                                placeholder="08123456789">
                            <p x-show="errors.no_telp" class="mt-1 text-sm text-red-500" x-text="errors.no_telp"></p>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                x-model="formData.password"
                                @input="checkPasswordStrength()"
                                @blur="validateField('password')"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-10"
                                :class="errors.password ? 'border-red-500' : 'border-gray-300'"
                                placeholder="Minimal 8 karakter">
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Password Strength Indicator --}}
                        <div x-show="formData.password" class="mt-2">
                            <div class="flex gap-1 mb-1">
                                <div class="h-1 flex-1 rounded" :class="passwordStrength >= 1 ? (passwordStrength === 1 ? 'bg-red-500' : passwordStrength === 2 ? 'bg-orange-500' : 'bg-green-500') : 'bg-gray-200'"></div>
                                <div class="h-1 flex-1 rounded" :class="passwordStrength >= 2 ? (passwordStrength === 2 ? 'bg-orange-500' : 'bg-green-500') : 'bg-gray-200'"></div>
                                <div class="h-1 flex-1 rounded" :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-200'"></div>
                            </div>
                            <p class="text-xs" :class="passwordStrength === 1 ? 'text-red-500' : passwordStrength === 2 ? 'text-orange-500' : 'text-green-500'" x-text="passwordStrengthText"></p>
                        </div>

                        <p x-show="errors.password" class="mt-1 text-sm text-red-500" x-text="errors.password"></p>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                x-model="formData.password_confirmation"
                                @blur="validateField('password_confirmation')"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-10"
                                :class="errors.password_confirmation ? 'border-red-500' : 'border-gray-300'"
                                placeholder="Ulangi password">
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p x-show="errors.password_confirmation" class="mt-1 text-sm text-red-500" x-text="errors.password_confirmation"></p>
                    </div>

                    {{-- Terms & Conditions --}}
                    <div>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                x-model="formData.agree_terms"
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-0.5">
                            <span class="text-sm text-gray-600">
                                Saya setuju dengan
                                <a href="/terms" class="text-blue-600 hover:underline">Syarat & Ketentuan</a>
                                dan
                                <a href="/privacy" class="text-blue-600 hover:underline">Kebijakan Privasi</a>
                            </span>
                        </label>
                        <p x-show="errors.agree_terms" class="mt-1 text-sm text-red-500" x-text="errors.agree_terms"></p>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Mendaftar...' : 'Daftar Sekarang'"></span>
                    </button>
                </form>

                {{-- Login Link --}}
                <p class="text-center text-sm text-gray-600 mt-6 ">
                    Sudah punya akun?
                    <a href="/login" class="text-blue-600 hover:underline font-semibold">Login di sini</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function registration() {
            return {
                formData: {
                    name: '',
                    email: '',
                    no_telp: '',
                    password: '',
                    password_confirmation: '',
                    agree_terms: false,
                    role: ''
                },
                errors: {},
                loading: false,
                showPassword: false,
                showConfirmPassword: false,
                passwordStrength: 0,
                passwordStrengthText: '',

                validateField(field) {
                    this.errors[field] = '';

                    switch (field) {
                        case 'name':
                            if (!this.formData.name.trim()) {
                                this.errors.name = 'Nama lengkap wajib diisi';
                            } else if (this.formData.name.length < 3) {
                                this.errors.name = 'Nama minimal 3 karakter';
                            }
                            break;

                        case 'email':
                            if (!this.formData.email.trim()) {
                                this.errors.email = 'Email wajib diisi';
                            } else if (!this.isValidEmail(this.formData.email)) {
                                this.errors.email = 'Format email tidak valid';
                            }
                            break;

                        case 'no_telp':
                            if (!this.formData.no_telp.trim()) {
                                this.errors.no_telp = 'No. telepon wajib diisi';
                            } else if (!this.isValidno_telp(this.formData.no_telp)) {
                                this.errors.no_telp = 'Format no. telepon tidak valid';
                            }
                            break;

                        case 'role':
                            if (!this.formData.role) {
                                this.errors.role = 'Role wajib dipilih';
                            }
                            break;

                        case 'password':
                            if (!this.formData.password) {
                                this.errors.password = 'Password wajib diisi';
                            } else if (this.formData.password.length < 8) {
                                this.errors.password = 'Password minimal 8 karakter';
                            }
                            break;

                        case 'password_confirmation':
                            if (!this.formData.password_confirmation) {
                                this.errors.password_confirmation = 'Konfirmasi password wajib diisi';
                            } else if (this.formData.password !== this.formData.password_confirmation) {
                                this.errors.password_confirmation = 'Password tidak cocok';
                            }
                            break;

                        case 'agree_terms':
                            if (!this.formData.agree_terms) {
                                this.errors.agree_terms = 'Anda harus menyetujui syarat & ketentuan';
                            }
                            break;
                    }
                },

                isValidEmail(email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                },

                isValidno_telp(no_telp) {
                    return /^(08|62)[0-9]{8,11}$/.test(no_telp.replace(/[\s-]/g, ''));
                },

                checkPasswordStrength() {
                    const password = this.formData.password;
                    let strength = 0;

                    if (password.length >= 8) strength++;
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                    if (/[0-9]/.test(password) && /[^a-zA-Z0-9]/.test(password)) strength++;

                    this.passwordStrength = strength;

                    if (strength === 1) {
                        this.passwordStrengthText = 'Password lemah';
                    } else if (strength === 2) {
                        this.passwordStrengthText = 'Password sedang';
                    } else if (strength === 3) {
                        this.passwordStrengthText = 'Password kuat';
                    }
                },

                validateForm() {
                    this.validateField('name');
                    this.validateField('email');
                    this.validateField('no_telp');
                    this.validateField('role');
                    this.validateField('password');
                    this.validateField('password_confirmation');
                    this.validateField('agree_terms');

                    return Object.values(this.errors).every(error => !error);
                },

                async submitForm() {
                    if (!this.validateForm()) {
                        return;
                    }

                    this.loading = true;

                    const formPayLoad = new FormData();

                    // Masukkan semua data teks ke FormData
                    for (const key in this.formData) {
                        formPayLoad.append(key, this.formData[key]);
                    }

                    try {
                        // Simulate API call
                        await new Promise(resolve => setTimeout(resolve, 2000));

                        const csrf = document.querySelector('input[name="_token"]').value;;

                        // In production, replace with actual API call:
                        const response = await fetch("{{route('register.post')}}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: formPayLoad
                        });

                        // 1. Ambil teks mentah dulu untuk didebug jika bukan JSON
                        const text = await response.text();

                        let result;
                        try {
                            result = JSON.parse(text); // Coba ubah ke JSON
                        } catch (e) {
                            console.error("Server tidak mengirim JSON. Ini isinya:", text);
                            alert("Terjadi kesalahan server (bukan JSON). Cek Console log.");
                            return;
                        }

                        if (response.ok) {
                            alert('Registrasi berhasil! Silakan login.');
                            window.location.href = "{{ route('login') }}";
                        } else {
                            // Menangani error validasi (422) atau error lainnya
                            const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                            alert('Gagal: ' + errorMsg);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan: ' + error.message);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>

</html>