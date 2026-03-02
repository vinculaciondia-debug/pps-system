<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Agregar campo para identificar si un supervisor es admin
        Schema::table('supervisores', function (Blueprint $table) {
            $table->boolean('es_admin')->default(false)->after('activo');
        });
    }

    public function down()
    {
        Schema::table('supervisores', function (Blueprint $table) {
            $table->dropColumn('es_admin');
        });
    }
};