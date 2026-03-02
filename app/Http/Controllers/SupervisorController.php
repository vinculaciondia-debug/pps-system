<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudPPS;
use App\Models\Supervision;
use App\Models\ReporteSupervision;

class SupervisorController extends Controller
{
    // Mostrar el dashboard del supervisor
    public function dashboard()
    {
        $supervisorId = Auth::id();

        $estudiantes = SolicitudPPS::where('supervisor_id', $supervisorId)
            ->where('estado_solicitud', 'APROBADA')
            ->get();

        $totalEstudiantes = $estudiantes->count();
        $completadas = 0;
        $pendientes = 0;

        foreach ($estudiantes as $solicitud) {
            $reportes = \App\Models\Supervision::where('solicitud_pps_id', $solicitud->id)->count();
            if ($reportes >= 2) {
                $completadas++;
            } else {
                $pendientes++;
            }
        }

        return view('supervisores.dashboard', compact('totalEstudiantes', 'completadas', 'pendientes'));
    }

    // Método para mostrar estudiantes asignados (vista estudiantes.blade.php)
    public function estudiantesAsignados()
    {
        $supervisorId = Auth::id();

        $estudiantes = SolicitudPPS::where('supervisor_id', $supervisorId)
            ->where('estado_solicitud', 'APROBADA')
            ->with('user') // para mostrar el nombre del estudiante
            ->get();

        return view('supervisores.estudiantes', compact('estudiantes'));
    }

    // 📌 Alias para que coincida con la ruta 'supervisores.estudiantes'
    public function estudiantes()
    {
        return $this->estudiantesAsignados();
    }

    // Historial de reportes de supervisión
    public function historial()
    {
        $supervisorId = Auth::id();

        $reportes = ReporteSupervision::with('solicitud.user')
            ->whereHas('solicitud', function ($query) use ($supervisorId) {
                $query->where('supervisor_id', $supervisorId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('supervisores.historial', compact('reportes'));
    }

    // Aquí irán más métodos después para subir reportes
}
