@extends('layouts.supervisores')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8 px-3 sm:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        {{-- ============================================
             SECCIÓN 1: HEADER
             ============================================ --}}
        <div class="flex flex-col gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-unahblue">Alumnos Asignados</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Gestiona las supervisiones de tus estudiantes</p>
            </div>
            
            {{-- Contadores --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="px-4 py-3 bg-blue-100 rounded-xl border-2 border-blue-200">
                    <p class="text-xs text-blue-600 font-semibold uppercase">Total</p>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-700">{{ $contadores['total'] }}</p>
                </div>
                <div class="px-4 py-3 bg-yellow-100 rounded-xl border-2 border-yellow-200">
                    <p class="text-xs text-yellow-600 font-semibold uppercase">Aprobadas</p>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-700">{{ $contadores['aprobadas'] }}</p>
                </div>
                <div class="px-4 py-3 bg-green-100 rounded-xl border-2 border-green-200">
                    <p class="text-xs text-green-600 font-semibold uppercase">Finalizadas</p>
                    <p class="text-2xl sm:text-3xl font-bold text-green-700">{{ $contadores['finalizadas'] }}</p>
                </div>
            </div>
        </div>

        {{-- ============================================
             SECCIÓN 2: ALERTAS
             ============================================ --}}
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
                    <p class="text-red-800 font-medium text-sm sm:text-base">{{ session('error') }}</p>
                </div>
            </div>
        @endif



        {{-- ============================================
             SECCIÓN 3: FILTROS Y BÚSQUEDA
             ============================================ --}}
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 space-y-3">
            {{-- Pestañas de estado --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                @php
                    $estadoActual = $estado ?? request('estado', 'APROBADA');
                    $linkBase = fn($e) => route('supervisor.alumnos.index', array_merge(request()->except('page','busqueda'), ['estado' => $e]));
                @endphp

                <a href="{{ $linkBase('APROBADA') }}"
                   class="px-3 py-1.5 rounded-full text-sm font-semibold border transition
                   {{ $estadoActual === 'APROBADA' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' }}">
                    Activas ({{ $contadores['aprobadas'] }})
                </a>

                <a href="{{ $linkBase('FINALIZADA') }}"
                   class="px-3 py-1.5 rounded-full text-sm font-semibold border transition
                   {{ $estadoActual === 'FINALIZADA' ? 'bg-green-600 text-white border-green-600' : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' }}">
                    Finalizadas ({{ $contadores['finalizadas'] }})
                </a>

                <a href="{{ $linkBase('TODAS') }}"
                   class="px-3 py-1.5 rounded-full text-sm font-semibold border transition
                   {{ $estadoActual === 'TODAS' ? 'bg-gray-800 text-white border-gray-800' : 'bg-gray-100 text-gray-800 border-gray-200 hover:bg-gray-200' }}">
                    Todas ({{ $contadores['aprobadas'] + $contadores['finalizadas'] }})
                </a>
            </div>

            <form method="GET" action="{{ route('supervisor.alumnos.index') }}" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                {{-- Conservar estado actual al buscar --}}
                <input type="hidden" name="estado" value="{{ $estadoActual }}">

                <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre o correo..."
                    class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Buscar
                    </button>

                    @if(request('busqueda'))
                        {{-- Limpiar solo la búsqueda, manteniendo el estado actual --}}
                        <a href="{{ route('supervisor.alumnos.index', array_merge(request()->except('page','busqueda'), ['estado' => $estadoActual])) }}"
                           class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-sm sm:text-base text-center flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>


        {{-- ============================================
             SECCIÓN 4: LISTA DE ALUMNOS
             ============================================ --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($alumnos->count() > 0)
                
                {{-- DESKTOP: Tabla --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Estudiante</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Empresa</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Supervisiones</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($alumnos as $alumno)
                                @php
                                    $supervisiones = $alumno->supervisiones->count();
                                    $colorSupervision = $supervisiones == 0 ? 'text-red-700 bg-red-100' : ($supervisiones == 1 ? 'text-yellow-700 bg-yellow-100' : 'text-green-700 bg-green-100');
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                                {{ strtoupper(substr($alumno->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $alumno->user->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $alumno->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $alumno->nombre_empresa }}</p>
                                        <p class="text-sm text-gray-600">{{ Str::limit($alumno->puesto_trabajo, 30) }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full font-semibold text-sm {{ $colorSupervision }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            {{ $supervisiones }}/2
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($alumno->estado_solicitud === 'APROBADA')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Aprobada</span>
                                        @elseif($alumno->estado_solicitud === 'FINALIZADA')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Finalizada</span>
                                        @endif
                                    </td>
<td class="px-6 py-4">
    <div class="flex items-center justify-center gap-2 flex-wrap">
        {{-- Ver detalle --}}
        <a href="{{ route('supervisor.alumnos.show', $alumno->id) }}"
           class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-semibold flex items-center gap-1 hover:bg-blue-100 transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver Detalle
        </a>

        {{-- Formato 1 - Azul sobrio --}}
        <a href="{{ route('supervisor.alumnos.formatos.supervision', ['solicitud' => $alumno->id, 'numero' => 1]) }}"
           class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition whitespace-nowrap flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            F1
        </a>

        {{-- Formato 2 - Gris sobrio --}}
        <a href="{{ route('supervisor.alumnos.formatos.supervision', ['solicitud' => $alumno->id, 'numero' => 2]) }}"
           class="px-3 py-1.5 bg-gray-600 text-white rounded-lg text-sm font-semibold hover:bg-gray-700 transition whitespace-nowrap flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            F2
        </a>

        {{-- Subir supervisión - Verde sobrio --}}
        @if($supervisiones < 2 && $alumno->estado_solicitud !== 'FINALIZADA')
            <button onclick="abrirModalSupervision({{ $alumno->id }}, {{ $supervisiones + 1 }})"
                    class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm font-semibold flex items-center gap-1 hover:bg-green-700 transition whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Subir
            </button>
        @endif
    </div>
</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE: Cards --}}
                <div class="lg:hidden divide-y divide-gray-200">
                    @foreach($alumnos as $alumno)
                        @php
                            $supervisiones = $alumno->supervisiones->count();
                            $colorSupervision = $supervisiones == 0 ? 'text-red-700 bg-red-100' : ($supervisiones == 1 ? 'text-yellow-700 bg-yellow-100' : 'text-green-700 bg-green-100');
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ strtoupper(substr($alumno->user->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 truncate">{{ $alumno->user->name }}</p>
                                        <p class="text-sm text-gray-600 truncate">{{ $alumno->user->email }}</p>
                                    </div>
                                </div>
                                @if($alumno->estado_solicitud === 'APROBADA')
                                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold flex-shrink-0">Aprobada</span>
                                @elseif($alumno->estado_solicitud === 'FINALIZADA')
                                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold flex-shrink-0">Finalizada</span>
                                @endif
                            </div>
                            
                            <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600 font-semibold mb-1">EMPRESA</p>
                                <p class="font-semibold text-gray-900">{{ $alumno->nombre_empresa }}</p>
                                <p class="text-sm text-gray-600">{{ $alumno->puesto_trabajo }}</p>
                            </div>

                            <div class="mb-3 flex items-center justify-center">
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-semibold {{ $colorSupervision }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Supervisiones: {{ $supervisiones }}/2
                                </span>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('supervisor.alumnos.show', $alumno->id) }}" class="w-full px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver Detalle Completo
                                </a>
                                
                                {{-- Formatos en una fila --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('supervisor.alumnos.formatos.supervision', ['solicitud' => $alumno->id, 'numero' => 1]) }}"
                                       class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold flex items-center justify-center gap-2 shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Formato 1
                                    </a>
                                    
                                    <a href="{{ route('supervisor.alumnos.formatos.supervision', ['solicitud' => $alumno->id, 'numero' => 2]) }}"
                                       class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-semibold flex items-center justify-center gap-2 shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Formato 2
                                    </a>
                                </div>
                                
                                {{-- Subir supervisión --}}
                                @if($supervisiones < 2 && $alumno->estado_solicitud !== 'FINALIZADA')
                                    <button onclick="abrirModalSupervision({{ $alumno->id }}, {{ $supervisiones + 1 }})" 
                                            class="w-full px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold flex items-center justify-center gap-2 shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        Subir Supervisión #{{ $supervisiones + 1 }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                    {{ $alumnos->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-gray-600 font-semibold">No tienes alumnos asignados</p>
                    <p class="text-gray-500 text-sm mt-1">Cuando el administrador te asigne estudiantes, aparecerán aquí</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Modales --}}
@include('supervisor.alumnos.modales')

{{-- Scripts --}}
@include('supervisor.alumnos.scripts')

@endsection