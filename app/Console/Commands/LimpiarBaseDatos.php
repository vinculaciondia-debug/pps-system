<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarBaseDatos extends Command
{
    protected $signature   = 'db:limpiar {--force : No pide confirmación}';
    protected $description = 'Vacía todas las tablas de datos dejando solo el usuario admin';

    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  Esto BORRARÁ todos los datos excepto el admin. ¿Continuar?')) {
                $this->info('Cancelado.');
                return 0;
            }
        }

        // Guardar admin antes de limpiar
        $admin = DB::table('users')->where('rol', 'admin')->orderBy('id')->first();

        if (!$admin) {
            $this->error('No se encontró ningún usuario con rol admin. Operación cancelada.');
            return 1;
        }

        $this->info("Admin encontrado: {$admin->name} ({$admin->email})");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tablas = [
            'audit_logs',
            'supervisiones',
            'documentos',
            'solicitudes_cancelacion',
            'solicitudes_actualizacion',
            'solicitud_p_p_s',
            'supervisores',
            'empresas',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
        ];

        foreach ($tablas as $tabla) {
            try {
                DB::table($tabla)->truncate();
                $this->line("  ✓ {$tabla} vaciada");
            } catch (\Exception $e) {
                $this->warn("  - {$tabla} no existe o error: " . $e->getMessage());
            }
        }

        // Vaciar users y dejar solo el admin
        DB::table('users')->truncate();
        DB::table('users')->insert((array) $admin);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->newLine();
        $this->info('✅ Base de datos limpiada. Solo queda el admin: ' . $admin->email);

        return 0;
    }
}
