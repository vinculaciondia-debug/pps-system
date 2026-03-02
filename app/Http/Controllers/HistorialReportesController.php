<?php

namespace App\Http\Controllers;

use App\Models\ReporteSupervision;
use App\Models\SolicitudPPS;
use Illuminate\Support\Facades\Auth;

class HistorialReportesController extends Controller
{
    // Mostrar historial de reportes del supervisor actual
    public function index()
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
}
