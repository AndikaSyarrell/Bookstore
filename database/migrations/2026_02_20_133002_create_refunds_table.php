<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Buyer
            $table->string('refund_number')->unique(); // REF-20260215-XXXXXX
            
            $table->enum('reason', [
                'buyer_cancel',
                'payment_expired',
                'stock_unavailable',
                'seller_cancel',
                'auto_cancel_no_payment',
                'product_defect',
                'wrong_item',
                'other'
            ]);
            $table->text('reason_detail')->nullable();
            
            $table->decimal('refund_amount', 15, 2);
            $table->enum('refund_method', [
                'bank_transfer',
                'store_credit',
                'cash'
            ])->default('bank_transfer');
            
            $table->enum('status', [
                'pending',    // Menunggu approval seller
                'approved',   // Disetujui seller, refund sudah ditransfer
                'rejected'    // Ditolak seller
            ])->default('pending');
            
            //  Buyer's Bank Account for Refund (input by buyer)
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            
            //  Refund Proof (uploaded by seller after transfer)
            $table->string('refund_proof')->nullable(); // Image of transfer receipt
            
            // Notes from seller
            $table->text('admin_notes')->nullable(); // Rename to seller_notes in usage
            
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });

        // Add refund_id to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'refund_id')) {
                $table->foreignId('refund_id')->nullable()->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('orders', 'auto_cancel_at')) {
                $table->timestamp('auto_cancel_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['refund_id']);
            $table->dropColumn(['refund_id', 'auto_cancel_at']);
        });
        
        Schema::dropIfExists('refunds');
    }
};