<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'solicitud_p_p_s';

        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn('solicitud_p_p_s', 'observaciones')) {
                    $table->text('observaciones')->nullable()->after('estado_solicitud');
                }
            });

            // Si existe la columna antigua 'observacion', copia sus valores a 'observaciones'
            if (Schema::hasColumn($table, 'observacion') && Schema::hasColumn($table, 'observaciones')) {
                // Copia segura sin borrar la columna antigua
                DB::statement("UPDATE {$table} SET observaciones = observacion WHERE observaciones IS NULL AND observacion IS NOT NULL");
            }
        }
    }

    public function down(): void
    {
        // No borres datos en producción
        // if (Schema::hasTable('solicitud_p_p_s') && Schema::hasColumn('solicitud_p_p_s','observaciones')) {
        //     Schema::table('solicitud_p_p_s', function (Blueprint $table) {
        //         $table->dropColumn('observaciones');
        //     });
        // }
    }
};






