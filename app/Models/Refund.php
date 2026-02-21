<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'refund_number',
        'reason',
        'reason_detail',
        'refund_amount',
        'refund_method',
        'status',
        //Buyer's bank details
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        //Refund proof from seller
        'refund_proof',
        'admin_notes', // Actually seller notes
        'approved_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user (buyer)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get reason label
     */
    public function getReasonLabelAttribute()
    {
        $labels = [
            'buyer_cancel' => 'Buyer Cancelled',
            'payment_expired' => 'Payment Expired',
            'stock_unavailable' => 'Stock Unavailable',
            'seller_cancel' => 'Seller Cancelled',
            'auto_cancel_no_payment' => 'Auto Cancelled - No Payment',
            'product_defect' => 'Product Defect',
            'wrong_item' => 'Wrong Item Received',
            'other' => 'Other',
        ];

        return $labels[$this->reason] ?? $this->reason;
    }

    /**
     * Get masked account number
     */
    public function getMaskedAccountNumberAttribute()
    {
        if (!$this->bank_account_number) {
            return null;
        }

        $number = $this->bank_account_number;
        $length = strlen($number);
        
        if ($length <= 4) {
            return $number;
        }
        
        return str_repeat('*', $length - 4) . substr($number, -4);
    }

    /**
     * Approve refund
     */
    public function approve($proofImage = null, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'refund_proof' => $proofImage,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Reject refund
     */
    public function reject($notes)
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Check if has buyer bank details
     */
    public function hasBuyerBankDetails()
    {
        return $this->bank_name && $this->bank_account_number && $this->bank_account_name;
    }

    /**
     * Scope for pending refunds
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved refunds
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected refunds
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}