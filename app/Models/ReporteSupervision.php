<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteSupervision extends Model
{
    use HasFactory;

    protected $table = 'supervisiones';

    protected $fillable = [
        'solicitud_pps_id',
        'numero_supervision',
        'comentario',
        'archivo',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudPPS::class, 'solicitud_pps_id');
    }
}
