<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Documento;
use App\Models\SolicitudPPS;

class DocumentoApiController extends Controller
{
    /**
     * Listar documentos de una solicitud (solo propietario, admin o supervisor).
     */
    public function index($solicitudId)
    {
        $user = Auth::user();
        $solicitud = SolicitudPPS::with('documentos')->findOrFail($solicitudId);

        $this->authorizeApi($solicitud);

        return response()->json([
            'solicitud_id' => $solicitud->id,
            'documentos'   => $solicitud->documentos,
        ]);
    }

    /**
     * Subir documento por tipo (Estudiante).
     */
    public function store(Request $request, $solicitudId)
    {
        $request->validate([
            'tipo'    => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();
        $solicitud = SolicitudPPS::findOrFail($solicitudId);

        if ($solicitud->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Si ya existía un documento de ese tipo, eliminarlo
        if ($prev = Documento::where('solicitud_pps_id', $solicitud->id)->where('tipo', $request->tipo)->first()) {
            Storage::disk('private')->delete($prev->ruta);
            $prev->delete();
        }

        // Guardar archivo en storage/app/private
        $ruta = $request->file('archivo')->store("documentos/{$user->id}", 'private');

        $doc = Documento::create([
            'solicitud_pps_id' => $solicitud->id,
            'tipo'             => $request->tipo,
            'ruta'             => $ruta,
        ]);

        return response()->json([
            'success'   => true,
            'mensaje'   => 'Documento subido correctamente.',
            'documento' => $doc,
        ]);
    }

    /**
     * Ver documento inline (solo si es PDF).
     */
    public function ver($id)
    {
        $documento = Documento::with('solicitud')->findOrFail($id);
        $this->authorizeApi($documento->solicitud);

        $path = storage_path("app/private/{$documento->ruta}");
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            return response()->file($path);
        }

        // Si no es PDF, descargarlo
        return Storage::disk('private')->download(
            $documento->ruta,
            $documento->tipo . '.' . $ext
        );
    }

    /**
     * Descargar documento siempre con nombre legible.
     */
    public function descargar($id)
    {
        $documento = Documento::with('solicitud')->findOrFail($id);
        $this->authorizeApi($documento->solicitud);

        $ext = pathinfo($documento->ruta, PATHINFO_EXTENSION);
        $nombreDescarga = $documento->tipo . '.' . $ext;

        return Storage::disk('private')->download($documento->ruta, $nombreDescarga);
    }

    /**
     * Eliminar documento.
     */
    public function destroy($id)
    {
        $documento = Documento::with('solicitud')->findOrFail($id);
        $user = Auth::user();

        if ($documento->solicitud?->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        Storage::disk('private')->delete($documento->ruta);
        $documento->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Documento eliminado correctamente.',
        ]);
    }

    /**
     * Reglas de autorización comunes (admin, supervisor asignado o dueño).
     */
    private function authorizeApi($solicitud): void
    {
        $user = Auth::user();

        $esAdmin      = method_exists($user, 'isAdmin') && $user->isAdmin();
        $esSupervisor = method_exists($user, 'isSupervisor') && $user->isSupervisor()
            && (int)($solicitud->supervisor_id ?? 0) === (int)($user->id);
        $esPropietario = (int)$solicitud->user_id === (int)$user->id;

        if (!($esAdmin || $esSupervisor || $esPropietario)) {
            abort(403, 'No autorizado.');
        }
    }
}