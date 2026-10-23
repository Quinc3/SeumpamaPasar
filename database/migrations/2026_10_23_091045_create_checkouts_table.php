<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('reference')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending_payment', 'paid', 'cancelled'])->default('pending_payment');
            $table->timestamps();

            $table->index(['user_id', 'status'], 'checkouts_user_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};