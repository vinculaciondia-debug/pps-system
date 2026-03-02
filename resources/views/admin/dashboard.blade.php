@extends('layouts.admin')

@section('title', 'Dashboard Administrativo - PPS')

@push('styles')

@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="flex flex-col lg:items-start gap-3 mb-6">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold text-unahblue">Dashboard Administrativo</h1>
            <p class="text-slate-500">Sistema PPS - Panel de Control General</p>
        </div>
    </div>

    {{-- Métricas Principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Solicitudes --}}
        <div class="bg-white border border-slate-200 border-l-4 border-l-blue-500 rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-slate-500 text-sm mb-1">
                            <i class="fas fa-file-alt text-blue-600"></i> Total Solicitudes
                        </div>
                        <h2 class="text-2xl font-bold">{{ number_format($totalSolicitudes) }}</h2>
                        @if($tendenciaSolicitudes != 0)
                            <small class="block mt-1 {{ $tendenciaSolicitudes > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                <i class="fas fa-arrow-{{ $tendenciaSolicitudes > 0 ? 'up' : 'down' }}"></i>
                                {{ abs($tendenciaSolicitudes) }}% vs mes anterior
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendientes de Revisar --}}
        <div class="bg-white border border-slate-200 border-l-4 border-l-amber-500 rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-slate-500 text-sm mb-1">
                            <i class="fas fa-clock text-amber-500"></i> Pendientes de Revisar
                        </div>
                        <h2 class="text-2xl font-bold">{{ number_format($solicitadas) }}</h2>
                        <small class="text-slate-500">Requieren atención inmediata</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estudiantes Activos --}}
        <div class="bg-white border border-slate-200 border-l-4 border-l-emerald-500 rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-slate-500 text-sm mb-1">
                            <i class="fas fa-users text-emerald-600"></i> Estudiantes Activos
                        </div>
                        <h2 class="text-2xl font-bold">{{ number_format($estudiantesActivos) }}</h2>
                        @if($tendenciaEstudiantes != 0)
                            <small class="block mt-1 {{ $tendenciaEstudiantes > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                <i class="fas fa-arrow-{{ $tendenciaEstudiantes > 0 ? 'up' : 'down' }}"></i>
                                {{ abs($tendenciaEstudiantes) }}% este mes
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Supervisores Activos --}}
        <div class="bg-white border border-slate-200 border-l-4 border-l-cyan-500 rounded-xl shadow-sm hover:shadow-md transition">
            <div class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-slate-500 text-sm mb-1">
                            <i class="fas fa-user-check text-cyan-600"></i> Supervisores Activos
                        </div>
                        <h2 class="text-2xl font-bold">{{ number_format($supervisoresActivos) }}</h2>
                        <small class="text-slate-500">Capacidad: {{ number_format($capacidadSupervisores, 1) }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribución por Estado (cards) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white border border-slate-200 border-l-4 border-l-amber-500 rounded-xl">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <small class="block text-slate-500">Solicitadas</small>
                        <h4 class="text-xl font-bold">{{ $solicitadas }}</h4>
                    </div>
                    <i class="fas fa-clock text-amber-500/60 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 border-l-4 border-l-emerald-500 rounded-xl">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <small class="block text-slate-500">Aprobadas</small>
                        <h4 class="text-xl font-bold">{{ $aprobadas }}</h4>
                    </div>
                    <i class="fas fa-check-circle text-emerald-600/60 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 border-l-4 border-l-indigo-500 rounded-xl">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <small class="block text-slate-500">Finalizadas</small>
                        <h4 class="text-xl font-bold">{{ $finalizadas }}</h4>
                    </div>
                    <i class="fas fa-flag-checkered text-indigo-600/60 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 border-l-4 border-l-rose-500 rounded-xl">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <small class="block text-slate-500">Rechazadas</small>
                        <h4 class="text-xl font-bold">{{ $rechazadas }}</h4>
                    </div>
                    <i class="fas fa-times-circle text-rose-600/60 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 border-l-4 border-l-slate-400 rounded-xl">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <small class="block text-slate-500">Canceladas</small>
                        <h4 class="text-xl font-bold">{{ $canceladas }}</h4>
                    </div>
                    <i class="fas fa-ban text-slate-500/60 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 border-l-4 border-l-cyan-500 rounded-xl">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <small class="block text-slate-500">Docs Pendientes</small>
                        <h4 class="text-xl font-bold">{{ $documentosPendientes }}</h4>
                    </div>
                    <i class="fas fa-file-upload text-cyan-600/60 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- Tendencia de Solicitudes --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl">
            <div class="px-4 py-3 border-b border-slate-200">
                <h5 class="font-semibold text-slate-700"><i class="fas fa-chart-line text-blue-600"></i> Tendencia de Solicitudes (Últimos 6 meses)</h5>
            </div>
            <div class="p-4">
                <div class="relative h-80">
                    <canvas id="chartTendencia" class="absolute inset-0"></canvas>
                </div>
            </div>
        </div>

        {{-- Distribución por Estado --}}
        <div class="bg-white border border-slate-200 rounded-xl">
            <div class="px-4 py-3 border-b border-slate-200">
                <h5 class="font-semibold text-slate-700"><i class="fas fa-chart-pie text-emerald-600"></i> Distribución por Estado</h5>
            </div>
            <div class="p-4">
                <div class="relative h-80">
                    <canvas id="chartDistribucion" class="absolute inset-0"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Solicitudes por Mes del Año Actual --}}
    <div class="mb-6">
        <div class="bg-white border border-slate-200 rounded-xl">
            <div class="px-4 py-3 border-b border-slate-200">
                <h5 class="font-semibold text-slate-700"><i class="fas fa-calendar-alt text-cyan-600"></i> Solicitudes por Mes - {{ date('Y') }}</h5>
            </div>
            <div class="p-4">
                <div class="relative h-72">
                    <canvas id="chartMesAnioActual" class="absolute inset-0"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Supervisores Más Activos --}}
    <div class="mb-6">
        <div class="bg-white border border-slate-200 rounded-xl">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                <h5 class="font-semibold text-slate-700"><i class="fas fa-user-tie text-purple-600"></i> Supervisores Más Activos</h5>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">Supervisor</th>
                                <th class="px-4 py-2 text-center font-semibold">Estudiantes</th>
                                <th class="px-4 py-2 text-center font-semibold">Capacidad</th>
                                <th class="px-4 py-2 text-left font-semibold">Ocupación</th>
                                <th class="px-4 py-2 text-center font-semibold">Eficiencia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($supervisoresMasActivos as $supervisor)
                            @php
                                $barClass = $supervisor['ocupacion'] >= 90 ? 'bg-rose-500' : ($supervisor['ocupacion'] >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $supervisor['nombre'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $supervisor['estudiantes'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $supervisor['capacidad'] }}</td>
                                <td class="px-4 py-3 w-1/3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-2 {{ $barClass }} rounded-full transition-all duration-500"
                                                 style="width: {{ $supervisor['ocupacion'] }}%"></div>
                                        </div>
                                        <span class="text-slate-500 text-xs">{{ number_format($supervisor['ocupacion'], 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        {{ number_format($supervisor['eficiencia'], 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    No hay supervisores activos registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Solicitudes Recientes --}}
    <div class="mb-6">
        <div class="bg-white border border-slate-200 rounded-xl">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                <h5 class="font-semibold text-slate-700"><i class="fas fa-list text-blue-600"></i> Solicitudes Recientes</h5>
                <a href="{{ route('admin.solicitudes.pendientes') }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 text-sm">
                    Ver todas <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">ID</th>
                                <th class="px-4 py-2 text-left font-semibold">Estudiante</th>
                                <th class="px-4 py-2 text-left font-semibold">Cuenta</th>
                                <th class="px-4 py-2 text-left font-semibold">Estado</th>
                                <th class="px-4 py-2 text-left font-semibold">Fecha</th>
                                <th class="px-4 py-2 text-left font-semibold">Prioridad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($solicitudesRecientes as $solicitud)
                            @php
                                $estadoClass = match($solicitud['estado']) {
                                    'SOLICITADA' => 'bg-amber-100 text-amber-800',
                                    'APROBADA' => 'bg-emerald-100 text-emerald-700',
                                    'FINALIZADA' => 'bg-indigo-100 text-indigo-700',
                                    'RECHAZADA' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-slate-200 text-slate-700',
                                };
                                $prioClass = match($solicitud['prioridad']) {
                                    'alta' => 'bg-rose-100 text-rose-700',
                                    'media' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-emerald-100 text-emerald-700',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $solicitud['codigo'] }}</td>
                                <td class="px-4 py-3">{{ $solicitud['estudiante'] }}</td>
                                <td class="px-4 py-3"><small class="text-slate-500">{{ $solicitud['cuenta'] }}</small></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $estadoClass }}">
                                        {{ $solicitud['estado'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3"><small>{{ $solicitud['fecha'] }}</small></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $prioClass }}">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ ucfirst($solicitud['prioridad']) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                    No hay solicitudes recientes
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Atajos Rápidos --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.solicitudes.pendientes') }}" class="group">
            <div class="bg-white border border-blue-200 rounded-xl h-full shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                <div class="p-6 text-center">
                    <i class="fas fa-file-alt text-3xl text-blue-600 mb-3"></i>
                    <h5 class="font-semibold text-slate-800">Gestionar Solicitudes</h5>
                    <p class="text-slate-500">Revisar y aprobar solicitudes pendientes</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.supervisores.index') }}" class="group">
            <div class="bg-white border border-emerald-200 rounded-xl h-full shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                <div class="p-6 text-center">
                    <i class="fas fa-user-check text-3xl text-emerald-600 mb-3"></i>
                    <h5 class="font-semibold text-slate-800">Gestionar Supervisores</h5>
                    <p class="text-slate-500">Administrar supervisores y asignaciones</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.reportes') }}" class="group">
            <div class="bg-white border border-cyan-200 rounded-xl h-full shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                <div class="p-6 text-center">
                    <i class="fas fa-chart-bar text-3xl text-cyan-600 mb-3"></i>
                    <h5 class="font-semibold text-slate-800">Generar Reportes</h5>
                    <p class="text-slate-500">Exportar reportes y estadísticas</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tendencia
    const ctxTendencia = document.getElementById('chartTendencia').getContext('2d');
    new Chart(ctxTendencia, {
        type: 'line',
        data: {
            labels: {!! json_encode($solicitudesPorMes->pluck('mes')) !!},
            datasets: [
                {
                    label: 'Total Solicitudes',
                    data: {!! json_encode($solicitudesPorMes->pluck('solicitudes')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.12)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Aprobadas',
                    data: {!! json_encode($solicitudesPorMes->pluck('aprobadas')) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Rechazadas',
                    data: {!! json_encode($solicitudesPorMes->pluck('rechazadas')) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.12)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 5 } }
            }
        }
    });

    // Distribución
    const ctxDistribucion = document.getElementById('chartDistribucion').getContext('2d');
    new Chart(ctxDistribucion, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($distribucionEstados->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($distribucionEstados->pluck('value')) !!},
                backgroundColor: {!! json_encode($distribucionEstados->pluck('color')) !!},
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    // Por mes del año actual
    const ctxMesAnioActual = document.getElementById('chartMesAnioActual').getContext('2d');
    new Chart(ctxMesAnioActual, {
        type: 'bar',
        data: {
            labels: {!! json_encode($solicitudesPorMesAnioActual->pluck('mes')) !!},
            datasets: [
                {
                    label: 'Total',
                    data: {!! json_encode($solicitudesPorMesAnioActual->pluck('total')) !!},
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                },
                {
                    label: 'Aprobadas',
                    data: {!! json_encode($solicitudesPorMesAnioActual->pluck('aprobadas')) !!},
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y}` } }
            },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 5 } } }
        }
    });
});
</script>
@endpush