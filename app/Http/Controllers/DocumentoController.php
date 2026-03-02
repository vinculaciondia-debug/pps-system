<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\SolicitudPPS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentoController extends Controller
{
    /**
     * LISTADO (Estudiante): /estudiantes/documentos
     * Muestra documentos de la ÚLTIMA solicitud activa del alumno autenticado.
     */
    public function index()
    {
        $userId = Auth::id();

        // Buscar última solicitud activa (no cancelada ni finalizada)
        $solicitud = SolicitudPPS::where('user_id', $userId)
            ->with('documentos')
            ->whereNotIn('estado_solicitud', [
                SolicitudPPS::EST_CANCELADA,
                SolicitudPPS::EST_FINALIZADA,
            ])
            ->orderByDesc('id')
            ->first();

        // Si no hay solicitud activa, buscar la última (aunque esté finalizada/cancelada)
        if (!$solicitud) {
            $solicitud = SolicitudPPS::where('user_id', $userId)
                ->with('documentos')
                ->orderByDesc('id')
                ->first();
        }

        $documentos = $solicitud?->documentos ?? collect();

        Log::info('Documentos cargados para user_id ' . $userId . ': ' . $documentos->count());

        return view('estudiantes.documentos.index', [
            'titulo'     => 'Mis documentos',
            'solicitud'  => $solicitud,
            'documentos' => $documentos,
            'mensaje'    => $solicitud ? null : 'Aún no has enviado ninguna solicitud.',
        ]);
    }

    /**
     * LISTADO por solicitud (Admin/Supervisor/Estudiante).
     */
    public function indexBySolicitud(int $id)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        $solicitud = SolicitudPPS::with('documentos')->findOrFail($id);

        $esAdmin      = ($user->rol === 'admin') || (method_exists($user, 'isAdmin') && $user->isAdmin());
        $esSupervisor = ($user->rol === 'supervisor') || (method_exists($user, 'isSupervisor') && $user->isSupervisor());
        $esEstudiante = ($user->rol === 'estudiante') || (method_exists($user, 'isEstudiante') && $user->isEstudiante());

        if ($esAdmin) {
            // admin ve todo
        } elseif ($esSupervisor) {
            if ((int)$solicitud->supervisor_id !== (int)$user->id) {
                abort(403, 'No autorizado para ver estos documentos.');
            }
        } elseif ($esEstudiante) {
            if ((int)$solicitud->user_id !== (int)$user->id) {
                abort(403, 'No autorizado para ver estos documentos.');
            }
        } else {
            abort(403, 'No autorizado.');
        }

        $documentos = $solicitud->documentos ?? collect();

        return view('estudiantes.documentos.index', [
            'titulo'     => 'Documentos de la solicitud #' . $solicitud->id,
            'solicitud'  => $solicitud,
            'documentos' => $documentos,
        ]);
    }

    /**
     * Subir documento por tipo (Estudiante).
     * Ahora soporta carta_finalizacion con validaciones especiales
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'solicitud_pps_id' => 'required|exists:solicitud_p_p_s,id',
                'tipo' => 'required|string',
                'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ], [
                'archivo.max' => 'El archivo no debe superar los 5MB',
                'archivo.mimes' => 'Solo se permiten archivos PDF, DOC, DOCX, JPG, JPEG, PNG',
            ]);

            $solicitud = SolicitudPPS::findOrFail($request->solicitud_pps_id);
            
            // Verificar que la solicitud pertenece al usuario
            if ($solicitud->user_id !== Auth::id()) {
                return back()->with('error', 'No tienes permiso para subir documentos a esta solicitud.');
            }

            // Verificar que la solicitud esté activa
            if (!in_array($solicitud->estado_solicitud, ['SOLICITADA', 'APROBADA'])) {
                return back()->with('error', 'No puedes subir documentos a una solicitud en estado: ' . $solicitud->estado_solicitud);
            }

            $tipo = strtolower($request->tipo);

            // ⭐ CASO ESPECIAL: Carta de Finalización
            if ($tipo === 'carta_finalizacion') {
                // Buscar carta anterior (pendiente o rechazada)
                $cartaAnterior = Documento::where('solicitud_pps_id', $solicitud->id)
                    ->where('tipo', 'carta_finalizacion')
                    ->whereIn('estado_revision', ['PENDIENTE', 'RECHAZADA'])
                    ->first();
                
                if ($cartaAnterior) {
                    // Eliminar el archivo anterior del storage
                    if (Storage::disk('public')->exists($cartaAnterior->ruta)) {
                        Storage::disk('public')->delete($cartaAnterior->ruta);
                    }
                    
                    // Eliminar el registro anterior
                    $cartaAnterior->delete();
                    
                    Log::info('Carta de finalización anterior eliminada', [
                        'documento_id' => $cartaAnterior->id,
                        'estado' => $cartaAnterior->estado_revision,
                        'solicitud_id' => $solicitud->id
                    ]);
                }
            } else {
                // Para otros documentos, verificar duplicados (comportamiento normal)
                $existe = Documento::where('solicitud_pps_id', $solicitud->id)
                    ->where('tipo', $tipo)
                    ->exists();

                if ($existe) {
                    return back()->with('error', 'Ya existe un documento de tipo: ' . str_replace('_', ' ', $tipo));
                }
            }

            // Guardar archivo
            $archivo = $request->file('archivo');
            $extension = $archivo->getClientOriginalExtension();
            $nombreArchivo = $tipo . '_' . time() . '_' . uniqid() . '.' . $extension;
            $ruta = $archivo->storeAs('documentos', $nombreArchivo, 'public');

            // Crear documento
            $documento = Documento::create([
                'solicitud_pps_id' => $solicitud->id,
                'tipo' => $tipo,
                'ruta' => $ruta,
                'estado_revision' => $tipo === 'carta_finalizacion' ? 'PENDIENTE' : null,
            ]);

            Log::info('Documento guardado exitosamente', [
                'documento_id' => $documento->id,
                'tipo' => $tipo,
                'solicitud_id' => $solicitud->id,
                'user_id' => Auth::id()
            ]);

            $mensaje = $tipo === 'carta_finalizacion' 
                ? 'Carta de finalización subida correctamente. Está siendo revisada por el administrador.' 
                : 'Documento guardado exitosamente.';

            return redirect()->route('estudiantes.dashboard')->with('success', $mensaje);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación al guardar documento', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al guardar documento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'Error al guardar el documento: ' . $e->getMessage());
        }
    }

    /**
     * Ver el documento en el navegador (inline si es PDF/imagen).
     */
    public function ver($id)
    {
        $documento = Documento::with('solicitud')->findOrFail($id);
        $this->authorizeView($documento);

        $pathPrivate = storage_path("app/private/{$documento->ruta}");
        $pathPublic  = storage_path("app/public/{$documento->ruta}");
        $path = file_exists($pathPrivate) ? $pathPrivate : $pathPublic;

        if (!file_exists($path)) {
            Log::error('Archivo no encontrado: ' . $documento->ruta);
            abort(404, 'El archivo no existe en el servidor.');
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $downloadName = basename($path);

        if ($ext === 'pdf') {
            return response()->file($path, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
            ]);
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return response()->file($path, [
                'Content-Type'        => mime_content_type($path),
                'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
            ]);
        }

        return response()->download($path, $downloadName);
    }

    /**
     * Descargar el documento.
     */
    public function descargar($id)
    {
        $documento = Documento::with('solicitud')->findOrFail($id);
        $this->authorizeView($documento);

        if (Storage::disk('private')->exists($documento->ruta)) {
            $nombreDescarga = basename($documento->ruta);
            return Storage::disk('private')->download($documento->ruta, $nombreDescarga);
        }

        if (Storage::disk('public')->exists($documento->ruta)) {
            $nombreDescarga = basename($documento->ruta);
            return Storage::disk('public')->download($documento->ruta, $nombreDescarga);
        }

        Log::error('Archivo no encontrado en storage: ' . $documento->ruta);
        abort(404, 'El archivo no existe en el servidor.');
    }

    /**
     * Eliminar un documento.
     * NO permite eliminar carta_finalizacion una vez subida
     */
    public function destroy($id)
    {
        $documento = Documento::with('solicitud')->findOrFail($id);

        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        $esAdmin = ($user->rol === 'admin') || (method_exists($user, 'isAdmin') && $user->isAdmin());
        $esDueno = (int)($documento->solicitud?->user_id) === (int)$user->id;

        if (!$esAdmin && !$esDueno) {
            abort(403, 'No autorizado para eliminar este documento.');
        }

        // NO permitir eliminar carta de finalización (solo admin puede forzarlo si es necesario)
        if ($documento->tipo === 'carta_finalizacion' && !$esAdmin) {
            return back()->with('error', 'No puedes eliminar la carta de finalización una vez subida. Contacta al administrador si necesitas cambiarla.');
        }

        try {
            // Borrar en private y también en public por compatibilidad
            Storage::disk('private')->delete($documento->ruta);
            Storage::disk('public')->delete($documento->ruta);

            $documento->delete();

            Log::info('Documento eliminado: ID=' . $id . ' por usuario=' . $user->id);

            return back()->with('success', 'Documento eliminado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar documento: ' . $e->getMessage());
            
            return back()->with('error', 'Error al eliminar el documento.');
        }
    }

    /**
     * Regla de autorización común (admin, supervisor asignado o propietario).
     */
    private function authorizeView(Documento $documento): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado.');
        }

        $sol = $documento->solicitud;

        // Admin
        if (($user->rol === 'admin') || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
            return;
        }

        // Supervisor asignado
        if (($user->rol === 'supervisor') || (method_exists($user, 'isSupervisor') && $user->isSupervisor())) {
            if ((int)($sol->supervisor_id ?? 0) === (int)$user->id) {
                return;
            }
            abort(403, 'No autorizado. Supervisor no asignado.');
        }

        // Estudiante dueño
        if (($user->rol === 'estudiante') || (method_exists($user, 'isEstudiante') && $user->isEstudiante())) {
            if ((int)$sol->user_id === (int)$user->id) {
                return;
            }
            abort(403, 'No autorizado. Documento de otro estudiante.');
        }

        abort(403, 'No autorizado.');
    }
}