<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Formato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class formatoController extends Controller
{
    /**
     * Mostrar formatos disponibles para supervisores
     */
    public function index()
    {
        $formatos = Formato::where('tipo_destinatario', 'supervisor')
                          ->where('visible', 1)
                          ->latest('id')
                          ->paginate(12);

        return view('supervisor.formatos', compact('formatos'));
    }

    /**
     * Descargar formato
     */
    public function download($id)
    {
        try {
            $formato = Formato::where('tipo_destinatario', 'supervisor')
                            ->where('visible', 1)
                            ->findOrFail($id);
            
            $rutaCompleta = public_path($formato->ruta);

            if (!file_exists($rutaCompleta)) {
                return redirect()
                    ->route('supervisor.formatos')
                    ->with('error', 'El archivo no existe');
            }

            $extension = pathinfo($formato->ruta, PATHINFO_EXTENSION);
            $nombreDescarga = $formato->nombre . '.' . $extension;

            Log::info('Supervisor descargando formato: ' . $nombreDescarga);

            return response()->download($rutaCompleta, $nombreDescarga);

        } catch (\Exception $e) {
            Log::error('Error al descargar formato (supervisor): ' . $e->getMessage());
            return redirect()
                ->route('supervisor.formatos')
                ->with('error', 'Error al descargar el formato');
        }
    }
}