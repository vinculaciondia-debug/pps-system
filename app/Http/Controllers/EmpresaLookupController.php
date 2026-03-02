<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmpresaLookupController extends Controller
{
    /**
     * Buscar empresas por nombre (autocomplete)
     * GET /estudiantes/empresas/buscar?q=uni
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $empresas = Empresa::query()
            ->where('activa', true)
            ->where('nombre', 'like', "%{$q}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get(['id', 'nombre']);

        return response()->json($empresas);
    }

    /**
     * Crear empresa nueva (desde autocomplete)
     * POST /estudiantes/empresas
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:255',
                // Solo letras, números, espacios y símbolos comunes
                'regex:/^[\pL\pN\s\.\,&\-\(\)\/]+$/u',
            ],
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios y símbolos: . , & - ( ) /',
        ]);

        $nombreNormalizado = $this->normalizarNombreEmpresa($data['nombre']);

        // Buscar si ya existe (evitar duplicados por formato)
        $empresa = Empresa::where('nombre', $nombreNormalizado)->first();

        if (!$empresa) {
            try {
                $empresa = Empresa::create([
                    'nombre' => $nombreNormalizado,
                    'activa' => true,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // En caso de colisión UNIQUE por concurrencia
                Log::warning('Colisión UNIQUE en empresas', [
                    'nombre' => $nombreNormalizado,
                ]);

                $empresa = Empresa::where('nombre', $nombreNormalizado)->firstOrFail();
            }
        }

        return response()->json([
            'id' => $empresa->id,
            'nombre' => $empresa->nombre,
        ], 201);
    }

    /**
     * Normaliza el nombre de la empresa
     * - Trim
     * - Un solo espacio
     * - Title Case
     * - Fuerza siglas comunes en mayúscula
     */
    private function normalizarNombreEmpresa(string $nombre): string
    {
        //  Trim + colapsar espacios múltiples
        $nombre = preg_replace('/\s+/u', ' ', trim($nombre));

        //  Todo a minúsculas
        $nombre = mb_strtolower($nombre, 'UTF-8');

        // Title Case
        $nombre = mb_convert_case($nombre, MB_CASE_TITLE, 'UTF-8');

        // Reglas especiales para siglas
        $map = [
            'Unah' => 'UNAH',
            'Hondutel' => 'HONDUTEL',
            'S.a.' => 'S.A.',
            'S. A.' => 'S.A.',
            'Sa' => 'S.A.',
            'S De Rl' => 'S. DE R.L.',
            'De' => 'de',
            'Los' => 'los',
            'Del' => 'del',
            'La' => 'la',
            'El' => 'el',
            'Y' => 'y',
        ];

        foreach ($map as $from => $to) {
            $nombre = preg_replace('/\b' . preg_quote($from, '/') . '\b/u', $to, $nombre);
        }

        return $nombre;
    }
}
