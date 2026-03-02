@extends('layouts.admin')

@section('content')
<div class="min-h-screen py-4 sm:py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        {{-- Botón volver --}}
        <div class="mb-6">
            <a href="{{ route('admin.formatos.index') }}" 
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Volver a Formatos</span>
            </a>
        </div>

        {{-- Layout de 2 columnas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- COLUMNA IZQUIERDA: Formulario --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                    
                    {{-- Header --}}
                    <div class="mb-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-3 mb-2">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Formato
                        </h1>
                        <p class="text-sm sm:text-base text-gray-600">Modifica el nombre, archivo o visibilidad del formato</p>
                    </div>

                    {{-- Mensajes --}}
                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-6">
                            <p class="text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6">
                            <p class="text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6">
                            <p class="text-sm font-semibold text-red-800 mb-1">Hay errores:</p>
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Formulario --}}
                    <form action="{{ route('admin.formatos.update', $formato->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Nombre del formato --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nombre del formato
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Ej: Formulario PPS-IA-01"
                                   value="{{ old('nombre', $formato->nombre) }}"
                                   required>
                            @error('nombre')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Archivo actual y opción de reemplazo --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Archivo actual
                            </label>
                            
                            {{-- Info del archivo actual --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $extension = pathinfo($formato->ruta, PATHINFO_EXTENSION);
                                            $isPdf = $extension === 'pdf';
                                        @endphp
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-lg {{ $isPdf ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ strtoupper($extension) }}
                                        </span>
                                        <span class="text-sm text-gray-700 font-medium">{{ $formato->nombre }}.{{ $extension }}</span>
                                    </div>
                                    
                                    @if(file_exists(public_path($formato->ruta)))
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            No encontrado
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Opción para subir nuevo archivo --}}
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Reemplazar archivo (opcional)
                            </label>
                            
                            <div class="relative">
                                <input type="file" 
                                       name="archivo" 
                                       id="archivo"
                                       accept=".pdf,.doc,.docx"
                                       class="hidden">
                                
                                <label for="archivo" 
                                       class="flex flex-col items-center justify-center w-full py-8 px-6 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-600 mb-1">
                                        <span class="text-blue-600 font-semibold">Click aquí</span> para seleccionar un nuevo archivo
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        PDF, DOCX o DOC • Máximo 10 MB
                                    </p>
                                </label>

                                <div id="file-name" class="hidden mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-blue-800 font-medium">Nuevo archivo: <span id="file-name-text"></span></span>
                                    </div>
                                </div>
                            </div>

                            @error('archivo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Visible para estudiantes --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="visible" 
                                       value="1" 
                                       {{ old('visible', $formato->visible) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mt-0.5 flex-shrink-0">
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">Visible para usuarios</span>
                                    <p class="text-xs text-gray-600 mt-1">El formato estará disponible para el tipo de usuario seleccionado</p>
                                </div>
                            </label>
                        </div>

                        {{-- NUEVO: Tipo de destinatario --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Destinatario del formato
                                <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Opción Estudiantes --}}
                                <label class="relative flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                                    <input type="radio" 
                                           name="tipo_destinatario" 
                                           value="estudiante" 
                                           {{ old('tipo_destinatario', $formato->tipo_destinatario) === 'estudiante' ? 'checked' : '' }}
                                           class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2 mt-0.5 flex-shrink-0">
                                    <div class="ml-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span class="text-sm font-bold text-gray-800">Estudiantes</span>
                                        </div>
                                        <p class="text-xs text-gray-600">Los estudiantes podrán ver y descargar este formato</p>
                                    </div>
                                </label>

                                {{-- Opción Supervisores --}}
                                <label class="relative flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                    <input type="radio" 
                                           name="tipo_destinatario" 
                                           value="supervisor"
                                           {{ old('tipo_destinatario', $formato->tipo_destinatario) === 'supervisor' ? 'checked' : '' }}
                                           class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 focus:ring-2 mt-0.5 flex-shrink-0">
                                    <div class="ml-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            <span class="text-sm font-bold text-gray-800">Supervisores</span>
                                        </div>
                                        <p class="text-xs text-gray-600">Los supervisores podrán ver y descargar este formato</p>
                                    </div>
                                </label>
                            </div>
                            
                            @error('tipo_destinatario')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                            <button type="submit"
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Guardar Cambios
                            </button>
                            <a href="{{ route('admin.formatos.index') }}"
                               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </a>
                        </div>

                    </form>

                </div>
            </div>

            {{-- COLUMNA DERECHA: Consejos --}}
            <div class="lg:col-span-1">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 top-6">
                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <h3 class="text-base font-bold text-yellow-900">Consejos</h3>
                    </div>
                    
                    <ul class="space-y-3 text-sm text-yellow-800">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Si subes un nuevo archivo, el anterior será <strong>eliminado permanentemente</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Puedes cambiar el <strong>nombre</strong> sin necesidad de resubir el archivo</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Si desactivas <strong>"Visible"</strong>, los estudiantes no podrán verlo</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- JavaScript para mostrar nombre del archivo --}}
<script>
document.getElementById('archivo').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    const fileNameContainer = document.getElementById('file-name');
    const fileNameText = document.getElementById('file-name-text');
    
    if (fileName) {
        fileNameText.textContent = fileName;
        fileNameContainer.classList.remove('hidden');
    } else {
        fileNameContainer.classList.add('hidden');
    }
});
</script>
@endsection