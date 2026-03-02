<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Supervision;
use App\Models\SolicitudPPS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupervisorReporteController extends Controller
{
    /**
     * Mostrar índice de reportes (resumen + tabla de solicitudes finalizadas)
     * + (temporal) historial paginado para modal si lo sigues usando.
     */
    public function index(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        if (!$supervisor) {
            return redirect()->route('supervisor.dashboard')
                ->with('error', 'No tienes perfil de supervisor');
        }

        // Filtros
        $año = (int) $request->get('año', now()->year);
        $estudiante = trim((string) $request->get('estudiante', ''));
        $soloSupervisadas = (bool) $request->get('supervisadas', false);

        /**
         * SOLICITUDES FINALIZADAS (tabla principal)
         */
        $query = SolicitudPPS::query()
            ->where('supervisor_id', $supervisor->id)
            ->where('estado_solicitud', 'FINALIZADA')
            ->where($this->filtroAnioFinalizacion($año))
            ->with(['user'])
            ->withCount('supervisiones');

        if ($estudiante !== '') {
            $query->whereHas('user', function ($q) use ($estudiante) {
                $q->where('name', 'like', "%{$estudiante}%")
                  ->orWhere('email', 'like', "%{$estudiante}%");
            });
        }

        if ($soloSupervisadas) {
            $query->having('supervisiones_count', '>=', 2);
        }

        $solicitudes = $query->orderByDesc('updated_at')->paginate(10);

        /**
         * HISTORIAL (paginación separada) - útil si sigues con modal.
         * Si luego lo mueves a pantalla independiente, puedes quitarlo del index.
         */
        $historialQuery = Supervision::query()
            ->whereHas('solicitud', function ($sq) use ($supervisor, $año) {
                $sq->where('supervisor_id', $supervisor->id)
                   ->where($this->filtroAnioFinalizacion($año));
            })
            ->with([
                'solicitud:id,user_id,nombre_empresa,estado_solicitud',
                'solicitud.user:id,name,email',
            ])
            ->orderByDesc('created_at');

        if ($estudiante !== '') {
            $historialQuery->whereHas('solicitud.user', function ($uq) use ($estudiante) {
                $uq->where('name', 'like', "%{$estudiante}%")
                   ->orWhere('email', 'like', "%{$estudiante}%");
            });
        }

        $historial = $historialQuery->paginate(10, ['*'], 'page_historial');

        /**
         * ESTADÍSTICAS (keys SIEMPRE definidas)
         */
        $estadisticas = [
            'total_finalizadas' => 0,
            'total_supervisiones' => 0,
            'total_supervisadas' => 0,
            'promedio_duracion' => 0,
            'estudiantes_activos' => 0,
        ];

        // Total finalizadas (por año coherente)
        $estadisticas['total_finalizadas'] = SolicitudPPS::query()
            ->where('supervisor_id', $supervisor->id)
            ->where('estado_solicitud', 'FINALIZADA')
            ->where($this->filtroAnioFinalizacion($año))
            ->count();

        // Total supervisiones realizadas (solo sobre solicitudes FINALIZADAS del año)
        $estadisticas['total_supervisiones'] = Supervision::query()
            ->whereHas('solicitud', function ($sq) use ($supervisor, $año) {
                $sq->where('supervisor_id', $supervisor->id)
                   ->where('estado_solicitud', 'FINALIZADA')
                   ->where($this->filtroAnioFinalizacion($año));
            })
            ->count();

        // Total prácticas con 2 supervisiones (2/2) o más
        $estadisticas['total_supervisadas'] = SolicitudPPS::query()
            ->where('supervisor_id', $supervisor->id)
            ->where('estado_solicitud', 'FINALIZADA')
            ->where($this->filtroAnioFinalizacion($año))
            ->withCount('supervisiones')
            ->having('supervisiones_count', '>=', 2)
            ->count();

        // Promedio duración (por fecha_fin del año)
        $estadisticas['promedio_duracion'] = $this->calcularPromedioDuracion($supervisor->id, $año);

        // Estudiantes activos
        $estadisticas['estudiantes_activos'] = SolicitudPPS::query()
            ->where('supervisor_id', $supervisor->id)
            ->where('estado_solicitud', 'APROBADA')
            ->count();

        return view('supervisor.reportes.index', [
            'solicitudes' => $solicitudes,
            'estadisticas' => $estadisticas,
            'historial' => $historial, // si luego lo quitas del index, elimina esto
            'supervisor' => $supervisor,
            'filtros' => [
                'año' => $año,
                'estudiante' => $estudiante,
                'supervisadas' => $soloSupervisadas ? '1' : '',
            ],
        ]);
    }

    /**
     * NUEVO: Pantalla dedicada (paginada) para historial de supervisiones
     * Usa numero_supervision.
     */
    public function historial(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        if (!$supervisor) {
            return redirect()->route('supervisor.dashboard')
                ->with('error', 'No tienes perfil de supervisor');
        }

        $año = (int) $request->get('año', now()->year);
        $estudiante = trim((string) $request->get('estudiante', ''));
        $numero = $request->get('numero'); // '1' o '2' opcional

        $query = Supervision::query()
            ->whereHas('solicitud', function ($sq) use ($supervisor, $año) {
                $sq->where('supervisor_id', $supervisor->id)
                   ->where($this->filtroAnioFinalizacion($año));
            })
            ->with([
                'solicitud:id,user_id,nombre_empresa,estado_solicitud',
                'solicitud.user:id,name,email',
            ])
            ->orderByDesc('created_at');

        if ($estudiante !== '') {
            $query->whereHas('solicitud.user', function ($uq) use ($estudiante) {
                $uq->where('name', 'like', "%{$estudiante}%")
                   ->orWhere('email', 'like', "%{$estudiante}%");
            });
        }

        // IMPORTANTE: tu campo real
        if ($numero === '1' || $numero === '2') {
            $query->where('numero_supervision', (int) $numero);
        }

        $historial = $query->paginate(15)->appends($request->query());

        return view('supervisor.reportes.historial', [
            'historial' => $historial,
            'filtros' => [
                'año' => $año,
                'estudiante' => $estudiante,
                'numero' => $numero,
            ],
        ]);
    }

    /**
     * Exportar a xlsx
     */
    public function exportExcel(Request $request)
    {
        try {
            $supervisor = Auth::user()->supervisor;
            if (!$supervisor) {
                return back()->with('error', 'No tienes perfil de supervisor');
            }

            $año = (int) $request->get('año', now()->year);
            $estudiante = trim((string) $request->get('estudiante', ''));
            $soloSupervisadas = (bool) $request->get('supervisadas', false);

            $query = SolicitudPPS::query()
                ->where('supervisor_id', $supervisor->id)
                ->where('estado_solicitud', 'FINALIZADA')
                ->where($this->filtroAnioFinalizacion($año))
                ->with(['user'])
                ->withCount('supervisiones');

            if ($estudiante !== '') {
                $query->whereHas('user', function ($q) use ($estudiante) {
                    $q->where('name', 'like', "%{$estudiante}%")
                      ->orWhere('email', 'like', "%{$estudiante}%");
                });
            }

            if ($soloSupervisadas) {
                $query->having('supervisiones_count', '>=', 2);
            }

            $solicitudes = $query->orderByDesc('updated_at')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet()->setTitle('Reportes');

            $headings = [
                'Estudiante', 'Email', 'Empresa', 'Puesto',
                'Fecha Inicio', 'Estado', 'Supervisiones', 'Fecha Finalización'
            ];
            $sheet->fromArray($headings, null, 'A1');

            $r = 2;
            foreach ($solicitudes as $s) {
                $sheet->setCellValue("A{$r}", optional($s->user)->name);
                $sheet->setCellValue("B{$r}", optional($s->user)->email);
                $sheet->setCellValue("C{$r}", $s->nombre_empresa);
                $sheet->setCellValue("D{$r}", $s->puesto_trabajo);

                if ($s->fecha_inicio) {
                    $sheet->setCellValue("E{$r}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($s->fecha_inicio));
                }

                $sheet->setCellValue("F{$r}", $s->estado_solicitud);
                $sheet->setCellValue("G{$r}", (int) ($s->supervisiones_count ?? 0));

                $fechaFinal = $s->fecha_fin ?? $s->updated_at;
                $sheet->setCellValue("H{$r}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($fechaFinal));

                $r++;
            }
            $last = max(2, $r - 1);

            foreach (['E', 'H'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$last}")
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
            }

            $sheet->getStyle('A1:H1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(24);

            foreach (range('A', 'H') as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }

            $sheet->getStyle("A1:H{$last}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()
                ->setRGB('DDDDDD');

            for ($i = 2; $i <= $last; $i++) {
                if ($i % 2 === 0) {
                    $sheet->getStyle("A{$i}:H{$i}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('F7FAFC');
                }
            }

            for ($i = 2; $i <= $last; $i++) {
                if (strtoupper((string) $sheet->getCell("F{$i}")->getValue()) === 'FINALIZADA') {
                    $sheet->getStyle("F{$i}")->getFont()->setBold(true)->getColor()->setRGB('166534');
                }
            }

            $sheet->setAutoFilter("A1:H1");
            $sheet->freezePane('A2');

            $writer = new Xlsx($spreadsheet);
            $file = "reportes_supervisor_{$año}.xlsx";

            return new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$file}\"",
                'Cache-Control' => 'max-age=0',
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $supervisor = Auth::user()->supervisor;
            if (!$supervisor) {
                return back()->with('error', 'No tienes perfil de supervisor');
            }

            $año = (int) $request->get('año', now()->year);
            $estudiante = trim((string) $request->get('estudiante', ''));
            $soloSupervisadas = (bool) $request->get('supervisadas', false);

            $query = SolicitudPPS::query()
                ->where('supervisor_id', $supervisor->id)
                ->where('estado_solicitud', 'FINALIZADA')
                ->where($this->filtroAnioFinalizacion($año))
                ->with(['user'])
                ->withCount('supervisiones');

            if ($estudiante !== '') {
                $query->whereHas('user', function ($q) use ($estudiante) {
                    $q->where('name', 'like', "%{$estudiante}%")
                      ->orWhere('email', 'like', "%{$estudiante}%");
                });
            }

            if ($soloSupervisadas) {
                $query->having('supervisiones_count', '>=', 2);
            }

            $solicitudes = $query->orderByDesc('updated_at')->get();

            $estadisticas = [
                'total' => $solicitudes->count(),
                'supervisiones' => $solicitudes->sum(fn ($s) => (int) ($s->supervisiones_count ?? 0)),
            ];

            $pdf = Pdf::loadView('supervisor.reportes.pdf', [
                'solicitudes' => $solicitudes,
                'supervisor' => $supervisor,
                'año' => $año,
                'estadisticas' => $estadisticas,
            ]);

            return $pdf->download("reportes_supervisor_{$año}.pdf");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    /**
     * Filtro de año coherente:
     * - Si existe fecha_fin, usamos año de fecha_fin
     * - Si fecha_fin es null, usamos updated_at como fallback
     */
    private function filtroAnioFinalizacion(int $año)
    {
        return function ($q) use ($año) {
            $q->whereYear('fecha_fin', $año)
              ->orWhere(function ($q2) use ($año) {
                  $q2->whereNull('fecha_fin')
                     ->whereYear('updated_at', $año);
              });
        };
    }

    /**
     * Calcular promedio de duración (días) por año (fecha_fin).
     * Evita negativos y registros inválidos.
     */
    private function calcularPromedioDuracion(int $supervisorId, ?int $year = null): int
    {
        $q = SolicitudPPS::query()
            ->where('supervisor_id', $supervisorId)
            ->where('estado_solicitud', 'FINALIZADA')
            ->whereNotNull('fecha_inicio')
            ->whereNotNull('fecha_fin');

        if ($year) {
            $q->whereYear('fecha_fin', $year);
        }

        $solicitudes = $q->get(['fecha_inicio', 'fecha_fin']);

        if ($solicitudes->isEmpty()) {
            return 0;
        }

        $duraciones = $solicitudes->map(function ($s) {
            $ini = $s->fecha_inicio;
            $fin = $s->fecha_fin;

            if (!$ini || !$fin) return 0;
            if ($fin->lt($ini)) return 0;

            return $ini->diffInDays($fin);
        });

        return (int) round($duraciones->avg());
    }
}
