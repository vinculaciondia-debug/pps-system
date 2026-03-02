<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\SolicitudPPS;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class FormatoSupervisionController extends Controller
{
    /**
     * Descarga el formato de supervisión (1ra o 2da) en DOCX.
     */
    public function downloadDocx(Request $request, SolicitudPPS $solicitud, int $numero)
    {
        $user = $request->user();

        // 1) Validar rol usando TUS helpers, no Spatie
        if (!$user->esSupervisorInstitucional() && !$user->esAdminInstitucional()) {
            abort(403, 'No autorizado (rol).');
        }

        // 2) Si es supervisor institucional, validar que sea el asignado (si hay uno)
        if ($user->esSupervisorInstitucional()) {
            $solicitud->load('supervisor.user'); // supervisor -> user

            if ($solicitud->supervisor && (int)$solicitud->supervisor->user_id !== (int)$user->id) {
                abort(403, 'Solo el supervisor asignado puede generar el formato.');
            }
        }

        // 3) Elegir plantilla
        $plantillas = [
            1 => 'Formato_1ra_Supervision.docx',
            2 => 'Formato_2da_Supervision.docx',
        ];

        if (!isset($plantillas[$numero])) {
            abort(404, 'Número de supervisión inválido.');
        }

        $templatePath = storage_path('app/' . $plantillas[$numero]);
        if (!file_exists($templatePath)) {
            abort(404, 'Plantilla no encontrada en storage/app.');
        }

        // 4) Mapear datos a placeholders
        $data = $this->mapData($solicitud, $user);

        $template = new TemplateProcessor($templatePath);
        foreach ($data as $k => $v) {
            $template->setValue($k, $v ?? '');
        }

        // 5) Guardar temporal y descargar
        $tmp = tempnam(sys_get_temp_dir(), 'sup_') . '.docx';
        $template->saveAs($tmp);

        $nombreAlumno = $solicitud->user->name ?? 'alumno';

        return response()->download(
            $tmp,
            "Supervision_{$numero}_{$nombreAlumno}.docx"
        )->deleteFileAfterSend(true);
    }

    /**
     * Mapea los datos del modelo -> placeholders del DOCX.
     */
    private function mapData(SolicitudPPS $s, $supervisor): array
    {
        $u = $s->user;

        return [
            // ---------- ESTUDIANTE ----------
            'est_nombre_completo'   => strtoupper($u->name ?? ''),
            'est_cuenta'            => $s->numero_cuenta ?? '',
            'est_dni'               => $s->dni_estudiante ?? '',
            'est_celular'           => $s->telefono_alumno ?? '',
            'est_correo'            => $u->email ?? '',

            // ---------- EMPRESA ----------
            'inst_nombre'           => strtoupper($s->nombre_empresa ?? ''),
            'inst_direccion'        => $s->direccion_empresa ?? '',

            // ---------- JEFE INMEDIATO ----------
            'jefe_nombre'           => strtoupper($s->nombre_jefe ?? ''),
            'jefe_cargo'            => $s->cargo_jefe ?? '',
            'jefe_correo'           => $s->correo_jefe ?? '',
            'jefe_telefono'         => $s->numero_jefe ?? '',
            'jefe_celular'          => $s->numero_jefe ?? '',
            'nivel_academico'  => ucfirst($s->nivel_academico_jefe ?? ''),

            // ---------- PRÁCTICA PROFESIONAL ----------
            'pp_modalidad'          => ucfirst($s->modalidad ?? ''),
            'pp_inicio'             => $this->fmtLargo($s->fecha_inicio),
            'pp_fin'                => $this->fmtLargo($s->fecha_fin),
            'pp_jornada'            => $s->horas_semanales ? $s->horas_semanales . ' horas/semana' : '',
            'pp_horario'            => $s->horario ?? '',

            // ---------- VALIDACIÓN ----------
            'lugar_y_fecha'         => 'Tegucigalpa, ' . now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
            'supervisor_nombre'     => strtoupper($supervisor->name ?? ''),
        ];
    }

    /**
     * Formatea fecha en formato largo español: "jueves 11 de diciembre de 2025"
     */
    private function fmtLargo($value): string
    {
        if (!$value) return '';

        try {
            return \Carbon\Carbon::parse($value)
                ->locale('es')
                ->isoFormat('dddd D [de] MMMM [de] YYYY');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    /**
     * Formatea fecha en formato corto: "11/12/2025"
     */
    private function fmt($value): string
    {
        if (!$value) return '';

        try {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
