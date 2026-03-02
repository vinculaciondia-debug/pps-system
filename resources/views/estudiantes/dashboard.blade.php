@extends('layouts.estudiantes')

@section('content')

<div class="min-h-screen bg-gray-100 py-4 sm:py-6 lg:py-8 px-3 sm:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-3 sm:gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-unahblue">
                    ¡Hola, {{ auth()->user()->name }}!
                </h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">
                    Aquí está el estado de tu práctica profesional
                </p>
            </div>

            @if($solicitud)
                @php
                    $statusConfig = [
                        'SOLICITADA' => [
                            'bg' => 'bg-gradient-to-r from-yellow-400 to-orange-400',
                            'text' => 'text-white',
                            'icon' => '⏳',
                            'label' => 'En Revisión'
                        ],
                        'APROBADA' => [
                            'bg' => 'bg-gradient-to-r from-green-400 to-emerald-500',
                            'text' => 'text-white',
                            'icon' => '✓',
                            'label' => 'Aprobada'
                        ],
                        'RECHAZADA' => [
                            'bg' => 'bg-gradient-to-r from-red-400 to-pink-500',
                            'text' => 'text-white',
                            'icon' => '✕',
                            'label' => 'Rechazada'
                        ],
                        'CANCELADA' => [
                            'bg' => 'bg-gradient-to-r from-gray-400 to-gray-500',
                            'text' => 'text-white',
                            'icon' => '⊘',
                            'label' => 'Cancelada'
                        ],
                        'FINALIZADA' => [
                            'bg' => 'bg-gradient-to-r from-blue-500 to-indigo-600',
                            'text' => 'text-white',
                            'icon' => '🎓',
                            'label' => 'Finalizada'
                        ],
                    ];
                    $status = $statusConfig[$solicitud->estado_solicitud] ?? $statusConfig['SOLICITADA'];
                @endphp

                <div class="flex items-center justify-start">
                    <span class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold shadow-lg {{ $status['bg'] }} {{ $status['text'] }}">
                        <span class="text-sm sm:text-base">{{ $status['icon'] }}</span>
                        <span>{{ $status['label'] }}</span>
                    </span>
                </div>
            @endif
        </div>

{{-- ALERTS --}}
@if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-3 sm:p-4 rounded-lg shadow-md">
        <div class="flex items-start">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <p class="text-red-800 font-medium text-sm sm:text-base">{{ session('error') }}</p>
        </div>
    </div>
@endif

@if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-3 sm:p-4 rounded-lg shadow-md">
        <div class="flex items-start">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            <p class="text-green-800 font-medium text-sm sm:text-base">{{ session('success') }}</p>
        </div>
    </div>
@endif

{{--  ALERTA: PRÁCTICA PROFESIONAL RECHAZADA --}}
@if($solicitud && $solicitud->estado_solicitud === 'RECHAZADA' && !empty($solicitud->observaciones))
    <div id="alert-practica-rechazada" class="bg-red-50 border-l-4 border-red-500 p-4 sm:p-5 rounded-lg shadow-md">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1 min-w-0">
                <h3 class="text-red-800 font-bold mb-2">Solicitud de Práctica Rechazada</h3>
                <div class="p-3 bg-white rounded border border-red-200 mb-3">
                    <p class="text-sm font-semibold text-gray-700 mb-1">Motivo del rechazo:</p>
                    <p class="text-sm text-gray-700 break-words whitespace-pre-wrap">{{ $solicitud->observaciones }}</p>
                </div>
                <a href="{{ route('estudiantes.solicitud') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Nueva Solicitud
                </a>
            </div>
            <button onclick="document.getElementById('alert-practica-rechazada').remove()" 
                    class="text-red-600 hover:text-red-800 flex-shrink-0 ml-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
@endif

{{--  ALERTAS DE ACTUALIZACIÓN DE DATOS --}}
@php
    // Buscar la última actualización procesada (aprobada o rechazada)
    $ultimaActualizacion = \App\Models\SolicitudActualizacion::where('user_id', auth()->id())
        ->whereIn('estado', ['APROBADA', 'RECHAZADA'])
        ->latest('id')
        ->first();
@endphp

{{-- Script inline para evitar flash (ANTES de renderizar la alerta) --}}
@if($ultimaActualizacion && (!$solicitud || $solicitud->estado_solicitud !== 'RECHAZADA'))
<script>
    // Ejecutar INMEDIATAMENTE antes de que el DOM esté listo
    (function() {
        const alertId = {{ $ultimaActualizacion->id }};
        const cerrada = localStorage.getItem('alert-actualizacion-' + alertId);
        
        if (cerrada === 'cerrada') {
            // Crear un style tag para ocultar la alerta ANTES de renderizarLA
            const style = document.createElement('style');
            style.innerHTML = '#alert-actualizacion-' + alertId + ' { display: none !important; }';
            document.head.appendChild(style);
        }
    })();
</script>
@endif

{{-- Solo mostrar si NO hay rechazo de práctica activo --}}
@if($ultimaActualizacion && (!$solicitud || $solicitud->estado_solicitud !== 'RECHAZADA'))
    
    @if($ultimaActualizacion->estado === 'APROBADA')
        {{-- Actualización de datos aprobada --}}
        <div id="alert-actualizacion-{{ $ultimaActualizacion->id }}" 
             class="alert-actualizacion bg-green-50 border-l-4 border-green-500 p-4 sm:p-5 rounded-lg shadow-md"
             data-alert-id="{{ $ultimaActualizacion->id }}">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <h3 class="text-green-800 font-bold mb-1">Actualización de Datos Aprobada</h3>
                    <p class="text-green-700 text-sm">Tu solicitud de cambio de información fue aprobada.</p>
                    @if($ultimaActualizacion->observacion)
                        <div class="mt-2 p-3 bg-white rounded border border-green-200">
                            <p class="text-sm text-gray-700 break-words">
                                <span class="font-semibold">Comentario del admin:</span><br>
                                <span class="whitespace-pre-wrap">{{ $ultimaActualizacion->observacion }}</span>
                            </p>
                        </div>
                    @endif
                    <p class="text-green-600 text-xs mt-2">Aprobada el {{ $ultimaActualizacion->updated_at->format('d/m/Y H:i') }}</p>
                </div>
                <button onclick="cerrarAlertaPermanente({{ $ultimaActualizacion->id }})" 
                        class="text-green-600 hover:text-green-800 flex-shrink-0 ml-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>

    @elseif($ultimaActualizacion->estado === 'RECHAZADA')
        {{-- Actualización de datos rechazada --}}
        <div id="alert-actualizacion-{{ $ultimaActualizacion->id }}" 
             class="alert-actualizacion bg-orange-50 border-l-4 border-orange-500 p-4 sm:p-5 rounded-lg shadow-md"
             data-alert-id="{{ $ultimaActualizacion->id }}">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <h3 class="text-orange-800 font-bold mb-1">Actualización de Datos Rechazada</h3>
                    <p class="text-orange-700 text-sm">Tu solicitud de cambio de información fue rechazada.</p>
                    @if($ultimaActualizacion->observacion)
                        <div class="mt-2 p-3 bg-white rounded border border-orange-200">
                            <p class="text-sm text-gray-700 break-words">
                                <span class="font-semibold">Motivo del rechazo:</span><br>
                                <span class="whitespace-pre-wrap">{{ $ultimaActualizacion->observacion }}</span>
                            </p>
                        </div>
                    @endif
                    <p class="text-orange-600 text-xs mt-2">Rechazada el {{ $ultimaActualizacion->updated_at->format('d/m/Y H:i') }}</p>
                    <a href="{{ route('estudiantes.actualizacion.create') }}" 
                       class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Enviar Nueva Actualización
                    </a>
                </div>
                <button onclick="cerrarAlertaPermanente({{ $ultimaActualizacion->id }})" 
                        class="text-orange-600 hover:text-orange-800 flex-shrink-0 ml-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif
    
@endif

        {{-- NO HAY SOLICITUD --}}
        @if(!$solicitud || in_array($solicitud->estado_solicitud, ['RECHAZADA', 'CANCELADA']))
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 lg:p-12 text-center border border-blue-100">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-3">
                    @if(!$solicitud)
                        Comienza tu práctica profesional
                    @else
                        Inicia una nueva solicitud
                    @endif
                </h2>
                <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">
                    @if(!$solicitud)
                        Aún no has enviado ninguna solicitud. Completa el formulario para comenzar.
                    @else
                        Tu solicitud anterior fue {{ strtolower($solicitud->estado_solicitud) }}. Puedes crear una nueva.
                    @endif
                </p>
                <a href="{{ route('estudiantes.solicitud') }}"
                   class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transform transition hover:scale-105 shadow-lg text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Crear Nueva Solicitud</span>
                </a>
            </div>
        @endif

        {{-- SI HAY SOLICITUD ACTIVA --}}
        @if($solicitud && in_array($solicitud->estado_solicitud, ['SOLICITADA', 'APROBADA', 'FINALIZADA']))

        {{-- CARDS DE ESTADÍSTICAS --}}
        @php 
            $docCount = optional($solicitud->documentos)->count() ?? 0;
            $supervisionesCount = $solicitud 
                ? \DB::table('supervisiones')->where('solicitud_pps_id', $solicitud->id)->count() 
                : 0;
            
            // Determinar si la práctica requiere supervisiones
            $requiereSupervision = $solicitud->tipo_practica === 'normal';
            
            // LÓGICA DE PROGRESO DINÁMICO según tipo de práctica
            $progressPercent = 0;
            $progressSteps = [];
            
            if ($requiereSupervision) {
                // PRÁCTICA NORMAL (con supervisiones)
                // Paso 1: Solicitud enviada con documentos (25%)
                if ($solicitud->estado_solicitud !== 'CANCELADA') {
                    $progressPercent = 25;
                    $progressSteps[] = 'Solicitud enviada';
                    
                    // Paso 2: Aprobada por admin (50%)
                    if (in_array($solicitud->estado_solicitud, ['APROBADA', 'FINALIZADA'])) {
                        $progressPercent = 50;
                        $progressSteps[] = 'Aprobada por admin';
                        
                        // Paso 3: Supervisiones en proceso (60-75%)
                        if ($supervisionesCount >= 1) {
                            $progressPercent = 60;
                            $progressSteps[] = 'Primera supervisión';
                        }
                        
                        if ($supervisionesCount >= 2) {
                            $progressPercent = 75;
                            $progressSteps[] = 'Segunda supervisión';
                        }
                        
                        // Paso 4: Finalizada (100%)
                        if ($solicitud->estado_solicitud === 'FINALIZADA') {
                            $progressPercent = 100;
                            $progressSteps[] = 'Práctica finalizada';
                        }
                    }
                }
            } else {
                // PRÁCTICA POR TRABAJO (sin supervisiones)
                // Paso 1: Solicitud enviada con documentos (33%)
                if ($solicitud->estado_solicitud !== 'CANCELADA') {
                    $progressPercent = 33;
                    $progressSteps[] = 'Solicitud enviada';
                    
                    // Paso 2: Aprobada por admin (66%)
                    if (in_array($solicitud->estado_solicitud, ['APROBADA', 'FINALIZADA'])) {
                        $progressPercent = 66;
                        $progressSteps[] = 'Aprobada por admin';
                        
                        // Paso 3: Finalizada (100%)
                        if ($solicitud->estado_solicitud === 'FINALIZADA') {
                            $progressPercent = 100;
                            $progressSteps[] = 'Práctica finalizada';
                        }
                    }
                }
            }
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
            {{-- Card 1: Estado --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 hover:shadow-2xl transition-shadow overflow-hidden min-w-0">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide truncate">Estado</h3>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 mb-3 sm:mb-4 truncate">{{ $status['label'] }}</div>
                <div class="w-full bg-gray-200 rounded-full h-2 sm:h-3 overflow-hidden">
                    <div class="h-2 sm:h-3 rounded-full {{ $status['bg'] }} transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-0 mt-2 sm:mt-3">
                    <p class="text-xs sm:text-sm text-gray-600">{{ $progressPercent }}%</p>
                    @if($progressPercent < 100 && $solicitud->estado_solicitud !== 'RECHAZADA' && $solicitud->estado_solicitud !== 'CANCELADA')
                        <p class="text-xs text-blue-600 font-medium truncate">
                            @if($progressPercent < 50)
                                Esperando aprobación
                            @elseif($supervisionesCount < 2)
                                {{ 2 - $supervisionesCount }} supervisión pendiente
                            @else
                                Casi lista
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            {{-- Card 2: Documentos --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 hover:shadow-2xl transition-shadow overflow-hidden min-w-0">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide truncate">Documentos</h3>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-100 to-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                @php
                    $docsEsperados = $solicitud->tipo_practica === 'normal' ? 4 : 4;
                @endphp
                <div class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 mb-2">{{ $docCount }} de {{ $docsEsperados }}</div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden mb-2 sm:mb-3">
                    <div class="h-2 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 transition-all duration-500" style="width: {{ ($docCount / $docsEsperados) * 100 }}%"></div>
                </div>
                <a href="{{ route('estudiantes.solicitudes.documentos', $solicitud->id) }}"
                   class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm">
                    <span>Ver todos</span>
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Card 3: Supervisiones --}}
            @if($requiereSupervision)
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 hover:shadow-2xl transition-shadow overflow-hidden min-w-0">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide truncate">Supervisiones</h3>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 mb-2">{{ $supervisionesCount }} de 2</div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full bg-gradient-to-r from-purple-400 to-pink-500 transition-all duration-500" style="width: {{ ($supervisionesCount / 2) * 100 }}%"></div>
                </div>
                <p class="text-xs sm:text-sm text-gray-600 mt-2 sm:mt-3">{{ $supervisionesCount >= 2 ? 'Completadas' : 'En proceso' }}</p>
            </div>
            @else
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 hover:shadow-2xl transition-shadow overflow-hidden min-w-0">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide truncate">Tipo de Práctica</h3>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 mb-2">Por Trabajo</div>
                <p class="text-xs sm:text-sm text-gray-600">Sin supervisiones requeridas</p>
            </div>
            @endif

            {{-- Card 4: Fecha --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 hover:shadow-2xl transition-shadow overflow-hidden min-w-0">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide truncate">Fecha</h3>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 mb-2">{{ $solicitud->created_at->format('d/m/Y') }}</div>
                <p class="text-xs sm:text-sm text-gray-600 truncate">{{ $solicitud->created_at->diffForHumans() }}</p>
            </div>
        </div>

            @if($solicitud->supervisor_id && $solicitud->supervisor)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-200">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-1">Supervisor Asignado</h3>
                <p class="text-base sm:text-lg font-semibold text-blue-700 mb-2">{{ $solicitud->supervisor->user->name }}</p>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 text-sm text-gray-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">{{ $solicitud->supervisor->user->email }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


{{-- ACCIONES RÁPIDAS / SUBIR CARTA DE FINALIZACIÓN --}}
@if(in_array($solicitud->estado_solicitud, ['SOLICITADA', 'APROBADA']))
    @php
        $tieneCartaFinalizacion = $solicitud->documentos->where('tipo', 'carta_finalizacion')->count() > 0;
        $cartaRechazada = $solicitud->documentos
        ->where('tipo', 'carta_finalizacion')
        ->where('estado_revision', 'RECHAZADA')
        ->sortByDesc('id')
        ->first();
    
        // SOLO PRÁCTICAS NORMALES pueden subir carta de finalización
        // Y solo después de tener 2 supervisiones completadas
        $puedeSubirCarta = $requiereSupervision && $supervisionesCount >= 2;
        
        // Calcular fecha límite (30 días CORRIDOS desde la segunda supervisión)
        $fechaLimite = null;
        $diasRestantes = null;
        $plazoVencido = false;
        
        if ($puedeSubirCarta) {
            // Obtener la fecha de la segunda supervisión
            $segundaSupervision = \DB::table('supervisiones')
                ->where('solicitud_pps_id', $solicitud->id)
                ->orderBy('created_at', 'asc')
                ->skip(1)
                ->first();
            
            if ($segundaSupervision) {
                $fechaInicio = \Carbon\Carbon::parse($segundaSupervision->created_at);
                $fechaActual = \Carbon\Carbon::now();
                
                // Calcular 30 días CORRIDOS (simplemente sumar 30 días)
                $fechaLimite = $fechaInicio->copy()->addDays(30);
                
                // Calcular días restantes (diferencia en días)
                $diasRestantes = $fechaActual->diffInDays($fechaLimite, false);
                
                // Si es negativo, ya venció
                if ($diasRestantes < 0) {
                    $diasRestantes = 0;
                    $plazoVencido = true;
                }
                
                // Verificar si el plazo ha vencido
                $plazoVencido = $fechaActual->gt($fechaLimite);
            }
        }
    @endphp

    {{-- Si puede subir carta de finalización --}}
    @if($puedeSubirCarta && !$plazoVencido)
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border-2 border-green-500">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">✓ Supervisiones Completadas</h3>
                    
                    {{-- Alerta de plazo --}}
                    @if($diasRestantes !== null)
                        <div class="mb-3 p-3 rounded-lg {{ $diasRestantes <= 5 ? 'bg-red-50 border-2 border-red-300' : 'bg-yellow-50 border-2 border-yellow-300' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 {{ $diasRestantes <= 5 ? 'text-red-600' : 'text-yellow-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-bold {{ $diasRestantes <= 5 ? 'text-red-800' : 'text-yellow-800' }} text-sm">
                                        {{ floor($diasRestantes) }} {{ floor($diasRestantes) == 1 ? 'día restante' : 'días restantes' }}
                                    </p>
                                    <p class="text-xs {{ $diasRestantes <= 5 ? 'text-red-700' : 'text-yellow-700' }}">
                                        Fecha límite: {{ $fechaLimite->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($cartaRechazada)
                        <div class="bg-red-50 border-2 border-red-400 rounded-lg p-4 mb-3">
                            <div class="flex items-start gap-3 mb-3">
                                <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-bold text-red-800">Carta Rechazada</p>
                                    <p class="text-sm text-red-700 mt-1">{{ $cartaRechazada->observacion_revision }}</p>
                                    <p class="text-xs text-red-600 mt-2">Rechazada: {{ $cartaRechazada->revisado_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <button onclick="document.getElementById('modalSubirCartaFinalizacion').classList.remove('hidden')" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm">
                                Subir Nueva Carta
                            </button>
                        </div>
                    @elseif($tieneCartaFinalizacion)
                        @php
                            $cartaPendiente = $solicitud->documentos
                                ->where('tipo', 'carta_finalizacion')
                                ->where('estado_revision', 'PENDIENTE')
                                ->first();
                        @endphp
                        @if($cartaPendiente)
                            <div class="bg-white border-2 border-yellow-400 rounded-lg p-4 mb-3">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="font-bold text-yellow-800">Carta de Finalización en Revisión</p>
                                        <p class="text-sm text-yellow-700">El administrador está revisando tu carta</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <button onclick="document.getElementById('modalSubirCartaFinalizacion').classList.remove('hidden')" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transform transition hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Subir Carta de Finalización
                        </button>
                    @endif
                </div>
            </div>
        </div>
    
    {{-- Plazo vencido --}}
    @elseif($puedeSubirCarta && $plazoVencido)
        <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border-2 border-red-500">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg sm:text-xl font-bold text-red-800 mb-2">Plazo Vencido</h3>
                    <p class="text-sm text-red-700 mb-3">
                        El plazo de 30 días hábiles para subir la carta de finalización ha expirado.
                    </p>
                    <div class="bg-white border-2 border-red-300 rounded-lg p-3 mb-3">
                        <p class="text-xs text-red-800">
                            <span class="font-semibold">Fecha límite:</span> {{ $fechaLimite->format('d/m/Y') }}<br>
                            <span class="font-semibold">Vencido desde:</span> {{ $fechaLimite->diffForHumans() }}
                        </p>
                    </div>
                    <p class="text-xs text-red-700 font-medium">
                        Por favor, contacta con la coordinación de Informática Administrativa para regularizar tu situación.
                    </p>
                </div>
            </div>
        </div>
    
    @else
        {{-- Mostrar acciones rápidas normales --}}
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 overflow-hidden">
            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Acciones Rápidas
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <a href="{{ route('estudiantes.actualizacion.create') }}"
                   class="flex items-center gap-3 p-3 sm:p-4 border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-all group min-w-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">Actualizar Datos</p>
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Modifica tu información</p>
                    </div>
                </a>


                <a href="{{ route('estudiantes.cancelacion.create', $solicitud->id) }}"
                   class="flex items-center gap-3 p-3 sm:p-4 border-2 border-gray-200 rounded-xl hover:border-red-400 hover:bg-red-50 transition-all group min-w-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">Cancelar Práctica</p>
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Anular solicitud</p>
                    </div>
                </a>
            </div>
        </div>
    @endif
@endif
        {{-- ACCIONES RÁPIDAS (solo para solicitudes activas) --}}
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 overflow-hidden">

        {{-- CONTENIDO PRINCIPAL --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            
            {{-- Documentos Enviados --}}
            <div class="lg:col-span-2 bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 overflow-hidden">
                <div class="flex items-center justify-between mb-4 sm:mb-6 min-w-0">
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-800 flex items-center gap-2 min-w-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="hidden sm:inline truncate">Documentos Enviados</span>
                        <span class="sm:hidden truncate">Documentos</span>
                    </h3>
                    <a href="{{ route('estudiantes.solicitudes.documentos', $solicitud->id) }}" 
                       class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap flex-shrink-0 ml-2">
                        Ver todos →
                    </a>
                </div>

                @if($docCount === 0)
                    <div class="text-center py-8 sm:py-12">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 text-sm sm:text-base">Aún no has subido documentos</p>
                    </div>
                @else
                    <div class="space-y-2 sm:space-y-3">
                        @foreach($solicitud->documentos as $doc)
                            <div class="flex flex-col gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors border border-gray-200 min-w-0">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-800 capitalize text-sm sm:text-base truncate">{{ str_replace('_', ' ', $doc->tipo) }}</p>
                                        @if(!empty($doc->created_at))
                                            <p class="text-xs text-gray-500 truncate">{{ $doc->created_at->format('d/m/Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('estudiantes.documentos.ver', $doc->id) }}" target="_blank" 
                                       class="flex-1 sm:flex-none px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-blue-600 hover:bg-blue-100 rounded-lg transition-colors text-center">
                                        Ver
                                    </a>
                                    <a href="{{ route('estudiantes.documentos.descargar', $doc->id) }}" 
                                       class="flex-1 sm:flex-none px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-green-600 hover:bg-green-100 rounded-lg transition-colors text-center">
                                        Descargar
                                    </a>
                                    <form method="POST" action="{{ route('estudiantes.documentos.eliminar', $doc->id) }}" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar este documento?');" class="flex-1 sm:flex-none">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Timeline de Progreso --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-5 lg:p-6 border border-blue-100 overflow-hidden min-w-0">
                <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="truncate">Progreso</span>
                </h3>

                @php
                    if ($requiereSupervision) {
                        // Timeline para PRÁCTICA NORMAL
                        $timeline = [
                            [
                                'label' => 'Solicitud enviada', 
                                'done' => true,
                                'subtitle' => $docCount . ' documentos'
                            ],
                            [
                                'label' => 'Revisión admin', 
                                'done' => in_array($solicitud->estado_solicitud, ['APROBADA','FINALIZADA']),
                                'subtitle' => $solicitud->estado_solicitud === 'APROBADA' || $solicitud->estado_solicitud === 'FINALIZADA' ? 'Aprobada' : 'Esperando'
                            ],
                            [
                                'label' => 'Supervisiones', 
                                'done' => $supervisionesCount >= 2,
                                'subtitle' => $supervisionesCount . ' de 2'
                            ],
                                                       [
                                'label' => 'Finalizada', 
                                'done' => $solicitud->estado_solicitud === 'FINALIZADA',
                                'subtitle' => $solicitud->estado_solicitud === 'FINALIZADA' ? 'Completada' : 'Pendiente'
                            ],
                        ];
                    } else {
                        // Timeline para PRÁCTICA POR TRABAJO
                        $timeline = [
                            [
                                'label' => 'Solicitud enviada', 
                                'done' => true,
                                'subtitle' => $docCount . ' documentos'
                            ],
                            [
                                'label' => 'Revisión admin', 
                                'done' => in_array($solicitud->estado_solicitud, ['APROBADA','FINALIZADA']),
                                'subtitle' => $solicitud->estado_solicitud === 'APROBADA' || $solicitud->estado_solicitud === 'FINALIZADA' ? 'Aprobada' : 'Esperando'
                            ],
                            [
                                'label' => 'Finalizada', 
                                'done' => $solicitud->estado_solicitud === 'FINALIZADA',
                                'subtitle' => $solicitud->estado_solicitud === 'FINALIZADA' ? 'Completada' : 'Pendiente'
                            ]
                        ];
                    }
                @endphp

                <div class="relative">
                    @foreach($timeline as $index => $step)
                        <div class="flex items-start gap-3 sm:gap-4 {{ $index < count($timeline) - 1 ? 'mb-6 sm:mb-8' : '' }}">
                            <div class="relative flex flex-col items-center flex-shrink-0">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full {{ $step['done'] ? 'bg-gradient-to-br from-green-400 to-emerald-500' : 'bg-gray-200' }} flex items-center justify-center shadow-lg">
                                    @if($step['done'])
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    @else
                                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-white rounded-full"></div>
                                    @endif
                                </div>
                                @if($index < count($timeline) - 1)
                                    <div class="w-0.5 h-10 sm:h-12 {{ $step['done'] ? 'bg-green-400' : 'bg-gray-200' }} mt-2"></div>
                                @endif
                            </div>
                            <div class="pt-1 sm:pt-2 min-w-0 flex-1">
                                <p class="font-semibold {{ $step['done'] ? 'text-gray-800' : 'text-gray-400' }} text-sm sm:text-base truncate">{{ $step['label'] }}</p>
                                <p class="text-xs {{ $step['done'] ? 'text-green-600' : 'text-gray-500' }} mt-1 truncate">{{ $step['subtitle'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        @endif

    </div>
</div>

<script>
// Función para cerrar alerta permanentemente
function cerrarAlertaPermanente(alertId) {
    // Guardar en localStorage que esta alerta fue cerrada
    localStorage.setItem('alert-actualizacion-' + alertId, 'cerrada');
    
    // Ocultar la alerta con animación
    const alert = document.getElementById('alert-actualizacion-' + alertId);
    if (alert) {
        alert.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}
</script>

{{-- Modal: Subir Carta de Finalización --}}
@if($solicitud && in_array($solicitud->estado_solicitud, ['SOLICITADA', 'APROBADA', 'FINALIZADA']))
<div id="modalSubirCartaFinalizacion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-xl font-bold">Subir Carta de Finalización</h2>
            <button onclick="document.getElementById('modalSubirCartaFinalizacion').classList.add('hidden')" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('estudiantes.documentos.guardar') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <input type="hidden" name="solicitud_pps_id" value="{{ $solicitud->id }}">
            <input type="hidden" name="tipo" value="carta_finalizacion">
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-2">Requisitos importantes:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>La carta debe estar firmada por tu jefe inmediato</li>
                            <li>Debe indicar la fecha de finalización de la práctica</li>
                            <li>Formato PDF (máximo 5MB)</li>
                        </ul>
                        
                        {{-- Botón para descargar formato --}}
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <a href="{{ asset('formatos/carta_finalizacion.docx') }}" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Descargar Formato de Carta
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALERTA IMPORTANTE EN EL MODAL --}}
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-amber-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-amber-800">
                        <p class="font-bold mb-2">IMPORTANTE - Entrega Física Requerida</p>
                        <p class="leading-relaxed">
                            La carta es válida <strong>hasta que se haya enviado físicamente</strong> a la coordinación de la carrera de Informática Administrativa.
                        </p>
                        <p class="mt-2 text-xs text-amber-700">
                            Esta carga digital es solo para fines de seguimiento y registro en el sistema.
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Archivo de la Carta <span class="text-red-600">*</span>
                </label>
                <input type="file" name="archivo" accept=".pdf" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-600 mt-2">Solo archivos PDF, máximo 5MB</p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('modalSubirCartaFinalizacion').classList.add('hidden')"
                       
                        class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition shadow-lg">
                   
                    Subir Carta
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Cerrar modal al hacer clic fuera --}}
<script>
document.getElementById('modalSubirCartaFinalizacion')?.addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>

@endsection