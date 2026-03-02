@extends('layouts.admin')

@section('content')
<div class="min-h-screen b-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header mejorado --}}
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <div>
                    <h1 class="text-3xl font-bold text-unahblue">Solicitudes de Actualización</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Gestiona las solicitudes de cambio de datos de los estudiantes
                    </p>
                </div>
            </div>
        </div>

        {{-- Alertas mejoradas --}}
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Barra de búsqueda mejorada --}}
        <div class="mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <form method="GET" action="{{ route('admin.solicitudes.actualizacion') }}" class="flex gap-3 flex-col sm:flex-row">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   name="busqueda" 
                                   value="{{ request('busqueda') }}"
                                   placeholder="Buscar por nombre o correo electrónico..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:shadow-md hover:from-blue-700 hover:to-blue-800 transition-all font-medium flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Buscar
                    </button>
                    @if(request('busqueda'))
                        <a href="{{ route('admin.solicitudes.actualizacion') }}" 
                           class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Tabs mejorados --}}
        <div class="mb-6">
            <div class="flex gap-2 border-b border-gray-200 overflow-x-auto">
                <a href="{{ route('admin.solicitudes.actualizacion') }}" 
                   class="py-3 px-4 border-b-2 font-medium text-sm transition-all whitespace-nowrap {{ !request('estado') ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Todas
                        <span class="ml-2 bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs font-semibold">
                            {{ $contadores['total'] }}
                        </span>
                    </div>
                </a>
                <a href="{{ route('admin.solicitudes.actualizacion', ['estado' => 'PENDIENTE']) }}" 
                   class="py-3 px-4 border-b-2 font-medium text-sm transition-all whitespace-nowrap {{ request('estado') === 'PENDIENTE' ? 'border-yellow-600 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pendientes
                        <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2 rounded-full text-xs font-semibold">
                            {{ $contadores['pendientes'] }}
                        </span>
                    </div>
                </a>
                <a href="{{ route('admin.solicitudes.actualizacion', ['estado' => 'APROBADA']) }}" 
                   class="py-3 px-4 border-b-2 font-medium text-sm transition-all whitespace-nowrap {{ request('estado') === 'APROBADA' ? 'border-green-600 text-green-600 bg-green-50' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aprobadas
                        <span class="ml-2 bg-green-100 text-green-800 py-0.5 px-2 rounded-full text-xs font-semibold">
                            {{ $contadores['aprobadas'] }}
                        </span>
                    </div>
                </a>
                <a href="{{ route('admin.solicitudes.actualizacion', ['estado' => 'RECHAZADA']) }}" 
                   class="py-3 px-4 border-b-2 font-medium text-sm transition-all whitespace-nowrap {{ request('estado') === 'RECHAZADA' ? 'border-red-600 text-red-600 bg-red-50' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m2-2l2 2"/>
                        </svg>
                        Rechazadas
                        <span class="ml-2 bg-red-100 text-red-800 py-0.5 px-2 rounded-full text-xs font-semibold">
                            {{ $contadores['rechazadas'] }}
                        </span>
                    </div>
                </a>
            </div>
        </div>

        {{-- Lista de solicitudes --}}
        @if($solicitudes->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay solicitudes</h3>
                <p class="text-gray-600">
                    @if(request('busqueda'))
                        No se encontraron resultados para "<strong>{{ request('busqueda') }}</strong>".
                    @else
                        Aún no se han enviado solicitudes de actualización.
                    @endif
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($solicitudes as $solicitud)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row items-start justify-between gap-6">
                                
                                {{-- Info del estudiante (Lado izquierdo) --}}
                                <div class="flex-1 min-w-0 w-full lg:w-auto">
                                    <div class="flex items-start gap-4 mb-5">
                                        {{-- Avatar --}}
                                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <span class="text-lg font-bold text-white">
                                                {{ strtoupper(substr($solicitud->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        
                                        {{-- Datos del estudiante --}}
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-base font-semibold text-gray-900 truncate">{{ $solicitud->user->name }}</h3>
                                            <p class="text-sm text-gray-600 truncate">{{ $solicitud->user->email }}</p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Solicitud #{{ $solicitud->id }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Motivo --}}
                                    <div class="mb-4">
                                        <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2 block">Motivo del cambio</label>
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                            <p class="text-sm text-gray-700 break-words leading-relaxed">{{ $solicitud->motivo }}</p>
                                        </div>
                                    </div>

                                    {{-- Observación (si existe) --}}
                                    @if($solicitud->observacion)
                                        <div class="mb-4">
                                            <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2 block">Motivo del rechazo</label>
                                            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                                <p class="text-sm text-blue-900 break-words leading-relaxed">{{ $solicitud->observacion }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Metadata --}}
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($solicitud->updated_at !== $solicitud->created_at)
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Act: {{ $solicitud->updated_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones (Lado derecho) --}}
                                <div class="w-full lg:w-auto flex flex-col items-start lg:items-end gap-4 flex-shrink-0 lg:pl-6 border-t lg:border-t-0 lg:border-l border-gray-200 pt-5 lg:pt-0 lg:pl-6">
                                    
                                    {{-- Badge de estado --}}
                                    <div class="w-full lg:w-auto">
                                        @if($solicitud->estado === 'PENDIENTE')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z"/>
                                                </svg>
                                                Pendiente
                                            </span>
                                        @elseif($solicitud->estado === 'APROBADA')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Aprobada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Rechazada
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Botón Ver archivo --}}
                                    @if($solicitud->archivo)
                                        <a href="{{ route('admin.actualizacion.ver-archivo', $solicitud->id) }}" 
                                           target="_blank"
                                           class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver archivo
                                        </a>
                                    @endif

                                    {{-- Botones de acción (solo si está pendiente) --}}
                                    @if($solicitud->estado === 'PENDIENTE')
                                        <div class="w-full lg:w-auto flex gap-2 flex-col lg:flex-row">
                                            <button onclick="openModal({{ $solicitud->id }}, 'APROBADA')" 
                                                    class="flex-1 lg:flex-none px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:shadow-md hover:from-green-700 hover:to-green-800 transition-all">
                                                <span class="flex items-center justify-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Aprobar
                                                </span>
                                            </button>
                                            <button onclick="openModal({{ $solicitud->id }}, 'RECHAZADA')" 
                                                    class="flex-1 lg:flex-none px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:shadow-md hover:from-red-700 hover:to-red-800 transition-all">
                                                <span class="flex items-center justify-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Rechazar
                                                </span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación mejorada --}}
            <div class="mt-8">
                <div class="flex justify-center">
                    {{ $solicitudes->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal mejorado de confirmación --}}
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all">
        
        {{-- Header del modal con fondo de color --}}
        <div class="h-16 bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl flex items-center justify-between px-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white bg-opacity-30 rounded-lg flex items-center justify-center">
                    <svg id="headerIcon" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-white" id="modalTitle">Confirmar acción</h2>
            </div>

            <button onclick="closeModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body del modal --}}
        <div class="p-8">
            
            {{-- Formulario --}}
            <form id="confirmForm" method="POST" action="">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="estado" id="modalEstado">
                
                {{-- Campo de observación --}}
                <div class="mb-6">
                    <label for="observacion" class="block text-sm font-semibold text-gray-900 mb-2">
                        Nota o comentario <span id="requiredLabel" class="text-red-500 font-bold hidden">(obligatorio)</span><span id="optionalLabel" class="text-gray-500 font-normal">(opcional)</span>
                    </label>
                    <textarea name="observacion" 
                              id="observacion" 
                              rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none"
                              placeholder="Agrega una nota adicional..."></textarea>
                    <p id="helpText" class="mt-2 text-xs text-gray-600">
                        Puedes añadir una observación que verá el estudiante
                    </p>
                </div>

                {{-- Botones de acción --}}
                <div class="flex gap-3 justify-end flex-col sm:flex-row">
                    <button type="button" 
                            onclick="closeModal()" 
                            class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            id="modalSubmit"
                            class="w-full sm:w-auto px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:shadow-lg hover:from-green-700 hover:to-green-800 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span id="submitText">Confirmar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(solicitudId, estado) {
    const modal = document.getElementById('confirmModal');
    const form = document.getElementById('confirmForm');
    const title = document.getElementById('modalTitle');
    const estadoInput = document.getElementById('modalEstado');
    const submitBtn = document.getElementById('modalSubmit');
    const submitText = document.getElementById('submitText');
    const observacionField = document.getElementById('observacion');
    const requiredLabel = document.getElementById('requiredLabel');
    const optionalLabel = document.getElementById('optionalLabel');
    const helpText = document.getElementById('helpText');
    
    form.action = `/admin/actualizacion/${solicitudId}`;
    estadoInput.value = estado;
    
    if (estado === 'APROBADA') {
        // Configurar modal para APROBAR
        title.textContent = 'Aprobar solicitud de actualización';
        
        // Header - Mantener verde
        const header = modal.querySelector('.h-16');
        header.className = 'h-16 bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl flex items-center justify-between px-6 relative overflow-hidden';
        
        // Comentario opcional
        observacionField.required = false;
        observacionField.placeholder = 'Agrega una nota adicional...';
        observacionField.className = 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none';
        
        requiredLabel.classList.add('hidden');
        optionalLabel.classList.remove('hidden');
        helpText.textContent = 'Puedes añadir una observación que verá el estudiante';
        
        submitBtn.className = 'px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:shadow-lg hover:from-green-700 hover:to-green-800 transition-all flex items-center gap-2';
        submitText.textContent = 'Aprobar Solicitud';
        
    } else {
        // Configurar modal para RECHAZAR
        title.textContent = 'Rechazar solicitud de actualización';
        
        // Header - Cambiar a rojo
        const header = modal.querySelector('.h-16');
        header.className = 'h-16 bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl flex items-center justify-between px-6 relative overflow-hidden';
        
        // Comentario OBLIGATORIO
        observacionField.required = true;
        observacionField.placeholder = 'Explica el motivo del rechazo...';
        observacionField.className = 'w-full px-4 py-3 border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all resize-none';
        
        requiredLabel.classList.remove('hidden');
        optionalLabel.classList.add('hidden');
        helpText.textContent = 'El estudiante verá este motivo en su dashboard';
        
        submitBtn.className = 'px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:shadow-lg hover:from-red-700 hover:to-red-800 transition-all flex items-center gap-2';
        submitText.textContent = 'Rechazar Solicitud';
    }
    
    // Limpiar campo de observación
    document.getElementById('observacion').value = '';
    
    // Mostrar modal
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('hidden');
    document.getElementById('observacion').value = '';
}

// Cerrar modal al hacer click fuera
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Cerrar modal al presionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection




