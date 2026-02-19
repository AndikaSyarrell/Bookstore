<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_name'); // BCA, Mandiri, BNI, BRI, etc.
            $table->string('account_number');
            $table->string('account_holder_name');
            $table->boolean('is_primary')->default(false); // Primary account for payments
            $table->boolean('is_verified')->default(false); // Admin verification
            $table->text('verification_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_primary']);
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};