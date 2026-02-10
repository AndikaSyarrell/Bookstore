<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add order_number for easy tracking
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->after('id');
            }
            
            // Expand shipping_address to text for JSON storage
            $table->text('shipping_address')->change();
            
            // Add subtotal, tax, shipping cost breakdown
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('total_amount');
            }
            
            if (!Schema::hasColumn('orders', 'tax')) {
                $table->decimal('tax', 15, 2)->default(0)->after('subtotal');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 15, 2)->default(0)->after('tax');
            }
            
            // Add notes field
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('shipping_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'subtotal', 'tax', 'shipping_cost', 'notes']);
        });
    }
};