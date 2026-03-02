@php
    use Carbon\Carbon;
@endphp

<table class="min-w-full bg-white border border-gray-300">
    <thead>
        <tr class="bg-gray-100 text-left">
            <th class="py-2 px-4 border-b">ID</th>
            <th class="py-2 px-4 border-b">Estudiante</th>
            <th class="py-2 px-4 border-b">Tipo de Práctica</th>
            <th class="py-2 px-4 border-b">Estado</th>
            <th class="py-2 px-4 border-b">Fecha</th>
            <th class="py-2 px-4 border-b">Motivo</th>
            <th class="py-2 px-4 border-b text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($solicitudes as $solicitud)
            <tr>
                <td class="py-2 px-4 border-b">{{ $solicitud->id }}</td>
                <td class="py-2 px-4 border-b">{{ $solicitud->user->name }}</td>
                <td class="py-2 px-4 border-b capitalize">{{ $solicitud->tipo_practica }}</td>
                <td class="py-2 px-4 border-b">
                    <span class="inline-block px-2 py-1 text-xs rounded font-semibold
                        @switch($solicitud->estado_solicitud)
                            @case('SOLICITADA') bg-yellow-200 text-yellow-800 @break
                            @case('RECHAZADA') bg-red-200 text-red-800 @break
                            @case('APROBADA') bg-green-200 text-green-800 @break
                            @case('CANCELADA') bg-gray-300 text-gray-700 @break
                            @case('FINALIZADA') bg-blue-200 text-blue-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">
                        {{ $solicitud->estado_solicitud }}
                    </span>
                </td>
                <td class="py-2 px-4 border-b">{{ Carbon::parse($solicitud->created_at)->format('d/m/Y') }}</td>

                <td class="py-2 px-4 border-b text-sm text-gray-700">
                    @if($solicitud->estado_solicitud === 'RECHAZADA')
                        {{ $solicitud->observacion ?? '—' }}
                    @elseif($solicitud->estado_solicitud === 'CANCELADA')
                        {{ $solicitud->motivo_cancelacion ?? '—' }}
                    @else
                        —
                    @endif
                </td>

                <td class="py-2 px-4 border-b text-center space-x-1">
                    @if (in_array('aprobar', $acciones))
                        <form method="POST" action="{{ route('admin.solicitudes.aprobar', $solicitud->id) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white text-sm px-2 py-1 rounded hover:bg-green-600">Aprobar</button>
                        </form>
                    @endif

                    @if (in_array('rechazar', $acciones))
                        <button onclick="abrirModalRechazo({{ $solicitud->id }})"
                            class="bg-red-500 text-white text-sm px-2 py-1 rounded hover:bg-red-600">
                            Rechazar
                        </button>
                    @endif

                    @if (in_array('cancelar', $acciones))
                        <button onclick="abrirModalCancelar({{ $solicitud->id }})"
                            class="bg-yellow-600 text-white text-sm px-2 py-1 rounded hover:bg-yellow-700">
                            Cancelar
                        </button>
                    @endif

                    @if (in_array('finalizar', $acciones))
                        <form method="POST" action="{{ route('admin.solicitudes.finalizar', $solicitud->id) }}">
                            @csrf
                            <button type="submit" class="bg-blue-600 text-white text-sm px-2 py-1 rounded hover:bg-blue-700">Finalizar</button>
                        </form>
                    @endif

                    @if (in_array('ver_documentos', $acciones))
                        <a href="{{ route('admin.solicitudes.documentos', $solicitud->id) }}"
                           class="bg-indigo-500 text-white text-sm px-2 py-1 rounded hover:bg-indigo-600">
                            Ver documentos
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-gray-500">No hay solicitudes encontradas.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Modal de Rechazo -->
<div id="modal-rechazo" class="fixed z-50 top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
        <h2 class="text-xl font-bold text-red-600 mb-4">Rechazar Solicitud</h2>

        <form method="POST" id="form-rechazo" action="">
            @csrf
            @method('PUT')
            <label for="observacion" class="block mb-2 font-medium text-sm">Motivo del rechazo:</label>
            <textarea name="observacion" id="observacion" rows="4" required
                class="w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:ring focus:border-red-400"></textarea>

            <div class="flex justify-end mt-4 space-x-2">
                <button type="button" onclick="cerrarModalRechazo()"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Rechazar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Cancelación -->
<div id="modal-cancelar" class="fixed z-50 top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
        <h2 class="text-xl font-bold text-yellow-600 mb-4">Cancelar Solicitud</h2>

        <form method="POST" id="form-cancelar" action="">
            @csrf
            <label for="motivo_cancelacion" class="block mb-2 font-medium text-sm">Motivo de cancelación:</label>
            <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="4" required
                class="w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:ring focus:border-yellow-400"></textarea>

            <div class="flex justify-end mt-4 space-x-2">
                <button type="button" onclick="cerrarModalCancelar()"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalRechazo(id) {
        const form = document.getElementById('form-rechazo');
        form.action = `/admin/solicitudes/${id}/rechazar`;
        document.getElementById('modal-rechazo').classList.remove('hidden');
    }

    function cerrarModalRechazo() {
        document.getElementById('modal-rechazo').classList.add('hidden');
        document.getElementById('observacion').value = '';
    }

    function abrirModalCancelar(id) {
        const form = document.getElementById('form-cancelar');
        form.action = `/admin/solicitudes/${id}/cancelar`;
        document.getElementById('modal-cancelar').classList.remove('hidden');
    }

    function cerrarModalCancelar() {
        document.getElementById('modal-cancelar').classList.add('hidden');
        document.getElementById('motivo_cancelacion').value = '';
    }
</script>
