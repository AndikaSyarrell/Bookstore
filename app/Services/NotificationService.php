<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NewNotification;

class NotificationService
{
    /**
     * Create and broadcast a notification
     */
    public static function create($userId, $type, $title, $message, $data = [], $actionUrl = null)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'read' => false,
        ]);

        // Broadcast the notification
        broadcast(new NewNotification($notification))->toOthers();

        return $notification;
    }

    /**
     * Notify new order to seller
     */
    public static function notifyNewOrder($order)
    {
        return self::create(
            $order->seller_id,
            'new_order',
            'New Order Received!',
            "New order #{$order->order_number} from {$order->buyer->name}",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total_amount,
            ],
            route('order.show', $order->id)
        );
    }

    /**
     * Notify order status update to buyer
     */
    public static function notifyOrderStatus($order, $status, $message = null)
    {
        $titles = [
            'pending_verification' => 'Payment Proof Uploaded',
            'processing' => 'Order is Being Processed',
            'shipped' => 'Order Shipped!',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            'payment_rejected' => 'Payment Rejected',
        ];

        $messages = [
            'pending_verification' => "Your payment proof for order #{$order->order_number} is being verified",
            'processing' => "Your order #{$order->order_number} is being prepared",
            'shipped' => "Your order #{$order->order_number} has been shipped and is on the way!",
            'delivered' => "Your order #{$order->order_number} has been delivered. Thank you!",
            'cancelled' => "Your order #{$order->order_number} has been cancelled",
            'payment_rejected' => "Your payment for order #{$order->order_number} was rejected. Please try again.",
        ];

        return self::create(
            $order->buyer_id,
            'order_status',
            $titles[$status] ?? 'Order Update',
            $message ?? $messages[$status] ?? "Order #{$order->order_number} status updated",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
            ],
            route('order.show', $order->id)
        );
    }

    /**
     * Notify payment verification to buyer
     */
    public static function notifyPaymentVerified($order, $approved = true)
    {
        if ($approved) {
            return self::create(
                $order->buyer_id,
                'payment_verified',
                'Payment Approved! ✓',
                "Your payment for order #{$order->order_number} has been verified. We're preparing your order!",
                [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                route('order.show', $order->id)
            );
        } else {
            return self::create(
                $order->buyer_id,
                'payment_rejected',
                'Payment Rejected',
                "Your payment proof for order #{$order->order_number} was rejected. Please upload a valid proof.",
                [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                route('order.show', $order->id)
            );
        }
    }

    /**
     * Notify order shipped to buyer
     */
    public static function notifyOrderShipped($order, $carrier = null, $trackingNumber = null)
    {
        $message = "Your order #{$order->order_number} has been shipped!";
        
        if ($carrier && $trackingNumber) {
            $message .= " Tracking: {$carrier} - {$trackingNumber}";
        }

        return self::create(
            $order->buyer_id,
            'order_shipped',
            'Order Shipped! 🚚',
            $message,
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'carrier' => $carrier,
                'tracking_number' => $trackingNumber,
            ],
            route('order.show', $order->id)
        );
    }

    /**
     * Notify new message
     */
    public static function notifyNewMessage($message, $recipientId)
    {
        $sender = $message->user;
        
        return self::create(
            $recipientId,
            'new_message',
            "New message from {$sender->name}",
            substr($message->message, 0, 50) . (strlen($message->message) > 50 ? '...' : ''),
            [
                'chat_id' => $message->chat_id,
                'message_id' => $message->id,
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
            ],
            route('messages.show', $message->chat_id)
        );
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread count for a user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Delete old read notifications (older than 30 days)
     */
    public static function cleanupOldNotifications()
    {
        return Notification::where('read', true)
            ->where('read_at', '<', now()->subDays(30))
            ->delete();
    }
}