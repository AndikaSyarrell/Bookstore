<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'method',
        'amount',
        'status',
        'transaction_id',
        'virtual_account',
        'bank_name',
        'account_name',
        'proof_image',
        'note',
        'paid_at',
        'expired_at'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime'
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if payment is expired
     */
    public function isExpired()
    {
        return $this->expired_at && $this->expired_at->isPast();
    }
    
    /**
     * Get payment status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'expired' => 'secondary'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }
}