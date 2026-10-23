<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->enum('seller_type', ['individual', 'business']);
            $table->string('store_name')->unique();
            $table->text('description');
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->timestamps();
            $table->index('status', 'seller_profiles_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};