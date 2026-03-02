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
        Schema::create('parametros_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique()->comment('Identificador único del parámetro');
            $table->text('valor')->comment('Valor del parámetro');
            $table->text('descripcion')->nullable()->comment('Descripción del parámetro');
            $table->string('tipo', 50)->default('texto')->comment('Tipo de dato: entero, texto, booleano, array');
            $table->string('categoria', 50)->nullable()->comment('Categoría del parámetro para agrupar');
            $table->boolean('editable')->default(true)->comment('Si puede ser editado desde la interfaz admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_sistema');
    }
};