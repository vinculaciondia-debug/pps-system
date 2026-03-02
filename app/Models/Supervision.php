<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervision extends Model
{
    use HasFactory;

    protected $table = 'supervisiones';

    protected $fillable = [
        'solicitud_pps_id',
        'numero_supervision',
        'fecha_supervision',      // ✅ AGREGAR esta línea
         'ausencia_supervisor',
        'comentario',
        'archivo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'fecha_supervision' => 'date',  // ✅ AGREGAR esta línea
    ];

    /**
     * Relación con la solicitud PPS
     */
    public function solicitud()
    {
        return $this->belongsTo(SolicitudPPS::class, 'solicitud_pps_id');
    }

    /**
     * Relación con el supervisor (a través de la solicitud)
     */
    public function supervisor()
    {
        return $this->solicitud->supervisor ?? null;
    }

    /**
     * Obtener la fecha de supervisión (usa fecha_supervision si existe, sino created_at)
     */
    public function getFechaSupervisionAttribute()
    {
        // ✅ CAMBIAR esta línea completa:
        return $this->attributes['fecha_supervision'] ?? $this->created_at;
    }
}