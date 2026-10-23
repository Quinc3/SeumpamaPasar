<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_products', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['electricity_bill', 'electricity_token', 'pulse', 'data_package']);
            $table->string('code')->unique();
            $table->string('name');
            $table->string('provider')->nullable();
            $table->decimal('nominal_value', 15, 2)->nullable();
            $table->decimal('base_price', 15, 2);
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active'], 'digital_products_type_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_products');
    }
};