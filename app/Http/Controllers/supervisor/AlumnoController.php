<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use App\Models\SolicitudPPS;
use App\Models\Supervision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AlumnoController extends Controller
{
    /**
     * Mostrar lista de alumnos asignados
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Obtener supervisor
        $supervisor = Supervisor::where('user_id', $user->id)->firstOrFail();

        // Estado por defecto: APROBADA
        $estado = $request->input('estado', 'APROBADA');
        
        // Query base
        $query = SolicitudPPS::with(['user', 'supervisiones'])
            ->where('supervisor_id', $supervisor->id);
        
        // Filtros
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->whereHas('user', function($q) use ($busqueda) {
                $q->where('name', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        // Filtro por estado (TODAS no filtra)
        if ($estado && $estado !== 'TODAS') {
            $query->where('estado_solicitud', $estado);
        } else {
            // En "TODAS", mostrar APROBADAS primero
            $query->orderByRaw("CASE WHEN estado_solicitud = 'APROBADA' THEN 0 ELSE 1 END");
        }
        
        $alumnos = $query->latest('id')
            ->paginate(15)
            ->appends($request->query());
        
        $contadores = [
            'total' => SolicitudPPS::where('supervisor_id', $supervisor->id)
                ->whereIn('estado_solicitud', ['SOLICITADA', 'APROBADA'])
                ->count(),
            'aprobadas' => SolicitudPPS::where('supervisor_id', $supervisor->id)
                ->where('estado_solicitud', 'APROBADA')
                ->count(),
            'finalizadas' => SolicitudPPS::where('supervisor_id', $supervisor->id)
                ->where('estado_solicitud', 'FINALIZADA')
                ->count(),
        ];
        
        return view('supervisor.alumnos.index', compact('alumnos', 'contadores', 'supervisor', 'estado'));
    }

    /**
     * Ver detalle de un alumno
     */
    public function show($id)
    {
        try {
            $solicitud = SolicitudPPS::with([
                'user',
                'supervisor.user',
                'supervisiones' => function($query) {
                    $query->orderBy('numero_supervision', 'asc');
                },
                'documentos'
            ])->findOrFail($id);

            // Verificar que el supervisor tenga acceso a este estudiante
            $supervisorId = auth()->user()->supervisor->id;
            
            if ($solicitud->supervisor_id !== $supervisorId) {
                abort(403, 'No tienes permiso para ver este estudiante');
            }

            // Solo prácticas normales tienen supervisor
            if ($solicitud->tipo_practica !== 'normal') {
                abort(404, 'Esta práctica no requiere supervisión');
            }

            $totalSupervisiones = $solicitud->supervisiones->count();
            
            // Verificar si puede subir carta de finalización
            $puedeSubirCartaFinalizacion = $totalSupervisiones >= 2 && 
                                          $solicitud->estado_solicitud === 'APROBADA';

            return view('supervisor.alumnos.show', compact(
                'solicitud', 
                'totalSupervisiones',
                'puedeSubirCartaFinalizacion'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar alumno: ' . $e->getMessage());
            return redirect()->route('supervisor.alumnos.index')
                ->with('error', 'Error al cargar información del estudiante');
        }
    }

    /**
     * Subir supervisión
     */
  /**
 * Subir supervisión
 */
public function subirSupervision(Request $request, $solicitudId)
{
    $request->validate([
        'numero_supervision' => 'required|integer|min:1|max:2',
        'fecha_supervision' => 'required|date|before_or_equal:today',
        'comentario' => 'nullable|string|max:1000',
        'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ], [
        'numero_supervision.required' => 'El número de supervisión es obligatorio',
        'numero_supervision.min' => 'El número debe ser 1 o 2',
        'numero_supervision.max' => 'El número debe ser 1 o 2',
        'fecha_supervision.required' => 'La fecha de supervisión es obligatoria',
        'fecha_supervision.date' => 'La fecha debe ser válida',
        'fecha_supervision.before_or_equal' => 'La fecha no puede ser futura',
        'comentario.max' => 'El comentario no puede exceder 1000 caracteres',
        'archivo.required' => 'El archivo es obligatorio',
        'archivo.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG',
        'archivo.max' => 'El archivo no puede superar 5MB',
    ]);

    $user = Auth::user();
    
    // Obtener supervisor
    $supervisor = Supervisor::where('user_id', $user->id)->first();
    
    if (!$supervisor) {
        return back()->with('error', 'No se encontró el perfil de supervisor');
    }

    // Verificar que la solicitud pertenezca a este supervisor
    $solicitud = SolicitudPPS::where('id', $solicitudId)
        ->where('supervisor_id', $supervisor->id)
        ->firstOrFail();

    try {
        \DB::beginTransaction();

        // Verificar que no exista ya esa supervisión
        $existeSupervision = Supervision::where('solicitud_pps_id', $solicitudId)
            ->where('numero_supervision', $request->numero_supervision)
            ->exists();

        if ($existeSupervision) {
            return back()->with('error', 'Ya existe una supervisión con ese número');
        }

        // Guardar archivo
        $archivo = $request->file('archivo');
        $nombreArchivo = 'supervision_' . $request->numero_supervision . '_' . time() . '.' . $archivo->getClientOriginalExtension();
        $rutaArchivo = $archivo->storeAs('supervisiones/' . $solicitudId, $nombreArchivo, 'private');

        // Procesar ausencia según checkboxes
        $ausenciaSupervisor = null;
        if ($request->filled('ausencia_entrada') && $request->filled('ausencia_salida')) {
            $ausenciaSupervisor = 'ambas';
        } elseif ($request->filled('ausencia_entrada')) {
            $ausenciaSupervisor = 'entrada';
        } elseif ($request->filled('ausencia_salida')) {
            $ausenciaSupervisor = 'salida';
        }

        // Crear registro de supervisión
        $supervision = Supervision::create([
            'solicitud_pps_id' => $solicitudId,
            'numero_supervision' => $request->numero_supervision,
            'fecha_supervision' => $request->fecha_supervision,
            'ausencia_supervisor' => $ausenciaSupervisor,
            'comentario' => $request->comentario ?: null,
            'archivo' => $rutaArchivo,
        ]);

        \DB::commit();

        Log::info('Supervisión creada: Solicitud #' . $solicitudId . ' - Supervisión #' . $request->numero_supervision);

        return back()->with('success', 'Supervisión #' . $request->numero_supervision . ' subida exitosamente');

    } catch (\Exception $e) {
        \DB::rollBack();
        
        Log::error('Error al subir supervisión: ' . $e->getMessage());
        
        return back()->with('error', 'Error al subir supervisión: ' . $e->getMessage());
    }
}

    /**
     * Descargar archivo de supervisión
     */
    public function descargarSupervision($id)
    {
        $user = Auth::user();
        $supervisor = Supervisor::where('user_id', $user->id)->first();
        
        if (!$supervisor) {
            abort(403, 'No autorizado');
        }

        $supervision = Supervision::findOrFail($id);
        
        // Verificar que la supervisión pertenezca a una solicitud del supervisor
        if ($supervision->solicitud->supervisor_id != $supervisor->id) {
            abort(403, 'No autorizado');
        }

        if (!Storage::disk('private')->exists($supervision->archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('private')->download($supervision->archivo);
    }

    /**
     * Eliminar supervisión (solo si aún no se completó el proceso)
     */
    public function eliminarSupervision($id)
    {
        $user = Auth::user();
        $supervisor = Supervisor::where('user_id', $user->id)->first();
        
        if (!$supervisor) {
            abort(403, 'No autorizado');
        }

        $supervision = Supervision::findOrFail($id);
        
        // Verificar autorización
        if ($supervision->solicitud->supervisor_id != $supervisor->id) {
            abort(403, 'No autorizado');
        }

        // Verificar que la solicitud no esté finalizada
        if ($supervision->solicitud->estado_solicitud == 'FINALIZADA') {
            return back()->with('error', 'No se puede eliminar una supervisión de una práctica finalizada');
        }

        try {
            \DB::beginTransaction();

            // Eliminar archivo
            if (Storage::disk('private')->exists($supervision->archivo)) {
                Storage::disk('private')->delete($supervision->archivo);
            }

            // Eliminar registro
            $supervision->delete();

            \DB::commit();

            Log::info('Supervisión eliminada: ID #' . $id);

            return back()->with('success', 'Supervisión eliminada exitosamente');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            Log::error('Error al eliminar supervisión: ' . $e->getMessage());
            
            return back()->with('error', 'Error al eliminar supervisión');
        }
    }

    /**
     * API: Obtener datos de alumno para el modal
     */
    public function obtenerDatos($id)
    {
        $user = Auth::user();
        $supervisor = Supervisor::where('user_id', $user->id)->first();
        
        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Supervisor no encontrado'], 404);
        }

        $solicitud = SolicitudPPS::with(['user', 'supervisiones'])
            ->where('id', $id)
            ->where('supervisor_id', $supervisor->id)
            ->firstOrFail();

        $totalSupervisiones = $solicitud->supervisiones()->count();

        return response()->json([
            'success' => true,
            'solicitud' => $solicitud,
            'total_supervisiones' => $totalSupervisiones,
            'puede_subir_carta' => $totalSupervisiones >= 2,
        ]);
    }


}