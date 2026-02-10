<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Process checkout and create order
     * Called from cart checkout
     */
    public function processCheckout(Request $request)
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
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'shipping' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $items = collect($request->items);
            
            // Group items by seller
            $itemsBySeller = $items->groupBy(function ($item) {
                $product = Product::find($item['product_id']);
                return $product->seller_id;
            });

            $createdOrders = [];

            // Create separate order for each seller
            foreach ($itemsBySeller as $sellerId => $sellerItems) {
                // Calculate totals for this seller
                $sellerSubtotal = $sellerItems->sum(function ($item) {
                    return $item['quantity'] * $item['price'];
                });
                
                $sellerTax = $sellerSubtotal * 0.11; // 11% tax
                $sellerShipping = 15000; // Flat rate per seller
                $sellerTotal = $sellerSubtotal + $sellerTax + $sellerShipping;

                // Create order
                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),
                    'buyer_id' => $user->id,
                    'seller_id' => $sellerId,
                    'order_date' => now(),
                    'status' => 'pending_payment',
                    'shipping_address' => json_encode($request->shipping_address),
                    'subtotal' => $sellerSubtotal,
                    'tax' => $sellerTax,
                    'shipping_cost' => $sellerShipping,
                    'total_amount' => $sellerTotal,
                    'notes' => $request->notes ?? null
                ]);

                // Create order details
                foreach ($sellerItems as $item) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'total_price' => $item['quantity'] * $item['price']
                    ]);

                    // Remove from cart
                    CartItem::where('id', $item['cart_item_id'])->delete();
                    
                    // Reduce stock
                    $product = Product::find($item['product_id']);
                    $product->decrement('stock', $item['quantity']);
                }

                // Create payment record
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'amount' => $sellerTotal,
                    'method' => 'bank_transfer',
                    'status' => 'pending',
                    'transaction_id' => $this->generateTransactionId(),
                    'expired_at' => now()->addHours(24) // 24 hours to pay
                ]);

                $createdOrders[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'seller_id' => $sellerId,
                    'total' => $sellerTotal
                ];
            }

            DB::commit();

            // If single order, redirect to detail
            // If multiple orders, redirect to orders list
            if (count($createdOrders) === 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully',
                    'redirect_url' => route('buyer.orders.show', $createdOrders[0]['order_id'])
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => count($createdOrders) . ' orders placed successfully',
                    'redirect_url' => route('buyer.orders.index')
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show order detail
     */
    public function show($id)
    {
        $order = Order::with(['buyer', 'seller', 'orderDetails.product', 'payment'])
            ->where(function($query) {
                $query->where('buyer_id', Auth::id())
                      ->orWhere('seller_id', Auth::id());
            })
            ->findOrFail($id);

        return view('buyer.orders.show', compact('order'));
    }

    /**
     * List user's orders
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role->name === 'buyer') {
            $orders = Order::where('buyer_id', $user->id)
                ->with(['seller', 'payment'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $orders = Order::where('seller_id', $user->id)
                ->with(['buyer', 'payment'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('buyer.orders.index', compact('orders'));
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request, $orderId)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $order = Order::where('buyer_id', Auth::id())->findOrFail($orderId);
            $payment = $order->payment;

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found'
                ], 404);
            }

            // Delete old proof if exists
            if ($payment->proof_image) {
                Storage::disk('public')->delete('payment-proofs/' . $payment->proof_image);
            }

            // Upload new proof
            $file = $request->file('proof_image');
            $filename = 'proof_' . $order->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('payment-proofs', $filename, 'public');

            // Update payment
            $payment->update([
                'proof_image' => $filename,
                'note' => $request->notes,
                'status' => 'pending_verification'
            ]);

            // Update order status
            $order->update(['status' => 'pending_verification']);

            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded successfully',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload proof: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment (Seller)
     */
    public function verifyPayment(Request $request, $orderId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);

        try {
            $order = Order::where('seller_id', Auth::id())->findOrFail($orderId);
            $payment = $order->payment;

            if ($request->action === 'approve') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'note' => $request->notes
                ]);

                $order->update(['status' => 'processing']);

                $message = 'Payment verified successfully';
            } else {
                $payment->update([
                    'status' => 'rejected',
                    'note' => $request->notes
                ]);

                $order->update(['status' => 'payment_rejected']);

                $message = 'Payment rejected';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'order' => $order->load('payment')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status (Seller)
     */
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            $order = Order::where('seller_id', Auth::id())->findOrFail($orderId);

            $order->update([
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order (Buyer)
     */
    public function cancel($orderId)
    {
        try {
            $order = Order::where('buyer_id', Auth::id())->findOrFail($orderId);

            // Only allow cancel if pending_payment or payment_rejected
            if (!in_array($order->status, ['pending_payment', 'payment_rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel order in current status'
                ], 400);
            }

            // Return stock
            foreach ($order->details as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }

            $order->update(['status' => 'cancelled']);
            $order->payment->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId()
    {
        return 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
}