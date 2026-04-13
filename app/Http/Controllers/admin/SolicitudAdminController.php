<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\SolicitudPPS;
use App\Models\Supervisor;
use App\Models\User;
use App\Models\Documento;
use App\Services\AuditService;

class SolicitudAdminController extends Controller
{
    /**
     * Mostrar todas las solicitudes pendientes
     */
    public function pendientes(Request $request)
    {
        $busqueda = $request->query('busqueda');

        $query = SolicitudPPS::with(['user', 'documentos'])
            ->where('estado_solicitud', 'SOLICITADA');

        // Búsqueda por nombre o email del estudiante
        if ($busqueda) {
            $query->whereHas('user', function($q) use ($busqueda) {
                $q->where('name', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            })->orWhere('numero_cuenta', 'LIKE', "%{$busqueda}%");
        }

        $solicitudes = $query->latest('id')->paginate(15);

        // Contadores
        $contadores = [
            'pendientes' => SolicitudPPS::where('estado_solicitud', 'SOLICITADA')->count(),
            'aprobadas' => SolicitudPPS::where('estado_solicitud', 'APROBADA')->count(),
            'rechazadas' => SolicitudPPS::where('estado_solicitud', 'RECHAZADA')->count(),
            'finalizadas' => SolicitudPPS::where('estado_solicitud', 'FINALIZADA')->count(),
        ];

        return view('admin.solicitudes.pendientes', compact('solicitudes', 'contadores'));
    }

    /**
     * Ver foto del estudiante (modificado para permitir acceso a supervisores)
     */
    public function verFoto($id)
    {
        $solicitud = SolicitudPPS::findOrFail($id);

        // permisos (deja tu lógica igual)
        $user = auth()->user();

        // soportar ambos sistemas: Spatie y campo users.rol
        $esAdmin = ($user->rol ?? null) === 'admin' || ($user->rol ?? null) === 'vinculacion' || $user->hasRole('admin');
        $esSupervisor = ($user->rol ?? null) === 'supervisor' || $user->hasRole('supervisor');

        $tieneAcceso = false;

        if ($esAdmin) {
            $tieneAcceso = true;
        } elseif ($esSupervisor && $user->supervisor) {
            $tieneAcceso = ((int)$solicitud->supervisor_id === (int)$user->supervisor->id);
        }

        abort_unless($tieneAcceso, 403, 'No tienes permiso para ver esta foto');

        
        $path = Storage::disk('private')->path($solicitud->foto_estudiante);

        if (!file_exists($path)) {
            Log::error('Foto no encontrada en disco private', [
                'solicitud_id' => $solicitud->id,
                'db_path' => $solicitud->foto_estudiante,
                'full_path' => $path,
            ]);
            abort(404, 'Archivo de foto no encontrado');
        }

        return response()->file($path);
    }


        /**
         * Ver detalle completo de una solicitud
         */
            public function show($id)
            {
                try {
                    $solicitud = SolicitudPPS::with([
                        'user',
                        'supervisor.user',
                        'documentos',      // ← IMPORTANTE: Cargar documentos
                        'supervisiones'
                    ])->findOrFail($id);

                    // Agregar URL de foto si existe
                    $solicitud->foto_estudiante_url = $solicitud->foto_estudiante 
                        ? route('admin.solicitud.foto', $solicitud->id) 
                        : null;

                    return response()->json([
                        'success' => true,
                        'solicitud' => $solicitud
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al obtener solicitud', [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Error al cargar la solicitud'
                    ], 500);
                }
            }

        /**
         * Aprobar solicitud y asignar supervisor
         */
        public function aprobar(Request $request, $id)
        {
            try {
                $solicitud = SolicitudPPS::findOrFail($id);

                if ($solicitud->estado_solicitud !== 'SOLICITADA') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Solo se pueden aprobar solicitudes en estado SOLICITADA'
                    ], 400);
                }

                // Si es práctica por trabajo, aprobar sin asignar supervisor
                if ($solicitud->tipo_practica === 'trabajo') {
                    $solicitud->update([
                        'estado_solicitud' => 'APROBADA',
                        'supervisor_id' => null,
                    ]);

                    Log::info('Práctica por trabajo aprobada sin supervisor: Solicitud #' . $id);

                    AuditService::log(
                        'aprobar_solicitud',
                        "Aprobó la solicitud #{$id} (práctica por trabajo) del estudiante {$solicitud->user->name}",
                        'SolicitudPPS', (int)$id,
                        ['tipo' => 'trabajo', 'estudiante' => $solicitud->user->name]
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Práctica por trabajo aprobada exitosamente (sin asignación de supervisor)'
                    ]);
                }

                // Para prácticas normales: asignar supervisor
                $modoAsignacion = $request->input('modo_asignacion', 'automatico');
                $supervisorId = null;
                $nombreSupervisor = '';

                if ($modoAsignacion === 'manual') {
                    // Modo manual: validar y usar el supervisor seleccionado
                    $request->validate([
                        'supervisor_id' => 'required|exists:supervisores,id'
                    ], [
                        'supervisor_id.required' => 'Debes seleccionar un supervisor en modo manual'
                    ]);
                    
                    $supervisorId = $request->supervisor_id;
                    
                    // Verificar disponibilidad del supervisor seleccionado
                    $supervisor = Supervisor::with('user')->findOrFail($supervisorId);
                    $asignados = SolicitudPPS::where('supervisor_id', $supervisorId)
                        ->whereIn('estado_solicitud', ['APROBADA', 'FINALIZADA'])
                        ->count();

                    if ($asignados >= $supervisor->max_estudiantes) {
                        return response()->json([
                            'success' => false,
                            'message' => 'El supervisor seleccionado ya alcanzó su capacidad máxima'
                        ], 400);
                    }
                    
                    $nombreSupervisor = $supervisor->user->name ?? 'Supervisor #' . $supervisor->id;
                    
                } else {
                    // Modo automático: buscar supervisor con menor carga
                    $supervisor = Supervisor::with('user')
                        ->where('activo', true)
                        ->whereRaw('(
                            SELECT COUNT(*) 
                            FROM solicitud_p_p_s 
                            WHERE solicitud_p_p_s.supervisor_id = supervisores.id 
                            AND solicitud_p_p_s.estado_solicitud IN ("APROBADA", "FINALIZADA")
                            AND solicitud_p_p_s.deleted_at IS NULL
                        ) < supervisores.max_estudiantes')
                        ->withCount(['solicitudes as asignados' => function($query) {
                            $query->whereIn('estado_solicitud', ['APROBADA', 'FINALIZADA']);
                        }])
                        ->orderBy('asignados', 'asc')
                        ->first();

                    if (!$supervisor) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No hay supervisores disponibles en este momento. Todos han alcanzado su capacidad máxima.'
                        ], 400);
                    }

                    $supervisorId = $supervisor->id;
                    $nombreSupervisor = $supervisor->user->name ?? 'Supervisor #' . $supervisor->id;
                }

                // Asignar supervisor y aprobar
                $solicitud->update([
                    'estado_solicitud' => 'APROBADA',
                    'supervisor_id' => $supervisorId,
                ]);

                $mensaje = $modoAsignacion === 'automatico'
                    ? "Solicitud aprobada y asignada automáticamente al supervisor: {$nombreSupervisor}"
                    : "Solicitud aprobada y asignada manualmente al supervisor: {$nombreSupervisor}";

                AuditService::log(
                    'aprobar_solicitud',
                    "Aprobó la solicitud #{$id} del estudiante {$solicitud->user->name} — Supervisor asignado: {$nombreSupervisor}",
                    'SolicitudPPS', (int)$id,
                    ['tipo' => 'normal', 'modo_asignacion' => $modoAsignacion, 'supervisor' => $nombreSupervisor, 'estudiante' => $solicitud->user->name]
                );

                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);

            } catch (\Exception $e) {
                Log::error('Error al aprobar solicitud', [
                    'solicitud_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aprobar la solicitud: ' . $e->getMessage()
                ], 500);
            }
        }

        /**
         * Rechazar solicitud con motivo
         */
        public function rechazar(Request $request, $id)
        {
            $request->validate([
                'observaciones' => 'required|string|max:1000',
            ], [
                'observaciones.required' => 'Debes explicar el motivo del rechazo',
                'observaciones.max' => 'El motivo no puede superar los 1000 caracteres',
            ]);

            try {
                $solicitud = SolicitudPPS::findOrFail($id);

                $solicitud->estado_solicitud = 'RECHAZADA';
                $solicitud->observaciones = $request->observaciones;
                $solicitud->supervisor_id = null;
                $solicitud->save();

                Log::info('Solicitud #' . $id . ' rechazada. Motivo: ' . $request->observaciones);

                AuditService::log(
                    'rechazar_solicitud',
                    "Rechazó la solicitud #{$id} del estudiante {$solicitud->user->name}",
                    'SolicitudPPS', (int)$id,
                    ['estudiante' => $solicitud->user->name, 'motivo' => $request->observaciones]
                );

                return redirect()
                    ->route('admin.solicitudes.pendientes')
                    ->with('success', 'Solicitud rechazada correctamente. El estudiante ha sido notificado.');

            } catch (\Exception $e) {
                Log::error('Error al rechazar solicitud: ' . $e->getMessage());
                
                return back()->with('error', 'Error: ' . $e->getMessage());
            }
        }

        /**
         * Obtener supervisores disponibles para asignar
         */
        public function getSupervisoresDisponibles()
        {
            try {
                //  Obtener supervisores activos con sus datos de usuario
                $supervisores = Supervisor::with('user')
                    ->where('activo', 1)
                    ->get()
                    ->map(function($supervisor) {
                        // Calcular estudiantes asignados
                        $asignados = $supervisor->estudiantes_asignados;

                        return [
                            'id' => $supervisor->id,
                            'nombre' => $supervisor->user->name ?? 'Supervisor #' . $supervisor->id,
                            'email' => $supervisor->user->email ?? 'N/A',
                            'asignados' => $asignados,
                            'max_estudiantes' => $supervisor->max_estudiantes,
                            'disponibles' => $supervisor->cupos_disponibles,
                            'lleno' => $supervisor->estaLleno(),
                            'porcentaje_ocupacion' => $supervisor->porcentaje_ocupacion,
                        ];
                    })
                    ->sortBy(function($supervisor) {
                        // Ordenar: primero los que tienen más cupo disponible
                        return $supervisor['lleno'] ? 999 : $supervisor['asignados'];
                    })
                    ->values();

                Log::info('Supervisores disponibles obtenidos: ' . $supervisores->count());

                return response()->json([
                    'success' => true,
                    'supervisores' => $supervisores
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Error al obtener supervisores: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar supervisores: ' . $e->getMessage(),
                    'supervisores' => []
                ], 500);
            }
        }

        /**
     * Cambiar supervisor asignado a una solicitud
     */
        public function cambiarSupervisor(Request $request, $id)
        {
        $request->validate([
            'supervisor_id' => 'required|exists:supervisores,id',
        ], [
            'supervisor_id.required' => 'Debes seleccionar un supervisor',
            'supervisor_id.exists' => 'El supervisor seleccionado no existe',
        ]);

        try {
            $solicitud = SolicitudPPS::findOrFail($id);

            // Verificar que la solicitud esté aprobada
            if ($solicitud->estado_solicitud !== 'APROBADA') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se puede cambiar el supervisor de solicitudes aprobadas'
                ], 400);
            }

            // Obtener el nuevo supervisor
            $nuevoSupervisor = Supervisor::with('user')
                ->where('id', $request->supervisor_id)
                ->where('activo', 1)
                ->first();

            if (!$nuevoSupervisor) {
                return response()->json([
                    'success' => false,
                    'message' => 'El supervisor seleccionado no está activo o no existe'
                ], 400);
            }

            // Verificar capacidad del nuevo supervisor
            if ($nuevoSupervisor->estaLleno()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El supervisor seleccionado ya alcanzó su capacidad máxima'
                ], 400);
            }

            $supervisorAnterior = $solicitud->supervisor ? $solicitud->supervisor->user->name : 'Ninguno';

            // Cambiar supervisor
            $solicitud->supervisor_id = $request->supervisor_id;
            $solicitud->save();

            Log::info('Supervisor cambiado en solicitud #' . $id . ' | Anterior: ' . $supervisorAnterior . ' | Nuevo: ' . $nuevoSupervisor->user->name);

            AuditService::log(
                'cambiar_supervisor',
                "Cambió el supervisor de la solicitud #{$id} ({$solicitud->user->name}): {$supervisorAnterior} → {$nuevoSupervisor->user->name}",
                'SolicitudPPS', (int)$id,
                ['estudiante' => $solicitud->user->name, 'supervisor_anterior' => $supervisorAnterior, 'supervisor_nuevo' => $nuevoSupervisor->user->name]
            );

            // Retornar JSON en lugar de redirect
            return response()->json([
                'success' => true,
                'message' => 'Supervisor cambiado exitosamente de ' . $supervisorAnterior . ' a ' . $nuevoSupervisor->user->name
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error al cambiar supervisor: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar supervisor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar solicitudes aprobadas
     */
    public function aprobadas(Request $request)
    {
    $busqueda = $request->query('busqueda');

    $query = SolicitudPPS::withCount('supervisiones') // ← Esto crea automáticamente supervisiones_count
        ->with(['user', 'supervisor.user', 'documentos'])
        ->where('estado_solicitud', 'APROBADA');

    if ($busqueda) {
        $query->whereHas('user', function($q) use ($busqueda) {
            $q->where('name', 'LIKE', "%{$busqueda}%")
              ->orWhere('email', 'LIKE', "%{$busqueda}%");
        })->orWhere('numero_cuenta', 'LIKE', "%{$busqueda}%");
    }

    $solicitudes = $query->latest('id')->paginate(15);

    $contadores = [
        'pendientes' => SolicitudPPS::where('estado_solicitud', 'SOLICITADA')->count(),
        'aprobadas' => SolicitudPPS::where('estado_solicitud', 'APROBADA')->count(),
        'rechazadas' => SolicitudPPS::where('estado_solicitud', 'RECHAZADA')->count(),
        'finalizadas' => SolicitudPPS::where('estado_solicitud', 'FINALIZADA')->count(),
    ];

    return view('admin.solicitudes.aprobadas', compact('solicitudes', 'contadores'));
    }

    /**
     * Mostrar solicitudes rechazadas
     */
    public function rechazadas(Request $request)
    {
        $busqueda = $request->query('busqueda');

        $query = SolicitudPPS::with(['user', 'documentos'])
            ->where('estado_solicitud', 'RECHAZADA');

        if ($busqueda) {
            $query->whereHas('user', function($q) use ($busqueda) {
                $q->where('name', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        $solicitudes = $query->latest('id')->paginate(15);

        $contadores = [
            'pendientes' => SolicitudPPS::where('estado_solicitud', 'SOLICITADA')->count(),
            'aprobadas' => SolicitudPPS::where('estado_solicitud', 'APROBADA')->count(),
            'rechazadas' => SolicitudPPS::where('estado_solicitud', 'RECHAZADA')->count(),
            'finalizadas' => SolicitudPPS::where('estado_solicitud', 'FINALIZADA')->count(),
        ];

        return view('admin.solicitudes.rechazadas', compact('solicitudes', 'contadores'));
    }

    /**
     * Mostrar solicitudes finalizadas
     */
    public function finalizadas(Request $request)
    {
    $busqueda = $request->get('busqueda');
    
    // Query con eager loading de todas las relaciones necesarias
    $query = SolicitudPPS::with([
        'user',
        'supervisor.user',
        'supervisiones',
        'documentos'
    ])
    ->where('estado_solicitud', 'FINALIZADA')
    ->orderBy('updated_at', 'desc');
    
    // Filtro de búsqueda
    if ($busqueda) {
        $query->where(function($q) use ($busqueda) {
            $q->where('numero_cuenta', 'LIKE', "%{$busqueda}%")
              ->orWhereHas('user', function($q2) use ($busqueda) {
                  $q2->where('name', 'LIKE', "%{$busqueda}%")
                     ->orWhere('email', 'LIKE', "%{$busqueda}%");
              });
        });
    }
    
    $solicitudes = $query->paginate(15)->appends(['busqueda' => $busqueda]);
    
    // Contadores
    $contadores = [
        'pendientes' => SolicitudPPS::where('estado_solicitud', 'SOLICITADA')->count(),
        'aprobadas' => SolicitudPPS::where('estado_solicitud', 'APROBADA')->count(),
        'rechazadas' => SolicitudPPS::where('estado_solicitud', 'RECHAZADA')->count(),
        'finalizadas' => SolicitudPPS::where('estado_solicitud', 'FINALIZADA')->count(),
    ];
    
    return view('admin.solicitudes.finalizadas', compact('solicitudes', 'contadores'));
 }
    /**
     * Mostrar solicitudes canceladas
     */
    public function canceladas(Request $request)
    {
        $busqueda = $request->query('busqueda');

        $query = SolicitudPPS::with(['user', 'documentos'])
            ->where('estado_solicitud', 'CANCELADA');

        if ($busqueda) {
            $query->whereHas('user', function($q) use ($busqueda) {
                $q->where('name', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        $solicitudes = $query->latest('id')->paginate(15);

        $contadores = [
            'pendientes' => SolicitudPPS::where('estado_solicitud', 'SOLICITADA')->count(),
            'aprobadas' => SolicitudPPS::where('estado_solicitud', 'APROBADA')->count(),
            'rechazadas' => SolicitudPPS::where('estado_solicitud', 'RECHAZADA')->count(),
            'finalizadas' => SolicitudPPS::where('estado_solicitud', 'FINALIZADA')->count(),
            'canceladas' => SolicitudPPS::where('estado_solicitud', 'CANCELADA')->count(),
        ];

        return view('admin.solicitudes.canceladas', compact('solicitudes', 'contadores'));
    }

    /**
     * Ver documentos de una solicitud
     */
    public function verDocumentos($id)
    {
        $solicitud = SolicitudPPS::with(['user', 'documentos'])->findOrFail($id);
        
        return view('admin.solicitudes.documentos', compact('solicitud'));
    }

    public function verSupervision($id)
{
    try {
        $supervision = \App\Models\Supervision::findOrFail($id);
        
        // Verificar que el archivo existe
        if (!$supervision->archivo) {
            abort(404, 'Archivo no encontrado');
        }
        
        if (!\Storage::disk('private')->exists($supervision->archivo)) {
            abort(404, 'Archivo no encontrado en el servidor');
        }
        
        $path = \Storage::disk('private')->path($supervision->archivo);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        Log::info('Admin visualizando supervisión #' . $id);
        
        // Mostrar inline si es PDF o imagen
        if ($extension === 'pdf') {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        }
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return response()->file($path, [
                'Content-Type' => mime_content_type($path),
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        }
        
        // Si no es PDF ni imagen, forzar descarga
        return \Storage::disk('private')->download($supervision->archivo);
        
    } catch (\Exception $e) {
        Log::error('Error al ver supervisión: ' . $e->getMessage());
        abort(404, 'Archivo no encontrado');
    }
}

    public function descargarSupervision($id)
 {
    try {
        $supervision = \App\Models\Supervision::findOrFail($id);
        
        // Verificar que el archivo existe
        if (!$supervision->archivo) {
            abort(404, 'Archivo no encontrado');
        }
        
        if (!\Storage::disk('private')->exists($supervision->archivo)) {
            abort(404, 'Archivo no encontrado en el servidor');
        }
        
        Log::info('Admin descargando supervisión #' . $id);
        
        return \Storage::disk('private')->download($supervision->archivo);
        
    } catch (\Exception $e) {
        Log::error('Error al descargar supervisión: ' . $e->getMessage());
        abort(404, 'Archivo no encontrado');
    }
 }
    /**
     * Finalizar solicitud
     */
public function finalizar(Request $request, $id)
{
    try {
        $solicitud = SolicitudPPS::findOrFail($id);

        if ($solicitud->estado_solicitud !== 'APROBADA') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden finalizar solicitudes en estado APROBADA'
            ], 400);
        }

        // Para prácticas normales: verificar requisitos
        if ($solicitud->tipo_practica === 'normal') {
            // Verificar que tenga 2 supervisiones
            $supervisionesCount = $solicitud->supervisiones()->count();
            if ($supervisionesCount < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requieren 2 supervisiones completadas para finalizar'
                ], 400);
            }

            // Verificar que tenga carta de finalización
            $tieneCartaFinalizacion = $solicitud->documentos()
                ->whereRaw('LOWER(tipo) = ?', ['carta_finalizacion'])
                ->exists();
                
            if (!$tieneCartaFinalizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere la carta de finalización para completar la práctica'
                ], 400);
            }
        }

        // Finalizar la solicitud
        // updated_at se actualizará automáticamente por Laravel
        $solicitud->update([
            'estado_solicitud' => 'FINALIZADA',
        ]);

        Log::info('Solicitud finalizada', [
            'solicitud_id' => $id,
            'tipo_practica' => $solicitud->tipo_practica,
            'estudiante' => $solicitud->user->name,
            'finalizada_en' => $solicitud->updated_at
        ]);

        AuditService::log(
            'finalizar_solicitud',
            "Finalizó la práctica #{$id} del estudiante {$solicitud->user->name}",
            'SolicitudPPS', (int)$id,
            ['estudiante' => $solicitud->user->name, 'tipo' => $solicitud->tipo_practica]
        );

        return response()->json([
            'success' => true,
            'message' => 'Práctica finalizada exitosamente'
        ]);

    } catch (\Exception $e) {
        Log::error('Error al finalizar solicitud', [
            'solicitud_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al finalizar la práctica: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Rechazar carta de finalización con observación
 */
public function rechazarCartaFinalizacion(Request $request, SolicitudPPS $solicitud, Documento $documento)
{
    $request->validate([
        'observacion' => 'required|string|max:1000'
    ], [
        'observacion.required' => 'Debes explicar el motivo del rechazo',
        'observacion.max' => 'La observación no puede superar los 1000 caracteres'
    ]);

    try {
        // Verificar que sea una carta de finalización
        if (strtolower($documento->tipo) !== 'carta_finalizacion') {
            return response()->json([
                'success' => false,
                'message' => 'El documento no es una carta de finalización.'
            ], 400);
        }

        // Verificar que la carta pertenece a la solicitud
        if ($documento->solicitud_pps_id !== $solicitud->id) {
            return response()->json([
                'success' => false,
                'message' => 'El documento no pertenece a esta solicitud.'
            ], 400);
        }

        // Actualizar el documento con el rechazo
        $documento->update([
            'estado_revision' => 'RECHAZADA',
            'observacion_revision' => $request->observacion,
            'revisado_por' => auth()->id(),
            'revisado_at' => now()
        ]);

        Log::info('Carta de finalización rechazada', [
            'documento_id' => $documento->id,
            'solicitud_id' => $solicitud->id,
            'estudiante' => $solicitud->user->name,
            'motivo' => $request->observacion,
            'revisado_por' => auth()->user()->name
        ]);

        // Retornar JSON
        return response()->json([
            'success' => true,
            'message' => 'Carta rechazada. El estudiante fue notificado y puede subir una nueva.'
        ]);

    } catch (\Exception $e) {
        Log::error('Error al rechazar carta de finalización', [
            'error' => $e->getMessage(),
            'documento_id' => $documento->id,
            'solicitud_id' => $solicitud->id
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al rechazar la carta: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Aprobar carta de finalización
 */
public function aprobarCartaFinalizacion(SolicitudPPS $solicitud, Documento $documento)
{
    try {
        // Verificar que sea una carta de finalización
        if (strtolower($documento->tipo) !== 'carta_finalizacion') {
            return response()->json([
                'success' => false,
                'message' => 'El documento no es una carta de finalización.'
            ], 400);
        }

        // Verificar que la carta pertenece a la solicitud
        if ($documento->solicitud_pps_id !== $solicitud->id) {
            return response()->json([
                'success' => false,
                'message' => 'El documento no pertenece a esta solicitud.'
            ], 400);
        }

        // Actualizar el documento con la aprobación
        $documento->update([
            'estado_revision' => 'APROBADA',
            'observacion_revision' => null, // Limpiar observaciones anteriores
            'revisado_por' => auth()->id(),
            'revisado_at' => now()
        ]);

        Log::info('Carta de finalización aprobada', [
            'documento_id' => $documento->id,
            'solicitud_id' => $solicitud->id,
            'estudiante' => $solicitud->user->name,
            'revisado_por' => auth()->user()->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Carta de finalización aprobada exitosamente.'
        ]);

    } catch (\Exception $e) {
        Log::error('Error al aprobar carta de finalización', [
            'error' => $e->getMessage(),
            'documento_id' => $documento->id,
            'solicitud_id' => $solicitud->id
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al aprobar la carta: ' . $e->getMessage()
        ], 500);
    }
}
}