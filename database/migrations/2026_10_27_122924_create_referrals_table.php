<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliate_profiles')->restrictOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referral_code', 16);
            $table->string('session_id');
            $table->timestamp('landed_at');
            $table->foreignId('checkout_id')->nullable()->constrained('checkouts')->nullOnDelete();
            $table->timestamps();

            $table->index(['affiliate_id', 'created_at']);
            $table->index('checkout_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};