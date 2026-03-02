<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SolicitudPPS;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ✅ Solicitud visible en dashboard:
        // - Incluye: SOLICITADA, APROBADA, RECHAZADA, FINALIZADA
        // - Excluye: CANCELADA (ya no debe verse)
        $solicitud = SolicitudPPS::with(['documentos:id,solicitud_pps_id,tipo,ruta,created_at'])
            ->where('user_id', $user->id)
            ->whereIn('estado_solicitud', [
                SolicitudPPS::EST_SOLICITADA,
                SolicitudPPS::EST_APROBADA,
                SolicitudPPS::EST_RECHAZADA,
                SolicitudPPS::EST_FINALIZADA,
            ])
            ->latest('id')
            ->first();

        // Tipos requeridos para “puntitos” (ajústalos si cambian)
        $requeridos = ['carta_presentacion','carta_aceptacion','ia01','ia02'];

        if (!$solicitud) {
            // No hay solicitud visible en dashboard → puede solicitar
            return response()->json([
                'solicitudActiva'     => null,
                'puedeSolicitar'      => true,
                'progreso'            => [],       // pasos por estado (si tu UI los usa)
                'progresoDocumentos'  => [         // puntitos por documentos
                    'requeridos'  => $requeridos,
                    'completados' => [],
                    'porcentaje'  => 0,
                ],
                'totalDocumentos'     => 0,
            ]);
        }

        // Documentos entregados (para el modal “Ver documentos”)
        $docEnt = $solicitud->documentos
            ->sortByDesc('created_at')
            ->values()
            ->map(fn($d) => [
                'id'         => $d->id,
                'tipo'       => $d->tipo,
                'ruta'       => $d->ruta, // se descarga vía /api/v1/documentos/{id}/download (Policy)
                'created_at' => optional($d->created_at)->format('Y-m-d H:i'),
            ]);

        $estado = $solicitud->estado_solicitud;

        // Progreso por “estado” (si en tu UI pintas una línea de tiempo)
        $steps = [
            ['label' => 'Solicitud enviada (en proceso)', 'done' => in_array($estado, [
                SolicitudPPS::EST_SOLICITADA, SolicitudPPS::EST_APROBADA, SolicitudPPS::EST_RECHAZADA, SolicitudPPS::EST_FINALIZADA
            ])],
            ['label' => 'Revisión de supervisor', 'done' => in_array($estado, [
                SolicitudPPS::EST_APROBADA, SolicitudPPS::EST_RECHAZADA, SolicitudPPS::EST_FINALIZADA
            ])],
            ['label' => 'Aprobada', 'done' => in_array($estado, [
                SolicitudPPS::EST_APROBADA, SolicitudPPS::EST_FINALIZADA
            ])],
            ['label' => 'Finalizada', 'done' => $estado === SolicitudPPS::EST_FINALIZADA],
        ];

        // ✅ Progreso por documentos (puntitos verdes)
        $tiposCargados = $solicitud->documentos->pluck('tipo')->all();
        $completados   = array_values(array_intersect($requeridos, $tiposCargados));
        $porcentaje    = count($requeridos) ? round(count($completados) * 100 / count($requeridos)) : 0;

        // ✅ Puede volver a solicitar si la última está FINALIZADA
        $puedeSolicitar = ($estado === SolicitudPPS::EST_FINALIZADA) ? true : false;

        return response()->json([
            'solicitudActiva' => [
                'id'          => $solicitud->id,
                'estado'      => $estado, // En tu UI: "SOLICITADA" => "En proceso"
                'created_at'  => optional($solicitud->created_at)->format('Y-m-d H:i'),
                'observacion' => $solicitud->observacion, // comentario si RECHAZADA
                'documentos'  => $docEnt,
            ],
            'puedeSolicitar'      => $puedeSolicitar,
            'progreso'            => $steps,
            'progresoDocumentos'  => [
                'requeridos'  => $requeridos,
                'completados' => $completados,
                'porcentaje'  => $porcentaje,
            ],
            'totalDocumentos'     => $docEnt->count(),
        ]);
    }
}



