@extends('layouts.admin')

@section('title', 'Log de Auditoría')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-unahblue">Log de Auditoría</h1>
        <span class="text-sm text-slate-500">Total: {{ $logs->total() }} registros</span>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.audit.index') }}"
          class="grid gap-4 md:grid-cols-5 bg-white border border-slate-200 rounded-xl p-4 mb-6">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Acción</label>
            <select name="accion" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">Todas</option>
                @foreach($acciones as $accion)
                    <option value="{{ $accion }}" {{ request('accion') === $accion ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', ucfirst($accion)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Usuario</label>
            <input type="text" name="usuario" value="{{ request('usuario') }}"
                   placeholder="Nombre del usuario..."
                   class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold w-full">
                Filtrar
            </button>
            @if(request('accion') || request('usuario') || request('desde') || request('hasta'))
                <a href="{{ route('admin.audit.index') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-semibold whitespace-nowrap">
                    Limpiar
                </a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Fecha</th>
                        <th class="px-4 py-3 text-left font-semibold">Usuario</th>
                        <th class="px-4 py-3 text-left font-semibold">Rol</th>
                        <th class="px-4 py-3 text-left font-semibold">Acción</th>
                        <th class="px-4 py-3 text-left font-semibold">Descripción</th>
                        <th class="px-4 py-3 text-left font-semibold">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-500 whitespace-nowrap text-xs">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-800">
                            {{ $log->user_nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $rolColors = [
                                    'admin'       => 'bg-purple-100 text-purple-700',
                                    'vinculacion' => 'bg-blue-100 text-blue-700',
                                    'supervisor'  => 'bg-emerald-100 text-emerald-700',
                                    'estudiante'  => 'bg-amber-100 text-amber-700',
                                ];
                                $cls = $rolColors[$log->user_rol] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $cls }}">
                                {{ ucfirst($log->user_rol ?? '—') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $accionColors = [
                                    'aprobar_solicitud'  => 'bg-emerald-100 text-emerald-700',
                                    'rechazar_solicitud' => 'bg-rose-100 text-rose-700',
                                    'finalizar_solicitud'=> 'bg-indigo-100 text-indigo-700',
                                    'cancelar_solicitud' => 'bg-slate-200 text-slate-600',
                                    'cambiar_supervisor' => 'bg-orange-100 text-orange-700',
                                    'cambiar_rol_usuario'=> 'bg-yellow-100 text-yellow-700',
                                    'subir_supervision'  => 'bg-cyan-100 text-cyan-700',
                                ];
                                $cls2 = $accionColors[$log->accion] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $cls2 }}">
                                {{ str_replace('_', ' ', $log->accion) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700 max-w-sm">
                            {{ $log->descripcion }}
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs font-mono">
                            {{ $log->ip ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                            No hay registros de auditoría.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
