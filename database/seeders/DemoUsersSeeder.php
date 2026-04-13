<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Supervisor;

class DemoUsersSeeder extends Seeder
{
    const ADMIN_ID        = 5;
    const PASS_DEMO       = 'Aa1234567@';

    public function run(): void
    {
        // ── 1. Limpiar datos relacionados a usuarios que se van a borrar ─────
        $this->command->info('Limpiando datos anteriores...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Obtener IDs a borrar (todos menos el admin)
        $idsABorrar = User::where('id', '!=', self::ADMIN_ID)->pluck('id');

        if ($idsABorrar->isNotEmpty()) {
            // Solicitudes y datos relacionados
            $solicitudesIds = DB::table('solicitud_p_p_s')
                ->whereIn('user_id', $idsABorrar)
                ->pluck('id');

            if ($solicitudesIds->isNotEmpty()) {
                DB::table('documentos')->whereIn('solicitud_pps_id', $solicitudesIds)->delete();
                DB::table('supervisiones')->whereIn('solicitud_pps_id', $solicitudesIds)->delete();
                DB::table('solicitudes_cancelacion')->whereIn('solicitud_id', $solicitudesIds)->delete();
            }

            DB::table('solicitud_p_p_s')->whereIn('user_id', $idsABorrar)->delete();
            DB::table('solicitudes_actualizacion')->whereIn('user_id', $idsABorrar)->delete();
            DB::table('solicitudes_cancelacion')->whereIn('user_id', $idsABorrar)->delete();

            // Perfiles de supervisor
            $supervisorIds = DB::table('supervisores')
                ->whereIn('user_id', $idsABorrar)
                ->pluck('id');

            if ($supervisorIds->isNotEmpty()) {
                DB::table('supervisores')->whereIn('id', $supervisorIds)->delete();
            }

            // Roles Spatie
            DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->whereIn('model_id', $idsABorrar)
                ->delete();

            // Sesiones y tokens
            DB::table('sessions')->whereIn('user_id', $idsABorrar)->delete();
            DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\\Models\\User')
                ->whereIn('tokenable_id', $idsABorrar)
                ->delete();

            // Borrar usuarios
            User::whereIn('id', $idsABorrar)->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Datos anteriores eliminados.');

        // ── 2. Crear Supervisores ────────────────────────────────────────────
        $this->command->info('Creando supervisores...');

        $supervisores = [
            ['name' => 'Carlos Alberto Mejía Rivera',    'email' => 'carlos.mejia@unah.edu.hn'],
            ['name' => 'Diana Lucía Flores Aguilar',     'email' => 'diana.flores@unah.edu.hn'],
            ['name' => 'Roberto José Pineda Martínez',   'email' => 'roberto.pineda@unah.edu.hn'],
            ['name' => 'Sandra Patricia Reyes Canales',  'email' => 'sandra.reyes@unah.edu.hn'],
            ['name' => 'Miguel Ángel Torres Bustamante', 'email' => 'miguel.torres@unah.edu.hn'],
        ];

        foreach ($supervisores as $datos) {
            $user = User::create([
                'name'              => $datos['name'],
                'email'             => $datos['email'],
                'password'          => Hash::make(self::PASS_DEMO),
                'rol'               => 'supervisor',
                'email_verified_at' => now(),
            ]);

            $user->assignRole('supervisor');

            Supervisor::create([
                'user_id'        => $user->id,
                'activo'         => true,
                'max_estudiantes' => 8,
                'es_admin'       => false,
            ]);
        }

        $this->command->info('5 supervisores creados.');

        // ── 3. Crear Estudiantes ─────────────────────────────────────────────
        $this->command->info('Creando estudiantes...');

        $estudiantes = [
            ['name' => 'José Antonio Hernández López',    'email' => 'jose.hernandez@unah.hn',    'cuenta' => '20181001234'],
            ['name' => 'María Fernanda Díaz Suazo',       'email' => 'maria.diaz@unah.hn',         'cuenta' => '20181002345'],
            ['name' => 'Luis Eduardo Castillo Paz',       'email' => 'luis.castillo@unah.hn',      'cuenta' => '20191003456'],
            ['name' => 'Ana Gabriela Molina Soto',        'email' => 'ana.molina@unah.hn',         'cuenta' => '20191004567'],
            ['name' => 'Kevin Josué Álvarez Cruz',        'email' => 'kevin.alvarez@unah.hn',      'cuenta' => '20201005678'],
            ['name' => 'Karla Beatriz Núñez Ramos',       'email' => 'karla.nunez@unah.hn',        'cuenta' => '20201006789'],
            ['name' => 'Edwin Ramón Gutiérrez Valladares','email' => 'edwin.gutierrez@unah.hn',    'cuenta' => '20181007890'],
            ['name' => 'Sonia Elizabeth Romero Banegas',  'email' => 'sonia.romero@unah.hn',       'cuenta' => '20181008901'],
            ['name' => 'Oscar Mauricio Reconco Elvir',    'email' => 'oscar.reconco@unah.hn',      'cuenta' => '20191009012'],
            ['name' => 'Daniela Paola Zúniga Fonseca',   'email' => 'daniela.zuniga@unah.hn',     'cuenta' => '20191010123'],
            ['name' => 'Ricardo Alejandro Meza Euceda',   'email' => 'ricardo.meza@unah.hn',       'cuenta' => '20201011234'],
            ['name' => 'Wendy Carolina Zelaya Maradiaga', 'email' => 'wendy.zelaya@unah.hn',       'cuenta' => '20201012345'],
            ['name' => 'Jorge Luis Amador Perdomo',       'email' => 'jorge.amador@unah.hn',       'cuenta' => '20181013456'],
            ['name' => 'Patricia Guadalupe Orellana Paz', 'email' => 'patricia.orellana@unah.hn',  'cuenta' => '20181014567'],
            ['name' => 'Erick Daniel Cerrato Valladares', 'email' => 'erick.cerrato@unah.hn',      'cuenta' => '20191015678'],
            ['name' => 'Cindy Marisol Ponce Zavala',      'email' => 'cindy.ponce@unah.hn',        'cuenta' => '20191016789'],
            ['name' => 'Mario Antonio Funez Sierra',      'email' => 'mario.funez@unah.hn',        'cuenta' => '20201017890'],
            ['name' => 'Leslie Johanna Leiva Andino',     'email' => 'leslie.leiva@unah.hn',       'cuenta' => '20201018901'],
            ['name' => 'Bryan Josué Cálix Portillo',      'email' => 'bryan.calix@unah.hn',        'cuenta' => '20181019012'],
            ['name' => 'Fátima Abigail Discua Rodas',     'email' => 'fatima.discua@unah.hn',      'cuenta' => '20181020123'],
        ];

        foreach ($estudiantes as $datos) {
            $user = User::create([
                'name'              => $datos['name'],
                'email'             => $datos['email'],
                'password'          => Hash::make(self::PASS_DEMO),
                'rol'               => 'estudiante',
                'email_verified_at' => now(),
            ]);

            $user->assignRole('estudiante');
        }

        $this->command->info('20 estudiantes creados.');

        // ── Resumen ──────────────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  SEEDER COMPLETADO');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  Admin:        adminpps.dia@unah.edu.hn');
        $this->command->info('  Pass admin:   PPS_2024@_01_DIA');
        $this->command->info('  Supervisores: 5 creados');
        $this->command->info('  Estudiantes:  20 creados');
        $this->command->info('  Pass demo:    Aa1234567@');
        $this->command->info('═══════════════════════════════════════════');
    }
}
