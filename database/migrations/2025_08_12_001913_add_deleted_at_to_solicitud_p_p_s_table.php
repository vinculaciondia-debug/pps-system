<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'solicitud_p_p_s';

        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn('solicitud_p_p_s', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        // No destructivo en prod
        // if (Schema::hasTable('solicitud_p_p_s') && Schema::hasColumn('solicitud_p_p_s', 'deleted_at')) {
        //     Schema::table('solicitud_p_p_s', function (Blueprint $table) {
        //         $table->dropSoftDeletes();
        //     });
        // }
    }
};




