<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Documento extends Model
{
    use HasFactory;

    /** Tabla real */
    protected $table = 'documentos';

    protected $fillable = [
        'solicitud_pps_id',
        'tipo',
        'ruta',
        'estado_revision',
        'observacion_revision',
        'revisado_por',
        'revisado_at'
    ];

    /** Casts de fechas */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'revisado_at' => 'datetime',
    ];

    /** Tipos válidos (opcional: para validar o pintar etiquetas) */
    public const TIPOS_VALIDOS = [
        'carta_presentacion',
        'carta_aceptacion',
        'ia01',
        'ia02',
        'colegiacion',
        'constancia_trabajo',
        'constancia_aprobacion',
    ];

    /** Relaciones */
    public function solicitud()
    {
        // documentos.solicitud_pps_id -> solicitud_p_p_s.id
        return $this->belongsTo(SolicitudPPS::class, 'solicitud_pps_id', 'id');
    }

    /** Scopes útiles */
    public function scopeDeSolicitud(Builder $q, int $solicitudId): Builder
    {
        return $q->where('solicitud_pps_id', $solicitudId);
    }

    public function scopeDelUsuario(Builder $q, int $userId): Builder
    {
        // Filtra documentos pertenecientes a solicitudes del usuario
        return $q->whereHas('solicitud', function (Builder $s) use ($userId) {
            $s->where('user_id', $userId);
        });
    }

    /** Helpers (opcionales) */
    public function getExtAttribute(): ?string
    {
        return pathinfo($this->ruta ?? '', PATHINFO_EXTENSION) ?: null;
    }

    public function getNombreDescargaAttribute(): string
    {
        $ext = $this->ext ?: 'pdf';
        return "{$this->tipo}.{$ext}";
    }

        public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    // Scope para cartas de finalización pendientes
    public function scopeCartasFinalizacionPendientes($query)
    {
        return $query->where('tipo', 'carta_finalizacion')
                     ->where('estado_revision', 'PENDIENTE');
    }

    // Verificar si es una carta de finalización
    public function esCartaFinalizacion()
    {
        return $this->tipo === 'carta_finalizacion';
    }
    // Verificar si está aprobada
    public function estaAprobada()
    {
        return $this->estado_revision === 'APROBADA';
    }

    // Verificar si está rechazada
    public function estaRechazada()
    {
        return $this->estado_revision === 'RECHAZADA';
    }

}




