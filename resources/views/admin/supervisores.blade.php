@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-4 sm:py-6 lg:py-8 px-3 sm:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

        {{-- ============================================
             SECCIÓN 1: HEADER Y CONTADORES
             ============================================ --}}
        <div class="flex flex-col gap-3 sm:gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-unahblue">Gestión de Supervisores</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Administra los supervisores y su capacidad de estudiantes</p>
                </div>
                <button onclick="abrirModalCrear()" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg text-sm sm:text-base flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Supervisor
                </button>
            </div>
            
            {{-- Contadores --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="px-4 py-3 bg-blue-100 rounded-xl border-2 border-blue-200">
                    <p class="text-xs text-blue-600 font-semibold uppercase">Total</p>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-700">{{ $contadores['total'] }}</p>
                </div>
                <div class="px-4 py-3 bg-green-100 rounded-xl border-2 border-green-200">
                    <p class="text-xs text-green-600 font-semibold uppercase">Activos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-green-700">{{ $contadores['activos'] }}</p>
                </div>
                <div class="px-4 py-3 bg-red-100 rounded-xl border-2 border-red-200">
                    <p class="text-xs text-red-600 font-semibold uppercase">Inactivos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-red-700">{{ $contadores['inactivos'] }}</p>
                </div>
                <div class="px-4 py-3 bg-yellow-100 rounded-xl border-2 border-yellow-200">
                    <p class="text-xs text-yellow-600 font-semibold uppercase">Cupo Lleno</p>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-700">{{ $contadores['llenos'] }}</p>
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
        <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4">
            <form method="GET" action="{{ route('admin.supervisores.index') }}" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre o correo..."
                    class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                
                <select name="estado" class="px-3 sm:px-4 py-2 text-sm sm:text-base border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Buscar
                    </button>
                    @if(request('busqueda') || request('estado'))
                        <a href="{{ route('admin.supervisores.index') }}" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-sm sm:text-base text-center flex items-center justify-center gap-2">
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
             SECCIÓN 4: TABLA DE SUPERVISORES
             ============================================ --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($supervisores->count() > 0)
                
                {{-- DESKTOP: Tabla completa --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Supervisor</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Capacidad</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Asignados</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Disponibles</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($supervisores as $supervisor)
                                @php
                                    $asignados = $supervisor->estudiantes_asignados ?? 0;
                                    $disponibles = $supervisor->max_estudiantes - $asignados;
                                    $porcentaje = $supervisor->max_estudiantes > 0 ? round(($asignados / $supervisor->max_estudiantes) * 100) : 0;
                                    $colorBarra = $porcentaje >= 90 ? 'bg-red-500' : ($porcentaje >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                                {{ strtoupper(substr($supervisor->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $supervisor->user->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $supervisor->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <p class="text-lg font-bold text-gray-900">{{ $supervisor->max_estudiantes }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <p class="text-lg font-bold text-blue-700">{{ $asignados }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <p class="text-lg font-bold {{ $disponibles > 0 ? 'text-green-700' : 'text-red-700' }}">
                                                {{ $disponibles }}
                                            </p>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="{{ $colorBarra }} h-2 rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-600">{{ $porcentaje }}%</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $supervisor->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $supervisor->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="verDetalle({{ $supervisor->id }})" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold flex items-center gap-1" title="Ver detalle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Ver
                                            </button>
                                            <button onclick="abrirModalEditar({{ $supervisor->id }})" class="px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-sm font-semibold flex items-center gap-1" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Editar
                                            </button>
                                            <form method="POST" action="{{ route('admin.supervisores.toggle', $supervisor->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 {{ $supervisor->activo ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-lg transition text-sm font-semibold flex items-center gap-1" 
                                                        onclick="return confirm('¿Estás seguro de {{ $supervisor->activo ? 'desactivar' : 'activar' }} este supervisor?')">
                                                    @if($supervisor->activo)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                        Desactivar
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Activar
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE: Cards --}}
                <div class="lg:hidden divide-y divide-gray-200">
                    @foreach($supervisores as $supervisor)
                        @php
                            $asignados = $supervisor->estudiantes_asignados ?? 0;
                            $disponibles = $supervisor->max_estudiantes - $asignados;
                            $porcentaje = $supervisor->max_estudiantes > 0 ? round(($asignados / $supervisor->max_estudiantes) * 100) : 0;
                            $colorBarra = $porcentaje >= 90 ? 'bg-red-500' : ($porcentaje >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ strtoupper(substr($supervisor->user->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 truncate">{{ $supervisor->user->name }}</p>
                                        <p class="text-sm text-gray-600 truncate">{{ $supervisor->user->email }}</p>
                                    </div>
                                </div>
                                <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $supervisor->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} flex-shrink-0">
                                    {{ $supervisor->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <div class="p-2 bg-blue-50 rounded-lg text-center">
                                    <p class="text-xs text-blue-600 font-semibold">Capacidad</p>
                                    <p class="text-lg font-bold text-blue-800">{{ $supervisor->max_estudiantes }}</p>
                                </div>
                                <div class="p-2 bg-purple-50 rounded-lg text-center">
                                    <p class="text-xs text-purple-600 font-semibold">Asignados</p>
                                    <p class="text-lg font-bold text-purple-800">{{ $asignados }}</p>
                                </div>
                                <div class="p-2 bg-green-50 rounded-lg text-center">
                                    <p class="text-xs text-green-600 font-semibold">Disponibles</p>
                                    <p class="text-lg font-bold text-green-800">{{ $disponibles }}</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-600 font-semibold">Ocupación</span>
                                    <span class="text-xs text-gray-600 font-bold">{{ $porcentaje }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $colorBarra }} h-2 rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="verDetalle({{ $supervisor->id }})" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </button>
                                    <button onclick="abrirModalEditar({{ $supervisor->id }})" class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-sm font-semibold flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('admin.supervisores.toggle', $supervisor->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full px-3 py-2 {{ $supervisor->activo ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-lg transition text-sm font-semibold flex items-center justify-center gap-1" 
                                            onclick="return confirm('¿Estás seguro?')">
                                        @if($supervisor->activo)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Desactivar
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Activar
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-gray-600 font-semibold">No hay supervisores registrados</p>
                    <p class="text-gray-500 text-sm mt-1">Comienza agregando un nuevo supervisor</p>
                    <button onclick="abrirModalCrear()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center gap-2 mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear Supervisor
                    </button>
                </div>
            @endif
        </div>
               {{-- Paginación --}}
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $supervisores->links() }}
                    </div>
                </div>
    </div>
</div>

{{-- Modales --}}
@include('admin.supervisores.modales')

{{-- Scripts --}}
@include('admin.supervisores.scripts')

@endsection