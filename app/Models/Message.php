<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'message',
        'type',
        'metadata',
        'read',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read' => 'boolean',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get metadata as array
     */
    public function getMetadataArrayAttribute()
    {
        return json_decode($this->metadata, true);
    }

    /**
     * Check if message is from current user
     */
    public function isFromCurrentUser()
    {
        return $this->user_id === auth()->id();
    }

}