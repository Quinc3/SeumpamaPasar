<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('escrow_amount', 15, 2)->default(0)->after('refunded_at');
            $table->timestamp('escrowed_at')->nullable()->after('escrow_amount');
            $table->timestamp('released_at')->nullable()->after('escrowed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['escrow_amount', 'escrowed_at', 'released_at']);
        });
    }
};