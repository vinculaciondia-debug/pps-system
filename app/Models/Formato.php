<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formato extends Model
{
    protected $table = 'formatos';
    
    protected $fillable = [
        'nombre',
        'ruta',
        'tipo',     
        'visible',
        'version',
        'tipo_destinatario', 
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    /**
     * Scope para formatos visibles
     */
    public function scopeVisibles($query)
    {
        return $query->where('visible', 1);
    }

    /**
     * Obtener la ruta completa del archivo
     */
    public function getRutaCompletaAttribute()
    {
        return public_path($this->ruta);
    }

    /**
     * Verificar si el archivo existe
     */
    public function existeArchivo()
    {
        return file_exists($this->ruta_completa);
    }

    /**
     * Obtener extensión del archivo
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->ruta, PATHINFO_EXTENSION);
    }
}