<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $user = Auth::user()->load('role');
        return view('dashboard.profile.index', compact('user'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'no_telp' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user->update($request->only([
            'name',
            'email',
            'no_telp',
            'address',
            'city',
            'province',
            'postal_code',
            'birth_date',
            'gender',
        ]));

        return back()->with('success', 'Profile updated successfully');
    }

    /**
     * Update profile picture
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        // Delete old image if exists
        if ($user->img) {
            Storage::disk('public')->delete('profile/' . $user->img);
        }

        // Upload new image
        $file = $request->file('img');
        $filename = $user->id . '-' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('profile', $filename, 'public');

        $user->update(['img' => $filename]);

        return back()->with('success', 'Profile picture updated successfully');
    }

    /**
     * Remove profile picture
     */
    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->img) {
            Storage::disk('public')->delete('profile/' . $user->img);
            $user->update(['img' => null]);
        }

        return back()->with('success', 'Profile picture removed successfully');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password changed successfully');
    }
}