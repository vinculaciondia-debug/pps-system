@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-4 sm:py-6 lg:py-8 px-3 sm:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        {{-- HEADER Y CONTADORES --}}
        <div class="flex flex-col gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-unahblue">Solicitudes Aprobadas</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Gestiona las prácticas en proceso</p>
            </div>
            
            {{-- Contadores --}}
            <div class="grid grid-cols-2 sm:flex sm:flex-row gap-3">
                <div class="px-4 py-3 bg-yellow-100 rounded-xl border-2 border-yellow-200">
                    <p class="text-xs text-yellow-600 font-semibold uppercase">Pendientes</p>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-700">{{ $contadores['pendientes'] }}</p>
                </div>
                <div class="px-4 py-3 bg-green-100 rounded-xl border-2 border-green-200">
                    <p class="text-xs text-green-600 font-semibold uppercase">Aprobadas</p>
                    <p class="text-2xl sm:text-3xl font-bold text-green-700">{{ $contadores['aprobadas'] }}</p>
                </div>
                <div class="px-4 py-3 bg-red-100 rounded-xl border-2 border-red-200 hidden sm:block">
                    <p class="text-xs text-red-600 font-semibold uppercase">Rechazadas</p>
                    <p class="text-2xl sm:text-3xl font-bold text-red-700">{{ $contadores['rechazadas'] }}</p>
                </div>
                <div class="px-4 py-3 bg-blue-100 rounded-xl border-2 border-blue-200 hidden sm:block">
                    <p class="text-xs text-blue-600 font-semibold uppercase">Finalizadas</p>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-700">{{ $contadores['finalizadas'] }}</p>
                </div>
            </div>
        </div>

        {{-- ALERTAS --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-3 sm:p-4 rounded-lg shadow-md animate-fade-in">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <p class="text-green-800 font-medium text-sm sm:text-base">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-3 sm:p-4 rounded-lg shadow-md animate-fade-in">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-red-800 font-semibold text-sm sm:text-base mb-1">Error al procesar la solicitud</p>
                        <p class="text-red-700 text-xs sm:text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- BUSCADOR --}}
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4">
            <form method="GET" action="{{ route('admin.solicitudes.aprobadas') }}" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre, correo o cuenta..."
                    class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm sm:text-base">
                        Buscar
                    </button>
                    @if(request('busqueda'))
                        <a href="{{ route('admin.solicitudes.aprobadas') }}" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-sm sm:text-base text-center">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLA DE SOLICITUDES --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($solicitudes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        {{-- ENCABEZADOS --}}
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Estudiante</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Supervisor</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Supervisiones</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Progreso</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        
                        {{-- FILAS --}}
                        <tbody class="divide-y divide-gray-100">
                            @foreach($solicitudes as $solicitud)
                            @php
                                $esPorTrabajo = $solicitud->tipo_practica === 'trabajo';
                                $supervisionesCount = $solicitud->supervisiones_count;
                                $tieneCartaFinalizacion = $solicitud->documentos->where('tipo', 'carta_finalizacion')->count() > 0;
                                
                                // LOGICA: Si es por trabajo, puede finalizarse siempre
                                $puedeFinalizarse = $esPorTrabajo 
                                    ? true 
                                    : ($supervisionesCount >= 2 && $tieneCartaFinalizacion);
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                {{-- ESTUDIANTE --}}
                                <td class="px-4 py-4">
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">{{ $solicitud->user->name }}</p>
                                        <p class="text-xs text-gray-600 truncate">{{ $solicitud->user->email }}</p>
                                        <p class="text-xs text-gray-500">{{ $solicitud->numero_cuenta }}</p>
                                    </div>
                                </td>

                                {{-- TIPO --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap
                                        {{ $esPorTrabajo ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        @if($esPorTrabajo)
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                            </svg>
                                            Trabajo
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"/>
                                            </svg>
                                            Normal
                                        @endif
                                    </span>
                                </td>

                                {{-- SUPERVISOR --}}
                                <td class="px-4 py-4">
                                    @if($solicitud->supervisor)
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">{{ $solicitud->supervisor->user->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $solicitud->supervisor->user->email }}</p>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic">Sin asignar</p>
                                    @endif
                                </td>

                                {{-- SUPERVISIONES --}}
                                <td class="px-4 py-4 text-center">
                                    @if($esPorTrabajo)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"/>
                                            </svg>
                                            No aplica
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                            {{ $supervisionesCount >= 2 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            {{ $supervisionesCount }}/2
                                        </span>
                                    @endif
                                </td>

                                {{-- PROGRESO --}}
                                <td class="px-4 py-4 text-center">
                                    @if($puedeFinalizarse)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-xs font-semibold whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                            </svg>
                                            Puede finalizar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                                            </svg>
                                            En proceso
                                        </span>
                                    @endif
                                </td>

                                {{-- ACCIONES --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="verDetalle({{ $solicitud->id }})" 
                                                class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold">
                                            Ver
                                        </button>
                                        <button onclick="cambiarSupervisor({{ $solicitud->id }})" 
                                                class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition text-sm font-semibold whitespace-nowrap">
                                            Cambiar Sup.
                                        </button>
                                        @if($puedeFinalizarse)
                                            <button onclick="finalizarSolicitud({{ $solicitud->id }})" 
                                                    class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-semibold">
                                                Finalizar
                                            </button>
                                        @else
                                            <button disabled 
                                                    class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed text-sm font-semibold" 
                                                    title="{{ $esPorTrabajo ? 'Práctica por trabajo' : 'Faltan supervisiones o carta de finalización' }}">
                                                Finalizar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $solicitudes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-600 font-semibold">No hay solicitudes aprobadas</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Modales --}}
@include('admin.solicitudes.modals-aprobadas')

{{-- Scripts --}}
@include('admin.solicitudes.scripts-aprobadas')

@endsection