<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $table = 'supervisores';

    protected $fillable = [
        'user_id',
        'activo',
        'max_estudiantes',
        'es_admin',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * Un supervisor pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un supervisor tiene muchas solicitudes asignadas
     * ✅ EXCLUYE las finalizadas y canceladas
     */
    public function solicitudes()
    {
        return $this->hasMany(\App\Models\SolicitudPPS::class, 'supervisor_id')
            ->whereNotIn('estado_solicitud', ['FINALIZADA', 'CANCELADA']);
    }

    /**
     * Todas las solicitudes (incluyendo finalizadas) - para historial
     */
    public function todasLasSolicitudes()
    {
        return $this->hasMany(\App\Models\SolicitudPPS::class, 'supervisor_id');
    }

    // ============================================
    // ACCESSORS (Atributos calculados)
    // ============================================

    /**
     * Número de estudiantes actualmente asignados
     * ✅ EXCLUYE finalizadas y canceladas
     */
    public function getEstudiantesAsignadosAttribute(): int
    {
        return \App\Models\SolicitudPPS::where('supervisor_id', $this->id)
            ->whereIn('estado_solicitud', ['SOLICITADA', 'APROBADA'])
            ->count();
    }

    /**
     * Cupos disponibles
     */
    public function getCuposDisponiblesAttribute(): int
    {
        return max(0, $this->max_estudiantes - $this->estudiantes_asignados);
    }

    /**
     * Porcentaje de ocupación
     */
    public function getPorcentajeOcupacionAttribute(): int
    {
        if ($this->max_estudiantes == 0) {
            return 0;
        }
        
        return round(($this->estudiantes_asignados / $this->max_estudiantes) * 100);
    }

    /**
     * Verificar si está lleno
     */
    public function estaLleno(): bool
    {
        return $this->estudiantes_asignados >= $this->max_estudiantes;
    }

    /**
     * Puede recibir más estudiantes
     */
    public function puedeRecibirMas(): bool
    {
        return !$this->estaLleno() && $this->activo;
    }
}