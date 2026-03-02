@extends('layouts.estudiantes')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-unahblue">Mis Documentos</h1>
        <span class="text-sm text-unahblue/70">
            Total: <strong>{{ $documentos->count() }}</strong>
        </span>
    </div>

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Estado vacío --}}
    @if($documentos->isEmpty())
        <div class="rounded-xl border border-unahblue/10 bg-white p-8 text-center text-unahblue/70">
            Aún no has subido documentos para tu solicitud.
        </div>
    @else
        <div class="overflow-x-auto rounded-xl shadow border border-unahblue/10 bg-white">
            <table class="min-w-full divide-y divide-unahblue/20">
                <thead class="bg-unahblue text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium">Tipo</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Fecha</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unahblue/10">
                @foreach ($documentos as $doc)
                    <tr>
                        <td class="px-6 py-4 text-sm text-unahblue">
                            {{ ucfirst(str_replace('_',' ', $doc->tipo)) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-unahblue/80">
                            {{ optional($doc->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('estudiantes.documentos.ver', $doc->id) }}" target="_blank"
                                   class="px-3 py-1 rounded bg-unahblue text-white text-sm hover:bg-unahblue/90">Ver</a>

                                <a href="{{ route('estudiantes.documentos.descargar', $doc->id) }}"
                                   class="px-3 py-1 rounded bg-unahgold text-unahblue text-sm hover:bg-yellow-400">Descargar</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection