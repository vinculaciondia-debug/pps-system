<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParametrosSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parametros = [
            [
                'clave' => 'dias_acceso_post_finalizacion',
                'valor' => '60',
                'descripcion' => 'Número de días que un estudiante puede acceder al sistema después de finalizar su práctica profesional',
                'tipo' => 'entero',
                'categoria' => 'acceso',
                'editable' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'clave' => 'coordinador_vinculacion',
                'valor' => 'Lic. Dilma Ortega',  // ← CAMBIA ESTO por el nombre real
                'descripcion' => 'Nombre del coordinador o coordinadora de vinculación universitaria',
                'tipo' => 'texto',
                'categoria' => 'contacto',
                'editable' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'clave' => 'supervisiones_requeridas',
                'valor' => '2',
                'descripcion' => 'Número de supervisiones requeridas para completar una práctica profesional',
                'tipo' => 'entero',
                'categoria' => 'supervisiones',
                'editable' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insertar todos los parámetros
        DB::table('parametros_sistema')->insert($parametros);
    }
}