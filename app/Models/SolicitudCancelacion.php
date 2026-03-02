<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCancelacion extends Model
{
    protected $table = 'solicitudes_cancelacion';

    protected $fillable = [
        'user_id',
        'motivo',
        'observacion',
        'estado',
    ];
}

