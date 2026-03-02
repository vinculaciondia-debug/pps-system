@extends('layouts.supervisores')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8 px-3 sm:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-unahblue">Dashboard de Supervisor</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Bienvenido, {{ Auth::user()->name }}</p>
        </div>

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold uppercase">Total Asignados</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $totalAsignados }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold uppercase">Aprobadas</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $aprobadas }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold uppercase">Finalizadas</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $finalizadas }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

{{-- Información del Supervisor --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Tu Información
        </h3>
        <div class="space-y-3">
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Nombre</p>
                <p class="text-gray-900 font-semibold">{{ Auth::user()->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Correo</p>
                <p class="text-gray-900">{{ Auth::user()->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Capacidad Total</p>
                <p class="text-gray-900 font-bold">{{ $supervisor->max_estudiantes }} estudiantes</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Asignados Activos</p>
                <p class="text-gray-900 font-bold text-blue-600">{{ $totalAsignados }} estudiantes</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Cupos Disponibles</p>
                <p class="text-gray-900 font-bold {{ ($supervisor->max_estudiantes - $totalAsignados) > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $supervisor->max_estudiantes - $totalAsignados }} cupos
                </p>
            </div>
            <div class="pt-3 border-t border-gray-200">
                <p class="text-xs text-gray-600 font-semibold uppercase">Finalizadas</p>
                <p class="text-gray-500 font-semibold">{{ $finalizadas }} estudiantes</p>
            </div>
        </div>
    </div>
            {{-- Alumnos Recientes --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Alumnos Recientes
                </h3>
                @if($alumnosRecientes->count() > 0)
                    <div class="space-y-3">
                        @foreach($alumnosRecientes as $alumno)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($alumno->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $alumno->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $alumno->nombre_empresa }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('supervisor.alumnos.show', $alumno->id) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold">
                                    Ver
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('supervisor.alumnos.index') }}" class="block text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            Ver Todos los Alumnos
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-gray-600 font-semibold">No tienes alumnos asignados</p>
                        <p class="text-gray-500 text-sm">Cuando te asignen estudiantes, aparecerán aquí</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Acciones Rápidas --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Acciones Rápidas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                <a href="{{ route('supervisor.alumnos.index') }}" class="p-4 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Ver Alumnos</p>
                            <p class="text-sm text-gray-600">Gestionar asignados</p>
                        </div>
                    </div>
                </a>

                <!-- Nuevo acceso directo a Reportes -->
                <a href="{{ route('supervisor.reportes.index') }}" class="p-4 border-2 border-gray-200 rounded-xl hover:border-yellow-500 hover:bg-yellow-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Ver Reportes</p>
                            <p class="text-sm text-gray-600">Gestionar y aprobar</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection