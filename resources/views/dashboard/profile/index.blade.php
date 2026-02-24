@extends('layouts.app')

@section('title', 'Profile')
@section('content')
<div x-data="masterProfile()" class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-600 mt-1">Manage your account information</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-800 font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-red-800 font-semibold mb-2">Please correct the following errors:</h3>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="space-y-6">
            
            <!-- Profile Picture Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Profile Picture</h2>
                
                <div class="flex items-start gap-6">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($user->img)
                        <img src="{{ asset('storage/profile/' . $user->img) }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100">
                        @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-gray-100">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        @endif
                    </div>

                    <!-- Upload/Remove Buttons -->
                    <div class="flex-1">
                        <p class="text-gray-700 mb-4">Upload a new profile picture. JPG, PNG (Max 2MB)</p>
                        
                        <form action="{{ route('master.profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                            @csrf
                            <input type="file" name="img" accept="image/*" class="hidden" id="photo-upload" onchange="this.form.submit()">
                            <label for="photo-upload" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 cursor-pointer transition-colors">
                                Choose Photo
                            </label>
                        </form>

                        @if($user->img)
                        <form action="{{ route('master.profile.photo.remove') }}" method="POST" class="mt-3" onsubmit="return confirm('Remove profile picture?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">
                                Remove Photo
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Information</h2>
                
                <form action="{{ route('master.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Full Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Phone Number *</label>
                            <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Birth Date</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Gender</label>
                            <select name="gender" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Address Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Address Information</h2>
                
                <form action="{{ route('master.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Hidden fields untuk data yang tidak berubah -->
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="no_telp" value="{{ $user->no_telp }}">
                    <input type="hidden" name="birth_date" value="{{ $user->birth_date }}">
                    <input type="hidden" name="gender" value="{{ $user->gender }}">

                    <div class="grid grid-cols-1 gap-6">
                        
                        <!-- Street Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Street Address</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Enter your street address">
                        </div>

                        <!-- City, Province, Postal -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city', $user->city) }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="Jakarta">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Province</label>
                                <input type="text" name="province" value="{{ old('province', $user->province) }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="DKI Jakarta">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="12345">
                            </div>
                        </div>

                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            Save Address
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Change Password</h2>
                
                <form action="{{ route('master.profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        
                        <!-- Current Password -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Current Password *</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Enter your current password">
                        </div>

                        <!-- New Password -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">New Password *</label>
                            <input type="password" name="new_password" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Enter new password (min 8 characters)">
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Confirm New Password *</label>
                            <input type="password" name="new_password_confirmation" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Confirm your new password">
                        </div>

                        <!-- Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <strong>Password Requirements:</strong> Minimum 8 characters
                            </p>
                        </div>

                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Information (Read-only) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Account Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">User ID</label>
                        <p class="text-gray-900 font-semibold">#{{ $user->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                            {{ ucfirst($user->role->name) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Account Created</label>
                        <p class="text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Last Updated</label>
                        <p class="text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<script>
function masterProfile() {
    return {
        // Add any JavaScript functionality here if needed
    }
}
</script>
@endsection