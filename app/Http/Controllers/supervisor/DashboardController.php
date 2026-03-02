<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudPPS;
use App\Models\Supervisor;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener supervisor
        $supervisor = Supervisor::where('user_id', $user->id)->firstOrFail();
        
        // ✅ Total asignados ACTIVOS (sin finalizadas ni canceladas)
        $totalAsignados = SolicitudPPS::where('supervisor_id', $supervisor->id)
            ->whereIn('estado_solicitud', ['SOLICITADA', 'APROBADA'])
            ->count();
        
        // Contadores por estado
        $aprobadas = SolicitudPPS::where('supervisor_id', $supervisor->id)
            ->where('estado_solicitud', 'APROBADA')
            ->count();
        
        $finalizadas = SolicitudPPS::where('supervisor_id', $supervisor->id)
            ->where('estado_solicitud', 'FINALIZADA')
            ->count();
        
        // Alumnos recientes (solo activos)
        $alumnosRecientes = SolicitudPPS::with('user')
            ->where('supervisor_id', $supervisor->id)
            ->whereIn('estado_solicitud', ['APROBADA'])
            ->latest('id')
            ->take(5)
            ->get();
        
        return view('supervisor.dashboard', compact(
            'supervisor',
            'totalAsignados',
            'aprobadas',
            'finalizadas',
            'alumnosRecientes'
        ));
    }
}