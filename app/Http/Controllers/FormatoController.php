<?php

namespace App\Http\Controllers;

use App\Models\Formato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormatoController extends Controller
{
    /**
     * Mostrar formatos disponibles para estudiantes
     */
    public function index()
    {
        $formatos = Formato::where('tipo_destinatario', 'estudiante')
                          ->where('visible', 1)
                          ->latest('id')
                          ->paginate(12);

        return view('estudiantes.formatos', compact('formatos'));
    }

    /**
     * Descargar formato
     */
    public function download($id)
    {
        try {
            $formato = Formato::where('tipo_destinatario', 'estudiante')
                            ->where('visible', 1)
                            ->findOrFail($id);
            
            $rutaCompleta = public_path($formato->ruta);

            if (!file_exists($rutaCompleta)) {
                return redirect()
                    ->route('estudiantes.formatos')
                    ->with('error', 'El archivo no existe');
            }

            $extension = pathinfo($formato->ruta, PATHINFO_EXTENSION);
            $nombreDescarga = $formato->nombre . '.' . $extension;

            Log::info('Estudiante descargando formato: ' . $nombreDescarga);

            return response()->download($rutaCompleta, $nombreDescarga);

        } catch (\Exception $e) {
            Log::error('Error al descargar formato: ' . $e->getMessage());
            return redirect()
                ->route('estudiantes.formatos')
                ->with('error', 'Error al descargar el formato');
        }
    }

    /**
     * Ver formato (opcional)
     */
    public function view($id)
    {
        try {
            $formato = Formato::where('tipo_destinatario', 'estudiante')
                            ->where('visible', 1)
                            ->findOrFail($id);
            
            $rutaCompleta = public_path($formato->ruta);

            if (!file_exists($rutaCompleta)) {
                abort(404, 'Archivo no encontrado');
            }

            $extension = pathinfo($formato->ruta, PATHINFO_EXTENSION);
            $mimeType = $extension === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            return response()->file($rutaCompleta, [
                'Content-Type' => $mimeType,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al ver formato: ' . $e->getMessage());
            abort(404);
        }
    }
}