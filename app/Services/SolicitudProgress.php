<?php

namespace App\Services;

use App\Models\SolicitudPPS;

class SolicitudProgress
{
    public static function compute(SolicitudPPS $s): array
    {
        $docsReq = config('pps.required_docs.default');
        $subidos = collect($s->documentos ?? [])->pluck('tipo')->map(fn($v)=>strtolower($v))->unique();
        $faltantes = collect($docsReq)->diff($subidos)->values()->all();

        $p = 0;
        if ($s->id) $p += 10;                         // solicitud creada
        if ($s->supervisor_id) $p += 20;             // asignada
        $totalReq = max(1, count($docsReq));
        $cumplidos = count(array_intersect($docsReq, $subidos->all()));
        $p += (int) round(($cumplidos / $totalReq) * 40); // documentos
        if ($s->estado_solicitud === 'APROBADA') $p += 20;
        if ($s->estado_solicitud === 'FINALIZADA') $p += 10;

        return [
            'progress'    => max(0, min(100, $p)),
            'required'    => array_values($docsReq),
            'uploaded'    => $subidos->values()->all(),
            'missing'     => $faltantes,
            'estado'      => $s->estado_solicitud,
            'observacion' => $s->observacion,
            'asignada'    => (bool) $s->supervisor_id,
        ];
    }
}
