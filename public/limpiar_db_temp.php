<?php
/**
 * Script TEMPORAL para limpiar la base de datos.
 * ⚠️ BORRAR este archivo después de usarlo.
 */

// Carga el entorno Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Verificar admin
$admin = DB::table('users')->where('rol', 'admin')->orderBy('id')->first();

if (!$admin) {
    die('<p style="color:red">❌ No se encontró usuario admin. Operación cancelada.</p>');
}

echo "<pre style='font-family:monospace; font-size:14px; padding:20px'>";
echo "✅ Admin encontrado: {$admin->name} ({$admin->email})\n\n";

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
        echo "  ✓ {$tabla} vaciada\n";
    } catch (\Exception $e) {
        echo "  - {$tabla}: " . $e->getMessage() . "\n";
    }
}

// Guardar solo el admin en users
DB::table('users')->truncate();
DB::table('users')->insert((array) $admin);

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n✅ ¡Listo! Base de datos limpiada.\n";
echo "Solo queda el admin: <strong>{$admin->email}</strong>\n\n";
echo "<span style='color:red; font-weight:bold'>⚠️ IMPORTANTE: Borra este archivo ahora → /public/limpiar_db_temp.php</span>\n";
echo "</pre>";
