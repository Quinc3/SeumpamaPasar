<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_profile_id')->constrained('seller_profiles')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->enum('sales_channel', ['b2c', 'b2b', 'both']);
            $table->unsignedInteger('min_purchase_qty')->default(1);
            $table->enum('status', ['draft', 'pending_review', 'active', 'rejected', 'archived'])->default('draft');
            $table->text('rejection_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'category_id'], 'products_status_category_index');
            $table->index(['seller_profile_id', 'status'], 'products_seller_status_index');
            $table->index(['status', 'sales_channel'], 'products_status_channel_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};