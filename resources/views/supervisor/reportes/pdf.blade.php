<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #1e40af;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .estadisticas {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .estadistica-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            background: #f9fafb;
        }
        .estadistica-box h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #666;
        }
        .estadistica-box .numero {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #1e40af;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <h1> Reporte de Supervisiones</h1>
        <p><strong>Supervisor:</strong> {{ $supervisor->nombre }}</p>
        <p><strong>Período:</strong> Año {{ $año }}</p>
        <p><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="estadisticas">
        <div class="estadistica-box">
            <h3>Prácticas Finalizadas</h3>
            <div class="numero">{{ $estadisticas['total'] }}</div>
        </div>
        <div class="estadistica-box">
            <h3>Supervisiones Realizadas</h3>
            <div class="numero">{{ $estadisticas['supervisiones'] }}</div>
        </div>
        <div class="estadistica-box">
            <h3>Promedio por Práctica</h3>
            <div class="numero">{{ round($estadisticas['supervisiones'] / max(1, $estadisticas['total']), 1) }}</div>
        </div>
        <div class="estadistica-box">
            <h3>Año</h3>
            <div class="numero">{{ $año }}</div>
        </div>
    </div>

    {{-- TABLA --}}
    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Email</th>
                <th>Empresa</th>
                <th>Puesto</th>
                <th>Supervisiones</th>
                <th>Finalizado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($solicitudes as $solicitud)
                <tr>
                    <td><strong>{{ $solicitud->user->name }}</strong></td>
                    <td>{{ $solicitud->user->email }}</td>
                    <td>{{ $solicitud->nombre_empresa }}</td>
                    <td>{{ $solicitud->puesto_trabajo }}</td>
                    <td>{{ $solicitud->supervisiones->count() }}/2</td>
                    <td>{{ $solicitud->updated_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema PPS UNAH</p>
    </div>
</body>
</html>