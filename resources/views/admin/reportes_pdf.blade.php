<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte Solicitudes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        table { width:100%; border-collapse: collapse; margin-top:12px; }
        th, td { border:1px solid #ccc; padding:4px 6px; }
        th { background:#f2f2f2; }
    </style>
</head>
<body>
    <h1>Reporte de Solicitudes</h1>
    <p>Total: {{ $resumen['total'] }} | Aprobadas: {{ $resumen['aprobadas'] }} | Rechazadas: {{ $resumen['rechazadas'] }} | Finalizadas: {{ $resumen['finalizadas'] }} | Solicitadas: {{ $resumen['solicitadas'] }}</p>
    <table>
        <thead>
        <tr>
            <th>ID</th><th>Cuenta</th><th>Estado</th><th>Tipo</th><th>Supervisor</th><th>Fecha</th>
        </tr>
        </thead>
        <tbody>
        @foreach($solicitudes as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->numero_cuenta }}</td>
                <td>{{ $s->estado_solicitud }}</td>
                <td>{{ $s->tipo_practica }}</td>
                <td>{{ $s->supervisor_name ?? 'Sin asignar' }}</td>
                <td>{{ optional($s->created_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>