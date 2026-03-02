@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-4 sm:py-6 lg:py-8 px-3 sm:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        {{-- ============================================
             SECCIÓN 1: HEADER Y CONTADORES
             ============================================ --}}
        <div class="flex flex-col gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-unahblue">Solicitudes Pendientes</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Gestiona las solicitudes de práctica profesional</p>
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
                    <div class="flex-1">
                        <p class="text-red-800 font-semibold text-sm sm:text-base mb-1">Error al procesar la solicitud</p>
                        <p class="text-red-700 text-xs sm:text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-3 sm:p-4 rounded-lg shadow-md animate-fade-in">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-red-800 font-semibold text-sm sm:text-base mb-2">Se encontraron los siguientes errores:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-red-700 text-xs sm:text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================
             SECCIÓN 3: BUSCADOR
             ============================================ --}}
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4">
            <form method="GET" action="{{ route('admin.solicitudes.pendientes') }}" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre, correo o cuenta..."
                    class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm sm:text-base">
                        Buscar
                    </button>
                    @if(request('busqueda'))
                        <a href="{{ route('admin.solicitudes.pendientes') }}" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-sm sm:text-base text-center">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ============================================
             SECCIÓN 4: TABLA DE SOLICITUDES
             ============================================ --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($solicitudes->count() > 0)
                
                {{-- DESKTOP: Tabla completa --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Estudiante</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Tipo</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Documentos</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Fecha</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($solicitudes as $solicitud)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $solicitud->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $solicitud->user->email }}</p>
                                        <p class="text-xs text-gray-500">{{ $solicitud->numero_cuenta }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $solicitud->tipo_practica === 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($solicitud->tipo_practica) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded-lg text-sm">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $solicitud->documentos->count() }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">{{ $solicitud->created_at->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $solicitud->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="verDetalle({{ $solicitud->id }})" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold">Ver</button>
                                            <button onclick="abrirModalAprobar({{ $solicitud->id }})" class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-semibold">Aprobar</button>
                                            <button onclick="abrirModalRechazar({{ $solicitud->id }})" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold">Rechazar</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE: Cards --}}
                <div class="lg:hidden divide-y divide-gray-200">
                    @foreach($solicitudes as $solicitud)
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 truncate">{{ $solicitud->user->name }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ $solicitud->user->email }}</p>
                                    <p class="text-xs text-gray-500">{{ $solicitud->numero_cuenta }}</p>
                                </div>
                                <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full flex-shrink-0 {{ $solicitud->tipo_practica === 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($solicitud->tipo_practica) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm mb-3">
                                <div class="flex items-center gap-1 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold">{{ $solicitud->documentos->count() }} docs</span>
                                </div>
                                <span class="text-gray-500 text-xs">{{ $solicitud->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <button onclick="verDetalle({{ $solicitud->id }})" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold">Ver</button>
                                <button onclick="abrirModalAprobar({{ $solicitud->id }})" class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-semibold">✓</button>
                                <button onclick="abrirModalRechazar({{ $solicitud->id }})" class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold">✕</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $solicitudes->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600 font-semibold">No hay solicitudes pendientes</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- MODALES (Ver detalle, Aprobar, Rechazar)--}}
@include('admin.solicitudes.modals')

{{--  JAVASCRIPT--}}
@include('admin.solicitudes.scripts')

@endsection