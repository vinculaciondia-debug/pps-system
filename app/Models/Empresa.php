<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SolicitudPPS;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'direccion',
        'tipo',
        'activa',
    ];

    public function solicitudes()
    {
        return $this->hasMany(SolicitudPPS::class, 'empresa_id');
    }
}
