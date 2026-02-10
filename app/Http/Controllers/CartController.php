<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display cart page
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Get cart data for authenticated user
     */
    public function getCart()
    {
        try {
            $user = Auth::user();
            
            // Get or create cart for user
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'pending'],
                ['status' => 'pending']
            );

            // Get cart items with product details
            $cartItems = CartItem::where('cart_id', $cart->id)
                ->with('product')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'cart_id' => $item->cart_id,
                        'product_id' => $item->product_id,
                        'name' => $item->product->title ?? 'Unknown Product',
                        'price' => (float) $item->price,
                        'quantity' => $item->quantity,
                        'image' => '/storage/products/'.$item->product->img ?? null,
                    ];
                });

            return response()->json([
                'success' => true,
                'items' => $cartItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        try {
            $user = Auth::user();
            $product = Product::findOrFail($request->product_id);
            $quantity = $request->quantity ?? 1;

            // Get or create cart
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'pending'],
                ['status' => 'pending']
            );

            // Check if product already in cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Update quantity
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                // Create new cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_item' => [
                    'id' => $cartItem->id,
                    'product_id' => $product->id,
                    'name' => $product->title,
                    'price' => (float) $product->price,
                    'quantity' => $cartItem->quantity,
                    'image' => $product->image
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            
            // Verify ownership
            if ($cartItem->cart->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            if ($request->has('action')) {
                if ($request->action === 'increase') {
                    $cartItem->quantity += 1;
                } elseif ($request->action === 'decrease') {
                    $cartItem->quantity = max(1, $cartItem->quantity - 1);
                }
            } elseif ($request->has('quantity')) {
                $cartItem->quantity = max(1, (int) $request->quantity);
            }

            $cartItem->save();

            // Return updated cart
            return $this->getCart();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            
            // Verify ownership
            if ($cartItem->cart->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $cartItem->delete();

            // Return updated cart
            return $this->getCart();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync cart with server
     */
    public function sync(Request $request)
    {
        try {
            $user = Auth::user();
            $items = $request->input('items', []);

            // Get or create cart
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'pending'],
                ['status' => 'pending']
            );

            // Sync items
            foreach ($items as $item) {
                if (isset($item['product_id']) && isset($item['quantity'])) {
                    CartItem::updateOrCreate(
                        [
                            'cart_id' => $cart->id,
                            'product_id' => $item['product_id']
                        ],
                        [
                            'quantity' => $item['quantity'],
                            'price' => $item['price'] ?? 0
                        ]
                    );
                }
            }

            // Return updated cart
            return $this->getCart();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}