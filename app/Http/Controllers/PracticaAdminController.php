<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudPPS;
use App\Models\User;

class PracticaAdminController extends Controller
{
   

    public function aprobadas()
    {
        $solicitudes = SolicitudPPS::with('user')
            ->where('estado_solicitud', 'APROBADA')
            ->latest()
            ->get();

        return view('admin.solicitudes.aprobadas', compact('solicitudes'));
    }

    public function canceladas()
    {
        $solicitudes = SolicitudPPS::with('user')
            ->where('estado_solicitud', 'CANCELADA')
            ->latest()
            ->get();

        return view('admin.solicitudes.canceladas', compact('solicitudes'));
    }

    public function finalizadas()
    {
        $solicitudes = SolicitudPPS::with('user')
            ->where('estado_solicitud', 'FINALIZADA')
            ->latest()
            ->get();

        return view('admin.solicitudes.finalizadas', compact('solicitudes'));
    }

    public function pendientes()
    {
        $solicitudes = SolicitudPPS::with('user')
            ->whereIn('estado_solicitud', ['SOLICITADA', 'OBSERVADA'])
            ->latest()
            ->get();

        return view('admin.solicitudes.pendientes', compact('solicitudes'));
    }

        public function aprobar($id)
    {
        $solicitud = SolicitudPPS::findOrFail($id);
        $solicitud->estado_solicitud = 'APROBADA';
        $solicitud->save();

        return back()->with('status', 'Solicitud aprobada correctamente.');
    }

   
    

    public function finalizar($id)
    {
        $solicitud = SolicitudPPS::findOrFail($id);
        $solicitud->estado_solicitud = 'FINALIZADA';
        $solicitud->save();

        return back()->with('status', 'Práctica finalizada.');
    }

    public function verDocumentos($id)
{
    $solicitud = \App\Models\SolicitudPPS::with(['documentos', 'user'])->findOrFail($id);

    return view('admin.solicitudes.documentos', compact('solicitud'));
}


public function rechazar(Request $request, $id)
{
    $request->validate([
        'observacion' => 'required|string|min:5'
    ]);

    $solicitud = SolicitudPPS::findOrFail($id);
    $solicitud->estado_solicitud = 'RECHAZADA';
    $solicitud->observacion = $request->observacion;
    $solicitud->save();

    return redirect()->back()->with('status', 'Solicitud rechazada correctamente.');
}

public function cancelar(Request $request, $id)
{
    $request->validate([
        'motivo_cancelacion' => 'required|string|min:5'
    ]);

    $solicitud = SolicitudPPS::findOrFail($id);
    $solicitud->estado_solicitud = 'CANCELADA';
    $solicitud->motivo_cancelacion = $request->motivo_cancelacion;
    $solicitud->save();

    return redirect()->back()->with('status', 'Solicitud cancelada correctamente.');
}



}
