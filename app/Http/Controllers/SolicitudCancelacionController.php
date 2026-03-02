<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudPPS;
use App\Models\SolicitudCancelacion;

class SolicitudCancelacionController extends Controller
{
    /**
     * Muestra el formulario de cancelación.
     * Soporta:
     * - GET /estudiantes/cancelacion                  (sin id: usa la solicitud activa del usuario)
     * - GET /estudiantes/solicitudes/{id}/cancelar   (con id)
     */
    public function create(?int $id = null)
    {
        $userId = Auth::id();

        // Si NO viene id por la ruta, tomamos la última solicitud "activa" del usuario.
        // Activa = que se puede cancelar (SOLICITADA o APROBADA)
        if ($id === null) {
            $solicitud = SolicitudPPS::where('user_id', $userId)
                ->whereIn('estado_solicitud', [
                    SolicitudPPS::EST_SOLICITADA,
                    SolicitudPPS::EST_APROBADA,
                ])
                ->latest('id')
                ->first();
        } else {
            $solicitud = SolicitudPPS::where('id', $id)
                ->where('user_id', $userId)
                ->first();
        }

        if (!$solicitud) {
            return redirect()
                ->route('estudiantes.dashboard')
                ->with('warning', 'No tienes ninguna solicitud cancelable.');
        }

        // Defensa: solo permitir cancelar si está SOLICITADA o APROBADA
        if (!in_array($solicitud->estado_solicitud, [
            SolicitudPPS::EST_SOLICITADA,
            SolicitudPPS::EST_APROBADA,
        ], true)) {
            return redirect()
                ->route('estudiantes.dashboard')
                ->with('warning', 'No se puede cancelar en el estado actual.');
        }

        return view('estudiantes.solicitud_cancelacion', compact('solicitud'));
    }

    /**
     * Procesa la cancelación.
     * Soporta:
     * - POST /estudiantes/cancelacion                 (sin id: usa la solicitud activa del usuario)
     * - POST /estudiantes/solicitudes/{id}/cancelar  (con id)
     */
    public function cancelar(Request $request, ?int $id = null)
    {
        $request->validate([
            'motivo'  => 'required|string|max:1000',
            'archivo' => 'required|file|mimes:pdf|max:2048',
        ]);

        $userId = Auth::id();

        if ($id === null) {
            $solicitud = SolicitudPPS::where('user_id', $userId)
                ->whereIn('estado_solicitud', [
                    SolicitudPPS::EST_SOLICITADA,
                    SolicitudPPS::EST_APROBADA,
                ])
                ->latest('id')
                ->first();
        } else {
            $solicitud = SolicitudPPS::where('id', $id)
                ->where('user_id', $userId)
                ->first();
        }

        if (!$solicitud) {
            return redirect()
                ->route('estudiantes.dashboard')
                ->with('warning', 'No tienes ninguna solicitud cancelable.');
        }

        if (!in_array($solicitud->estado_solicitud, [
            SolicitudPPS::EST_SOLICITADA,
            SolicitudPPS::EST_APROBADA,
        ], true)) {
            return redirect()
                ->route('estudiantes.dashboard')
                ->with('warning', 'No se puede cancelar en el estado actual.');
        }

        // Guardar archivo en storage PRIVADO
        $rutaArchivo = $request->file('archivo')
            ->store("private/cancelaciones/{$solicitud->id}");

        // Registrar solicitud de cancelación (si tu schema tiene más campos, agrégalos)
        SolicitudCancelacion::create([
            'user_id' => $userId,
            // 'solicitud_id' => $solicitud->id, // descomenta si tu tabla lo tiene
            'motivo'   => $request->string('motivo'),
            'archivo'  => $rutaArchivo,
            'estado'   => 'PENDIENTE',
        ]);

        // Cambiar estado base (oculta del dashboard)
        $solicitud->estado_solicitud = SolicitudPPS::EST_CANCELADA;
        $solicitud->save();

        return redirect()
            ->route('estudiantes.dashboard')
            ->with('status', 'Solicitud cancelada correctamente. Ya no aparecerá en el dashboard.');
    }
}


