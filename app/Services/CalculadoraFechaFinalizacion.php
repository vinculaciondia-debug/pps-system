<?php

namespace App\Services;

use Carbon\Carbon;

class CalculadoraFechaFinalizacion
{
    /**
     * Calcula la fecha de finalización de la práctica
     * 
     * @param Carbon $fechaInicio Fecha de inicio de la práctica
     * @param int $horasTotales Total de horas requeridas (default: 800)
     * @param array $diasLaborables Horario por día de la semana
     * @param array $diasFeriados Días feriados que no se cuentan
     * @param int $diasAdicionales Días adicionales al final (default: 3)
     * @return array Resultado con fecha_fin y estadísticas
     */
    public function calcularFechaFin(
        Carbon $fechaInicio,
        int $horasTotales,
        array $diasLaborables,
        array $diasFeriados = [],
        int $diasAdicionales = 3
    ): array {
        // Calcular horas semanales totales
        $horasSemanales = $this->calcularHorasSemanales($diasLaborables);
        
        if ($horasSemanales == 0) {
            throw new \Exception('Debes seleccionar al menos un día laborable');
        }
        
        // Calcular días necesarios (aproximado)
        $semanasNecesarias = ceil($horasTotales / $horasSemanales);
        $diasEstimados = $semanasNecesarias * 7;
        
        // Normalizar días laborables a números
        $diasLaborablesActivos = $this->obtenerDiasActivos($diasLaborables);
        
        // Calcular fecha contando solo días laborables
        $fechaActual = $fechaInicio->copy();
        $horasAcumuladas = 0;
        $diasTrabajados = 0;
        $iteraciones = 0;
        $maxIteraciones = 1000; // Seguridad para evitar loops infinitos
        
        while ($horasAcumuladas < $horasTotales && $iteraciones < $maxIteraciones) {
            $iteraciones++;
            
            // Obtener el nombre del día en español
            $nombreDia = $this->obtenerNombreDia($fechaActual->dayOfWeek);
            
            // Verificar si es día laborable y NO es feriado
            if (isset($diasLaborables[$nombreDia]) && 
                $diasLaborables[$nombreDia]['activo'] === true &&
                !$this->esFeriado($fechaActual, $diasFeriados)) {
                
                // Sumar las horas de este día
                $horasAcumuladas += $diasLaborables[$nombreDia]['horas_laborales'];
                $diasTrabajados++;
            }
            
            // Avanzar un día
            $fechaActual->addDay();
        }
        
        // Retroceder un día (porque el while avanza uno de más)
        $fechaActual->subDay();
        
        // Agregar días adicionales (excluyendo no laborables y feriados)
        $diasAgregados = 0;
        while ($diasAgregados < $diasAdicionales && $iteraciones < $maxIteraciones) {
            $iteraciones++;
            $fechaActual->addDay();
            
            $nombreDia = $this->obtenerNombreDia($fechaActual->dayOfWeek);
            
            if (isset($diasLaborables[$nombreDia]) && 
                $diasLaborables[$nombreDia]['activo'] === true &&
                !$this->esFeriado($fechaActual, $diasFeriados)) {
                $diasAgregados++;
            }
        }
        
        return [
            'fecha_fin' => $fechaActual,
            'dias_trabajados' => $diasTrabajados,
            'dias_adicionales' => $diasAgregados,
            'horas_semanales' => $horasSemanales,
            'horas_acumuladas' => $horasAcumuladas,
            'semanas_necesarias' => ceil($diasTrabajados / count($diasLaborablesActivos)),
            'dias_feriados_excluidos' => $this->contarFeriadosExcluidos(
                $fechaInicio,
                $fechaActual,
                $diasFeriados,
                $diasLaborables
            ),
        ];
    }
    
    /**
     * Calcula las horas totales por semana
     */
    public function calcularHorasSemanales(array $diasLaborables): float
    {
        $total = 0;
        
        foreach ($diasLaborables as $dia => $config) {
            if (isset($config['activo']) && $config['activo'] === true) {
                $total += $config['horas_laborales'] ?? 0;
            }
        }
        
        return round($total, 2);
    }
    
    /**
     * Calcula el promedio de horas por día trabajado
     */
    public function calcularPromedioHorasDiarias(array $diasLaborables): float
    {
        $diasActivos = $this->obtenerDiasActivos($diasLaborables);
        
        if (count($diasActivos) == 0) {
            return 0;
        }
        
        $totalHoras = 0;
        foreach ($diasActivos as $dia) {
            $totalHoras += $diasLaborables[$dia]['horas_laborales'] ?? 0;
        }
        
        return round($totalHoras / count($diasActivos), 2);
    }
    
    /**
     * Calcula horas laborales considerando almuerzo implícito
     * Si trabaja 6+ horas, resta 1 hora automáticamente
     */
    public function calcularHorasLaborales(string $horaEntrada, string $horaSalida): float
    {
        try {
            $entrada = Carbon::createFromFormat('H:i', $horaEntrada);
            $salida = Carbon::createFromFormat('H:i', $horaSalida);
            
            // Si sale antes de entrar, es del día siguiente
            if ($salida->lessThan($entrada)) {
                $salida->addDay();
            }
            
            // Calcular horas corridas
            $horasCorridas = $salida->diffInMinutes($entrada) / 60;
            
            // REGLA IMPLÍCITA: Si trabaja 6+ horas, restar 1 hora de almuerzo
            $horasLaborales = $horasCorridas >= 6.0 
                ? $horasCorridas - 1.0 
                : $horasCorridas;
            
            return round($horasLaborales, 2);
            
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtiene los días que están activos
     */
    private function obtenerDiasActivos(array $diasLaborables): array
    {
        $activos = [];
        
        foreach ($diasLaborables as $dia => $config) {
            if (isset($config['activo']) && $config['activo'] === true) {
                $activos[] = $dia;
            }
        }
        
        return $activos;
    }
    
    /**
     * Convierte número de día (0-6) a nombre en español
     */
    private function obtenerNombreDia(int $numeroDia): string
    {
        $dias = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado',
        ];
        
        return $dias[$numeroDia] ?? 'lunes';
    }
    
    /**
     * Verifica si una fecha es feriado
     */
    /**
 * Verifica si una fecha es feriado
 * Acepta tanto ['2025-12-25'] como [['fecha' => '2025-12-25']]
 */
private function esFeriado(Carbon $fecha, array $diasFeriados): bool
{
    foreach ($diasFeriados as $feriado) {
        try {
            // Si es string directo (formato ['2025-12-25'])
            if (is_string($feriado)) {
                $fechaFeriado = Carbon::parse($feriado);
                if ($fecha->isSameDay($fechaFeriado)) {
                    return true;
                }
            }
            // Si es array con clave 'fecha' (formato [['fecha' => '2025-12-25']])
            elseif (is_array($feriado) && isset($feriado['fecha'])) {
                $fechaFeriado = Carbon::parse($feriado['fecha']);
                if ($fecha->isSameDay($fechaFeriado)) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            continue;
        }
    }
    
    return false;
}
    
    /**
     * Cuenta feriados que fueron excluidos del cálculo
     */
/**
 * Cuenta feriados que fueron excluidos del cálculo
 * Acepta tanto ['2025-12-25'] como [['fecha' => '2025-12-25']]
 */
private function contarFeriadosExcluidos(
    Carbon $inicio,
    Carbon $fin,
    array $diasFeriados,
    array $diasLaborables
): int {
    $count = 0;
    
    foreach ($diasFeriados as $feriado) {
        try {
            // Si es string directo
            if (is_string($feriado)) {
                $fechaFeriado = Carbon::parse($feriado);
            }
            // Si es array con clave 'fecha'
            elseif (is_array($feriado) && isset($feriado['fecha'])) {
                $fechaFeriado = Carbon::parse($feriado['fecha']);
            } else {
                continue;
            }
            
            // Solo contar si está en el rango y cae en día laborable
            if ($fechaFeriado->between($inicio, $fin)) {
                $nombreDia = $this->obtenerNombreDia($fechaFeriado->dayOfWeek);
                
                if (isset($diasLaborables[$nombreDia]) && 
                    $diasLaborables[$nombreDia]['activo'] === true) {
                    $count++;
                }
            }
        } catch (\Exception $e) {
            continue;
        }
    }
    
    return $count;
}
    
    /**
     * Valida que los datos sean correctos
     */
    public function validarDatos(array $diasLaborables): array
    {
        $errores = [];
        
        // Validar que al menos un día esté activo
        $hayDiaActivo = false;
        foreach ($diasLaborables as $dia => $config) {
            if (isset($config['activo']) && $config['activo'] === true) {
                $hayDiaActivo = true;
                
                // Validar que tenga horas válidas
                if (!isset($config['horas_laborales']) || $config['horas_laborales'] <= 0) {
                    $errores[] = "El día {$dia} debe tener horas laborales válidas";
                }
                
                if ($config['horas_laborales'] > 12) {
                    $errores[] = "El día {$dia} no puede tener más de 12 horas laborales";
                }
            }
        }
        
        if (!$hayDiaActivo) {
            $errores[] = "Debes seleccionar al menos un día laborable";
        }
        
        return $errores;
    }
}
