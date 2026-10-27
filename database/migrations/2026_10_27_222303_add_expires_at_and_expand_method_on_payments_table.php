<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('method', 50)->change();
            });
        } else {
            DB::statement("ALTER TABLE payments MODIFY COLUMN method VARCHAR(50) NOT NULL");
        }

        if (! Schema::hasColumn('payments', 'expires_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'expires_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('expires_at');
            });
        }
    }
};