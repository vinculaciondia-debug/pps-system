<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'solicitud_p_p_s'; // <- nombre real en tu BD

        // 1) Crear tabla solo si NO existe
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();

                // Relación con users (nullable para datos legacy)
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

                // Campos de negocio
                $table->enum('tipo_practica', ['normal', 'trabajo']);
                $table->enum('modalidad', ['presencial', 'semipresencial', 'teletrabajo'])->nullable();

                $table->string('numero_cuenta');
                $table->string('nombre_empresa');
                $table->string('direccion_empresa');
                $table->string('nombre_jefe');
                $table->string('numero_jefe');
                $table->string('correo_jefe');

                // Solo “trabajo”
                $table->string('puesto_trabajo')->nullable();
                $table->unsignedInteger('anios_trabajando')->nullable();

                // Solo “normal”
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->string('horario')->nullable();

                // Opcionales frecuentes
                $table->text('observaciones')->nullable();

                // Estado de flujo PPS
                $table->enum('estado_solicitud', ['SOLICITADA','APROBADA','RECHAZADA','CANCELADA','FINALIZADA'])
                      ->default('SOLICITADA');

                $table->timestamps();
                // Si en tu proyecto usas borrado lógico, descomenta:
                // $table->softDeletes();
            });
        }

        // 2) Si la tabla ya existe, AGREGAR SOLO lo que falte (idempotente)
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {

                if (!Schema::hasColumn($tableName, 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                }

                if (!Schema::hasColumn($tableName, 'tipo_practica')) {
                    $table->enum('tipo_practica', ['normal', 'trabajo'])->after('user_id');
                }
                if (!Schema::hasColumn($tableName, 'modalidad')) {
                    $table->enum('modalidad', ['presencial', 'semipresencial', 'teletrabajo'])->nullable();
                }

                if (!Schema::hasColumn($tableName, 'numero_cuenta')) {
                    $table->string('numero_cuenta');
                }
                if (!Schema::hasColumn($tableName, 'nombre_empresa')) {
                    $table->string('nombre_empresa');
                }
                if (!Schema::hasColumn($tableName, 'direccion_empresa')) {
                    $table->string('direccion_empresa');
                }
                if (!Schema::hasColumn($tableName, 'nombre_jefe')) {
                    $table->string('nombre_jefe');
                }
                if (!Schema::hasColumn($tableName, 'numero_jefe')) {
                    $table->string('numero_jefe');
                }
                if (!Schema::hasColumn($tableName, 'correo_jefe')) {
                    $table->string('correo_jefe');
                }

                if (!Schema::hasColumn($tableName, 'puesto_trabajo')) {
                    $table->string('puesto_trabajo')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'anios_trabajando')) {
                    $table->unsignedInteger('anios_trabajando')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'fecha_inicio')) {
                    $table->date('fecha_inicio')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'fecha_fin')) {
                    $table->date('fecha_fin')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'horario')) {
                    $table->string('horario')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'observaciones')) {
                    $table->text('observaciones')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'estado_solicitud')) {
                    $table->enum('estado_solicitud', ['SOLICITADA','APROBADA','RECHAZADA','CANCELADA','FINALIZADA'])
                          ->default('SOLICITADA');
                }

                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }

                // Si usas borrado lógico en el proyecto y no existe:
                // if (!Schema::hasColumn($tableName, 'deleted_at')) {
                //     $table->softDeletes();
                // }
            });
        }
    }

    public function down(): void
    {
        // NO se elimina la tabla para no perder datos.
        // Si necesitas revertir en desarrollo, hazlo manualmente con respaldo.
        // Schema::dropIfExists('solicitud_p_p_s');
    }
};


