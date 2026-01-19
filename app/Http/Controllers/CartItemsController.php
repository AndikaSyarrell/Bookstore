<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CartController;
use App\Models\Product as Book;

class CartItemsController extends Controller
{

    public function add($productId, $qty)
    {
        $cart = app(CartController::class)->getActiveCart();
        $product = Book::findOrFail($productId);

        return CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $productId],
            ['qty' => DB::raw("qty + $qty"), 'price' => $product->price]
        );
    }

    public function updateQty($itemId, $qty)
    {
        return CartItem::where('id', $itemId)
            ->update(['qty' => $qty]);
    }

    public function remove($itemId)
    {
        return CartItem::where('id', $itemId)->delete();
    }

    public function clear()
    {
        $cart = app(CartController::class)->getActiveCart();
        return CartItem::where('cart_id', $cart->id)->delete();
    }
}
