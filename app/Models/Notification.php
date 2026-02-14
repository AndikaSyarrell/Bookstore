<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute()
    {
        $icons = [
            'new_order' => 'shopping-cart',
            'order_status' => 'package',
            'payment_verified' => 'check-circle',
            'payment_rejected' => 'x-circle',
            'new_message' => 'message-circle',
            'order_shipped' => 'truck',
            'order_delivered' => 'check-square',
            'order_cancelled' => 'slash',
        ];

        return $icons[$this->type] ?? 'bell';
    }

    /**
     * Get color based on type
     */
    public function getColorAttribute()
    {
        $colors = [
            'new_order' => 'blue',
            'order_status' => 'indigo',
            'payment_verified' => 'green',
            'payment_rejected' => 'red',
            'new_message' => 'purple',
            'order_shipped' => 'yellow',
            'order_delivered' => 'green',
            'order_cancelled' => 'gray',
        ];

        return $colors[$this->type] ?? 'gray';
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}