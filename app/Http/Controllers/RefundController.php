<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Order;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RefundController extends Controller
{
    /**
     * Request refund (Buyer) - WITH BANK DETAILS
     */
    public function requestRefund(Request $request, $orderId)
    {
        $request->validate([
            'reason' => 'required|in:buyer_cancel,payment_expired,stock_unavailable,product_defect,wrong_item,other',
            'reason_detail' => 'nullable|string|max:500',
            // Buyer's bank account for refund
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
        ]);

        try {
            $order = Order::where('buyer_id', Auth::id())
                ->findOrFail($orderId);

            // Check if order can be refunded
            if (!in_array($order->status, ['pending_payment', 'pending_verification', 'payment_rejected', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be refunded at this stage'
                ], 400);
            }

            // Check if refund already exists
            if ($order->refund_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund request already exists for this order'
                ], 400);
            }

            // Create refund with buyer's bank details
            $buyerBankDetails = [
                'bank_name' => $request->bank_name,
                'account_number' => $request->bank_account_number,
                'account_name' => $request->bank_account_name,
            ];

            $refund = RefundService::createRefund(
                $order,
                $request->reason,
                $request->reason_detail,
                $buyerBankDetails
            );

            return response()->json([
                'success' => true,
                'message' => 'Refund request created successfully. Waiting for seller approval.',
                'refund' => $refund
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve refund (Seller) - WITH PROOF UPLOAD
     */
    public function approveRefund(Request $request, $refundId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
            'refund_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048' // Bukti transfer
        ]);

        try {
            $refund = Refund::with('order')->findOrFail($refundId);

            // Check authorization - SELLER ONLY
            if (Auth::id() !== $refund->order->seller_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Seller only'
                ], 403);
            }

            // Check if buyer provided bank details
            if (!$refund->hasBuyerBankDetails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buyer bank details not provided'
                ], 400);
            }

            // Upload refund proof
            $proofPath = null;
            if ($request->hasFile('refund_proof')) {
                $file = $request->file('refund_proof');
                $fileName = 'refund-' . $refund->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('refunds', $fileName, 'public');
                $proofPath = $fileName;
            }

            RefundService::approveRefund($refund, $proofPath, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Refund approved and processed successfully',
                'refund' => $refund->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject refund (Seller)
     */
    public function rejectRefund(Request $request, $refundId)
    {
        $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        try {
            $refund = Refund::with('order')->findOrFail($refundId);

            // Check authorization - SELLER ONLY
            if (Auth::id() !== $refund->order->seller_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Seller only'
                ], 403);
            }

            RefundService::rejectRefund($refund, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Refund rejected',
                'refund' => $refund->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View refund details
     */
    public function show($refundId)
    {
        $refund = Refund::with(['order.orderDetails.product', 'user'])
            ->findOrFail($refundId);

        // Check authorization
        if (Auth::id() !== $refund->user_id && 
            Auth::id() !== $refund->order->seller_id) {
            abort(403);
        }

        return view('buyer.orders.index', compact('refund'));
    }

    // /**
    //  * List refunds for buyer
    //  */
    // public function buyerRefunds()
    // {
    //     $refunds = Refund::where('user_id', Auth::id())
    //         ->with(['order'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return view('refunds.buyer-index', compact('refunds'));
    // }

    // /**
    //  * List refunds for seller
    //  */
    // public function sellerRefunds()
    // {
    //     $refunds = Refund::whereHas('order', function($query) {
    //             $query->where('seller_id', Auth::id());
    //         })
    //         ->with(['order', 'user'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return view('refund.seller-index', compact('refunds'));
    // }

    // /**
    //  * Seller refund dashboard
    //  */
    // public function sellerDashboard()
    // {
    //     $stats = [
    //         'pending' => Refund::whereHas('order', function($q) {
    //                 $q->where('seller_id', Auth::id());
    //             })->where('status', 'pending')->count(),
    //         'approved' => Refund::whereHas('order', function($q) {
    //                 $q->where('seller_id', Auth::id());
    //             })->where('status', 'approved')->count(),
    //         'rejected' => Refund::whereHas('order', function($q) {
    //                 $q->where('seller_id', Auth::id());
    //             })->where('status', 'rejected')->count(),
    //     ];

    //     $pendingRefunds = Refund::whereHas('order', function($q) {
    //             $q->where('seller_id', Auth::id());
    //         })
    //         ->where('status', 'pending')
    //         ->with(['order', 'user'])
    //         ->orderBy('created_at', 'asc')
    //         ->get();

    //     $allRefunds = Refund::whereHas('order', function($q) {
    //             $q->where('seller_id', Auth::id());
    //         })
    //         ->with(['order', 'user'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(20);

    //     return view('refunds.seller-dashboard', compact('stats', 'pendingRefunds', 'allRefunds'));
    // }
}