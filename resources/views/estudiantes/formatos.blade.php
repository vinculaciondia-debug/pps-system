@extends('layouts.estudiantes')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-unahblue flex items-center gap-3">
                    Formatos PPS
                </h1>
                <p class="text-gray-600 mt-1">Descarga los documentos necesarios para tu práctica profesional</p>
            </div>
        </div>

        {{-- BANNER INFORMATIVO --}}
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold mb-2">Acerca de estos formatos</h3>
                    <p class="text-sm text-white/90 mb-3">
                        Estos documentos son necesarios durante tu práctica profesional supervisada. Descárgalos, llénalos y entrégalos según las instrucciones.
                    </p>
                    <ul class="space-y-1 text-sm text-white/90">
                        <li>• Los archivos están en formato PDF o Word</li>
                        <li>• Puedes visualizarlos en línea antes de descargar</li>
                        <li>• Imprímelos y llénalos a mano o edítalos digitalmente</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- CONTENIDO PRINCIPAL --}}
        @if(isset($formatos) && count($formatos) > 0)
            {{-- ESTADÍSTICAS RÁPIDAS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Formatos</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ count($formatos) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-green-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Disponibles</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ count($formatos) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-purple-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Formatos</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">PDF/Word</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GRID DE FORMATOS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($formatos as $formato)
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:scale-105 group">
                        {{-- Icono y Número --}}
                        <div class="flex items-center justify-between mb-4">
                            @php
                                $extension = pathinfo($formato->ruta, PATHINFO_EXTENSION);
                                $bgColor = 'from-blue-100 to-indigo-100';
                                $iconColor = 'text-blue-600';
                                
                                if (in_array(strtolower($extension), ['doc', 'docx'])) {
                                    $bgColor = 'from-blue-100 to-indigo-100';
                                    $iconColor = 'text-blue-600';
                                } elseif (strtolower($extension) === 'pdf') {
                                    $bgColor = 'from-red-100 to-pink-100';
                                    $iconColor = 'text-red-600';
                                }
                            @endphp
                            <div class="w-14 h-14 bg-gradient-to-br {{ $bgColor }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                                #{{ $loop->iteration }}
                            </span>
                        </div>

                        {{-- Nombre del documento --}}
                        <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 min-h-[3.5rem]">
                            {{ $formato->nombre }}
                        </h3>

                        {{-- Información adicional --}}
                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                            @php
                                $iconColor = in_array(strtolower($extension), ['doc', 'docx']) ? 'text-blue-600' : 'text-red-600';
                                $formatoNombre = in_array(strtolower($extension), ['doc', 'docx']) ? 'Word' : 'PDF';
                            @endphp
                            <svg class="w-4 h-4 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                            </svg>
                            <span>Formato {{ $formatoNombre }}</span>
                        </div>

{{-- Botones de acción --}}
<div class="flex gap-2">
    @php
        $extension = pathinfo($formato->ruta, PATHINFO_EXTENSION);
    @endphp

    @if(in_array(strtolower($extension), ['doc', 'docx']))
        {{-- Solo descarga para Word --}}
        <a href="{{ route('estudiantes.formatos.download', $formato->id) }}" 
           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Descargar Word
        </a>
    @else
        {{-- Ver y descargar para PDF --}}
        <a href="{{ route('estudiantes.formatos.view', $formato->id) }}" 
           target="_blank"
           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver PDF
        </a>
        <a href="{{ route('estudiantes.formatos.download', $formato->id) }}" 
           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Descargar
        </a>
    @endif
</div>
                    </div>
                @endforeach
            </div>

            {{-- AYUDA ADICIONAL --}}
            <div class="bg-yellow-50 rounded-2xl shadow-xl p-6 border border-yellow-200">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 mb-2">Consejos importantes</h3>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Descarga todos los formatos antes de solicitar tu práctica</li>
                            <li>• Llena los documentos con tinta negra y letra legible en caso de hacerlo manualmente</li>
                            <li>• Consulta con el Área de Vinculación si tienes dudas sobre algún formato</li>
                            <li>• Guarda copias digitales de todos los documentos completados</li>
                        </ul>
                    </div>
                </div>
            </div>

        @else
            {{-- SIN FORMATOS DISPONIBLES --}}
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center border border-blue-100">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">No hay formatos disponibles</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Por el momento no hay documentos disponibles para descargar. 
                    Los formatos se publicarán próximamente.
                </p>
                <a href="{{ route('estudiantes.dashboard') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        @endif

    </div>
</div>
@endsection