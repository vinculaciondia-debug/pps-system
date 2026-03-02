<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\SolicitudActualizacion;
use App\Models\SolicitudPPS;
use App\Models\Documento;

class SolicitudActualizacionController extends Controller
{
    /**
     * Estudiante: mostrar formulario
     */

    public function create()
    {
        // Verificar que el estudiante tenga una solicitud activa
        $solicitudActiva = SolicitudPPS::where('user_id', Auth::id())
            ->whereIn('estado_solicitud', ['SOLICITADA', 'APROBADA'])
            ->latest('id')
            ->first();

        // Si NO tiene solicitud activa, redirigir al dashboard
        if (!$solicitudActiva) {
            return redirect()
                ->route('estudiantes.dashboard')
                ->with('error', 'No puedes solicitar una actualización sin tener una solicitud activa. Primero debes crear o tener aprobada una solicitud de práctica.');
        }

        // ✅ Si tiene solicitud activa, mostrar formulario
        return view('estudiantes.solicitud_actualizacion', compact('solicitudActiva'));
    }
    /**
     * Estudiante: guardar solicitud de actualización
     */
    public function store(Request $request)
    {
        $request->validate([
            'motivo' => 'required|string|max:1000',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10 MB
        ], [
            'motivo.required' => 'Debes explicar el motivo de la actualización',
            'archivo.required' => 'Debes subir un documento de respaldo',
            'archivo.mimes' => 'Solo se permiten archivos PDF, JPG, JPEG o PNG',
            'archivo.max' => 'El archivo no puede superar los 10 MB',
        ]);

        try {
            // Guardar archivo en storage/app/public/actualizaciones
            $archivo = $request->file('archivo')->store('actualizaciones', 'public');

            SolicitudActualizacion::create([
                'user_id' => Auth::id(),
                'motivo' => $request->motivo,
                'archivo' => $archivo,
                'estado' => SolicitudActualizacion::EST_PENDIENTE,
            ]);

            Log::info('Solicitud de actualización creada por user_id: ' . Auth::id());

            return redirect()
                ->route('estudiantes.dashboard')
                ->with('success', 'Solicitud de actualización enviada correctamente');

        } catch (\Exception $e) {
            Log::error('Error al crear solicitud de actualización: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error al enviar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Admin: ver todas las solicitudes de actualización
     */
    public function index(Request $request)
    {
        $estado = $request->query('estado'); // Filtro opcional por estado
        $busqueda = $request->query('busqueda'); // Búsqueda por nombre

        $query = SolicitudActualizacion::with('user');

        // Filtro por estado
        if ($estado && in_array($estado, ['PENDIENTE', 'APROBADA', 'RECHAZADA'])) {
            $query->where('estado', $estado);
        }

        // Búsqueda por nombre o email del estudiante
        if ($busqueda) {
            $query->whereHas('user', function($q) use ($busqueda) {
                $q->where('name', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        // Ordenar por más reciente y paginar
        $solicitudes = $query->latest('id')->paginate(15);

        // Contadores para los tabs
        $contadores = [
            'total' => SolicitudActualizacion::count(),
            'pendientes' => SolicitudActualizacion::where('estado', 'PENDIENTE')->count(),
            'aprobadas' => SolicitudActualizacion::where('estado', 'APROBADA')->count(),
            'rechazadas' => SolicitudActualizacion::where('estado', 'RECHAZADA')->count(),
        ];

        return view('admin.solicitudes.actualizacion', compact('solicitudes', 'contadores'));
    }

    /**
     * Admin: aprobar o rechazar actualización
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:APROBADA,RECHAZADA',
            'observacion' => 'nullable|string|max:1000',
        ]);

        try {
            $solicitudActualizacion = SolicitudActualizacion::findOrFail($id);

            // Actualizar estado y observación
            $solicitudActualizacion->estado = $request->estado;
            $solicitudActualizacion->observacion = $request->observacion;
            $solicitudActualizacion->save();

            Log::info('Solicitud actualización #' . $id . ' → ' . $request->estado);

            // SI SE APRUEBA: aplicar cambios a la solicitud PPS
            if ($request->estado === SolicitudActualizacion::EST_APROBADA) {
                $this->aplicarActualizacion($solicitudActualizacion);
            }

            return redirect()
                ->route('admin.solicitudes.actualizacion')
                ->with('success', 'Solicitud ' . strtolower($request->estado) . ' correctamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar solicitud: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Aplicar los cambios a la solicitud PPS original
     */
    private function aplicarActualizacion(SolicitudActualizacion $solicitudActualizacion)
    {
        try {
            Log::info('🔄 Iniciando aplicación de actualización #' . $solicitudActualizacion->id);

            // Buscar la solicitud PPS más reciente del estudiante (que no esté cancelada ni finalizada)
            $solicitudPPS = SolicitudPPS::where('user_id', $solicitudActualizacion->user_id)
                ->whereNotIn('estado_solicitud', ['CANCELADA', 'FINALIZADA'])
                ->latest('id')
                ->first();

            if (!$solicitudPPS) {
                Log::warning('No se encontró solicitud PPS activa para user_id: ' . $solicitudActualizacion->user_id);
                return;
            }

            Log::info('Solicitud PPS encontrada: #' . $solicitudPPS->id . ' (Estado: ' . $solicitudPPS->estado_solicitud . ')');

            //  AGREGAR EL DOCUMENTO DE ACTUALIZACIÓN
            if ($solicitudActualizacion->archivo) {
                $documento = Documento::create([
                    'solicitud_pps_id' => $solicitudPPS->id,
                    'tipo' => 'actualizacion',
                    'ruta' => $solicitudActualizacion->archivo,
                    'nombre_original' => 'Documento de actualización',
                ]);

                Log::info('Documento de actualización agregado: ID=' . $documento->id . ' a solicitud PPS #' . $solicitudPPS->id);
            } else {
                Log::warning(' No hay archivo adjunto en la actualización #' . $solicitudActualizacion->id);
            }

            
            Log::info('Cambios aplicados exitosamente a solicitud PPS #' . $solicitudPPS->id);
        } catch (\Exception $e) {
            Log::error('Error al aplicar actualización: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Ver archivo adjunto de la actualización
     */
    public function verArchivo($id)
    {
        $solicitud = SolicitudActualizacion::findOrFail($id);

        // Verificar que el usuario sea admin
        $user = Auth::user();
        if (!$user || ($user->rol !== 'admin' && !$user->isAdmin())) {
            abort(403, 'No autorizado.');
        }

        if (!$solicitud->archivo) {
            abort(404, 'No hay archivo adjunto.');
        }

        // Intentar en storage/app/public primero
        $pathPublic = storage_path("app/public/{$solicitud->archivo}");
        $pathPrivate = storage_path("app/private/{$solicitud->archivo}");

        $path = file_exists($pathPublic) ? $pathPublic : $pathPrivate;

        if (!file_exists($path)) {
            Log::error('Archivo no encontrado: ' . $solicitud->archivo);
            abort(404, 'El archivo no existe en el servidor.');
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Mostrar PDF inline
        if ($ext === 'pdf') {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="actualizacion_' . $solicitud->id . '.pdf"',
            ]);
        }

        // Mostrar imágenes inline
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            return response()->file($path, [
                'Content-Type' => mime_content_type($path),
                'Content-Disposition' => 'inline; filename="actualizacion_' . $solicitud->id . '.' . $ext . '"',
            ]);
        }

        // Otros archivos -> descargar
        return response()->download($path, 'actualizacion_' . $solicitud->id . '.' . $ext);
    }
}