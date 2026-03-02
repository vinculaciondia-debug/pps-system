@extends('layouts.supervisores')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-unahblue mb-6">Historial de Reportes</h1>

    <div class="bg-white rounded-lg shadow p-6">
        @if ($reportes->isEmpty())
            <p class="text-gray-600">No hay reportes registrados.</p>
        @else
            <table class="w-full text-sm text-left border border-gray-200">
                <thead class="bg-unahblue text-white">
                    <tr>
                        <th class="px-4 py-2">Estudiante</th>
                        <th class="px-4 py-2">Empresa</th>
                        <th class="px-4 py-2"># Supervisión</th>
                        <th class="px-4 py-2">Comentario</th>
                        <th class="px-4 py-2">Archivo</th>
                        <th class="px-4 py-2">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($reportes as $reporte)
                        <tr>
                            <td class="px-4 py-2">{{ $reporte->solicitud->user->name }}</td>
                            <td class="px-4 py-2">{{ $reporte->solicitud->empresa }}</td>
                            <td class="px-4 py-2 text-center">{{ $reporte->numero_supervision }}</td>
                            <td class="px-4 py-2">{{ $reporte->comentario ?? '—' }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ asset('storage/' . $reporte->archivo) }}" target="_blank"
                                   class="text-blue-600 hover:underline">
                                    Ver archivo
                                </a>
                            </td>
                            <td class="px-4 py-2">{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
