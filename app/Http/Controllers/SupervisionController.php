<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Supervision;
use App\Models\SolicitudPPS;

class SupervisionController extends Controller
{
    // Mostrar listado de estudiantes asignados al supervisor
    public function index()
    {
        $user = Auth::user();

        $solicitudes = SolicitudPPS::where('supervisor_id', $user->id)
            ->where('estado_solicitud', 'APROBADA')
            ->with('user')
            ->get();

        return view('supervisores.estudiantes', compact('solicitudes'));
    }

    // Formulario para ver y subir supervisiones
    public function show($solicitudId)
    {
        $solicitud = SolicitudPPS::with(['user', 'supervisiones'])->findOrFail($solicitudId);
        return view('supervisores.reportes.show', compact('solicitud'));
    }

    // Guardar supervisión
    public function store(Request $request, $solicitudId)
    {
        $request->validate([
            'numero_supervision' => 'required|in:1,2',
            'comentario' => 'required|string|max:1000',
            'archivo' => 'required|mimes:pdf|max:2048',
        ]);

        $ruta = $request->file('archivo')->store("supervisiones/{$solicitudId}", 'public');

        Supervision::create([
            'solicitud_pps_id' => $solicitudId,
            'numero_supervision' => $request->numero_supervision,
            'comentario' => $request->comentario,
            'archivo' => $ruta,
        ]);

        return back()->with('success', 'Supervisión registrada correctamente.');
    }
}
