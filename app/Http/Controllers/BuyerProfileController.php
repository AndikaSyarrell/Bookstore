<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BuyerProfileController extends Controller
{
    /**
     * Display the user's profile page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('profile.index', compact('user'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'no_telp' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id)
            ],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the user's profile picture
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'] // 2MB max
        ]);

        try {
            $user = Auth::user();

            // Delete old photo if exists
            if ($user->img) {
                Storage::disk('public')->delete('users/' . $user->img);
            }

            // Upload new photo
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('users', $filename, 'public');
                
                $user->img = $filename;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'photo_url' => $user->img ? asset('storage/users/' . $user->img) : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the user's profile picture
     */
    public function deletePhoto()
    {
        try {
            $user = Auth::user();

            if ($user->img) {
                Storage::disk('public')->delete('users/' . $user->img);
                $user->img = null;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile photo deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = Auth::user();

            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user profile data (API)
     */
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_telp' => $user->no_telp,
                'birth_date' => $user->birth_date,
                'gender' => $user->gender,
                'address' => $user->address,
                'city' => $user->city,
                'province' => $user->province,
                'postal_code' => $user->postal_code,
                'bio' => $user->bio,
                'img' => $user->img ? asset('storage/users/' . $user->img) : null,
                'role' => $user->role->name ?? null,
            ]
        ]);
    }
}