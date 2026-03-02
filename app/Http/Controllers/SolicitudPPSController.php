<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudPPS;
use App\Models\Documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\CalculadoraFechaFinalizacion;

class SolicitudPPSController extends Controller
{
    /**
     * Mostrar la pantalla de "Solicitar Práctica".
     */
    public function create()
    {
        $ultima = SolicitudPPS::with('documentos')
            ->where('user_id', Auth::id())
            ->latest('id')
            ->first();

        // Bloquear acceso si hay solicitud ACTIVA (SOLICITADA, APROBADA, FINALIZADA)
        $activa = $ultima && in_array($ultima->estado_solicitud, ['SOLICITADA', 'APROBADA', 'FINALIZADA']);

        $documentos = $ultima
            ? $ultima->documentos()->get(['id', 'tipo'])
            : collect();

        return view('estudiantes.solicitud', [
            'solicitud'  => $ultima,
            'activa'     => $activa,
            'documentos' => $documentos,
        ]);
    }

    /**
     * Guardar una nueva solicitud.
     */
    public function store(Request $request)
    {
        Log::info('📥 Iniciando store de solicitud', [
            'user_id' => Auth::id(),
            'tipo_practica' => $request->input('tipo_practica'),
            'dias_laborables_raw' => $request->input('dias_laborables'),
        ]);

        // Normalizar observaciones
        if ($request->filled('observaciones') && !$request->filled('observacion')) {
            $request->merge(['observacion' => $request->input('observaciones')]);
        }

        // ✅ NORMALIZAR dias_laborables: convertir "true"/"false" strings a boolean
        $diasLaborablesInput = $request->input('dias_laborables', []);
        $diasLaborablesNormalizados = [];

        foreach ($diasLaborablesInput as $dia => $config) {
            // Convertir explícitamente a boolean
            $activo = false;
            if (isset($config['activo'])) {
                if ($config['activo'] === 'true' || $config['activo'] === true || $config['activo'] === '1' || $config['activo'] === 1) {
                    $activo = true;
                }
            }
            
            $diasLaborablesNormalizados[$dia] = [
                'activo' => $activo,
                'hora_entrada' => $config['hora_entrada'] ?? null,
                'hora_salida' => $config['hora_salida'] ?? null,
                'horas_laborales' => isset($config['horas_laborales']) ? floatval($config['horas_laborales']) : 0,
            ];
        }

        // Logging detallado
        Log::info('📊 Días laborables normalizados:', [
            'original' => $diasLaborablesInput,
            'normalizado' => $diasLaborablesNormalizados
        ]);

        // Reemplazar en el request
        $request->merge(['dias_laborables' => $diasLaborablesNormalizados]);

        try {
            $validated = $request->validate([
                'tipo_practica'     => 'required|in:normal,trabajo',
                'modalidad' => [Rule::requiredIf($request->input('tipo_practica') === 'normal'), Rule::in(['Presencial','Semipresencial','Teletrabajo']),],
                'numero_cuenta'     => 'required|string|max:255',
                'telefono_alumno'   => 'required|string|max:50',
                'dni_estudiante'    => 'required|string|max:20', // ✅ NUEVO
                'foto_estudiante'   => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'nombre_empresa'    => 'required|string|max:255',
                'tipo_empresa'      => 'required|in:publica,privada',
                'direccion_empresa' => 'required|string|max:500',
                'nombre_jefe'       => 'required|string|max:255',
                'cargo_jefe'        => 'required|string|max:100', // ✅ NUEVO
                'nivel_academico_jefe' => [
    'required',
    Rule::in(['Bachillerato','Licenciado','Ingeniero','Abogado','Máster','Doctor']),
],
'empresa_id' => ['nullable','integer','exists:empresas,id'],
                'numero_jefe'       => 'required|string|max:50',
                'correo_jefe'       => 'required|email|max:255',
                'puesto_trabajo'    => 'nullable|string|max:255',
                'anios_trabajando'  => 'nullable|integer|min:0|max:100',
                'fecha_inicio'      => 'required|date|after_or_equal:today',
                'observacion'       => 'nullable|string|max:1000',

                // Validación simplificada
                'dias_laborables' => 'required|array',
                'dias_feriados' => 'nullable|string',

                // Documentos
                'documento_ia01'        => 'nullable|file|mimes:pdf|max:5120',
                'documento_ia02'        => 'nullable|file|mimes:pdf|max:5120',
                'colegiacion'           => 'nullable|file|mimes:pdf|max:5120',
                'carta_aceptacion'      => 'nullable|file|mimes:pdf|max:5120',
                'carta_presentacion'    => 'nullable|file|mimes:pdf|max:5120',
                'constancia_aprobacion' => 'nullable|file|mimes:pdf|max:5120',
                'constancia_trabajo'    => 'nullable|file|mimes:pdf|max:5120',
            ], [
                'telefono_alumno.required' => 'El número de teléfono es obligatorio',
                'foto_estudiante.required' => 'La foto del estudiante es obligatoria',
                'foto_estudiante.image' => 'Debe ser una imagen válida (JPG, PNG)',
                'tipo_empresa.required' => 'Debes indicar si es empresa pública o privada',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
                'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior',
                'dias_laborables.required' => 'Debes especificar tus días laborables',
                'dni_estudiante.required' => 'El DNI del estudiante es obligatorio',
                'cargo_jefe.required' => 'El cargo del jefe inmediato es obligatorio',
                'nivel_academico_jefe.required' => 'El nivel académico del jefe es obligatorio',
            ]);

            // ✅ Validar manualmente que haya al menos un día activo
            $hayDiaActivo = false;
            foreach ($validated['dias_laborables'] as $dia => $config) {
                if ($config['activo'] === true) {
                    $hayDiaActivo = true;
                    break;
                }
            }
            
            if (!$hayDiaActivo) {
                Log::warning('⚠️ No hay días activos');
                return back()
                    ->withInput()
                    ->with('error', 'Debes seleccionar al menos un día laborable');
            }

            Log::info('✅ Días laborables validados correctamente', [
                'dias_activos' => array_keys(array_filter($validated['dias_laborables'], fn($d) => $d['activo'] === true))
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validación', ['errors' => $e->errors()]);
            
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Por favor corrige los errores del formulario');
        }

        // Verificar solicitudes activas
        $solicitudActiva = SolicitudPPS::where('user_id', Auth::id())
            ->whereIn('estado_solicitud', ['SOLICITADA', 'APROBADA', 'FINALIZADA'])
            ->first();

        if ($solicitudActiva) {
            return redirect()
                ->route('estudiantes.solicitud')
                ->with('error', 'Ya tienes una solicitud activa. No puedes crear otra hasta que se finalice la actual.');
        }

        try {
            DB::beginTransaction();

            // ✅ Subir foto del estudiante
            $fotoPath = null;
            if ($request->hasFile('foto_estudiante')) {
                $foto = $request->file('foto_estudiante');
                $nombreFoto = "foto_" . Auth::id() . "_" . time() . "." . $foto->getClientOriginalExtension();
               $fotoPath = $foto->storeAs('fotos_estudiantes/' . Auth::id(), $nombreFoto, 'private');
                Log::info('✅ Foto estudiante subida', ['path' => $fotoPath]);
            }

            // ✅ Procesar días feriados (viene como JSON string)
            $diasFeriados = [];
            if ($request->filled('dias_feriados')) {
                $feriadosJson = $request->input('dias_feriados');
                try {
                    $decoded = json_decode($feriadosJson, true);
                    if (is_array($decoded)) {
                        $diasFeriados = $decoded;
                    }
                } catch (\Exception $e) {
                    Log::warning('⚠️ Error decodificando feriados', ['error' => $e->getMessage()]);
                }
            }
            
            Log::info('Feriados procesados:', ['count' => count($diasFeriados), 'data' => $diasFeriados]);

            // Usar el servicio de cálculo
            $calculadora = new CalculadoraFechaFinalizacion();
            
            // Calcular horas semanales y promedio
            $horasSemanales = $calculadora->calcularHorasSemanales($validated['dias_laborables']);
            $horasPromedioDiarias = $calculadora->calcularPromedioHorasDiarias($validated['dias_laborables']);
            
            // Calcular fecha de finalización con el nuevo sistema
            $fechaInicio = \Carbon\Carbon::parse($validated['fecha_inicio']);
            $horasTotales = 800;
            
            $resultado = $calculadora->calcularFechaFin(
                $fechaInicio,
                $horasTotales,
                $validated['dias_laborables'],
                $diasFeriados,
                3 // días adicionales
            );
            
            Log::info('📊 Cálculos completados:', [
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin_calculada' => $resultado['fecha_fin']->format('Y-m-d'),
                'dias_trabajados' => $resultado['dias_trabajados'],
                'horas_semanales' => $horasSemanales,
                'horas_promedio_diarias' => $horasPromedioDiarias,
            ]);
            
            // Crear horario legible (para compatibilidad con vistas antiguas)
            $horarioTexto = $this->generarHorarioTexto($validated['dias_laborables']);

            // Crear solicitud
            $solicitud = SolicitudPPS::create([
                'user_id'          => Auth::id(),
                'tipo_practica'    => $request->input('tipo_practica'),
                'modalidad'        => $request->input('modalidad'),
                'numero_cuenta'    => $request->input('numero_cuenta'),
                'telefono_alumno'  => $request->input('telefono_alumno'),
                'dni_estudiante'   => $request->input('dni_estudiante'), // ✅ NUEVO
                'foto_estudiante'  => $fotoPath,
                'nombre_empresa'   => $request->input('nombre_empresa'),
                'tipo_empresa'     => $request->input('tipo_empresa'),
                'direccion_empresa'=> $request->input('direccion_empresa'),
                'nombre_jefe'      => $request->input('nombre_jefe'),
                'cargo_jefe'       => $request->input('cargo_jefe'), // ✅ NUEVO
                'nivel_academico_jefe' => $request->input('nivel_academico_jefe'), // ✅ NUEVO
                'numero_jefe'      => $request->input('numero_jefe'),
                'correo_jefe'      => $request->input('correo_jefe'),
                'puesto_trabajo'   => $request->input('puesto_trabajo'),
                'anios_trabajando' => $request->input('anios_trabajando'),
                'empresa_id' => $request->input('empresa_id'),
                'fecha_inicio'     => $fechaInicio,
                'fecha_fin'        => $resultado['fecha_fin'],
                'horario'          => $horarioTexto,
                'horas_totales'    => $horasTotales,
                'fecha_finalizacion_calculada' => $resultado['fecha_fin'],
                'observacion'      => $request->input('observacion'),
                'estado_solicitud' => 'SOLICITADA',
                
                // Nuevos campos (guardar como JSON)
                'dias_laborables' => json_encode($validated['dias_laborables']),
                'dias_feriados' => json_encode($diasFeriados),
                'dias_adicionales' => 3,
                'horas_semanales' => $horasSemanales,
                'horas_promedio_diarias' => $horasPromedioDiarias,
            ]);

        Log::info('✅ Solicitud creada exitosamente', ['id' => $solicitud->id]);

// ============================================
// PROCESAR DOCUMENTOS CON LOGGING EXHAUSTIVO
// ============================================

Log::info('==================== INICIO DEBUG DOCUMENTOS ====================');

// 1️⃣ Ver TODOS los archivos que llegaron
Log::info('📦 TODOS los archivos en el request:', [
    'all_files' => array_keys($request->allFiles()),
    'total_count' => count($request->allFiles()),
]);

// 2️⃣ Verificar CADA campo individualmente
$camposEsperados = [
    'documento_ia01',
    'documento_ia02',
    'colegiacion',
    'carta_aceptacion',
    'carta_presentacion',
    'constancia_aprobacion',
    'constancia_trabajo',
];

Log::info('🔎 Verificación individual de campos:');
foreach ($camposEsperados as $campo) {
    $tiene = $request->hasFile($campo);
    
    Log::info("  Campo: {$campo}", [
        'hasFile' => $tiene ? 'SÍ ✅' : 'NO ❌',
        'file_info' => $tiene ? [
            'name' => $request->file($campo)->getClientOriginalName(),
            'size' => $request->file($campo)->getSize(),
            'mime' => $request->file($campo)->getMimeType(),
        ] : null,
    ]);
}

// 3️⃣ Procesar documentos
$documentos = [
    'documento_ia01'        => 'IA-01',
    'documento_ia02'        => 'IA-02',
    'colegiacion'           => 'COLEGIACION',
    'carta_aceptacion'      => 'CARTA_ACEPTACION',
    'carta_presentacion'    => 'CARTA_PRESENTACION',
    'constancia_aprobacion' => 'CONSTANCIA_100',
    'constancia_trabajo'    => 'CONSTANCIA_TRABAJO',
];

$documentosSubidos = 0;
$errores = [];

foreach ($documentos as $campo => $tipo) {
    Log::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    Log::info("🔄 Procesando: {$campo} → {$tipo}");
    
    if ($request->hasFile($campo)) {
        Log::info("  ✅ Tiene archivo, procediendo a guardar...");
        try {
            $archivo = $request->file($campo);
            $nombreArchivo = $tipo . '_' . time() . '_' . Auth::id() . '.pdf';
          $rutaArchivo = $archivo->storeAs('documentos_pps/' . Auth::id(), $nombreArchivo, 'private');

            Documento::create([
                'solicitud_pps_id' => $solicitud->id,
                'tipo'             => $tipo,
                'ruta'             => $rutaArchivo,
            ]);

            $documentosSubidos++;
            Log::info("  ✅ GUARDADO EXITOSAMENTE", ['ruta' => $rutaArchivo]);
            
        } catch (\Exception $e) {
            Log::error("  ❌ ERROR AL GUARDAR: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            $errores[] = ['campo' => $campo, 'error' => $e->getMessage()];
        }
    } else {
        Log::warning("  ⚠️ NO TIENE ARCHIVO (hasFile = false)");
    }
}

Log::info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
Log::info('📊 RESUMEN FINAL:', [
    'documentos_subidos' => $documentosSubidos,
    'documentos_esperados' => count($documentos),
    'errores_count' => count($errores),
    'errores' => $errores,
]);

Log::info('==================== FIN DEBUG DOCUMENTOS ====================');

            DB::commit();

            return redirect()
                ->route('estudiantes.dashboard')
                ->with('success', 'Solicitud enviada correctamente. Fecha estimada de finalización: ' . 
                       $resultado['fecha_fin']->format('d/m/Y'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Eliminar foto si falló
            if (isset($fotoPath) && Storage::disk('private')->exists($fotoPath)) {
                Storage::disk('private')->delete($fotoPath);
            }
            
            Log::error('❌ Error al guardar solicitud', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al enviar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar una solicitud de práctica.
     */
    public function cancelar(Request $request, $id)
    {
        $solicitud = SolicitudPPS::with('documentos')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!in_array($solicitud->estado_solicitud, ['SOLICITADA', 'APROBADA'])) {
            return redirect()->back()->with('error', 'No se puede cancelar esta solicitud.');
        }

        DB::transaction(function () use ($request, $solicitud) {
            // Registrar solicitud de cancelación (queda PENDIENTE para admin)
            DB::table('solicitudes_cancelacion')->insert([
                'user_id'    => Auth::id(),
                'motivo'     => $request->input('motivo', 'Cancelación solicitada por el estudiante'),
                'estado'     => 'PENDIENTE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Eliminar documentos asociados
            foreach ($solicitud->documentos as $doc) {
                Storage::disk('private')->delete($doc->ruta);
                $doc->delete();
            }

            // Cambiar estado y aplicar soft delete
            $solicitud->estado_solicitud = 'CANCELADA';
            $solicitud->save();
            $solicitud->delete();
        });

        return redirect()
            ->route('estudiantes.solicitud')
            ->with('status', 'Solicitud cancelada y documentos eliminados correctamente. Ahora puedes enviar una nueva.');
    }

    /**
     * Dashboard del estudiante.
     */
    public function dashboard()
    {
        $userId = Auth::id();

        $solicitud = SolicitudPPS::with('documentos')
            ->where('user_id', $userId)
            ->latest('id')
            ->first();

        return view('estudiantes.dashboard', compact('solicitud'));
    }

    /**
     * Ver documentos asociados a una solicitud.
     */
    public function verDocumentos($id)
    {
        $solicitud = SolicitudPPS::with('documentos')->findOrFail($id);

        if ($solicitud->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver los documentos de esta solicitud.');
        }

        return view('estudiantes.documentos.index', [
            'solicitud'  => $solicitud,
            'documentos' => $solicitud->documentos,
        ]);
    }

    /**
     * Ver documento individual.
     */
    public function ver($id)
    {
        $doc = Documento::findOrFail($id);

        if ($doc->solicitud->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este documento.');
        }

        return response()->file(storage_path("app/private/{$doc->ruta}"));
    }

    /**
     * Descargar documento.
     */
    public function descargar($id)
    {
        $doc = Documento::findOrFail($id);

        if ($doc->solicitud->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para descargar este documento.');
        }

        return Storage::disk('private')->download($doc->ruta, $doc->nombre_descarga);
    }

    /**
     * Eliminar documento.
     */
    public function eliminar($id)
    {
        $doc = Documento::findOrFail($id);

        if ($doc->solicitud->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar este documento.');
        }

        Storage::disk('private')->delete($doc->ruta);
        $doc->delete();

        return back()->with('success', 'Documento eliminado correctamente.');
    }

    /**
     * Genera un texto legible del horario (para compatibilidad)
     */
    private function generarHorarioTexto(array $diasLaborables): string
    {
        $dias = [];
        
        foreach ($diasLaborables as $nombreDia => $config) {
            if (isset($config['activo']) && $config['activo'] === true) {
                $entrada = $config['hora_entrada'] ?? '';
                $salida = $config['hora_salida'] ?? '';
                $horas = $config['horas_laborales'] ?? 0;
                
                $dias[] = ucfirst($nombreDia) . ": {$entrada} - {$salida} ({$horas} hrs)";
            }
        }
        
        return implode(' | ', $dias);
    }


    /**
 * Calcular fecha de finalización en tiempo real (AJAX)
 */
public function calcularFechaFinalizacion(Request $request)
{
    try {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'dias_laborables' => 'required|array',
            'dias_feriados' => 'nullable|string',
        ]);

        $fechaInicio = \Carbon\Carbon::parse($request->fecha_inicio);
        $diasLaborables = $request->dias_laborables;
        $feriados = json_decode($request->dias_feriados ?? '[]', true) ?? [];

        // Normalizar booleanos (JS envía "true"/"false" como strings)
        foreach ($diasLaborables as $dia => $config) {
            $diasLaborables[$dia]['activo'] = 
                ($config['activo'] === 'true' || $config['activo'] === true);
            $diasLaborables[$dia]['horas_laborales'] = 
                floatval($config['horas_laborales'] ?? 0);
        }

        // 🆕 CREAR INSTANCIA del service (no llamada estática)
        $calculadora = new \App\Services\CalculadoraFechaFinalizacion();
        
        // 🆕 LLAMAR al método con los parámetros correctos
        $resultado = $calculadora->calcularFechaFin(
            $fechaInicio,
            800, // horas totales requeridas
            $diasLaborables,
            $feriados,
            3 // días adicionales
        );

        return response()->json([
            'success' => true,
            'fecha_fin' => $resultado['fecha_fin']->format('Y-m-d'),
            'fecha_fin_formateada' => $resultado['fecha_fin']->format('d/m/Y'),
            'semanas_necesarias' => $resultado['semanas_necesarias'],
            'horas_semanales' => $resultado['horas_semanales'],
            'dias_trabajados' => $resultado['dias_trabajados'],
            'dias_feriados_excluidos' => $resultado['dias_feriados_excluidos'],
            'horas_acumuladas' => $resultado['horas_acumuladas'],
        ]);

    } catch (\Exception $e) {
        \Log::error('Error calculando fecha: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al calcular: ' . $e->getMessage()
        ], 500);
    }
}
}