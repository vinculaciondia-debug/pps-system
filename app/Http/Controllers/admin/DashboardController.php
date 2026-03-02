<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SolicitudPPS;
use App\Models\User;
use App\Models\Supervisor;
use App\Models\Documento;
use App\Models\Supervision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del admin
     */
   public function index(Request $request)
    {


         // ✅ VERIFICAR SI EL ADMIN TAMBIÉN ES SUPERVISOR
    $user = auth()->user();
    $esSupervisor = \App\Models\Supervisor::where('user_id', $user->id)->exists();
    
    // ✅ Solo redirigir al selector si NO viene con el parámetro "force"
    if ($esSupervisor && !$request->has('force')) {
        return redirect()->route('dashboard');
    }

        // ==========================================
        // MÉTRICAS PRINCIPALES
        // ==========================================
        // Como SolicitudPPS usa SoftDeletes, Laravel automáticamente excluye los deleted_at
        $totalSolicitudes = SolicitudPPS::count();
        $solicitadas = SolicitudPPS::where('estado_solicitud', 'SOLICITADA')->count();
        $aprobadas = SolicitudPPS::where('estado_solicitud', 'APROBADA')->count();
        $finalizadas = SolicitudPPS::where('estado_solicitud', 'FINALIZADA')->count();
        $rechazadas = SolicitudPPS::where('estado_solicitud', 'RECHAZADA')->count();
        $canceladas = SolicitudPPS::where('estado_solicitud', 'CANCELADA')->count();

        // Estudiantes activos (con solicitud APROBADA o SOLICITADA, no eliminadas)
        $estudiantesActivos = SolicitudPPS::whereIn('estado_solicitud', ['APROBADA', 'SOLICITADA'])
            ->distinct('user_id')
            ->count('user_id');

        // Supervisores activos (que tienen estudiantes asignados)
        $supervisoresActivos = Supervisor::where('activo', true)
            ->get()
            ->filter(function($supervisor) {
                return $supervisor->estudiantes_asignados > 0;
            })
            ->count();

        // Documentos pendientes de revisión (solo de solicitudes activas)
        $documentosPendientes = Documento::whereHas('solicitud', function($query) {
            $query->where('estado_solicitud', 'SOLICITADA');
        })->count();

        // Capacidad total de supervisores
        $totalCapacidad = Supervisor::where('activo', true)->sum('max_estudiantes');
        $estudiantesAsignados = SolicitudPPS::whereIn('estado_solicitud', ['APROBADA', 'SOLICITADA'])
            ->whereNotNull('supervisor_id')
            ->count();
        $capacidadSupervisores = $totalCapacidad > 0 
            ? round(($estudiantesAsignados / $totalCapacidad) * 100, 2) 
            : 0;

        // ==========================================
        // TENDENCIA DE SOLICITUDES (ÚLTIMOS 6 MESES)
        // ==========================================
        // Solo activas
        $solicitudesPorMes = SolicitudPPS::select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('YEAR(created_at) as anio'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN estado_solicitud = "APROBADA" THEN 1 ELSE 0 END) as aprobadas'),
                DB::raw('SUM(CASE WHEN estado_solicitud = "RECHAZADA" THEN 1 ELSE 0 END) as rechazadas')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get()
            ->map(function($item) {
                $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return [
                    'mes' => $meses[$item->mes - 1],
                    'solicitudes' => $item->total,
                    'aprobadas' => $item->aprobadas,
                    'rechazadas' => $item->rechazadas
                ];
            });

        // ==========================================
        // DISTRIBUCIÓN POR ESTADO (PARA GRÁFICA PIE)
        // ==========================================
        $distribucionEstados = collect([
            ['name' => 'Solicitadas', 'value' => $solicitadas, 'color' => '#f59e0b'],
            ['name' => 'Aprobadas', 'value' => $aprobadas, 'color' => '#10b981'],
            ['name' => 'Finalizadas', 'value' => $finalizadas, 'color' => '#3b82f6'],
            ['name' => 'Rechazadas', 'value' => $rechazadas, 'color' => '#ef4444'],
            ['name' => 'Canceladas', 'value' => $canceladas, 'color' => '#6b7280']
        ]);

        // ==========================================
        // SOLICITUDES POR MES DEL AÑO ACTUAL
        // ==========================================
        $solicitudesPorMesAnioActual = collect([]);
        for ($mes = 1; $mes <= 12; $mes++) {
            $total = SolicitudPPS::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', $mes)
                ->count();
            
            $aprobadasMes = SolicitudPPS::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', $mes)
                ->where('estado_solicitud', 'APROBADA')
                ->count();
            
            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            
            $solicitudesPorMesAnioActual->push([
                'mes' => $meses[$mes - 1],
                'total' => $total,
                'aprobadas' => $aprobadasMes
            ]);
        }

        // ==========================================
        // SUPERVISORES MÁS ACTIVOS (TOP 5)
        // ==========================================
        $supervisoresMasActivos = Supervisor::where('activo', true)
            ->with('user')
            ->get()
            ->map(function($supervisor) {
                $estudiantesAsignados = $supervisor->estudiantes_asignados;
                
                // Calcular porcentaje de ocupación
                $ocupacion = $supervisor->max_estudiantes > 0 
                    ? round(($estudiantesAsignados / $supervisor->max_estudiantes) * 100, 2) 
                    : 0;
                
                // Calcular eficiencia basada en supervisiones completadas
                $supervisionesCompletadas = Supervision::whereHas('solicitud', function($query) use ($supervisor) {
                    $query->where('supervisor_id', $supervisor->id)
                          ->whereIn('estado_solicitud', ['APROBADA', 'SOLICITADA']);
                })->count();
                
                // Eficiencia: supervisiones completadas / (estudiantes * 2 supervisiones mínimas)
                $supervisionesEsperadas = $estudiantesAsignados * 2;
                $eficiencia = $supervisionesEsperadas > 0 
                    ? min(round(($supervisionesCompletadas / $supervisionesEsperadas) * 100, 2), 100)
                    : 0;

                return [
                    'id' => $supervisor->id,
                    'nombre' => $supervisor->user->name ?? 'Sin nombre',
                    'estudiantes' => $estudiantesAsignados,
                    'capacidad' => $supervisor->max_estudiantes,
                    'ocupacion' => $ocupacion,
                    'eficiencia' => $eficiencia
                ];
            })
            ->filter(function($supervisor) {
                return $supervisor['estudiantes'] > 0;
            })
            ->sortByDesc('estudiantes')
            ->take(5)
            ->values();

        // ==========================================
        // SOLICITUDES RECIENTES (ÚLTIMAS 5) - Solo activas
        // ==========================================
        $solicitudesRecientes = SolicitudPPS::with(['user', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($solicitud) {
                // Determinar prioridad basada en días transcurridos y estado
                $diasDesdeCreacion = Carbon::parse($solicitud->created_at)->diffInDays(Carbon::now());
                $prioridad = 'baja';
                
                if ($solicitud->estado_solicitud === 'SOLICITADA') {
                    if ($diasDesdeCreacion >= 5) {
                        $prioridad = 'alta';
                    } elseif ($diasDesdeCreacion >= 2) {
                        $prioridad = 'media';
                    }
                }

                return [
                    'id' => $solicitud->id,
                    'codigo' => 'PPS-' . date('Y') . '-' . str_pad($solicitud->id, 3, '0', STR_PAD_LEFT),
                    'estudiante' => $solicitud->user->name ?? 'N/A',
                    'cuenta' => $solicitud->numero_cuenta ?? 'N/A',
                    'estado' => $solicitud->estado_solicitud,
                    'fecha' => $solicitud->created_at->format('Y-m-d'),
                    'prioridad' => $prioridad,
                    'dias_transcurridos' => $diasDesdeCreacion
                ];
            });

        // ==========================================
        // TENDENCIAS (COMPARACIÓN MES ANTERIOR)
        // ==========================================
        $solicitudesMesActual = SolicitudPPS::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        $solicitudesMesAnterior = SolicitudPPS::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $tendenciaSolicitudes = $solicitudesMesAnterior > 0 
            ? round((($solicitudesMesActual - $solicitudesMesAnterior) / $solicitudesMesAnterior) * 100, 2)
            : ($solicitudesMesActual > 0 ? 100 : 0);

        $estudiantesMesActual = SolicitudPPS::whereIn('estado_solicitud', ['APROBADA', 'SOLICITADA'])
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->distinct('user_id')
            ->count('user_id');
        
        $estudiantesMesAnterior = SolicitudPPS::whereIn('estado_solicitud', ['APROBADA', 'SOLICITADA'])
            ->whereMonth('updated_at', Carbon::now()->subMonth()->month)
            ->whereYear('updated_at', Carbon::now()->subMonth()->year)
            ->distinct('user_id')
            ->count('user_id');

        $tendenciaEstudiantes = $estudiantesMesAnterior > 0 
            ? round((($estudiantesMesActual - $estudiantesMesAnterior) / $estudiantesMesAnterior) * 100, 2)
            : ($estudiantesMesActual > 0 ? 100 : 0);

        // ==========================================
        // RETORNAR VISTA CON TODOS LOS DATOS
        // ==========================================
        return view('admin.dashboard', compact(
            // Métricas principales
            'totalSolicitudes',
            'solicitadas',
            'aprobadas',
            'finalizadas',
            'rechazadas',
            'canceladas',
            'estudiantesActivos',
            'supervisoresActivos',
            'documentosPendientes',
            'capacidadSupervisores',
            
            // Gráficas
            'solicitudesPorMes',
            'distribucionEstados',
            'solicitudesPorMesAnioActual',
            
            // Tablas
            'supervisoresMasActivos',
            'solicitudesRecientes',
            
            // Tendencias
            'tendenciaSolicitudes',
            'tendenciaEstudiantes'
        ));
    }

    /**
     * Obtiene datos para reportes filtrados (API)
     */
    public function reportes(Request $request)
    {
        $query = SolicitudPPS::with(['user', 'supervisor.user']);

        // Aplicar filtros
        if ($request->has('estado') && $request->estado !== 'all') {
            $query->where('estado_solicitud', $request->estado);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('created_at', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);
        }

        if ($request->has('supervisor_id') && $request->supervisor_id !== 'all') {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $solicitudes
        ]);
    }

    /**
     * Exporta reportes (se implementará con Laravel Excel)
     */
    public function exportarReporte(Request $request)
    {
        // Por ahora retorna JSON, luego se implementará con Laravel Excel
        $solicitudes = SolicitudPPS::with(['user', 'supervisor.user'])
            ->when($request->estado, function($query, $estado) {
                return $query->where('estado_solicitud', $estado);
            })
            ->when($request->fecha_inicio && $request->fecha_fin, function($query) use ($request) {
                return $query->whereBetween('created_at', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $solicitudes,
            'total' => $solicitudes->count(),
            'message' => 'Datos listos para exportar'
        ]);
    }
}