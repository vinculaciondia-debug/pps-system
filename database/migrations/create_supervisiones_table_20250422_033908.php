<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supervisiones')) {
            Schema::create('supervisiones', function (Blueprint $table) {
                $table->id();

                // Clave foránea correcta: debe apuntar a solicitud_pps (no solicitud_p_p_s)
                $table->foreignId('solicitud_pps_id')
                      ->constrained('solicitud_pps')
                      ->cascadeOnDelete();

                // Campos ejemplo (ajusta a tu modelo real)
                $table->unsignedBigInteger('supervisor_id')->nullable();
                $table->text('observaciones')->nullable();
                $table->timestamps();
            });
        } else {
            // Si la tabla ya existiera sin FK, intenta agregarla
            Schema::table('supervisiones', function (Blueprint $table) {
                if (!Schema::hasColumn('supervisiones', 'solicitud_pps_id')) {
                    $table->foreignId('solicitud_pps_id')
                          ->constrained('solicitud_pps')
                          ->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('supervisiones')) {
            Schema::table('supervisiones', function (Blueprint $table) {
                // elimina FK si existe
                try { $table->dropForeign('supervisiones_solicitud_pps_id_foreign'); } catch (\Throwable $e) {}
                try { $table->dropColumn('solicitud_pps_id'); } catch (\Throwable $e) {}
            });
            Schema::dropIfExists('supervisiones');
        }
    }
};

