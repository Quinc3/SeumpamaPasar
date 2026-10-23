<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkout_id')->constrained('checkouts')->restrictOnDelete();
            $table->foreignId('seller_profile_id')->constrained('seller_profiles')->restrictOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->restrictOnDelete();
            $table->decimal('subtotal', 15, 2);
            $table->enum('status', [
                'pending_payment',
                'pending_processing',
                'processing',
                'shipped',
                'completed',
                'cancelled',
            ])->default('pending_payment');

            $table->string('shipping_recipient_name');
            $table->string('shipping_phone');
            $table->string('shipping_line1');
            $table->string('shipping_city');
            $table->string('shipping_province');
            $table->string('shipping_postal_code', 20);

            $table->timestamp('stock_deducted_at')->nullable();
            $table->timestamp('stock_restored_at')->nullable();
            $table->decimal('refunded_amount', 15, 2)->default(0);
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            $table->index(['seller_profile_id', 'status'], 'orders_seller_status_index');
            $table->index(['buyer_id', 'status'], 'orders_buyer_status_index');
            $table->index(['checkout_id', 'status'], 'orders_checkout_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};