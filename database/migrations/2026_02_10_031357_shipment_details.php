<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Make tracking fields more flexible
            if (Schema::hasColumn('shipments', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->change();
            }
            
            if (Schema::hasColumn('shipments', 'carrier')) {
                $table->string('carrier')->nullable()->change();
            }
            
            // Add receipt/resi image
            if (!Schema::hasColumn('shipments', 'receipt_image')) {
                $table->string('receipt_image')->nullable()->after('carrier');
            }
            
            // Add shipping notes
            if (!Schema::hasColumn('shipments', 'notes')) {
                $table->text('notes')->nullable()->after('receipt_image');
            }
            
            // Add estimated delivery
            if (!Schema::hasColumn('shipments', 'estimated_delivery')) {
                $table->date('estimated_delivery')->nullable()->after('delivery_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['receipt_image', 'notes', 'estimated_delivery']);
        });
    }
};