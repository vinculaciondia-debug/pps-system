<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_nombre')->nullable();
            $table->string('user_rol')->nullable();
            $table->string('accion', 100);           // aprobar_solicitud, rechazar_solicitud, etc.
            $table->string('modelo', 100)->nullable(); // SolicitudPPS, User, etc.
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->text('descripcion');              // Texto legible del evento
            $table->json('datos_extra')->nullable();  // Info adicional (motivo, supervisor anterior, etc.)
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
