<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_cancelacion', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitudes_cancelacion', 'solicitud_id')) {
                $table->unsignedBigInteger('solicitud_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('solicitudes_cancelacion', 'archivo')) {
                $table->string('archivo')->nullable()->after('motivo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_cancelacion', function (Blueprint $table) {
            if (Schema::hasColumn('solicitudes_cancelacion', 'solicitud_id')) {
                $table->dropColumn('solicitud_id');
            }
            if (Schema::hasColumn('solicitudes_cancelacion', 'archivo')) {
                $table->dropColumn('archivo');
            }
        });
    }
};
