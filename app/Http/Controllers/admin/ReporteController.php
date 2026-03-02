<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- agregar
use App\Models\SolicitudPPS as Solicitud;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends Controller
{
    public function index()
    {
        // Listar supervisores por su ID real (tabla supervisores) y nombre (users.name)
        $supervisores = DB::table('supervisores as s')
            ->join('users as u','u.id','=','s.user_id')
            ->where('u.cod_rol', 3)
            ->orderBy('u.name')
            ->get(['s.id as id','u.name']);

        return view('admin.reportes', compact('supervisores'));
    }

    public function preview(Request $request)
    {
        $query = Solicitud::query()
            ->leftJoin('supervisores as sv','sv.id','=','solicitud_p_p_s.supervisor_id')
            ->leftJoin('users as sup','sup.id','=','sv.user_id')
            ->select([
                'solicitud_p_p_s.id',
                'solicitud_p_p_s.numero_cuenta',
                'solicitud_p_p_s.estado_solicitud',
                'solicitud_p_p_s.created_at',
                'solicitud_p_p_s.tipo_practica',
                'solicitud_p_p_s.supervisor_id',
                'sup.name as supervisor_name',
            ]);

        if ($request->filled('estado'))     $query->where('solicitud_p_p_s.estado_solicitud', $request->estado);
        if ($request->filled('supervisor')) $query->where('solicitud_p_p_s.supervisor_id', $request->supervisor); // id de tabla supervisores
        if ($request->filled('desde'))      $query->whereDate('solicitud_p_p_s.created_at','>=',$request->desde);
        if ($request->filled('hasta'))      $query->whereDate('solicitud_p_p_s.created_at','<=',$request->hasta);

        $solicitudes = $query->orderByDesc('solicitud_p_p_s.created_at')->limit(200)->get();

        $resumen = [
            'total'       => $solicitudes->count(),
            'aprobadas'   => $solicitudes->where('estado_solicitud','APROBADA')->count(),
            'rechazadas'  => $solicitudes->where('estado_solicitud','RECHAZADA')->count(),
            'finalizadas' => $solicitudes->where('estado_solicitud','FINALIZADA')->count(),
            'solicitadas' => $solicitudes->where('estado_solicitud','SOLICITADA')->count(),
        ];

        return response()->json(['resumen'=>$resumen,'data'=>$solicitudes]);
    }

    private function getFiltered(Request $request): array
    {
        $query = Solicitud::query()
            ->leftJoin('supervisores as sv','sv.id','=','solicitud_p_p_s.supervisor_id')
            ->leftJoin('users as sup','sup.id','=','sv.user_id')
            ->select([
                'solicitud_p_p_s.id',
                'solicitud_p_p_s.numero_cuenta',
                'solicitud_p_p_s.estado_solicitud',
                'solicitud_p_p_s.created_at',
                'solicitud_p_p_s.tipo_practica',
                'solicitud_p_p_s.supervisor_id',
                'sup.name as supervisor_name',
            ]);

        if ($request->filled('estado'))     $query->where('solicitud_p_p_s.estado_solicitud', $request->estado);
        if ($request->filled('supervisor')) $query->where('solicitud_p_p_s.supervisor_id', $request->supervisor);
        if ($request->filled('desde'))      $query->whereDate('solicitud_p_p_s.created_at','>=',$request->desde);
        if ($request->filled('hasta'))      $query->whereDate('solicitud_p_p_s.created_at','<=',$request->hasta);

        $solicitudes = $query->orderByDesc('solicitud_p_p_s.created_at')->get();

        $resumen = [
            'total'       => $solicitudes->count(),
            'aprobadas'   => $solicitudes->where('estado_solicitud','APROBADA')->count(),
            'rechazadas'  => $solicitudes->where('estado_solicitud','RECHAZADA')->count(),
            'finalizadas' => $solicitudes->where('estado_solicitud','FINALIZADA')->count(),
            'solicitadas' => $solicitudes->where('estado_solicitud','SOLICITADA')->count(),
        ];

        return compact('solicitudes','resumen');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getFiltered($request)['solicitudes'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Solicitudes');

        // Encabezados
        $headers = ['ID','Cuenta','Estado','Tipo','Supervisor','Fecha'];
        $sheet->fromArray($headers, null, 'A1');

        // Estilo encabezados
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Datos
        $row = 2;
        foreach ($data as $s) {
            $sheet->setCellValue("A{$row}", $s->id);
            $sheet->setCellValueExplicit("B{$row}", $s->numero_cuenta, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $s->estado_solicitud);
            $sheet->setCellValue("D{$row}", $s->tipo_practica);
            $sheet->setCellValue("E{$row}", $s->supervisor_name ?? 'Sin asignar');
            $sheet->setCellValue("F{$row}", optional($s->created_at)->format('d/m/Y'));

            // Bordes fila
            $sheet->getStyle("A{$row}:F{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Color Estado
            $color = match($s->estado_solicitud) {
                'APROBADA'   => 'C6EFCE',
                'RECHAZADA'  => 'FFC7CE',
                'FINALIZADA' => 'D9D2E9',
                'SOLICITADA' => 'FFE699',
                'CANCELADA'  => 'E7E6E6',
                default      => 'FFFFFF',
            };
            $sheet->getStyle("C{$row}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($color);

            $row++;
        }

        // Ajustes columnas
        foreach (['A'=>6,'B'=>18,'C'=>12,'D'=>12,'E'=>26,'F'=>12] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Wrap texto supervisor
        $sheet->getStyle("E2:E{$row}")->getAlignment()->setWrapText(true);

        // Alineación general
        $sheet->getStyle("A2:A{$row}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("F2:F{$row}")->getAlignment()->setHorizontal('center');

        // Freeze encabezado
        $sheet->freezePane('A2');

        // Autofiltro
        $sheet->setAutoFilter("A1:F" . ($row-1));

        // Ajustar impresión
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'reporte_solicitudes.xlsx', [
            'Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFiltered($request);
        $pdf = Pdf::loadView('admin.reportes_pdf', $data)->setPaper('a4','portrait');
        return $pdf->download('reporte_solicitudes.pdf');
    }
}