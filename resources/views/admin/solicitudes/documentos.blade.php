@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto py-8">
        <h2 class="text-2xl font-bold text-unahblue mb-6">
            Documentos de {{ $solicitud->user->name }} (Solicitud #{{ $solicitud->id }})
        </h2>

        @if ($solicitud->documentos->isEmpty())
            <p class="text-gray-600">No se han subido documentos para esta solicitud.</p>
        @else
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border-b text-left">Tipo</th>
                        <th class="py-2 px-4 border-b text-left">Archivo</th>
                        <th class="py-2 px-4 border-b text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($solicitud->documentos as $doc)
                        <tr>
                            <td class="py-2 px-4 border-b capitalize">{{ str_replace('_', ' ', $doc->tipo) }}</td>
                            <td class="py-2 px-4 border-b">{{ basename($doc->ruta) }}</td>
                            <td class="py-2 px-4 border-b text-center space-x-2">
                                <a href="{{ asset('storage/' . $doc->ruta) }}" target="_blank" class="text-blue-600 underline">👁️ Ver</a>
                                <a href="{{ asset('storage/' . $doc->ruta) }}" download class="text-green-600 underline">⬇️ Descargar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @endsection

