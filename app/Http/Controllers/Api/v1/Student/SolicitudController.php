<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Models\SolicitudPPS;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class SolicitudController extends Controller
{
    /**
     * GET /api/v1/solicitudes/mias?solo_activa=true
     * Lista solicitudes del alumno autenticado (con documentos)
     */
    public function mias(Request $request): JsonResponse
    {
        $userId     = Auth::id();
        $soloActiva = $request->boolean('solo_activa', false);

        $q = SolicitudPPS::where('user_id', $userId)->latest('id');

        if ($soloActiva) {
            $q->whereIn('estado_solicitud', [
                SolicitudPPS::EST_SOLICITADA,
                SolicitudPPS::EST_FINALIZADA,
            ]);
        }

        $solicitudes = $q->with('documentos:id,solicitud_pps_id,tipo,ruta,created_at')->get();

        return response()->json($solicitudes, 200);
    }

    /**
     * GET /api/v1/solicitudes/{id}
     * Detalle de la solicitud (solo propietario / admin / supervisor)
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $user = $request->user();

        $solicitud = SolicitudPPS::with('documentos:id,solicitud_pps_id,tipo,ruta,created_at')
            ->findOrFail($id);

        if (!($user->id === $solicitud->user_id || $user->hasRole('admin') || $user->hasRole('supervisor'))) {
            throw new AuthorizationException('No autorizado.');
        }

        return response()->json($solicitud, 200);
    }

    /**
     * POST /api/v1/solicitudes
     * Crea una nueva solicitud con estado SOLICITADA (bloquea duplicadas activas)
     */
    public function store(Request $request): JsonResponse
    {
        $userId = Auth::id();

        // Anti-duplicado: activa = no CANCELADA ni FINALIZADA
        $existeActiva = SolicitudPPS::where('user_id', $userId)
            ->whereNotIn('estado_solicitud', [
                SolicitudPPS::EST_CANCELADA,
                SolicitudPPS::EST_FINALIZADA,
            ])->exists();

        if ($existeActiva) {
            throw ValidationException::withMessages([
                'solicitud' => 'Ya tienes una solicitud activa.',
            ]);
        }

        $data = $request->validate([
            'tipo_practica'    => 'required|in:normal,trabajo',
            'modalidad'        => 'nullable|in:presencial,semipresencial,teletrabajo',
            'numero_cuenta'    => 'required|string|max:255',
            'nombre_empresa'   => 'required|string|max:255',
            'direccion_empresa'=> 'required|string|max:255',
            'nombre_jefe'      => 'required|string|max:255',
            'numero_jefe'      => 'required|string|max:255',
            'correo_jefe'      => 'required|email|max:255',
            'puesto_trabajo'   => 'nullable|string|max:255',
            'anios_trabajando' => 'nullable|integer|min:0',
            'fecha_inicio'     => 'nullable|date',
            'fecha_fin'        => 'nullable|date|after_or_equal:fecha_inicio',
            'horario'          => 'nullable|string|max:255',
            'telefono_alumno'  => 'nullable|string|max:255',
            'observaciones'    => 'nullable|string',
        ]);

        $solicitud = DB::transaction(function () use ($data, $userId) {
            return SolicitudPPS::create(array_merge($data, [
                'user_id'          => $userId,
                'estado_solicitud' => SolicitudPPS::EST_SOLICITADA,
            ]));
        });

        return response()->json([
            'message'   => 'Solicitud creada correctamente.',
            'solicitud' => $solicitud,
        ], 201);
    }

    /**
     * POST /api/v1/solicitudes/{id}/cancelar
     * Cancela la solicitud (deja de mostrarse en el dashboard)
     */
    public function cancelar(int $id, Request $request): JsonResponse
    {
        $userId = Auth::id();

        $solicitud = SolicitudPPS::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Solo permitir cancelar si no está finalizada/cancelada
        if (in_array($solicitud->estado_solicitud, [
            SolicitudPPS::EST_FINALIZADA,
            SolicitudPPS::EST_CANCELADA,
        ], true)) {
            return response()->json(['message' => 'No se puede cancelar en el estado actual.'], 422);
        }

        $solicitud->estado_solicitud   = SolicitudPPS::EST_CANCELADA;
        $solicitud->motivo_cancelacion = $request->input('motivo_cancelacion'); // opcional
        $solicitud->save();

        return response()->json([
            'message'   => 'Solicitud cancelada correctamente.',
            'solicitud' => [
                'id'               => $solicitud->id,
                'estado_solicitud' => $solicitud->estado_solicitud,
            ],
        ], 200);
    }

    /**
     * GET /api/v1/solicitudes/{id}/documentos
     * Lista documentos de la solicitud (si no está cancelada)
     */
    public function documentos(int $id, Request $request): JsonResponse
    {
        $solicitud = SolicitudPPS::with('documentos')->findOrFail($id);

        if ($solicitud->estado_solicitud === SolicitudPPS::EST_CANCELADA) {
            return response()->json([], 200);
        }

        return response()->json($solicitud->documentos, 200);
    }

    /**
     * GET /api/v1/documentos/{id}/download
     * Descarga un documento de manera segura (Policy)
     */
    public function descargarDocumento(int $id, Request $request)
    {
        $doc = Documento::findOrFail($id);
        $this->authorize('view', $doc);
        return Storage::disk('private')->download($doc->ruta);
    }

    /**
     * GET /api/v1/student/dashboard
     * Devuelve la solicitud activa (no cancelada) con sus documentos
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $solicitud = SolicitudPPS::with('documentos')
            ->where('user_id', $user->id)
            ->whereIn('estado_solicitud', [
                SolicitudPPS::EST_SOLICITADA,
                SolicitudPPS::EST_APROBADA,
                SolicitudPPS::EST_FINALIZADA,
            ])
            ->latest('id')
            ->first();

        if (!$solicitud) {
            return response()->json([
                'message' => 'No tienes solicitudes activas.',
                'solicitud' => null,
                'documentos' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Solicitud encontrada.',
            'solicitud' => $solicitud,
            'documentos' => $solicitud->documentos,
        ], 200);
    }
}
