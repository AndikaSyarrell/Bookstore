<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index()
    {
        return view('checkout.index');
    }

    /**
     * Process checkout
     */
    public function process(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.cart_item_id' => 'required|exists:cart_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'payment_method' => 'required|string|in:bank_transfer,cod,ewallet',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'shipping' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            // Get first product's seller for order
            $firstItem = $request->items[0];
            $product = Product::findOrFail($firstItem['product_id']);
            $sellerId = $product->seller_id;

            // Create order
            $order = Order::create([
                'buyer_id' => $user->id,
                'seller_id' => $sellerId,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $request->subtotal,
                'tax' => $request->tax,
                'shipping_cost' => $request->shipping,
                'total' => $request->total,
                'shipping_address' => json_encode($request->shipping_address),
                'payment_method' => $request->payment_method,
            ]);

            // Create order details
            foreach ($request->items as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price']
                ]);

                // Remove from cart
                CartItem::where('id', $item['cart_item_id'])->delete();
            }

            // Create payment record
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $request->total,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'transaction_id' => 'TRX-' . strtoupper(uniqid())
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process checkout: ' . $e->getMessage()
            ], 500);
        }
    }
}
