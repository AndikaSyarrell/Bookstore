<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'is_primary',
        'is_verified',
        'verification_notes',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the bank account
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get masked account number
     */
    public function getMaskedAccountNumberAttribute()
    {
        $number = $this->account_number;
        $length = strlen($number);
        
        if ($length <= 4) {
            return $number;
        }
        
        return str_repeat('*', $length - 4) . substr($number, -4);
    }

    /**
     * Get bank logo
     */
    public function getBankLogoAttribute()
    {
        $logos = [
            'BCA' => '🔵',
            'Mandiri' => '🟡',
            'BNI' => '🟠',
            'BRI' => '⚪',
            'CIMB Niaga' => '🔴',
            'Permata' => '🟢',
            'Danamon' => '🔵',
            'BTN' => '🟦',
            'OCBC NISP' => '🟥',
        ];

        return $logos[$this->bank_name] ?? '🏦';
    }

    /**
     * Set as primary account
     */
    public function setAsPrimary()
    {
        // Remove primary flag from other accounts
        BankAccount::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Set this as primary
        $this->update(['is_primary' => true]);
    }

    /**
     * Verify account
     */
    public function verify($notes = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Scope for primary account
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for verified accounts
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for user's accounts
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get available Indonesian banks
     */
    public static function getAvailableBanks()
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'Mandiri' => 'Bank Mandiri',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'CIMB Niaga' => 'CIMB Niaga',
            'Permata' => 'Bank Permata',
            'Danamon' => 'Bank Danamon',
            'BTN' => 'Bank Tabungan Negara (BTN)',
            'OCBC NISP' => 'OCBC NISP',
            'Maybank' => 'Maybank Indonesia',
            'Panin' => 'Bank Panin',
            'BTPN' => 'Bank BTPN',
            'Sinarmas' => 'Bank Sinarmas',
            'Bukopin' => 'Bank Bukopin',
            'BCA Syariah' => 'BCA Syariah',
            'Mandiri Syariah' => 'Bank Syariah Mandiri',
            'BNI Syariah' => 'BNI Syariah',
            'BRI Syariah' => 'BRI Syariah',
            'Muamalat' => 'Bank Muamalat',
            'Jenius' => 'Jenius (BTPN)',
            'Jago' => 'Bank Jago',
            'Seabank' => 'SeaBank',
            'Allo Bank' => 'Allo Bank',
            'Blu' => 'Blu (BCA Digital)',
            'Other' => 'Bank Lainnya',
        ];
    }
}