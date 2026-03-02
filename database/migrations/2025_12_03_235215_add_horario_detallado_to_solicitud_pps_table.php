<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitud_p_p_s', function (Blueprint $table) {
            // Horario detallado por día (JSON)
            // Guarda entrada, salida y horas laborales de cada día
            $table->json('dias_laborables')->nullable()->after('horario');
            
            // Días feriados que no se cuentan (JSON)
            // Ejemplo: [{"fecha": "2025-12-25", "motivo": "Navidad"}]
            $table->json('dias_feriados')->nullable()->after('dias_laborables');
            
            // Días adicionales al final (por defecto 3)
            $table->integer('dias_adicionales')->default(3)->after('dias_feriados');
            
            // Horas semanales calculadas
            $table->decimal('horas_semanales', 5, 2)->nullable()->after('dias_adicionales');
            
            // Promedio de horas por día trabajado
            $table->decimal('horas_promedio_diarias', 4, 2)->nullable()->after('horas_semanales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_p_p_s', function (Blueprint $table) {
            $table->dropColumn([
                'dias_laborables',
                'dias_feriados',
                'dias_adicionales',
                'horas_semanales',
                'horas_promedio_diarias'
            ]);
        });
    }
};