<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaQController extends Controller
{
    /**
     * Show FAQ page for seller
     */
    public function seller()
    {
        $faqs = [
            [
                'category' => 'Getting Started',
                'items' => [
                    [
                        'question' => 'How do I start selling on the platform?',
                        'answer' => 'To start selling, you need to: 1) Register as a seller, 2) Complete your profile, 3) Add at least one verified bank account, 4) Create your first product listing.'
                    ],
                    [
                        'question' => 'Do I need to verify my bank account?',
                        'answer' => 'Yes, you must add and verify at least one bank account to receive payments from buyers. Products from sellers without verified bank accounts will not be shown on the platform.'
                    ],
                    [
                        'question' => 'How many bank accounts can I add?',
                        'answer' => 'You can add multiple bank accounts. However, you must set one as your primary account which will be shown to buyers during checkout.'
                    ],
                ],
            ],
            [
                'category' => 'Product Management',
                'items' => [
                    [
                        'question' => 'How do I add a new product?',
                        'answer' => 'Go to Dashboard → Products → Add Product. Fill in the product title, author, category, description, price, stock quantity, and upload a product image.'
                    ],
                    [
                        'question' => 'Can I edit my product after publishing?',
                        'answer' => 'Yes, you can edit any product details at any time. Go to Dashboard → Products → Click Edit on the product you want to modify.'
                    ],
                    [
                        'question' => 'What happens when my product is out of stock?',
                        'answer' => 'Out of stock products (stock = 0) will not be shown to buyers. Make sure to restock and update your inventory to make them available again.'
                    ],
                ],
            ],
            [
                'category' => 'Order Processing',
                'items' => [
                    [
                        'question' => 'What is the order flow?',
                        'answer' => 'Order flow: 1) Buyer places order → 2) Buyer uploads payment proof → 3) You verify payment → 4) You process and ship the order → 5) Buyer receives and confirms delivery.'
                    ],
                    [
                        'question' => 'How do I verify payment?',
                        'answer' => 'When a buyer uploads payment proof, you will receive a notification. Go to Orders → Pending Verification → Review the payment proof → Click "Verify Payment" or "Reject" if incorrect.'
                    ],
                    [
                        'question' => 'What should I do after verifying payment?',
                        'answer' => 'After verifying payment, process the order immediately. Prepare the product, pack it securely, and arrange for shipment. Update the order status to "Processing".'
                    ],
                    [
                        'question' => 'How do I ship the order?',
                        'answer' => 'Go to Orders → Processing → Click "Ship Order" → Enter shipping details (carrier name, tracking number) → Upload receipt (optional) → Submit. The buyer will be notified automatically.'
                    ],
                    [
                        'question' => 'What happens if buyer doesn\'t pay within 3 hours?',
                        'answer' => 'Orders are automatically cancelled if the buyer does not upload payment proof within 3 hours of order creation. Stock will be automatically returned.'
                    ],
                ],
            ],
            [
                'category' => 'Refunds & Cancellations',
                'items' => [
                    [
                        'question' => 'What is the refund process?',
                        'answer' => 'When a buyer requests a refund: 1) You receive notification with buyer\'s bank details → 2) You review the request → 3) If approved, transfer the money to buyer\'s account → 4) Upload transfer proof → 5) Click "Approve Refund" → Stock is automatically returned.'
                    ],
                    [
                        'question' => 'Can I reject a refund request?',
                        'answer' => 'Yes, you can reject refund requests. Provide a clear reason for rejection. The buyer will be notified and the order status will be restored to its previous state.'
                    ],
                    [
                        'question' => 'What happens to stock when a refund is approved?',
                        'answer' => 'Stock is automatically returned to your inventory when you approve a refund. You don\'t need to manually update the stock quantity.'
                    ],
                ],
            ],
            [
                'category' => 'Payments & Finances',
                'items' => [
                    [
                        'question' => 'How do I receive payments?',
                        'answer' => 'Buyers transfer money directly to your bank account. They will see your verified bank account details during checkout. You verify the payment proof before processing the order.'
                    ],
                    [
                        'question' => 'When do I receive the money?',
                        'answer' => 'Payment is made directly by the buyer to your bank account before you ship the order. There is no platform escrow - you receive payment immediately.'
                    ],
                    [
                        'question' => 'Can I download sales reports?',
                        'answer' => 'Yes! Go to Dashboard → Monthly Sales Report → Select month and year → Click "Download Excel Report". The report includes all orders, products sold, and revenue details.'
                    ],
                ],
            ],
            [
                'category' => 'Communication',
                'items' => [
                    [
                        'question' => 'How do I communicate with buyers?',
                        'answer' => 'Use the built-in chat system. When a buyer messages you, you\'ll receive a notification. Go to Messages to view and respond to buyer inquiries in real-time.'
                    ],
                    [
                        'question' => 'What should I do if buyer asks for product information?',
                        'answer' => 'Respond promptly and professionally. Provide accurate information about the product, availability, shipping time, and any other relevant details.'
                    ],
                ],
            ],
        ];

        return view('dashboard.faq.index', compact('faqs'));
    }

    /**
     * Show FAQ page for buyer
     */
    public function buyer()
    {
        $faqs = [
            [
                'category' => 'Getting Started',
                'items' => [
                    [
                        'question' => 'How do I create an account?',
                        'answer' => 'Click "Register" → Select "Buyer" as your role → Fill in your name, email, phone, and password → Verify your email (if required) → Start shopping!'
                    ],
                    [
                        'question' => 'Is it safe to shop here?',
                        'answer' => 'Yes! All sellers must verify their bank accounts before listing products. You can see seller ratings and communicate with them directly before purchasing.'
                    ],
                ],
            ],
            [
                'category' => 'Browsing & Shopping',
                'items' => [
                    [
                        'question' => 'How do I find products?',
                        'answer' => 'Browse by category on the homepage, use the search bar to find specific books, or filter by author, price range, or seller.'
                    ],
                    [
                        'question' => 'Can I contact the seller before buying?',
                        'answer' => 'Yes! Click "Chat with Seller" on any product page to ask questions about the book, availability, or shipping details.'
                    ],
                    [
                        'question' => 'How do I know if a product is available?',
                        'answer' => 'Only products with stock will be shown on the platform. If you can see a product, it means it\'s available for purchase.'
                    ],
                ],
            ],
            [
                'category' => 'Placing Orders',
                'items' => [
                    [
                        'question' => 'How do I place an order?',
                        'answer' => 'Add product to cart → Review your cart → Click "Checkout" → Fill in shipping address → Review order summary → Click "Place Order".'
                    ],
                    [
                        'question' => 'Can I order from multiple sellers at once?',
                        'answer' => 'During checkout, your cart will be automatically organized by seller. Each seller will be a separate order with individual payment and shipping.'
                    ],
                    [
                        'question' => 'How long do I have to pay?',
                        'answer' => 'You have 3 hours from order creation to upload payment proof. Orders are automatically cancelled if payment is not received within this time.'
                    ],
                ],
            ],
            [
                'category' => 'Payment',
                'items' => [
                    [
                        'question' => 'What payment methods are accepted?',
                        'answer' => 'We accept bank transfers to the seller\'s verified bank account. You will see the seller\'s bank details (bank name, account number, account name) after placing your order.'
                    ],
                    [
                        'question' => 'How do I pay for my order?',
                        'answer' => 'After placing order: 1) Note the seller\'s bank details shown on order page → 2) Transfer the exact amount to that account → 3) Upload proof of payment (screenshot/receipt) → 4) Wait for seller to verify.'
                    ],
                    [
                        'question' => 'What should I include in payment proof?',
                        'answer' => 'Your payment proof should clearly show: transfer date and time, recipient name and account, transfer amount, and transaction reference number. Clear photos or screenshots work best.'
                    ],
                    [
                        'question' => 'What if the seller rejects my payment?',
                        'answer' => 'If your payment proof is rejected, check the rejection reason. You may need to re-upload a clearer image or verify that you transferred to the correct account and amount.'
                    ],
                ],
            ],
            [
                'category' => 'Shipping & Delivery',
                'items' => [
                    [
                        'question' => 'How long will shipping take?',
                        'answer' => 'Shipping time varies by seller location and shipping method. Typical delivery is 3-7 business days. You can track your order using the tracking number provided by the seller.'
                    ],
                    [
                        'question' => 'How do I track my order?',
                        'answer' => 'Go to My Orders → Click on your order → View tracking number and carrier information → Use the tracking link or visit the courier\'s website to track shipment status.'
                    ],
                    [
                        'question' => 'What if my order is damaged or wrong?',
                        'answer' => 'Contact the seller immediately through chat. Take photos of the damaged/wrong item. You can also request a refund if the issue cannot be resolved.'
                    ],
                ],
            ],
            [
                'category' => 'Refunds & Cancellations',
                'items' => [
                    [
                        'question' => 'How do I request a refund?',
                        'answer' => 'Go to My Orders → Select the order → Click "Request Refund" → Choose reason → Fill in your bank account details (where you want the refund) → Submit request.'
                    ],
                    [
                        'question' => 'When can I request a refund?',
                        'answer' => 'You can request refunds for orders in these statuses: Pending Payment, Pending Verification, or Processing. Once shipped, contact the seller first to discuss the issue.'
                    ],
                    [
                        'question' => 'How long does refund take?',
                        'answer' => 'Refunds are processed by the seller directly. Once the seller approves your refund request and transfers the money to your account, you should receive it within 1-3 business days depending on your bank.'
                    ],
                    [
                        'question' => 'What if my refund is rejected?',
                        'answer' => 'The seller will provide a reason for rejection. You can contact them through chat to discuss the issue further or escalate to customer support if needed.'
                    ],
                    [
                        'question' => 'Can I cancel my order?',
                        'answer' => 'You can cancel unpaid orders by requesting a refund. For paid orders, you must go through the refund process and provide a cancellation reason.'
                    ],
                ],
            ],
            [
                'category' => 'Order Completion',
                'items' => [
                    [
                        'question' => 'What should I do when I receive my order?',
                        'answer' => 'Check the package for any damage, verify that the correct items were received, and click "Confirm Delivery" on your order page. This helps sellers build their reputation.'
                    ],
                    [
                        'question' => 'What if I don\'t confirm delivery?',
                        'answer' => 'Orders will be automatically marked as delivered after a certain period. However, it\'s best to confirm promptly so sellers know you received your items successfully.'
                    ],
                ],
            ],
        ];

        return view('dashboard.faq.index', compact('faqs'));
    }
}