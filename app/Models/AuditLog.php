<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_nombre',
        'user_rol',
        'accion',
        'modelo',
        'modelo_id',
        'descripcion',
        'datos_extra',
        'ip',
    ];

    protected $casts = [
        'datos_extra' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
