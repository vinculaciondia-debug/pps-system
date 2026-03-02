<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('supervisiones', function (Blueprint $table) {
            // Agregar después de numero_supervision
            $table->date('fecha_supervision')->nullable()->after('numero_supervision');
        });
    }

    public function down()
    {
        Schema::table('supervisiones', function (Blueprint $table) {
            $table->dropColumn('fecha_supervision');
        });
    }
};