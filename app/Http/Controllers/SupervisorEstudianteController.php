<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudPPS;

class SupervisorEstudianteController extends Controller
{
    public function index()
    {
        $supervisorId = Auth::id();

        // Obtener solicitudes aprobadas asignadas a este supervisor
        $estudiantes = SolicitudPPS::with('user')
            ->where('estado_solicitud', 'APROBADA')
            ->where('supervisor_id', $supervisorId)
            ->get();

        return view('supervisores.estudiantes', compact('estudiantes'));
    }
}
