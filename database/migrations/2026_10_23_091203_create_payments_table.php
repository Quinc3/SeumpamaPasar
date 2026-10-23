<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkout_id')->unique()->constrained('checkouts')->restrictOnDelete();
            $table->enum('method', ['demo_transfer', 'demo_ewallet', 'demo_va']);
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('reference')->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamps();

            $table->index('status', 'payments_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};