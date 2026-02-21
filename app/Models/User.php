<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'address',
        'no_telp',
        'img',
        'birth_date',
        'gender',
        'bio',
        'city',
        'province',
        'postal_code',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function carts()
    {
        return $this->hasOne(Cart::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function primaryBankAccount()
    {
        return $this->hasOne(BankAccount::class)->where('is_primary', true);
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function isBuyer()
    {
        return $this->role->name === 'buyer';
    }

    public function isSeller()
    {
        return $this->role->name === 'seller';
    }

    public function isMaster()
    {
        return $this->role->name === 'Master';
    }

    public function message(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function refund()
    {
        return $this->hasMany(Refund::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('read', false);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
