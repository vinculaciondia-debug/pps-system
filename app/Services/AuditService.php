<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class AuditService
{
    /**
     * Registra una acción en el log de auditoría.
     *
     * @param string      $accion      Clave de la acción (ej: aprobar_solicitud)
     * @param string      $descripcion Texto legible (ej: "Aprobó la solicitud #12 de María López")
     * @param string|null $modelo      Nombre del modelo afectado (ej: SolicitudPPS)
     * @param int|null    $modeloId    ID del registro afectado
     * @param array       $datosExtra  Datos adicionales en JSON (motivo, supervisor anterior, etc.)
     */
    public static function log(
        string $accion,
        string $descripcion,
        ?string $modelo = null,
        ?int $modeloId = null,
        array $datosExtra = []
    ): void {
        try {
            $user = Auth::user();

            AuditLog::create([
                'user_id'     => $user?->id,
                'user_nombre' => $user?->name,
                'user_rol'    => $user?->rol,
                'accion'      => $accion,
                'modelo'      => $modelo,
                'modelo_id'   => $modeloId,
                'descripcion' => $descripcion,
                'datos_extra' => empty($datosExtra) ? null : $datosExtra,
                'ip'          => Request::ip(),
            ]);
        } catch (\Exception $e) {
            // Nunca interrumpir el flujo principal por un error de auditoría
            Log::error('Error al registrar auditoría: ' . $e->getMessage(), [
                'accion'      => $accion,
                'descripcion' => $descripcion,
            ]);
        }
    }
}
