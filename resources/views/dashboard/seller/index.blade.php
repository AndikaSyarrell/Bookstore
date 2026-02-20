@extends('layouts.app')

@section('Tittle', 'Profile')
@section('content')
<div x-data="sellerProfile()" x-init="init()" class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Seller Profile</h1>
            <p class="text-gray-600 mt-2">Manage your profile and bank accounts</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Sidebar - Stats & Photo -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Profile Photo -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Profile Photo</h3>
                    
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            <img 
                                :src="photoPreview || '{{ Auth::user()->img ? asset("storage/profile/" . Auth::user()->img) : "https://ui-avatars.com/api/?name=" . urlencode(Auth::user()->name) }}'"
                                alt="Profile"
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"
                            >
                            <label class="absolute bottom-0 right-0 p-2 bg-blue-600 text-white rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input type="file" @change="handlePhotoChange($event)" accept="image/*" class="hidden">
                            </label>
                        </div>
                        <p class="text-sm text-gray-600 mt-3">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Statistics</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Products</span>
                            <span class="font-bold text-gray-900">{{ $stats['total_products'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Active Products</span>
                            <span class="font-bold text-green-600">{{ $stats['active_products'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Orders</span>
                            <span class="font-bold text-gray-900">{{ $stats['total_orders'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Pending Orders</span>
                            <span class="font-bold text-yellow-600">{{ $stats['pending_orders'] }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Profile Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Profile Information</h3>
                        <button 
                            @click="editMode.profile = !editMode.profile"
                            class="text-sm text-blue-600 hover:text-blue-700"
                        >
                            <span x-show="!editMode.profile">Edit</span>
                            <span x-show="editMode.profile">Cancel</span>
                        </button>
                    </div>

                    <form @submit.prevent="updateProfile()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input 
                                    type="text" 
                                    x-model="profileData.name"
                                    :disabled="!editMode.profile"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                                    required
                                >
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input 
                                    type="email" 
                                    x-model="profileData.email"
                                    :disabled="!editMode.profile"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                                    required
                                >
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input 
                                    type="text" 
                                    x-model="profileData.no_telp"
                                    :disabled="!editMode.profile"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                                    required
                                >
                            </div>

                            <!-- Birth Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Birth Date</label>
                                <input 
                                    type="date" 
                                    x-model="profileData.birth_date"
                                    :disabled="!editMode.profile"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                                >
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea 
                                    x-model="profileData.address"
                                    :disabled="!editMode.profile"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                                ></textarea>
                            </div>
                        </div>

                        <div x-show="editMode.profile" class="mt-6 flex gap-3">
                            <button 
                                type="submit"
                                :disabled="isSaving"
                                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
                            >
                                <span x-show="!isSaving">Save Changes</span>
                                <span x-show="isSaving">Saving...</span>
                            </button>
                            <button 
                                type="button"
                                @click="editMode.profile = false; loadProfile()"
                                class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Change Password</h3>

                    <form @submit.prevent="changePassword()">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input 
                                    type="password" 
                                    x-model="passwordData.current_password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input 
                                    type="password" 
                                    x-model="passwordData.new_password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                    minlength="8"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input 
                                    type="password" 
                                    x-model="passwordData.new_password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </div>
                        </div>

                        <div class="mt-6">
                            <button 
                                type="submit"
                                :disabled="isChangingPassword"
                                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
                            >
                                <span x-show="!isChangingPassword">Change Password</span>
                                <span x-show="isChangingPassword">Changing...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Bank Accounts -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Bank Accounts</h3>
                        <button 
                            @click="showAddBankModal = true"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 text-sm"
                        >
                            + Add Bank Account
                        </button>
                    </div>

                    @if($bankAccounts->count() > 0)
                    <div class="space-y-3">
                        @foreach($bankAccounts as $account)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-2xl">{{ $account->bank_logo }}</span>
                                        <h4 class="font-semibold text-gray-900">{{ $account->bank_name }}</h4>
                                        @if($account->is_primary)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                            Primary
                                        </span>
                                        @endif
                                        @if($account->is_verified)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                            </svg>
                                            Verified
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $account->account_number }}</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ $account->account_holder_name }}</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if(!$account->is_primary)
                                    <button 
                                        @click="setPrimary({{ $account->id }})"
                                        class="text-sm text-blue-600 hover:text-blue-700"
                                    >
                                        Set Primary
                                    </button>
                                    @endif
                                    <button 
                                        @click="editBank({{ $account->id }})"
                                        class="p-2 text-gray-600 hover:text-blue-600"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="deleteBank({{ $account->id }})"
                                        class="p-2 text-gray-600 hover:text-red-600"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <p class="text-gray-600 mb-4">No bank accounts added yet</p>
                        <button 
                            @click="showAddBankModal = true"
                            class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700"
                        >
                            Add Your First Bank Account
                        </button>
                    </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
    <!-- Add/Edit Bank Modal -->
    <div 
        x-show="showAddBankModal || showEditBankModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showAddBankModal = false; showEditBankModal = false"></div>
            
            <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">
                    <span x-show="showAddBankModal">Add Bank Account</span>
                    <span x-show="showEditBankModal">Edit Bank Account</span>
                </h3>
                
                <form @submit.prevent="saveBankAccount()">
                    <!-- Bank Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                        <select 
                            x-model="bankData.bank_name"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select bank...</option>
                            @foreach(\App\Models\BankAccount::getAvailableBanks() as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
    
                    <!-- Account Number -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                        <input 
                            type="text" 
                            x-model="bankData.account_number"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="1234567890"
                        >
                    </div>
    
                    <!-- Account Holder Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name</label>
                        <input 
                            type="text" 
                            x-model="bankData.account_holder_name"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="As per bank records"
                        >
                    </div>
    
                    <!-- Primary Account -->
                    <div x-show="showAddBankModal" class="mb-6">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="bankData.is_primary" class="rounded">
                            <span class="text-sm text-gray-700">Set as primary account</span>
                        </label>
                    </div>
    
                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button 
                            type="submit"
                            :disabled="isSavingBank"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
                        >
                            <span x-show="!isSavingBank">Save</span>
                            <span x-show="isSavingBank">Saving...</span>
                        </button>
                        <button 
                            type="button"
                            @click="showAddBankModal = false; showEditBankModal = false"
                            class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
function sellerProfile() {
    return {
        editMode: {
            profile: false,
        },
        isSaving: false,
        isChangingPassword: false,
        isSavingBank: false,
        photoPreview: null,
        showAddBankModal: false,
        showEditBankModal: false,
        editingBankId: null,
        
        profileData: {
            name: '{{ Auth::user()->name }}',
            email: '{{ Auth::user()->email }}',
            no_telp: '{{ Auth::user()->no_telp }}',
            address: '{{ Auth::user()->address }}',
            birth_date: '{{ Auth::user()->birth_date }}',
        },

        passwordData: {
            current_password: '',
            new_password: '',
            new_password_confirmation: '',
        },

        bankData: {
            bank_name: '',
            account_number: '',
            account_holder_name: '',
            is_primary: false,
        },

        init() {
            // Initialization
        },

        loadProfile() {
            this.profileData = {
                name: '{{ Auth::user()->name }}',
                email: '{{ Auth::user()->email }}',
                no_telp: '{{ Auth::user()->no_telp }}',
                address: '{{ Auth::user()->address }}',
                birth_date: '{{ Auth::user()->birth_date }}',
            };
        },

        async handlePhotoChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload
            const formData = new FormData();
            formData.append('photo', file);

            try {
                const response = await fetch('{{route("sellerProfile.photo")}}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('Photo updated successfully');
                } else {
                    alert(data.message);
                    this.photoPreview = null;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to upload photo');
                this.photoPreview = null;
            }
        },

        async updateProfile() {
            this.isSaving = true;

            try {
                const response = await fetch('{{route("sellerProfile.update")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.profileData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Profile updated successfully');
                    this.editMode.profile = false;
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            } finally {
                this.isSaving = false;
            }
        },

        async changePassword() {
            if (this.passwordData.new_password !== this.passwordData.new_password_confirmation) {
                alert('Passwords do not match');
                return;
            }

            this.isChangingPassword = true;

            try {
                const response = await fetch('{{route("sellerProfile.password")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.passwordData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Password changed successfully');
                    this.passwordData = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: '',
                    };
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            } finally {
                this.isChangingPassword = false;
            }
        },

        async saveBankAccount() {
            this.isSavingBank = true;

            try {
                const url = this.editingBankId 
                    ? `{{route("sellerProfile.bank.update", "__ID__")}}`.replace("__ID__", this.editingBankId)
                    : `{{ route('sellerProfile.bank.add') }}`;
                
                const method = this.editingBankId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.bankData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Bank account saved successfully');
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            } finally {
                this.isSavingBank = false;
            }
        },

        editBank(id) {
            // In production, fetch bank data
            this.editingBankId = id;
            this.showEditBankModal = true;
        },

        async setPrimary(id) {
            if (!confirm('Set this as primary account?')) return;

            try {
                const response = await fetch(`{{route("sellerProfile.bank.primary", "__ID__")}}`.replace("__ID__", id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        },

        async deleteBank(id) {
            if (!confirm('Delete this bank account?')) return;

            try {
                const response = await fetch(`{{route("sellerProfile.bank.delete", "__ID__")}}`.replace("__ID__", id), {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        }
    }
}
</script>

<style>
[x-cloak] {
    display: none !important;
}
</style>
@endsection