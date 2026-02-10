<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'total_price',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function orders(){
        return $this->belongsTo(Order::class);
    }
}
