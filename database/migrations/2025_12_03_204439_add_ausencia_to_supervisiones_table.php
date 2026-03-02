<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('supervisiones', function (Blueprint $table) {
            // Solo guardamos si hubo ausencia: null, 'entrada', 'salida', 'ambas'
            $table->enum('ausencia_supervisor', ['entrada', 'salida', 'ambas'])->nullable()->after('fecha_supervision');
        });
    }

    public function down()
    {
        Schema::table('supervisiones', function (Blueprint $table) {
            $table->dropColumn('ausencia_supervisor');
        });
    }
};