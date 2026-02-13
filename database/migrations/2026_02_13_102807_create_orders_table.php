<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('chat_id');
            $table->string('telegram_username')->nullable();
            $table->string('reference')->unique();
            $table->string('status')->default('pending')->index();
            $table->unsignedBigInteger('total_amount');
            $table->string('currency', 3)->default('XOF');
            $table->string('payment_url')->nullable();
            $table->string('fedapay_transaction_id')->nullable();
            $table->string('fedapay_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('payment_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
