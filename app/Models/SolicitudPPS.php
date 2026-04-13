<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SolicitudPPS extends Model
{
    use HasFactory, SoftDeletes;

    /** Tabla real */
    protected $table = 'solicitud_p_p_s';

    /** Asignación masiva (coincide con columnas reales) */
    protected $fillable = [
        'user_id',
        'tipo_practica',
        'modalidad',
        'numero_cuenta',
        'telefono_alumno',
        'dni_estudiante',
        'foto_estudiante',
        'nombre_empresa',
        'tipo_empresa',
        'direccion_empresa',
        'nombre_jefe',
        'cargo_jefe',
        'empresa_id', 
        'nivel_academico_jefe', 
        'numero_jefe',
        'correo_jefe',
        'puesto_trabajo',
        'anios_trabajando',
        'fecha_inicio',
        'fecha_fin',
        'horario',
        'horas_totales',
        'fecha_finalizacion_calculada',
        'estado_solicitud',
        'observacion',
        'observaciones',
        'motivo_cancelacion',
        'supervisor_id',
        'dias_laborables',
        'dias_feriados',
        'dias_adicionales',
        'horas_semanales',
        'horas_promedio_diarias',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'fecha_finalizacion_calculada' => 'date',


        // ⬇️  3 CASTS NUEVOS ⬇️
    'dias_laborables' => 'array',
    'dias_feriados' => 'array',
    'horas_totales' => 'integer',
    ];

    /** Estados como constantes */
    public const EST_SOLICITADA  = 'SOLICITADA';
    public const EST_APROBADA    = 'APROBADA';
    public const EST_RECHAZADA   = 'RECHAZADA';
    public const EST_CANCELADA   = 'CANCELADA';
    public const EST_FINALIZADA  = 'FINALIZADA';
    public const EST_EN_PROCESO  = 'EN_PROCESO';

    /** ----- Relaciones ----- */

    /**
     * Relación: Una solicitud tiene muchos documentos
     */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'solicitud_pps_id', 'id')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Relación: Una solicitud pertenece a un usuario (estudiante)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Una solicitud pertenece a un supervisor
     */
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

    /**
     * Relación: Una solicitud tiene muchas supervisiones
     */
    public function supervisiones()
    {
        return $this->hasMany(Supervision::class, 'solicitud_pps_id', 'id')
                    ->orderBy('created_at', 'asc');
    }

    /** ----- Scopes útiles ----- */

    public function scopeDelUsuario(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    public function scopeActivas(Builder $q): Builder
    {
        return $q->whereIn('estado_solicitud', [
            self::EST_SOLICITADA,
            self::EST_APROBADA,
            self::EST_EN_PROCESO,
        ]);
    }

    public function scopeNoFinalizadas(Builder $q): Builder
    {
        return $q->whereNotIn('estado_solicitud', [self::EST_CANCELADA, self::EST_FINALIZADA]);
    }

    /** ----- Helpers de estado ----- */

    public function getEsActivaAttribute(): bool
    {
        return in_array($this->estado_solicitud, [
            self::EST_SOLICITADA,
            self::EST_APROBADA,
            self::EST_EN_PROCESO,
        ], true);
    }

    public function getPuedeCancelarAttribute(): bool
    {
        return in_array($this->estado_solicitud, [
            self::EST_SOLICITADA,
            self::EST_APROBADA,
            self::EST_RECHAZADA,
            self::EST_EN_PROCESO,
        ], true);
    }

    /** ----- Helpers para el dashboard ----- */

    public function progresoDocumentos(array $requeridos): array
    {
        $tiposCargados = $this->documentos()->pluck('tipo')->all();
        $completados   = array_values(array_intersect($requeridos, $tiposCargados));

        return [
            'requeridos'  => array_values($requeridos),
            'completados' => $completados,
            'porcentaje'  => count($requeridos)
                ? round(count($completados) * 100 / count($requeridos))
                : 0,
        ];
    }

    public function getObservacionesAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }
        return $this->attributes['observacion'] ?? null;
    }

    public function getObservacionAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }
        return $this->attributes['observaciones'] ?? null;
    }

    /**
     * ✅ NUEVO: Calcular fecha de finalización basada en 800 horas
     */
    public static function calcularFechaFinalizacion($fechaInicio, $horario, $diasFeriados = [])
    {
        if (!$fechaInicio || !$horario) {
            return null;
        }

        // Parsear horario
        preg_match('/(\d+):(\d+)\s*(AM|PM)?.*?(\d+):(\d+)\s*(AM|PM)?/i', $horario, $matches);
        
        $horasDiarias = 8; // Default
        
        if (count($matches) >= 6) {
            $horaInicio = (int)$matches[1];
            $minutosInicio = (int)($matches[2] ?? 0);
            $horaFin = (int)$matches[4];
            $minutosFin = (int)($matches[5] ?? 0);
            
            // Convertir AM/PM a 24h
            if (isset($matches[3]) && strtoupper($matches[3]) === 'PM' && $horaInicio < 12) {
                $horaInicio += 12;
            } elseif (isset($matches[3]) && strtoupper($matches[3]) === 'AM' && $horaInicio === 12) {
                $horaInicio = 0;
            }
            
            if (isset($matches[6]) && strtoupper($matches[6]) === 'PM' && $horaFin < 12) {
                $horaFin += 12;
            } elseif (isset($matches[6]) && strtoupper($matches[6]) === 'AM' && $horaFin === 12) {
                $horaFin = 0;
            }
            
            // Calcular horas incluyendo minutos
            $totalMinutosInicio = ($horaInicio * 60) + $minutosInicio;
            $totalMinutosFin = ($horaFin * 60) + $minutosFin;
            $diferenciaMinutos = $totalMinutosFin - $totalMinutosInicio;
            
            $horasDiarias = $diferenciaMinutos / 60;
            
            if ($horasDiarias <= 0) {
                $horasDiarias = 8;
            }
        }

        $diasNecesarios = ceil(800 / $horasDiarias);
        
        // Convertir feriados a array de fechas
        $feriadosSet = array_map(function($f) {
            return is_array($f) ? $f['fecha'] : $f;
        }, $diasFeriados);
        
        $fechaFin = Carbon::parse($fechaInicio);
        $diasAgregados = 0;

        while ($diasAgregados < $diasNecesarios) {
            $fechaFin->addDay();
            
            $fechaStr = $fechaFin->format('Y-m-d');
            
            // Solo contar días hábiles (lunes a viernes = 1 a 5) que NO sean feriados
            if ($fechaFin->isWeekday() && !in_array($fechaStr, $feriadosSet)) {
                $diasAgregados++;
            }
        }

        return $fechaFin->format('Y-m-d');
    }
}