<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Refund;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Facades\Log;

class RefundService
{
    /**
     * Create refund request with buyer's bank details
     */
    public static function createRefund(Order $order, $reason, $reasonDetail = null, $buyerBankDetails = [])
    {
        DB::beginTransaction();
        
        try {
            // Create refund record
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => $order->buyer_id,
                'refund_number' => self::generateRefundNumber(),
                'reason' => $reason,
                'reason_detail' => $reasonDetail,
                'refund_amount' => $order->total_amount,
                'refund_method' => 'bank_transfer', // Default to bank transfer
                'status' => 'pending',
                
                //Buyer's bank details for refund
                'bank_name' => $buyerBankDetails['bank_name'] ?? null,
                'bank_account_number' => $buyerBankDetails['account_number'] ?? null,
                'bank_account_name' => $buyerBankDetails['account_name'] ?? null,
            ]);

            // Update order
            $order->update([
                'status' => 'refund_pending',
                'refund_id' => $refund->id,
            ]);

            DB::commit();

            // Notify buyer
            NotificationService::create(
                $order->buyer_id,
                'refund_created',
                'Refund Request Created',
                "Your refund request #{$refund->refund_number} has been created and is pending seller approval.",
                [
                    'order_id' => $order->id,
                    'refund_id' => $refund->id,
                    'refund_number' => $refund->refund_number,
                ],
                route('order.show', $order->id)
            );

            //Notify seller - now seller handles approval
            NotificationService::create(
                $order->seller_id,
                'refund_request',
                'New Refund Request',
                "Buyer requests refund for order #{$order->order_number}. Please review and process.",
                [
                    'order_id' => $order->id,
                    'refund_id' => $refund->id,
                    'amount' => $refund->refund_amount,
                ],
                route('order.show', $order->id)
            );

            return $refund;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Auto cancel order if no payment within 3 hours
     */
    public static function autoCancelNoPayment(Order $order)
    {
        DB::beginTransaction();
        
        try {
            // Return stock
            foreach ($order->details as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }

            // Create refund record (for tracking)
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => $order->buyer_id,
                'refund_number' => self::generateRefundNumber(),
                'reason' => 'auto_cancel_no_payment',
                'reason_detail' => 'Order automatically cancelled - No payment received within 3 hours',
                'refund_amount' => $order->total_amount,
                'status' => 'completed', // Auto-complete karena tidak ada payment
            ]);

            // Update order
            $order->update([
                'status' => 'auto_cancelled',
                'refund_id' => $refund->id,
            ]);

            // Update payment
            if ($order->payment) {
                $order->payment->update(['status' => 'cancelled']);
            }

            DB::commit();

            // Notify buyer
            NotificationService::create(
                $order->buyer_id,
                'order_auto_cancelled',
                'Order Automatically Cancelled',
                "Order #{$order->order_number} has been automatically cancelled due to no payment within 3 hours.",
                [
                    'order_id' => $order->id,
                ],
                route('order.show', $order->id)
            );

            // Notify seller
            NotificationService::create(
                $order->seller_id,
                'order_auto_cancelled',
                'Order Auto-Cancelled',
                "Order #{$order->order_number} was automatically cancelled - buyer did not pay within 3 hours.",
                [
                    'order_id' => $order->id,
                ],
                route('order.show', $order->id)
            );

            return $refund;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     *Seller approves refund and uploads proof
     */
    public static function approveRefund(Refund $refund, $proofImage = null, $notes = null)
    {
        DB::beginTransaction();
        
        try {
            // Update refund status
            $refund->update([
                'status' => 'approved',
                'approved_at' => now(),
                'refund_proof' => $proofImage, //Bukti transfer dari seller
                'admin_notes' => $notes,
            ]);

            // Return stock
            foreach ($refund->order->orderDetails as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }

            // Update order status
            $refund->order->update(['status' => 'refunded']);

            DB::commit();

            // Notify buyer
            NotificationService::create(
                $refund->user_id,
                'refund_approved',
                'Refund Approved & Processed',
                "Your refund of Rp " . number_format($refund->refund_amount, 0, ',', '.') . " has been approved and transferred to your account.",
                [
                    'refund_id' => $refund->id,
                    'order_id' => $refund->order_id,
                ],
                route('order.show', $refund->order_id)
            );

            return $refund;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Seller rejects refund
     */
    public static function rejectRefund(Refund $refund, $notes)
    {
        $refund->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
        ]);

        // Restore order status
        $previousStatus = $refund->order->payment && $refund->order->payment->status === 'paid' 
            ? 'processing' 
            : 'pending_verification';
            
        $refund->order->update(['status' => $previousStatus]);

        // Notify buyer
        NotificationService::create(
            $refund->user_id,
            'refund_rejected',
            'Refund Request Rejected',
            "Your refund request #{$refund->refund_number} has been rejected. Reason: {$notes}",
            [
                'refund_id' => $refund->id,
                'order_id' => $refund->order_id,
            ],
            route('order.show', $refund->order_id)
        );

        return $refund;
    }

    /**
     * Check and process expired orders (called by scheduler)
     */
    public static function processExpiredOrders()
    {
        $threeHoursAgo = now()->subHours(3);

        // Get orders that are pending payment for more than 3 hours
        $expiredOrders = Order::where('status', 'pending_payment')
            ->where('created_at', '<=', $threeHoursAgo)
            ->whereNull('refund_id')
            ->get();

        $processed = 0;

        foreach ($expiredOrders as $order) {
            try {
                self::autoCancelNoPayment($order);
                $processed++;
            } catch (\Exception $e) {
                \Log::error("Failed to auto-cancel order #{$order->id}: " . $e->getMessage());
            }
        }

        return $processed;
    }

    /**
     * Generate unique refund number
     */
    private static function generateRefundNumber()
    {
        return 'REF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}