<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('digital_product_id')->nullable()->constrained('digital_products')->nullOnDelete();
            $table->enum('type', ['electricity_bill', 'electricity_token', 'pulse', 'data_package']);
            $table->string('customer_number', 32);
            $table->string('customer_name')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('reference')->unique();
            $table->string('token_code')->nullable();
            $table->string('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'digital_transactions_user_created_index');
            $table->index('status', 'digital_transactions_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_transactions');
    }
};