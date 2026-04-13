@extends('layouts.admin')

@section('title', 'Gestión de Empresas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-unahblue">Gestión de Empresas</h1>
        <button onclick="document.getElementById('modalCrear').classList.remove('hidden')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Empresa
        </button>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">{{ session('error') }}</div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.empresas.index') }}"
          class="grid gap-4 md:grid-cols-4 bg-white border border-slate-200 rounded-xl p-4 mb-6">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Buscar</label>
            <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                   placeholder="Nombre de empresa..."
                   class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Tipo</label>
            <select name="tipo" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">Todos</option>
                <option value="publica"  {{ request('tipo') === 'publica'  ? 'selected' : '' }}>Pública</option>
                <option value="privada"  {{ request('tipo') === 'privada'  ? 'selected' : '' }}>Privada</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Estado</label>
            <select name="estado" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">Todos</option>
                <option value="activa"    {{ request('estado') === 'activa'    ? 'selected' : '' }}>Activa</option>
                <option value="inactiva"  {{ request('estado') === 'inactiva'  ? 'selected' : '' }}>Inactiva</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold w-full">Buscar</button>
            @if(request('busqueda') || request('tipo') || request('estado'))
                <a href="{{ route('admin.empresas.index') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-semibold whitespace-nowrap">Limpiar</a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-700 text-sm">{{ $empresas->total() }} empresas registradas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nombre</th>
                        <th class="px-4 py-3 text-left font-semibold">Tipo</th>
                        <th class="px-4 py-3 text-left font-semibold">Dirección</th>
                        <th class="px-4 py-3 text-center font-semibold">Prácticas</th>
                        <th class="px-4 py-3 text-center font-semibold">Estado</th>
                        <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($empresas as $empresa)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $empresa->nombre }}</td>
                        <td class="px-4 py-3">
                            @if($empresa->tipo === 'publica')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Pública</span>
                            @elseif($empresa->tipo === 'privada')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Privada</span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600 max-w-xs truncate">{{ $empresa->direccion ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                {{ $empresa->total_estudiantes }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($empresa->activa)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Activa</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-600">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Editar --}}
                                <button onclick="abrirEditar({{ $empresa->id }}, '{{ addslashes($empresa->nombre) }}', '{{ $empresa->tipo }}', '{{ addslashes($empresa->direccion ?? '') }}')"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- Toggle activa --}}
                                <form method="POST" action="{{ route('admin.empresas.toggle', $empresa) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-1.5 {{ $empresa->activa ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition"
                                            title="{{ $empresa->activa ? 'Desactivar' : 'Activar' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $empresa->activa ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                        </svg>
                                    </button>
                                </form>

                                {{-- Eliminar --}}
                                @if($empresa->total_estudiantes === 0)
                                <form method="POST" action="{{ route('admin.empresas.destroy', $empresa) }}"
                                      onsubmit="return confirm('¿Eliminar la empresa {{ addslashes($empresa->nombre) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">No hay empresas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $empresas->links() }}
        </div>
    </div>
</div>

{{-- Modal Crear --}}
<div id="modalCrear" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-bold text-slate-800">Nueva Empresa</h3>
            <button onclick="document.getElementById('modalCrear').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.empresas.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" required maxlength="255"
                       class="w-full rounded-lg border-slate-300 text-sm" placeholder="Nombre de la empresa">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
                <select name="tipo" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Sin especificar</option>
                    <option value="publica">Pública</option>
                    <option value="privada">Privada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Dirección</label>
                <textarea name="direccion" rows="2" maxlength="500"
                          class="w-full rounded-lg border-slate-300 text-sm" placeholder="Dirección (opcional)"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalCrear').classList.add('hidden')"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-600">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Crear</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Editar --}}
<div id="modalEditar" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-bold text-slate-800">Editar Empresa</h3>
            <button onclick="document.getElementById('modalEditar').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="formEditar" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre *</label>
                <input type="text" id="editNombre" name="nombre" required maxlength="255"
                       class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
                <select id="editTipo" name="tipo" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Sin especificar</option>
                    <option value="publica">Pública</option>
                    <option value="privada">Privada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Dirección</label>
                <textarea id="editDireccion" name="direccion" rows="2" maxlength="500"
                          class="w-full rounded-lg border-slate-300 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEditar').classList.add('hidden')"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-600">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Guardar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function abrirEditar(id, nombre, tipo, direccion) {
    document.getElementById('editNombre').value    = nombre;
    document.getElementById('editTipo').value      = tipo || '';
    document.getElementById('editDireccion').value = direccion;
    document.getElementById('formEditar').action   = `/admin/empresas/${id}`;
    document.getElementById('modalEditar').classList.remove('hidden');
}
</script>
@endpush
@endsection
