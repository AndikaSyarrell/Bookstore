<?php

namespace App\Http\Controllers;

use App\Models\cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class CartController extends Controller
{
    public function getActiveCart()
    {
        return Cart::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'active']
        );
    }

    public function checkout()
    {
        $cart = Cart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $cart->update(['status' => 'checked_out']);

        return $cart;
    }
}
