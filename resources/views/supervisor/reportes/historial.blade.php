@extends('layouts.supervisores')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-unahblue">Historial de Supervisiones</h1>
                <p class="text-slate-600 mt-2">Listado detallado de supervisiones realizadas</p>
            </div>

            <a href="{{ route('supervisor.reportes.index', request()->query()) }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L6.414 8H16a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Volver a reportes
            </a>
        </div>

        {{-- FILTROS --}}
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="mb-6 pb-6 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Filtrar Historial</h2>
                <p class="text-slate-600 text-sm mt-1">Busca por año, estudiante o número de supervisión</p>
            </div>

            <form id="formHistorial" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4">

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

                    {{-- Buscar estudiante --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Buscar Estudiante</label>
                        <input id="buscarEstudiante" type="text" name="estudiante" placeholder="Nombre o correo..."
                               value="{{ $filtros['estudiante'] ?? '' }}"
                               class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Número de supervisión --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2"># Supervisión</label>
                        <select id="filtroNumero" name="numero"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" {{ empty($filtros['numero']) ? 'selected' : '' }}>Todas</option>
                            <option value="1" {{ ($filtros['numero'] ?? '') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($filtros['numero'] ?? '') == '2' ? 'selected' : '' }}>2</option>
                        </select>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                            class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        Buscar
                    </button>

                    <a href="{{ route('supervisor.reportes.historial') }}"
                       class="flex items-center justify-center gap-2 px-4 py-2 bg-slate-300 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-400 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- TABLA HISTORIAL --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">

            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Estudiante</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Empresa</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-700">Número de supervisión</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Comentario</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-700">Archivo</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($historial as $sup)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-slate-900 text-sm">{{ $sup->solicitud->user->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-500">{{ $sup->solicitud->user->email ?? '-' }}</p>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ $sup->solicitud->nombre_empresa ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded text-slate-700 text-xs font-semibold">
                                        {{ $sup->numero_supervision ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-700">
                                    <span class="text-xs">{{ optional($sup->created_at)->format('d/m/Y') ?? '-' }}</span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-700">
                                    <span class="text-xs">{{ \Illuminate\Support\Str::limit($sup->comentario ?? '', 80) }}</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @php
                                        $tieneArchivo = !empty($sup->archivo) || !empty($sup->ruta_archivo);
                                    @endphp

                                    @if($tieneArchivo)
                                        <a href="{{ route('supervisor.alumnos.supervision.descargar', $sup->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-slate-900 text-white text-xs rounded hover:bg-slate-800 transition">
                                            Descargar
                                        </a>
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
                                    <p class="text-slate-500 text-sm">No hay supervisiones con los filtros actuales</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="sm:hidden divide-y divide-slate-200">
                @forelse($historial as $sup)
                    <div class="p-4 space-y-3">
                        <div>
                            <p class="font-medium text-slate-900 text-sm">{{ $sup->solicitud->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $sup->solicitud->user->email ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-slate-600 font-medium">Empresa</p>
                                <p class="text-slate-700">{{ $sup->solicitud->nombre_empresa ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-600 font-medium">Numero Supervision</p>
                                <span class="inline-block px-2 py-1 text-indigo-700 text-xs font-semibold rounded">
                                    {{ $sup->numero_supervision ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-slate-600 font-medium">Fecha</p>
                                <p class="text-slate-700">{{ optional($sup->created_at)->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-600 font-medium">Archivo</p>
                                @php
                                    $tieneArchivo = !empty($sup->archivo) || !empty($sup->ruta_archivo);
                                @endphp
                                @if($tieneArchivo)
                                    <a href="{{ route('supervisor.alumnos.supervision.descargar', $sup->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-slate-900 text-white text-xs rounded hover:bg-slate-800 transition">
                                        Descargar
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200">
                            <p class="text-slate-600 font-medium text-xs mb-1">Comentario</p>
                            <p class="text-xs text-slate-700">{{ $sup->comentario ?? '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-slate-500 text-sm">No hay supervisiones con los filtros actuales</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINACIÓN --}}
            @if ($historial->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $historial->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    const form  = document.getElementById('formHistorial');
    const input = document.getElementById('buscarEstudiante');
    const anio  = document.getElementById('filtroAnio');
    const numero = document.getElementById('filtroNumero');

    if (!form) return;

    const KEY = 'supervisor_historial_ui';

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

    if (anio) anio.addEventListener('change', () => softSubmit());
    if (numero) numero.addEventListener('change', () => softSubmit());

    if (input) {
        let t = null;
        input.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => {
                const v = input.value.trim();

                // Si queda vacío, quitamos "estudiante" del query
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
