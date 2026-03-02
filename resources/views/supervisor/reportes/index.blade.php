@extends('layouts.supervisores')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-unahblue">Reportes y Estadísticas</h1>
            <p class="text-slate-600 mt-2">Análisis de tus supervisiones finalizadas</p>
        </div>

        {{-- CARDS (sin barra horizontal) --}}
        {{-- En desktop: 5 en una fila. En pantallas pequeñas: 1-2-3 según el tamaño --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
            {{-- Card 1 --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-emerald-500">
                <p class="text-slate-600 text-sm font-medium">Prácticas Finalizadas</p>
                <p class="text-3xl sm:text-4xl font-bold text-slate-900 mt-3">
                    {{ $estadisticas['total_finalizadas'] ?? 0 }}
                </p>
            </div>

            {{-- Card 2 --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <p class="text-slate-600 text-sm font-medium">Prácticas Supervisadas (2/2)</p>
                <p class="text-3xl sm:text-4xl font-bold text-slate-900 mt-3">
                    {{ $estadisticas['total_supervisadas'] ?? 0 }}
                </p>
            </div>

            {{-- Card 3 --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-slate-600 text-sm font-medium">Supervisiones Realizadas</p>
                <p class="text-3xl sm:text-4xl font-bold text-slate-900 mt-3">
                    {{ $estadisticas['total_supervisiones'] ?? 0 }}
                </p>
            </div>

            {{-- Card 4 --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-slate-600 text-sm font-medium">Duración Promedio</p>
                <p class="text-3xl sm:text-4xl font-bold text-slate-900 mt-3">
                    {{ $estadisticas['promedio_duracion'] ?? 0 }} <span class="text-lg">días</span>
                </p>
            </div>

            {{-- Card 5 --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-amber-500">
                <p class="text-slate-600 text-sm font-medium">Estudiantes Activos</p>
                <p class="text-3xl sm:text-4xl font-bold text-slate-900 mt-3">
                    {{ $estadisticas['estudiantes_activos'] ?? 0 }}
                </p>
            </div>
        </div>

        {{-- SECCIÓN DE FILTROS Y EXPORTACIÓN --}}
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 pb-6 border-b border-slate-200">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Filtrar Reportes</h2>
                    <p class="text-slate-600 text-sm mt-1">Personaliza los datos según tus necesidades</p>
                </div>
            </div>
            
            <form id="formReportes" method="GET" class="space-y-4">
                {{-- Filtros Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4">
                    
                    {{-- Año --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Año</label>
                        <select id="filtroAnio" name="año"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @for ($i = now()->year; $i >= now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ ($filtros['año'] ?? now()->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Búsqueda por nombre o correo --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Buscar Estudiante</label>
                        <input id="buscarEstudiante" type="text" name="estudiante" placeholder="Nombre o correo..." 
                               value="{{ $filtros['estudiante'] ?? '' }}"
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Solo supervisadas --}}
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input id="chkSupervisadas" type="checkbox" name="supervisadas" value="1"
                                   {{ !empty($filtros['supervisadas']) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Solo supervisadas (2/2)
                        </label>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        Buscar
                    </button>

                    <a href="{{ route('supervisor.reportes.index') }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-slate-300 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-400 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Limpiar
                    </a>

                    {{-- Ver historial --}}
                    <a href="{{ route('supervisor.reportes.historial', request()->query()) }}"
                       class="flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800 transition">
                        Ver historial de supervisiones
                    </a>

                    <a href="{{ route('supervisor.reportes.excel', request()->query()) }}" 
                       class="flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-medium hover:bg-emerald-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        Excel
                    </a>

                    <a href="{{ route('supervisor.reportes.pdf', request()->query()) }}" 
                       class="flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4z"/>
                        </svg>
                        PDF
                    </a>
                </div>
            </form>
        </div>

        {{-- TABLA DE RESULTADOS --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            {{-- Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Estudiante</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Empresa</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Puesto</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Período</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-700">Supervisiones</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Finalizado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($solicitudes as $solicitud)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-slate-900 text-sm">{{ $solicitud->user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $solicitud->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $solicitud->nombre_empresa }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $solicitud->puesto_trabajo }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    @if($solicitud->fecha_inicio && $solicitud->fecha_fin)
                                        <span class="text-xs">{{ $solicitud->fecha_inicio->format('d/m/Y') }} - {{ $solicitud->fecha_fin->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">
                                        {{ $solicitud->supervisiones_count ?? 0 }}/2
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($solicitud->updated_at)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs">{{ $solicitud->updated_at->format('d/m/Y') }}</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-slate-500 text-sm">No hay reportes disponibles</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="sm:hidden divide-y divide-slate-200">
                @forelse ($solicitudes as $solicitud)
                    <div class="p-4 space-y-3">
                        <div>
                            <p class="font-medium text-slate-900 text-sm">{{ $solicitud->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $solicitud->user->email }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-slate-600 font-medium">Empresa</p>
                                <p class="text-slate-700">{{ $solicitud->nombre_empresa }}</p>
                            </div>
                            <div>
                                <p class="text-slate-600 font-medium">Puesto</p>
                                <p class="text-slate-700">{{ $solicitud->puesto_trabajo }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-slate-600 font-medium">Período</p>
                                @if($solicitud->fecha_inicio && $solicitud->fecha_fin)
                                    <p class="text-slate-700">{{ $solicitud->fecha_inicio->format('d/m/Y') }} - {{ $solicitud->fecha_fin->format('d/m/Y') }}</p>
                                @else
                                    <p class="text-slate-400">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-slate-600 font-medium">Supervisiones</p>
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">
                                    {{ $solicitud->supervisiones_count ?? 0 }}/2
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                            <p class="text-slate-600 font-medium text-xs">Finalizado</p>
                            @if($solicitud->updated_at)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs">{{ $solicitud->updated_at->format('d/m/Y') }}</span>
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-slate-500 text-sm">No hay reportes disponibles</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINACIÓN --}}
            @if ($solicitudes->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $solicitudes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
(function () {
    const form  = document.getElementById('formReportes');
    const input = document.getElementById('buscarEstudiante');
    const anio  = document.getElementById('filtroAnio');
    const chk   = document.getElementById('chkSupervisadas');

    if (!form) return;

    const KEY = 'supervisor_reportes_ui';

    function buildUrlFromForm() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(new FormData(form));
        params.delete('page'); // reset paginación
        url.search = params.toString();
        return url.toString();
    }

    function saveUIState() {
        if (!input) return;
        const state = {
            y: window.scrollY || 0,
            focus: (document.activeElement === input),
            value: input.value || '',
            start: input.selectionStart ?? null,
            end: input.selectionEnd ?? null,
        };
        sessionStorage.setItem(KEY, JSON.stringify(state));
    }

    function restoreUIState() {
        const raw = sessionStorage.getItem(KEY);
        if (!raw) return;
        sessionStorage.removeItem(KEY);

        try {
            const state = JSON.parse(raw);
            requestAnimationFrame(() => {
                window.scrollTo({ top: state.y || 0, left: 0, behavior: 'instant' });
                if (input && state.focus) {
                    input.focus();
                    if (typeof state.start === 'number' && typeof state.end === 'number') {
                        input.setSelectionRange(state.start, state.end);
                    }
                }
            });
        } catch (e) {}
    }

    restoreUIState();

    function softNavigate(urlStr) {
        saveUIState();
        window.location.href = urlStr;
    }

    function softSubmit() {
        softNavigate(buildUrlFromForm());
    }

    // Año / checkbox: inmediato
    if (anio) anio.addEventListener('change', () => softSubmit());
    if (chk) chk.addEventListener('change', () => softSubmit());

    // Buscar: debounce + limpiar si queda vacío
    if (input) {
        let t = null;
        input.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => {
                const v = input.value.trim();

                if (v === '') {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('estudiante');
                    url.searchParams.delete('page');
                    softNavigate(url.toString());
                    return;
                }

                softSubmit();
            }, 450);
        });
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        softSubmit();
    });

})();
</script>
@endsection
