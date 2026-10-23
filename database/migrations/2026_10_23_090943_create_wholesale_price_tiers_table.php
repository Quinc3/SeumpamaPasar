<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wholesale_price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('min_quantity');
            $table->decimal('price', 15, 2);
            $table->timestamps();

            $table->unique(['product_id', 'min_quantity'], 'wholesale_tiers_product_quantity_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wholesale_price_tiers');
    }
};