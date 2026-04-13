<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCancelacion extends Model
{
    protected $table = 'solicitudes_cancelacion';

    protected $fillable = [
        'user_id',
        'solicitud_id',
        'motivo',
        'archivo',
        'observacion',
        'estado',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudPPS::class, 'solicitud_id');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

