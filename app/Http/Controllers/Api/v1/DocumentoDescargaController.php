<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Documento;

class DocumentoDescargaController extends Controller
{
    /**
     * GET /api/v1/documentos/{id}/download
     * Descarga solo si el documento pertenece al alumno autenticado.
     */
    public function download(Request $request, $id)
    {
        $user = $request->user();

        $doc = Documento::with(['solicitud' => function ($q) {
            $q->select('id','user_id');
        }])->findOrFail($id);

        if ($doc->solicitud->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if (!Storage::exists($doc->ruta)) {
            return response()->json(['message' => 'Archivo no encontrado.'], 404);
        }

        // Nombre de descarga legible
        $downloadName = $doc->tipo . '.' . pathinfo($doc->ruta, PATHINFO_EXTENSION);

        return Storage::download($doc->ruta, $downloadName);
    }

    /**
     * (Opcional) Vista embebida si en tu UI abres en pestaña nueva
     * GET /api/v1/documentos/{id}/view
     */
    public function view(Request $request, $id)
    {
        $user = $request->user();

        $doc = Documento::with(['solicitud' => function ($q) {
            $q->select('id','user_id');
        }])->findOrFail($id);

        if ($doc->solicitud->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if (!Storage::exists($doc->ruta)) {
            return response()->json(['message' => 'Archivo no encontrado.'], 404);
        }

        $mime = mime_content_type(storage_path('app/'.$doc->ruta));
        return response()->file(storage_path('app/'.$doc->ruta), [
            'Content-Type' => $mime,
        ]);
    }
}

