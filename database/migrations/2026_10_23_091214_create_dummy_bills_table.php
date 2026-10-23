<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dummy_bills', function (Blueprint $table) {
            $table->id();
            $table->string('meter_number', 32);
            $table->string('customer_name');
            $table->string('period', 7);
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->unique(['meter_number', 'period'], 'dummy_bills_meter_period_unique');
            $table->index('meter_number', 'dummy_bills_meter_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dummy_bills');
    }
};