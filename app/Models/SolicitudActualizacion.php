<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\SolicitudPPS;

class SolicitudActualizacion extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_actualizacion';

    protected $fillable = [
        'user_id',
        'motivo',
        'archivo',      // ✅ Agregado
        'estado',
        'observacion',
    ];

    // Estados
    const EST_PENDIENTE = 'PENDIENTE';
    const EST_APROBADA  = 'APROBADA';
    const EST_RECHAZADA = 'RECHAZADA';

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Relación con la solicitud PPS (para actualizar sus datos)
     */
    public function solicitudPPS()
    {
        return $this->hasOne(SolicitudPPS::class, 'user_id', 'user_id')
                    ->latest('id');
    }
}