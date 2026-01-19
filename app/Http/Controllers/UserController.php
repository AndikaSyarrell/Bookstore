<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UserController extends Controller
{

    public function create()
    {
        $roles = Role::all();
        return view('dashboard.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // 1. Validasi data (Ubah 'img' dari string ke image)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'no_telp' => 'required|string|max:20',
            'role' => 'required|int',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi file gambar
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already taken.',
            'no_telp.required' => 'Phone number is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Role is required.',
            'img.image' => 'The file must be an image.',
            'img.max' => 'Image size cannot exceed 2MB.'
        ]);

        // 2. Siapkan data untuk disimpan
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'no_telp' => $request->no_telp,
            'role_id' => $request->role,
            'birth_date' => $request->birthdate,
            'address' => $request->address,
            'img' => null, // Default null jika tidak ada upload
        ];

        // 3. Menangani Upload Gambar
        if ($request->hasFile('img')) { 
            $file = $request->file('img');

            // Nama file unik: name-timestamp.extension
            $fileName = Str::slug($request->name) . '-' . time() . '.' . $file->getClientOriginalExtension();

            // Simpan ke storage/app/public/profiles
            // Laravel otomatis membuat direktori jika belum ada
            // if(!Storage::exists('public/storage/profiles')){
            //     Storage::makeDirectory('public/storage/profiles');
            // }
            $file->storeAs('profiles', $fileName, 'public');

            // Simpan nama file ke array data user
            $userData['img'] = $fileName;
        }

        // 4. Eksekusi Create User
        $user = User::create($userData);

        // 5. Cek jika request adalah AJAX (untuk menyesuaikan respon JavaScript Anda)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => $user
            ], 201);
        }

        // Redirect normal jika bukan AJAX
        // return redirect()->route('users')->with('success', 'User created successfully.');
    }
}
