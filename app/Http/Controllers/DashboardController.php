<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SolicitudPPS;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Conteos por estado
        $pendientes = SolicitudPPS::whereIn('estado_solicitud', ['SOLICITADA', 'RECHAZADA'])->count();
        $aprobadas  = SolicitudPPS::where('estado_solicitud', 'APROBADA')->count();
        $canceladas = SolicitudPPS::where('estado_solicitud', 'CANCELADA')->count();
        $finalizadas = SolicitudPPS::where('estado_solicitud', 'FINALIZADA')->count();

        // Supervisores activos con cupo disponible
        $supervisoresDisponibles = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('supervisores', 'users.id', '=', 'supervisores.user_id')
            ->where('roles.name', 'supervisor')
            ->where('supervisores.activo', 1)
            ->whereRaw('(
                SELECT COUNT(*) FROM solicitud_p_p_s 
                WHERE solicitud_p_p_s.supervisor_id = users.id
            ) < supervisores.max_estudiantes')
            ->count();

        // Empresas con alumnos en práctica
        $empresas = SolicitudPPS::whereIn('estado_solicitud', ['APROBADA', 'FINALIZADA'])
            ->select('nombre_empresa', DB::raw('COUNT(*) as total'))
            ->groupBy('nombre_empresa')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact(
            'pendientes',
            'aprobadas',
            'canceladas',
            'finalizadas',
            'supervisoresDisponibles',
            'empresas'
        ));
    }
}
