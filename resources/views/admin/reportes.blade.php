@extends('layouts.admin')

@section('title','Módulo de Reportes')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-3xl sm:text-4xl font-bold text-unahblue pb-4">Reportes y Estadísticas</h1>

    <form id="formFiltros" class="grid gap-4 md:grid-cols-4 bg-white border border-slate-200 rounded-xl p-4 mb-6">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Estado</label>
            <select name="estado" class="w-full rounded-lg border-slate-300">
                <option value="">Todos</option>
                <option>SOLICITADA</option>
                <option>APROBADA</option>
                <option>RECHAZADA</option>
                <option>FINALIZADA</option>
                <option>CANCELADA</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Supervisor</label>
            <select name="supervisor" class="w-full rounded-lg border-slate-300">
                <option value="">Todos</option>
                @foreach($supervisores as $s)
                    <option value="{{ $s->id }}">{{ strtoupper($s->name) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Desde</label>
            <input type="date" name="desde" class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Hasta</label>
            <input type="date" name="hasta" class="w-full rounded-lg border-slate-300">
        </div>
        <div class="md:col-span-4 flex gap-2 pt-2">
            <button type="button" id="btnPreview" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">Previsualizar</button>
            <button type="button" id="btnLimpiar" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold">Limpiar</button>
        </div>
    </form>

    <div class="grid gap-4 md:grid-cols-5 mb-6" id="resumenContenedor" hidden>
        <div class="bg-white border border-slate-200 rounded-xl p-4"><p class="text-xs text-slate-500">Total</p><h3 class="text-xl font-bold" id="rTotal">0</h3></div>
        <div class="bg-white border border-slate-200 rounded-xl p-4"><p class="text-xs text-slate-500">Aprobadas</p><h3 class="text-xl font-bold text-emerald-600" id="rAprobadas">0</h3></div>
        <div class="bg-white border border-slate-200 rounded-xl p-4"><p class="text-xs text-slate-500">Rechazadas</p><h3 class="text-xl font-bold text-rose-600" id="rRechazadas">0</h3></div>
        <div class="bg-white border border-slate-200 rounded-xl p-4"><p class="text-xs text-slate-500">Finalizadas</p><h3 class="text-xl font-bold text-indigo-600" id="rFinalizadas">0</h3></div>
        <div class="bg-white border border-slate-200 rounded-xl p-4"><p class="text-xs text-slate-500">Solicitadas</p><h3 class="text-xl font-bold text-amber-600" id="rSolicitadas">0</h3></div>
    </div>

    <div class="flex flex-wrap gap-3 mb-4" id="exportBtns" hidden>
        <a id="linkPdf" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold">
            PDF
        </a>
        <a id="linkExcel" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold">
            Excel
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200">
            <h2 class="font-semibold text-slate-700 text-sm">Resultado (máx 200 filas)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm" id="tablaPreview">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">ID</th>
                        <th class="px-3 py-2 text-left font-semibold">Cuenta</th>
                        <th class="px-3 py-2 text-left font-semibold">Estado</th>
                        <th class="px-3 py-2 text-left font-semibold">Tipo</th>
                        <th class="px-3 py-2 text-left font-semibold">Supervisor</th>
                        <th class="px-3 py-2 text-left font-semibold">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const form = document.getElementById('formFiltros');
const btnPrev = document.getElementById('btnPreview');
const btnClear = document.getElementById('btnLimpiar');
const resumenBox = document.getElementById('resumenContenedor');
const exportBtns = document.getElementById('exportBtns');
const tbody = document.querySelector('#tablaPreview tbody');

btnPrev.addEventListener('click', async () => {
    const fd = new FormData(form);
    const params = new URLSearchParams(fd);
    const res = await fetch("{{ route('admin.reportes.preview') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: fd
    });
    if (!res.ok) { alert('Error'); return; }
    const json = await res.json();
    pintarResumen(json.resumen);
    pintarTabla(json.data);
    actualizarLinks(params.toString());
});

btnClear.addEventListener('click', () => {
    form.reset();
    resumenBox.hidden = true;
    exportBtns.hidden = true;
    tbody.innerHTML = '';
});

function pintarResumen(r) {
    resumenBox.hidden = false;
    exportBtns.hidden = false;
    rTotal.textContent = r.total;
    rAprobadas.textContent = r.aprobadas;
    rRechazadas.textContent = r.rechazadas;
    rFinalizadas.textContent = r.finalizadas;
    rSolicitadas.textContent = r.solicitadas;
}

function pintarTabla(rows) {
    tbody.innerHTML = rows.map(s => `
        <tr class="hover:bg-slate-50">
            <td class="px-3 py-2 font-medium text-slate-800">${s.id}</td>
            <td class="px-3 py-2"><small class="text-slate-500">${s.numero_cuenta ?? ''}</small></td>
            <td class="px-3 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold ${clsEstado(s.estado_solicitud)}">${s.estado_solicitud}</span></td>
            <td class="px-3 py-2">${s.tipo_practica ?? ''}</td>
            <td class="px-3 py-2">${s.supervisor_name ?? 'Sin asignar'}</td>
            <td class="px-3 py-2"><small>${formatFecha(s.created_at)}</small></td>
        </tr>
    `).join('');
}

function clsEstado(e) {
    switch(e) {
        case 'APROBADA': return 'bg-emerald-100 text-emerald-700';
        case 'RECHAZADA': return 'bg-rose-100 text-rose-700';
        case 'FINALIZADA': return 'bg-indigo-100 text-indigo-700';
        case 'SOLICITADA': return 'bg-amber-100 text-amber-800';
        case 'CANCELADA': return 'bg-slate-200 text-slate-600';
        default: return 'bg-slate-200 text-slate-700';
    }
}

function formatFecha(f) {
    if (!f) return '';
    return new Date(f).toLocaleDateString('es-HN');
}

function actualizarLinks(q) {
    document.getElementById('linkPdf').href = "{{ route('admin.reportes.export.pdf') }}?" + q;
    document.getElementById('linkExcel').href = "{{ route('admin.reportes.export.excel') }}?" + q;
}
</script>
@endpush