{{-- resources/views/seller/products/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div x-data="createProduct()" class="container mx-auto px-4 py-8">
        {{-- Form Card --}}
        <div class="max-w-1xl mx-auto">
            <form @submit.prevent="submitForm()" class="bg-white rounded-lg shadow-sm">
                @csrf
                <div class="p-6 space-y-6">
                    {{-- Product Image Section --}}
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                            Foto Produk
                        </h2>

                        {{-- Single Image Upload --}}
                        <div class="flex justify-center mb-4">
                            <div x-show="!productImage" class="w-full max-w-sm">
                                <label
                                    class="block w-full aspect-square bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                                    <svg class="w-16 h-16 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600 mb-1">Klik untuk upload foto</span>
                                    <span class="text-xs text-gray-500">PNG, JPG atau JPEG (Max. 2MB)</span>
                                    <input type="file" class="hidden" accept="image/png,image/jpeg,image/jpg"
                                        @change="handleImageUpload($event)">
                                </label>
                            </div>

                            <div x-show="productImage" class="w-full max-w-sm relative">
                                <div class="aspect-square bg-gray-100 rounded-lg border-2 border-gray-300 overflow-hidden">
                                    <img :src="productImage" class="w-full h-full object-cover">
                                </div>
                                <button type="button" @click="removeImage()"
                                    class="absolute top-3 right-3 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 text-center">Upload 1 foto produk <span
                                class="text-red-500">*</span></p>
                    </div>

                    {{-- Basic Information Section --}}
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                            Informasi Dasar
                        </h2>

                        <div class="space-y-4">
                            {{-- Product Name --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="formData.title" @blur="validateField('title')"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="errors.title ? 'border-red-500' : 'border-gray-300'"
                                    placeholder="Contoh: Laptop Gaming ASUS ROG">
                                <p x-show="errors.title" class="mt-1 text-sm text-red-500" x-text="errors.title"></p>
                            </div>

                            {{-- Category & author --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="formData.category" @change="validateField('category')"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="errors.category ? 'border-red-500' : 'border-gray-300'">
                                        <option value="">Pilih kategori</option>
                                        @foreach ($categories as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    <p x-show="errors.category" class="mt-1 text-sm text-red-500" x-text="errors.category">
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Author
                                    </label>
                                    <input type="text" x-model="formData.author"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Contoh: Samsung, Nike, dll">
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi Produk <span class="text-red-500">*</span>
                                </label>
                                <textarea x-model="formData.description" @blur="validateField('description')" rows="5"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="errors.description ? 'border-red-500' : 'border-gray-300'"
                                    placeholder="Jelaskan detail produk, spesifikasi, kondisi, dan informasi penting lainnya..."></textarea>
                                <div class="flex justify-between items-center mt-1">
                                    <p x-show="errors.description" class="text-sm text-red-500" x-text="errors.description">
                                    </p>
                                    <p class="text-sm text-gray-500" x-text="`${formData.description.length}/500`"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing & Stock Section --}}
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                            Harga & Stok
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Price --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="number" x-model="formData.price" @blur="validateField('price')"
                                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="errors.price ? 'border-red-500' : 'border-gray-300'" placeholder="0">
                                </div>
                                <p x-show="errors.price" class="mt-1 text-sm text-red-500" x-text="errors.price"></p>
                            </div>

                            {{-- Stock --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stok <span class="text-red-500">*</span>
                                </label>
                                <input type="number" x-model="formData.stock" @blur="validateField('stock')"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="errors.stock ? 'border-red-500' : 'border-gray-300'" placeholder="0">
                                <p x-show="errors.stock" class="mt-1 text-sm text-red-500" x-text="errors.stock"></p>
                            </div>

                            {{-- SKU --}}
                            {{-- <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    SKU
                                </label>
                                <input type="text" x-model="formData.sku"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="SKU-001">
                            </div> --}}
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div
                    class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('products') }}"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-center">
                        Back
                    </a>
                    <!-- <button type="button" @click="saveDraft()"
                        class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition">
                        Simpan Draft
                    </button> -->
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="loading ? 'Menyimpan...' : 'Publish Produk'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function createProduct() {
            return {
                formData: {
                    title: '{{ $p->title }}',
                    category: '{{ $p->category_id }}',
                    author: '{{ $p->author }}',
                    description: '{{ $p->description }}',
                    price: '{{ $p->price }}',
                    stock: '{{ $p->stock }}'
                },
                productImage: `{{ $p->img ? asset('storage/products/' . $p->img) : null }}`,
                productImagePreview: null,
                errors: {},
                loading: false,

                validateField(field) {
                    this.errors[field] = '';

                    switch (field) {
                        case 'title':
                            if (!this.formData.title.trim()) {
                                this.errors.title = 'Nama produk wajib diisi';
                            }
                            break;

                        case 'category':
                            if (!this.formData.category) {
                                this.errors.category = 'Kategori wajib dipilih';
                            }
                            break;
                            
                        case 'price':
                            if (!this.formData.price) {
                                this.errors.price = 'Harga wajib diisi';
                            }
                            break;

                        case 'stock':
                            if (this.formData.stock === '' || this.formData.stock < 0) {
                                this.errors.stock = 'Stok tidak valid';
                            }
                            break;
                    }
                },

                handleImageUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (!file.type.startsWith('image/')) {
                        alert('File harus berupa gambar');
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file maksimal 2MB');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.productImage = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    event.target.value = '';

                },

                removeImage() {
                    this.productImage = null;
                },

                validateForm() {
                    ['title', 'category', 'description', 'price', 'stock']
                        .forEach(field => this.validateField(field));

                    if (!this.productImage) {
                        alert('Foto produk wajib diupload');
                        return false;
                    }

                    return Object.values(this.errors).every(e => !e);
                },

                async submitForm() {
                    if (!this.validateForm()) return;

                    this.loading = true;

                    try {
                        const csrf = document.querySelector('input[name="_token"]').value;

                        const data = new FormData();

                        Object.entries(this.formData).forEach(([key, value]) => {
                            data.append(key, value);
                        });

                        data.append('img', this.productImage);

                        const response = await fetch("{{ route('products.update', $p->id) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: data
                        });

                        if (!response.ok) {
                            const err = await response.json();
                            throw new Error(err.message || 'Gagal menyimpan produk');
                        }

                        alert('Produk berhasil dipublish!');
                        location.reload();

                    } catch (error) {
                        alert(error.message);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>

@endsection