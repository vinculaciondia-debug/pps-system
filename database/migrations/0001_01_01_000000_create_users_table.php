<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones (sin tocar lo que ya existe).
     */
    public function up(): void
    {
        // USERS
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->foreignId('current_team_id')->nullable();
                $table->string('profile_photo_path', 2048)->nullable();
                $table->timestamps();
            });
        } else {
            // Si la tabla existe, añade SOLO lo que falte (seguro)
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'remember_token')) {
                    $table->rememberToken();
                }
                if (!Schema::hasColumn('users', 'current_team_id')) {
                    $table->foreignId('current_team_id')->nullable()->after('remember_token');
                }
                if (!Schema::hasColumn('users', 'profile_photo_path')) {
                    $table->string('profile_photo_path', 2048)->nullable()->after('current_team_id');
                }
                // Asegura timestamps
                if (!Schema::hasColumn('users', 'created_at') || !Schema::hasColumn('users', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }

        // PASSWORD RESET TOKENS
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // SESSIONS (ya confirmaste que existe; esto solo la crea si faltara)
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverso NO destructivo (no borra nada).
     * Se deja vacío para evitar pérdida de datos en producción.
     */
    public function down(): void
    {
        // Intencionalmente vacío para no eliminar tablas existentes.
        // Si alguna vez necesitas revertir en desarrollo, hazlo manualmente y con backup.
    }
};

