<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'seller_id',
        'buyer_id',
        'order_date',
        'status',
        'shipping_address',
        'total_ammount'
    ];

    public function orderDetails(){
        return $this->hasMany(OrderDetail::class);
    }

    public function buyer(){
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(){
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function payment(){
        return $this->hasMany(Payment::class);
    }

}
