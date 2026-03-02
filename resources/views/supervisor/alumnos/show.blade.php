@extends('layouts.supervisores')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8 px-3 sm:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        {{-- ============================================
             SECCIÓN 1: HEADER CON BREADCRUMB
             ============================================ --}}
        <div class="flex flex-col gap-3">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('supervisor.alumnos.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            Alumnos
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detalle</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detalle del Estudiante</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Información completa y supervisiones</p>
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
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-red-800 font-medium text-sm sm:text-base">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- ============================================
             SECCIÓN 3: INFORMACIÓN DEL ESTUDIANTE
             ============================================ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            
            {{-- Información Personal --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex flex-col items-center text-center mb-6">
                        {{-- Avatar con iniciales --}}
                    @if($solicitud->foto_estudiante)
                        <img
                            src="{{ url("/admin/solicitudes/{$solicitud->id}/foto") }}"
                            alt="Foto de {{ $solicitud->user->name }}"
                            class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-lg mb-4"
                        >
                    @else
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-3xl mb-4 shadow-lg">
                            {{ strtoupper(substr($solicitud->user->name, 0, 2)) }}
                        </div>
                    @endif

                        
                        <h2 class="text-xl font-bold text-gray-900">{{ $solicitud->user->name }}</h2>
                        <p class="text-gray-600 mt-1 break-all">{{ $solicitud->user->email }}</p>
                        @if($solicitud->telefono_alumno)
                            <p class="text-gray-600 flex items-center gap-2 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $solicitud->telefono_alumno }}
                            </p>
                        @endif
                    </div>

                    <div class="border-t pt-4 space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Número de Cuenta</p>
                            <p class="text-gray-900 font-bold">{{ $solicitud->numero_cuenta ?? 'No especificado' }}</p>
                        </div>
                        
                        @if($solicitud->dni_estudiante)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase mb-1">DNI (Identidad)</p>
                            <p class="text-gray-900 font-bold">{{ $solicitud->dni_estudiante }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Tipo de Práctica</p>
                            <p class="text-gray-900 font-semibold capitalize">{{ $solicitud->tipo_practica ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Modalidad</p>
                            <p class="text-gray-900 font-semibold capitalize">{{ $solicitud->modalidad ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase mb-1">Estado</p>
                            @if($solicitud->estado_solicitud === 'APROBADA')
                                <span class="inline-flex px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Aprobada</span>
                            @elseif($solicitud->estado_solicitud === 'FINALIZADA')
                                <span class="inline-flex px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Finalizada</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Fechas del Periodo --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fechas del Periodo
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Inicio</span>
                            <span class="text-sm font-bold text-blue-700">
                                {{ $solicitud->fecha_inicio ? \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') : 'No especificado' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Fin</span>
                            <span class="text-sm font-bold text-green-700">
                                {{ $solicitud->fecha_fin ? \Carbon\Carbon::parse($solicitud->fecha_fin)->format('d/m/Y') : 'No especificado' }}
                            </span>
                        </div>
                        @if($solicitud->horario)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600 font-semibold uppercase block mb-1">Horario</span>
                            <span class="text-sm text-gray-900">{{ $solicitud->horario }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Información de la Empresa y Supervisiones --}}
            <div class="lg:col-span-2 space-y-4">
                
                {{-- Información de la Empresa --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Información de la Empresa
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600 font-semibold uppercase mb-2">Nombre de la Empresa</p>
                            <p class="text-gray-900 font-bold">{{ $solicitud->nombre_empresa }}</p>
                        </div>
                        
                        <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600 font-semibold uppercase mb-2">Dirección</p>
                            <p class="text-gray-900">{{ $solicitud->direccion_empresa }}</p>
                        </div>
                        
                        {{-- Información del Jefe --}}
                        <div class="md:col-span-2 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700 font-bold uppercase mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Jefe Inmediato
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">Nombre</p>
                                    <p class="text-gray-900 font-bold">{{ $solicitud->nombre_jefe }}</p>
                                </div>
                                
                                @if($solicitud->cargo_jefe)
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">Cargo</p>
                                    <p class="text-gray-900 font-semibold">{{ $solicitud->cargo_jefe }}</p>
                                </div>
                                @endif
                                
                                @if($solicitud->nivel_academico_jefe)
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">Nivel Académico</p>
                                    <p class="text-gray-900 font-semibold capitalize">{{ $solicitud->nivel_academico_jefe }}</p>
                                </div>
                                @endif
                                
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">Teléfono</p>
                                    <p class="text-gray-900 font-semibold">{{ $solicitud->numero_jefe }}</p>
                                </div>
                                
                                @if($solicitud->correo_jefe)
                                <div class="sm:col-span-2">
                                    <p class="text-xs text-gray-600 font-semibold mb-1">Correo Electrónico</p>
                                    <p class="text-gray-900 break-all">{{ $solicitud->correo_jefe }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Supervisiones Realizadas --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Supervisiones ({{ $totalSupervisiones }}/2)
        </h3>
        
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            {{-- Botones de Formato mejorados --}}
            <a href="{{ route('supervisor.alumnos.formatos.supervision', ['solicitud' => $solicitud->id, 'numero' => 1]) }}"
               class="flex-1 sm:flex-none px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Formato 1
            </a>
            
            <a href="{{ route('supervisor.alumnos.formatos.supervision', ['solicitud' => $solicitud->id, 'numero' => 2]) }}"
               class="flex-1 sm:flex-none px-4 py-2.5 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition shadow-md flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Formato 2
            </a>

            {{-- Botón Subir Supervisión - Verde sobrio --}}
            @if($totalSupervisiones < 2 && $solicitud->estado_solicitud !== 'FINALIZADA')
                <button onclick="abrirModalSupervision({{ $solicitud->id }}, {{ $totalSupervisiones + 1 }})"
                        class="flex-1 sm:flex-none px-4 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition shadow-md flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Subir Supervisión #{{ $totalSupervisiones + 1 }}
                </button>
            @endif
        </div>
    </div>

    @if($totalSupervisiones > 0)
        <div class="space-y-4">
            @foreach($solicitud->supervisiones as $supervision)
                <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-700 font-bold text-lg">#{{ $supervision->numero_supervision }}</span>
                            </div>
                         <div>
    <p class="font-bold text-gray-900">Supervisión #{{ $supervision->numero_supervision }}</p>
   <p class="text-sm text-gray-600">
    <span class="font-semibold">Realizada:</span> 
    @if($supervision->fecha_supervision)
        {{ \Carbon\Carbon::parse($supervision->fecha_supervision)->format('d/m/Y') }}
    @else
        {{ $supervision->created_at->format('d/m/Y') }}
    @endif
</p>
    <p class="text-xs text-gray-500">
        Subida al sistema: {{ $supervision->created_at->format('d/m/Y H:i') }}
    </p>



  {{-- ✅ AGREGAR ESTE BLOQUE --}}
            @if($supervision->ausencia_supervisor)
                <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 rounded-md text-xs font-semibold">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Justificación en: 
                    @if($supervision->ausencia_supervisor === 'entrada')
                        Entrada
                    @elseif($supervision->ausencia_supervisor === 'salida')
                        Salida
                    @else
                        Entrada y Salida
                    @endif
                </div>
            @endif
            {{-- ✅ FIN DEL BLOQUE --}}



</div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('supervisor.alumnos.supervision.descargar', $supervision->id) }}" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar
                        </a>
                        @if($solicitud->estado_solicitud !== 'FINALIZADA')
                            <form method="POST" action="{{ route('supervisor.alumnos.supervision.eliminar', $supervision->id) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta supervisión?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="pl-15">
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $supervision->comentario }}</p>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-12 bg-gray-50 rounded-xl">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-600 font-semibold">No hay supervisiones registradas</p>
        <p class="text-gray-500 text-sm mt-1">Sube la primera supervisión para comenzar el seguimiento</p>
    </div>
@endif

@if($puedeSubirCartaFinalizacion)
    <div class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            <div>
                <p class="text-green-800 font-semibold">Supervisiones completadas</p>
                <p class="text-green-700 text-sm mt-1">El estudiante ya puede subir su carta de finalización de práctica.</p>
            </div>
        </div>
    </div>
@endif
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Modales --}}
@include('supervisor.alumnos.modales')

{{-- Scripts --}}
@include('supervisor.alumnos.scripts')

@endsection