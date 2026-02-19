<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SellerProfileController extends Controller
{
    /**
     * Display seller profile
     */
    public function index()
    {
        $user = User::find(Auth::id());
        
        // Check if user is seller
        if ($user->role->name !== 'seller') {
            abort(403, 'Unauthorized');
        }

        $bankAccounts = BankAccount::where('user_id', $user->id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics
        $stats = [
            'total_products' => $user->products()->count(),
            'active_products' => $user->products()->where('stock', '>', 0)->count(),
            'total_orders' => $user->sellerOrders()->count(),
            'pending_orders' => $user->sellerOrders()->where('status', 'pending_verification')->count(),
        ];

        return view('dashboard.seller.index', compact('user', 'bankAccounts', 'stats'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'no_telp' => 'required|string|unique:users,no_telp,' . Auth::id(),
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
        ]);

        try {
            Auth::user()->update([
                'name' => $request->name,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $user = User::find(Auth::id());

            // Delete old photo if exists
            if ($user->img && Storage::disk('public')->exists('profile/' . $user->img)) {
                Storage::disk('public')->delete('profile/' . $user->img);
            }

            // Store new photo
            $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->storeAs('profile', $fileName, 'public');

            $user->update(['img' => $fileName]);

            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo_url' => asset('storage/profile/' . $fileName)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        try {
            $user = User::find(Auth::id());

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add bank account
     */
    public function addBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
            'is_primary' => 'boolean',
        ]);

        try {
            $bankAccount = BankAccount::create([
                'user_id' => Auth::id(),
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'is_primary' => $request->is_primary ?? false,
            ]);

            // If set as primary, update others
            if ($request->is_primary) {
                $bankAccount->setAsPrimary();
            }

            return response()->json([
                'success' => true,
                'message' => 'Bank account added successfully',
                'bank_account' => $bankAccount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add bank account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bank account
     */
    public function updateBankAccount(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
        ]);

        try {
            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->findOrFail($id);

            $bankAccount->update([
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bank account updated successfully',
                'bank_account' => $bankAccount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bank account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set primary bank account
     */
    public function setPrimaryBankAccount($id)
    {
        try {
            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->findOrFail($id);

            $bankAccount->setAsPrimary();

            return response()->json([
                'success' => true,
                'message' => 'Primary account updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set primary account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete bank account
     */
    public function deleteBankAccount($id)
    {
        try {
            $bankAccount = BankAccount::where('user_id', Auth::id())
                ->findOrFail($id);

            // Don't allow deleting if it's the only account
            $accountCount = BankAccount::where('user_id', Auth::id())->count();
            if ($accountCount === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the only bank account'
                ], 400);
            }

            // If deleting primary, set another as primary
            if ($bankAccount->is_primary) {
                $newPrimary = BankAccount::where('user_id', Auth::id())
                    ->where('id', '!=', $id)
                    ->first();
                
                if ($newPrimary) {
                    $newPrimary->setAsPrimary();
                }
            }

            $bankAccount->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bank account deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank account: ' . $e->getMessage()
            ], 500);
        }
    }
}