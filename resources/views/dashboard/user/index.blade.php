@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6" x-data="{ showModal: false, editMode: false, selectedCategory: null }">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold mb-1">User Management</h2>
            <p class="text-gray-500 text-sm">Manage all platform users</p>
        </div>
        <a href="{{ route('users.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Add New User
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <form method="GET" action="{{ route('users') }}"
            class="grid md:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm mb-1">Search</label>
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name, email, or phone..."
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label class="block text-sm mb-1">Role</label>
                <select name="role"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        {{ request('role') == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full flex justify-center items-center gap-2">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </div>

        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Phone</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Joined</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-3">
                            {{ $user->id }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">

                                @if($user->img)
                                <img src="{{ asset('storage/profile/' . $user->img) }}"
                                    class="w-10 h-10 rounded-full object-cover">
                                @else
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                    {{ substr($user->name,0,1) }}
                                </div>
                                @endif

                                <div>
                                    <div class="font-medium">
                                        {{ $user->name }}
                                    </div>

                                    @if($user->city)
                                    <div class="text-gray-500 text-xs">
                                        {{ $user->city }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </td>

                        <td class="px-4 py-3">
                            {{ $user->email }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $user->no_telp }}
                        </td>

                        <td class="px-4 py-3">

                            <span class="
                                px-2 py-1 rounded text-xs font-medium
                                {{ $user->role->name === 'master' ? 'bg-red-100 text-red-700' :
                                   ($user->role->name === 'seller' ? 'bg-green-100 text-green-700' :
                                   'bg-blue-100 text-blue-700') }}">
                                {{ ucfirst($user->role->name) }}
                            </span>

                        </td>

                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex gap-2">

                                <template x-if="$store.status.onlineUsers.some(u => u.id === {{ $user->id }})">
                                    <div class="flex gap-2">

                                        <a href="{{ route('users.edit',$user->id) }}"
                                            class="border px-2 py-1 rounded text-blue-600 opacity-50 pointer-events-none">
                                            Edit
                                        </a>

                                        @if($user->id !== auth()->id())
                                        <button disabled
                                            class="border px-2 py-1 rounded text-slate-500">
                                            Delete
                                        </button>
                                        @endif

                                    </div>
                                </template>

                                <template x-if="!$store.status.onlineUsers.some(u => u.id === {{ $user->id }})">
                                    <div class="flex gap-2">

                                        <a href="{{ route('users.edit',$user->id) }}"
                                            class="border px-2 py-1 rounded text-blue-600 hover:bg-blue-50">
                                            Edit
                                        </a>

                                        @if($user->id !== auth()->id())
                                        <form method="POST"
                                            action="{{ route('users.destroy',$user->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this user?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="border px-2 py-1 rounded text-red-600 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                        @endif

                                    </div>
                                </template>

                            </div>
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="7"
                            class="text-center py-10 text-gray-500">

                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-users text-4xl"></i>
                                <p>No users found</p>
                            </div>

                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="flex justify-center mt-6">
        {{ $users->links() }}
    </div>
    @endif

</div>
@endsection