@extends('layouts.app')

@section('Title', 'Buyer Profile')

@section('content')
<div x-data="profilePage()" x-init="init()" class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-600 mt-2">Manage your personal information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sidebar - Profile Photo & Quick Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    
                    <!-- Profile Photo -->
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow-lg">
                                <img 
                                    :src="profilePhoto || 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=128&background=3B82F6&color=fff'"
                                    alt="Profile Photo"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                            
                            <!-- Upload Button Overlay -->
                            <label class="absolute bottom-0 right-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-700 transition-colors shadow-lg">
                                <input 
                                    type="file" 
                                    @change="uploadPhoto($event)"
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="hidden"
                                >
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </label>
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 mt-4" x-text="formData.name"></h2>
                        <p class="text-sm text-gray-500" x-text="formData.email"></p>
                        
                        <!-- Delete Photo Button -->
                        <button 
                            x-show="profilePhoto"
                            @click="deletePhoto()"
                            class="mt-3 text-sm text-red-600 hover:text-red-700 font-medium"
                        >
                            Remove Photo
                        </button>
                    </div>

                    <!-- Quick Stats -->
                    <div class="border-t border-gray-200 pt-6 space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-gray-600" x-text="formData.no_telp || 'No phone number'"></span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-gray-600" x-text="formData.city || 'No city'"></span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-600" x-text="formData.birth_date || 'No birth date'"></span>
                        </div>
                    </div>

                    <!-- Navigation Menu -->
                    <div class="border-t border-gray-200 pt-6 mt-6 space-y-2">
                        <button 
                            @click="activeTab = 'personal'"
                            :class="activeTab === 'personal' ? 'bg-blue-50 text-blue-600 border-blue-600' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors border border-transparent"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Personal Information
                        </button>
                        <button 
                            @click="activeTab = 'security'"
                            :class="activeTab === 'security' ? 'bg-blue-50 text-blue-600 border-blue-600' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors border border-transparent"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Security
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-2">
                
                <!-- Personal Information Tab -->
                <div x-show="activeTab === 'personal'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Personal Information</h2>

                    <form @submit.prevent="updateProfile()">
                        <div class="space-y-6">
                            
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.name"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="John Doe"
                                >
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    x-model="formData.email"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="john@example.com"
                                >
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    x-model="formData.no_telp"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="08123456789"
                                >
                            </div>

                            <!-- Birth Date & Gender -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Birth Date</label>
                                    <input 
                                        type="date" 
                                        x-model="formData.birth_date"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select 
                                        x-model="formData.gender"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea 
                                    x-model="formData.address"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Street address, building, apartment..."
                                ></textarea>
                            </div>

                            <!-- City, Province, Postal Code -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <input 
                                        type="text" 
                                        x-model="formData.city"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Jakarta"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                    <input 
                                        type="text" 
                                        x-model="formData.province"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="DKI Jakarta"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                    <input 
                                        type="text" 
                                        x-model="formData.postal_code"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="12345"
                                    >
                                </div>
                            </div>

                            <!-- Bio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                                <textarea 
                                    x-model="formData.bio"
                                    rows="4"
                                    maxlength="1000"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Tell us about yourself..."
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-1" x-text="`${(formData.bio || '').length}/1000 characters`"></p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex gap-4 pt-4">
                                <button 
                                    type="submit"
                                    :disabled="isSaving"
                                    class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                                >
                                    <span x-show="!isSaving">Save Changes</span>
                                    <span x-show="isSaving" class="flex items-center justify-center">
                                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Saving...
                                    </span>
                                </button>
                                <button 
                                    type="button"
                                    @click="loadUserData()"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Security Tab -->
                <div x-show="activeTab === 'security'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Security Settings</h2>

                    <form @submit.prevent="updatePassword()">
                        <div class="space-y-6">
                            
                            <!-- Current Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Current Password <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    x-model="passwordData.current_password"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter current password"
                                >
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    New Password <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    x-model="passwordData.new_password"
                                    required
                                    minlength="8"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter new password (min. 8 characters)"
                                >
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm New Password <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    x-model="passwordData.new_password_confirmation"
                                    required
                                    minlength="8"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Confirm new password"
                                >
                            </div>

                            <!-- Password Requirements -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-900 mb-2">Password Requirements:</h4>
                                <ul class="text-sm text-blue-700 space-y-1">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Minimum 8 characters
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Use a strong, unique password
                                    </li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button 
                                    type="submit"
                                    :disabled="isUpdatingPassword"
                                    class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                                >
                                    <span x-show="!isUpdatingPassword">Update Password</span>
                                    <span x-show="isUpdatingPassword" class="flex items-center justify-center">
                                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Updating...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function profilePage() {
    return {
        activeTab: 'personal',
        isSaving: false,
        isUpdatingPassword: false,
        profilePhoto: null,
        
        formData: {
            name: '{{ $user->name }}',
            email: '{{ $user->email }}',
            no_telp: '{{ $user->no_telp }}',
            birth_date: '{{ $user->birth_date }}',
            gender: '{{ $user->gender }}',
            address: '{{ $user->address }}',
            city: '{{ $user->city }}',
            province: '{{ $user->province }}',
            postal_code: '{{ $user->postal_code }}',
            bio: '{{ $user->bio }}',
        },

        passwordData: {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        },

        init() {
            this.loadUserData();
        },

        loadUserData() {
            // Load from server or use blade data
            this.profilePhoto = '{{ $user->img ? asset("storage/users/" . $user->img) : "" }}' || null;
        },

        async updateProfile() {
            this.isSaving = true;

            try {
                const response = await fetch('{{ route("Bprofile.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('Profile updated successfully!', 'success');
                } else {
                    this.showNotification(data.message || 'Failed to update profile', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('An error occurred', 'error');
            } finally {
                this.isSaving = false;
            }
        },

        async uploadPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                this.showNotification('File size must be less than 2MB', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('photo', file);

            try {
                const response = await fetch('{{ route("Bprofile.update-photo") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.profilePhoto = data.photo_url;
                    this.showNotification('Profile photo updated!', 'success');
                } else {
                    this.showNotification(data.message || 'Failed to upload photo', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('An error occurred', 'error');
            }
        },

        async deletePhoto() {
            if (!confirm('Are you sure you want to remove your profile photo?')) return;

            try {
                const response = await fetch('{{ route("Bprofile.remove-photo") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.profilePhoto = null;
                    this.showNotification('Profile photo removed', 'success');
                } else {
                    this.showNotification(data.message || 'Failed to delete photo', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('An error occurred', 'error');
            }
        },

        async updatePassword() {
            if (this.passwordData.new_password !== this.passwordData.new_password_confirmation) {
                this.showNotification('Passwords do not match', 'error');
                return;
            }

            this.isUpdatingPassword = true;

            try {
                const response = await fetch('{{ route("Bprofile.update-password") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.passwordData)
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('Password updated successfully!', 'success');
                    // Reset form
                    this.passwordData = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: ''
                    };
                } else {
                    this.showNotification(data.message || 'Failed to update password', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('An error occurred', 'error');
            } finally {
                this.isUpdatingPassword = false;
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-2xl text-white z-50 ${bgColor}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }
}
</script>
@endsection