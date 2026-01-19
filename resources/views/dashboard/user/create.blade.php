@extends('layouts.app')
@section('title', "tambah")

@section('content')
<div x-data="createUser()" class="container mx-auto px-4 py-8">
    <!-- {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="/admin/users" class="p-2 hover:bg-gray-100 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Tambah User Baru</h1>
                <p class="text-gray-600">Lengkapi formulir untuk menambahkan user</p>
            </div>
        </div>
    </div> -->

    {{-- Form Card --}}
    <div class="max-w-1xl mx-auto">
        <form class="bg-white rounded-lg shadow-sm" enctype="multipart/form-data">
            <div class="p-6 space-y-6">
                {{-- Personal Information Section --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        Informasi Pribadi
                    </h2>

                    {{-- Profile Picture --}}
                    <div class="mb-6 flex justify-center">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow-lg">
                                <img
                                    x-show="formData.img"
                                    :src="formData.img"
                                    alt="Profile"
                                    class="w-full h-full object-cover">
                                <div x-show="!formData.img" class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="uploadModalOpen = true"
                                class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Full Name --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input
                                name="name"
                                type="text"
                                x-model="formData.name"
                                @blur="validateField('name')"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="errors.name ? 'border-red-500' : 'border-gray-300'"
                                placeholder="Masukkan nama lengkap">
                            <p x-show="errors.name" class="mt-1 text-sm text-red-500" x-text="errors.name"></p>
                        </div>

                        {{-- Email --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                name="email"
                                type="email"
                                x-model="formData.email"
                                @blur="validateField('email')"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="errors.email ? 'border-red-500' : 'border-gray-300'"
                                placeholder="user@example.com">
                            <p x-show="errors.email" class="mt-1 text-sm text-red-500" x-text="errors.email"></p>
                        </div>

                        {{-- no_telp --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                No. Telepon <span class="text-red-500">*</span>
                            </label>
                            <input
                                name="no_telp"
                                type="number"
                                x-model="formData.no_telp"
                                @blur="validateField('no_telp')"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="errors.no_telp ? 'border-red-500' : 'border-gray-300'"
                                placeholder="08123456789">
                            <p x-show="errors.no_telp" class="mt-1 text-sm text-red-500" x-text="errors.no_telp"></p>
                        </div>

                        {{-- Birth Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Lahir
                            </label>
                            <input
                                name="birth_date"
                                type="date"
                                x-model="formData.birth_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        {{-- Gender --}}
                        <!-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Kelamin
                            </label>
                            <select
                                x-model="formData.gender"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div> -->

                        {{-- Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat
                            </label>
                            <textarea
                                name="address"
                                x-model="formData.address"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Masukkan alamat lengkap"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Account Information Section --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        Informasi Akun
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Status --}}
                        <!-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                x-model="formData.status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div> -->

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    name="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    x-model="formData.password"
                                    @blur="validateField('password')"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
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
                            <p x-show="errors.password" class="mt-1 text-sm text-red-500" x-text="errors.password"></p>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    name="cpassword"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    x-model="formData.password_confirmation"
                                    @blur="validateField('password_confirmation')"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
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

                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="filter"
                                x-model="formData.role"
                                @change="validateField('role')"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="errors.role ? 'border-red-500' : 'border-gray-300'">
                                <option value="">Pilih role</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            <p x-show="errors.role" class="mt-1 text-sm text-red-500" x-text="errors.role"></p>
                        </div>
                    </div>
                </div>

                {{-- Additional Options --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        Opsi Tambahan
                    </h2>

                    <div class="space-y-3">
                        {{-- Send Welcome Email --}}
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                x-model="formData.send_welcome_email"
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Kirim email selamat datang</span>
                                <p class="text-xs text-gray-500">User akan menerima email dengan kredensial login</p>
                            </div>
                        </label>

                        {{-- Require Password Change --}}
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                x-model="formData.require_password_change"
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Wajib ubah password saat login pertama</span>
                                <p class="text-xs text-gray-500">User diminta mengubah password setelah login pertama kali</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex gap-3 justify-end">
                <a
                    href="/admin/users"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>
                <button
                    type="button"
                    @click="submitForm()"
                    :disabled="loading"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan User'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Upload Profile Image Modal --}}
    <div
        x-show="uploadModalOpen"
        @click.self="uploadModalOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div
            @click.stop
            class="bg-white rounded-lg shadow-xl max-w-md w-full"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Upload Foto Profil</h3>
                <button
                    @click="uploadModalOpen = false"
                    class="p-2 hover:bg-gray-100 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                {{-- Preview Area --}}
                <div class="mb-6">
                    <div class="w-48 h-48 mx-auto rounded-full overflow-hidden bg-gray-100 border-4 border-gray-200">
                        <img
                            x-show="previewImage"
                            :src="previewImage"
                            alt="Preview"
                            class="w-full h-full object-cover">
                        <div x-show="!previewImage" class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Upload Area --}}
                <div class="mb-4">
                    <label
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 transition"
                        :class="dragOver ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleDrop($event)">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                            </p>
                            <p class="text-xs text-gray-500">PNG, JPG atau JPEG (MAX. 2MB)</p>
                        </div>
                        <input
                            type="file"
                            class="hidden image-input"
                            accept="image/png,image/jpeg,image/jpg"
                            @change="handleFileSelect($event)">
                    </label>
                    <p x-show="uploadError" class="mt-2 text-sm text-red-500" x-text="uploadError"></p>
                </div>

                {{-- File Info --}}
                <div x-show="selectedFile" class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedFile?.name"></p>
                                <p class="text-xs text-gray-500" x-text="formatFileSize(selectedFile?.size)"></p>
                            </div>
                        </div>
                        <button
                            @click="clearFile()"
                            class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Image Adjustments --}}
                <!-- <div x-show="previewImage" class="mb-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Zoom</label>
                        <input 
                            type="range" 
                            min="100" 
                            max="200" 
                            step="10"
                            x-model="imageZoom"
                            class="w-full"
                        >
                    </div>
                </div> -->
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex gap-3 justify-end">
                <button
                    @click="uploadModalOpen = false; clearFile();"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </button>
                <button
                    @click="saveProfileImage()"
                    :disabled="!selectedFile || uploadingImage"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg x-show="uploadingImage" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="uploadingImage ? 'Uploading...' : 'Simpan'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function createUser() {

        return {
            formData: {
                name: '',
                email: '',
                no_telp: '',
                birth_date: '',
                // gender: '',
                address: '',
                role: '',
                // status: 'active',
                password: '',
                password_confirmation: '',
                send_welcome_email: true,
                require_password_change: false,
                img: ''
            },
            errors: {},
            loading: false,
            showPassword: false,
            showConfirmPassword: false,
            uploadModalOpen: false,
            selectedFile: null,
            previewImage: null,
            uploadError: '',
            dragOver: false,
            imageZoom: 100,
            uploadingImage: false,

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processFile(file);
                }
            },

            handleDrop(event) {
                this.dragOver = false;
                const file = event.dataTransfer.files[0];
                if (file) {
                    this.processFile(file);
                }
            },

            processFile(file) {
                this.uploadError = '';

                // Validate file type
                if (!['image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
                    this.uploadError = 'Format file harus PNG, JPG, atau JPEG';
                    return;
                }

                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    this.uploadError = 'Ukuran file maksimal 2MB';
                    return;
                }

                this.selectedFile = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            clearFile() {
                this.selectedFile = null;
                this.previewImage = null;
                this.uploadError = '';
                this.imageZoom = 100;
            },

            formatFileSize(bytes) {
                if (!bytes) return '';
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            },

            async saveProfileImage() {
                if (!this.selectedFile) return;

                this.uploadingImage = true;

                try {
                    // Simulate upload delay
                    await new Promise(resolve => setTimeout(resolve, 1000));

                    // In production, upload to server here
                    // const formData = new FormData();
                    // formData.append('img', this.selectedFile);
                    // const response = await fetch('/api/upload-profile', {
                    //     method: 'POST',
                    //     body: formData
                    // });

                    // Set the preview image as profile image
                    this.formData.img = this.previewImage;
                    this.uploadModalOpen = false;
                    // this.clearFile();
                } catch (error) {
                    this.uploadError = 'Gagal mengupload gambar: ' + error.message;
                } finally {
                    this.uploadingImage = false;
                }
            },

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
                }
            },

            isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            },

            isValidno_telp(no_telp) {
                return /^(08|62)[0-9]{8,11}$/.test(no_telp.replace(/[\s-]/g, ''));
            },

            validateForm() {
                this.validateField('name');
                this.validateField('email');
                this.validateField('no_telp');
                this.validateField('role');
                this.validateField('password');
                this.validateField('password_confirmation');

                return Object.values(this.errors).every(error => !error);
            },


            async submitForm() {

                if (!this.validateForm()) {
                    alert('Mohon lengkapi semua field yang wajib diisi dengan benar');
                    return;
                }

                this.loading = true;

                try {
                    // 1. Gunakan FormData untuk mengirim File/Image
                    const formDataPayload = new FormData();

                    // Masukkan semua data teks ke FormData
                    for (const key in this.formData) {
                        formDataPayload.append(key, this.formData[key]);
                    }

                    if (this.selectedFile) {
                        formDataPayload.append('img', this.selectedFile); // pastikan key-nya 'img'
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // 2. Lakukan Fetch API
                    const response = await fetch('{{ route("users.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            // PENTING: Jangan set Content-Type secara manual saat menggunakan FormData
                            'Accept': 'application/json'
                        },
                        body: formDataPayload // Kirim sebagai FormData
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert('Data berhasil disimpan!');
                        this.formData = {
                            name: '',
                            email: '',
                            no_telp: '',
                            birth_date: '',
                            address: '',
                            role: '',
                            password: '',
                            password_confirmation: '',
                            send_welcome_email: true,
                            require_password_change: false,
                            img: ''
                        };
                        this.selectedFile = null;
                        this.previewImage = null;
                        this.errors = {};
                        // window.location.href = '/admin/categories';
                    } else {
                        // Menangani error validasi dari Laravel
                        const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                        alert('Gagal: ' + errorMsg);
                    }

                } catch (error) {
                    alert('Terjadi kesalahan koneksi: ' + error.message);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection