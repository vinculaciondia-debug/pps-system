<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'solicitud_pps';

        if (!Schema::hasTable($tableName)) return;

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        $tableName = 'solicitud_pps';

        if (!Schema::hasTable($tableName)) return;

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (Schema::hasColumn($tableName, 'user_id')) {
                try {
                    $table->dropConstrainedForeignId('user_id');
                } catch (\Throwable $e) {
                    try { $table->dropForeign($tableName.'_user_id_foreign'); } catch (\Throwable $e2) {}
                    try { $table->dropColumn('user_id'); } catch (\Throwable $e3) {}
                }
            }
        });
    }
};



