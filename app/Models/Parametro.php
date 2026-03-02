<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Parametro extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla
     */
    protected $table = 'parametros_sistema';

    /**
     * Campos que se pueden llenar masivamente
     */
    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'tipo',
        'categoria',
        'editable',
    ];

    /**
     * Obtener el valor de un parámetro por su clave
     * 
     * @param string $clave
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    public static function obtener(string $clave, $default = null)
    {
        // Usar caché para no consultar la BD cada vez (mejora de rendimiento)
        return Cache::remember("parametro_{$clave}", 3600, function () use ($clave, $default) {
            $parametro = self::where('clave', $clave)->first();
            
            if (!$parametro) {
                return $default;
            }

            // Convertir el valor según su tipo
            return self::convertirValor($parametro->valor, $parametro->tipo);
        });
    }

    /**
     * Convertir el valor según su tipo
     * 
     * @param string $valor
     * @param string $tipo
     * @return mixed
     */
    private static function convertirValor($valor, $tipo)
    {
        switch ($tipo) {
            case 'entero':
                return (int) $valor;
            
            case 'booleano':
                return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
            
            case 'array':
                return json_decode($valor, true) ?? [];
            
            case 'texto':
            default:
                return $valor;
        }
    }

    /**
     * Actualizar el valor de un parámetro
     * 
     * @param string $clave
     * @param mixed $nuevoValor
     * @return bool
     */
    public static function actualizar(string $clave, $nuevoValor): bool
    {
        $parametro = self::where('clave', $clave)->first();
        
        if (!$parametro) {
            return false;
        }

        $parametro->valor = $nuevoValor;
        $parametro->save();

        // Limpiar el caché
        Cache::forget("parametro_{$clave}");

        return true;
    }

    /**
     * Limpiar todo el caché de parámetros
     */
    public static function limpiarCache(): void
    {
        $claves = self::pluck('clave');
        
        foreach ($claves as $clave) {
            Cache::forget("parametro_{$clave}");
        }
    }
}