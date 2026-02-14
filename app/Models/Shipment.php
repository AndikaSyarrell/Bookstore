<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'tracking_url',
        'carrier',
        'receipt_image',
        'notes',
        'shipped_date',
        'estimated_delivery',
        'delivery_date',
    ];

    protected $casts = [
        'shipped_date' => 'date',
        'estimated_delivery' => 'date',
        'delivery_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the shipment
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get full receipt image URL
     */
    public function getReceiptUrlAttribute()
    {
        if ($this->receipt_image) {
            return asset('storage/shipment-receipts/' . $this->receipt_image);
        }
        return null;
    }

    /**
     * Check if shipment is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->estimated_delivery || $this->delivery_date) {
            return false;
        }
        return now()->isAfter($this->estimated_delivery);
    }

    /**
     * Get days until delivery
     */
    public function getDaysUntilDeliveryAttribute()
    {
        if (!$this->estimated_delivery || $this->delivery_date) {
            return null;
        }
        return now()->diffInDays($this->estimated_delivery, false);
    }
}