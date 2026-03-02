@extends('layouts.estudiantes')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-unahblue">Actualizar Datos</h1>
                <p class="text-gray-600 mt-1">Solicita cambios en tu información personal</p>
            </div>
            <a href="{{ route('estudiantes.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border-2 border-gray-300 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>

        {{-- ALERTAS --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div class="flex-1">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-red-800 font-medium">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- GRID DE 2 COLUMNAS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLUMNA IZQUIERDA: FORMULARIO --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARD: INFORMACIÓN IMPORTANTE --}}
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold mb-2">Información Importante</h3>
                            <ul class="space-y-1 text-sm text-white/90">
                                <li>• Los cambios solicitados requieren aprobación del administrador</li>
                                <li>• Debes adjuntar un documento PDF que respalde tu solicitud</li>
                                <li>• Recibirás una notificación cuando tu solicitud sea revisada</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- CARD: FORMULARIO --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-blue-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Nueva Solicitud
                        </h2>
                        <p class="text-gray-600 mt-1">Completa el formulario para solicitar cambios en tus datos</p>
                    </div>

                    <form id="updateForm" action="{{ route('estudiantes.actualizacion.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Motivo/Descripción --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">
                                Descripción de los cambios
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="motivo"
                                rows="6"
                                class="w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-gray-800 placeholder-gray-400
                                       focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors"
                                placeholder="Explica detalladamente qué datos deseas actualizar y por qué...&#10;&#10;Ejemplo:&#10;- Cambio de número de teléfono del jefe&#10;- Nueva dirección de trabajo&#10;- Actualización de correo electrónico"
                                required>{{ old('motivo') }}</textarea>
                            <p class="mt-2 text-sm text-gray-600">
                                Sé específico sobre qué información quieres cambiar
                            </p>
                            @error('motivo')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Documento de respaldo --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">
                                Documento de respaldo (PDF)
                                <span class="text-red-500">*</span>
                            </label>
                            
                            {{-- Área de subida --}}
                            <div id="uploadArea" class="relative">
                                <input
                                    type="file"
                                    name="archivo"
                                    accept="application/pdf"
                                    id="archivo"
                                    class="hidden"
                                    required
                                    onchange="handleFileSelect(this)">
                                
                                <label for="archivo" 
                                       id="uploadLabel"
                                       class="flex items-center justify-center gap-3 w-full px-6 py-8 border-2 border-dashed border-blue-300 rounded-xl bg-blue-50 hover:bg-blue-100 cursor-pointer transition-colors">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div class="text-center">
                                        <p class="text-sm font-semibold text-gray-800">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                                        <p class="text-xs text-gray-600 mt-1">Formato: PDF • Tamaño máximo: 10 MB</p>
                                    </div>
                                </label>
                            </div>

                            {{-- Preview del archivo --}}
                            <div id="filePreview" class="hidden mt-3 p-4 bg-green-50 border-2 border-green-500 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-green-800 truncate" id="fileNameDisplay"></p>
                                            <p class="text-xs text-green-600" id="fileSizeDisplay"></p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="removeFile()"
                                            class="ml-3 w-8 h-8 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200 text-red-600 transition flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <p class="mt-2 text-sm text-gray-600">
                                 Ejemplos: Constancia laboral, constancia de aceptación, etc.
                            </p>

                            @error('archivo')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Botón de envío --}}
                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg hover:shadow-xl transform hover:scale-105">
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- COLUMNA DERECHA: INFORMACIÓN Y AYUDA --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- CARD: QUÉ PUEDES ACTUALIZAR --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-blue-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Datos que puedes actualizar
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start gap-2">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span>Número de teléfono</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span>Correo electrónico</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span>Dirección de trabajo</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span>Información de contacto del jefe</span>
                        </li>
                        <li class="flex items-start gap-2 pt-3 border-t border-gray-200">
                            <span class="text-red-500 mt-0.5">✗</span>
                            <span class="text-gray-500">Número de cuenta</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 mt-0.5">✗</span>
                            <span class="text-gray-500">Nombre completo</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 mt-0.5">✗</span>
                            <span class="text-gray-500">Número de identidad</span>
                        </li>
                    </ul>
                </div>

                {{-- CARD: PROCESO --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-blue-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Proceso de actualización
                    </h3>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 text-blue-600 font-bold text-sm">
                                1
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Envías tu solicitud</p>
                                <p class="text-xs text-gray-600 mt-0.5">Con documento de respaldo</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 text-yellow-600 font-bold text-sm">
                                2
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Revisión pendiente</p>
                                <p class="text-xs text-gray-600 mt-0.5">El admin valida tu solicitud</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 text-green-600 font-bold text-sm">
                                3
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Aprobación</p>
                                <p class="text-xs text-gray-600 mt-0.5">Tus datos se actualizan</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD: AYUDA --}}
                <div class="bg-yellow-50 rounded-2xl shadow-xl p-6 border border-yellow-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ¿Necesitas ayuda?
                    </h3>
                    <p class="text-sm text-gray-700 mb-3">
                        Si tienes dudas sobre el proceso o necesitas asistencia, contacta al departamento de vinculación.
                    </p>
                    <a href="mailto:uvinculacion.dia@unah.edu.hn" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Enviar correo
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>

{{-- Scripts --}}
<script>
// Manejo de selección de archivo
function handleFileSelect(input) {
    const uploadLabel = document.getElementById('uploadLabel');
    const filePreview = document.getElementById('filePreview');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const fileSizeDisplay = document.getElementById('fileSizeDisplay');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validar tamaño (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('El archivo excede el tamaño máximo de 10 MB');
            input.value = '';
            return;
        }
        
        // Validar tipo
        if (file.type !== 'application/pdf') {
            alert('Solo se permiten archivos PDF');
            input.value = '';
            return;
        }
        
        // Mostrar preview
        uploadLabel.classList.add('hidden');
        filePreview.classList.remove('hidden');
        fileNameDisplay.textContent = file.name;
        fileSizeDisplay.textContent = `Tamaño: ${(file.size / 1024).toFixed(2)} KB`;
    }
}

// Remover archivo seleccionado
function removeFile() {
    const input = document.getElementById('archivo');
    const uploadLabel = document.getElementById('uploadLabel');
    const filePreview = document.getElementById('filePreview');
    
    input.value = '';
    uploadLabel.classList.remove('hidden');
    filePreview.classList.add('hidden');
}

// Configurar Drag & Drop
document.addEventListener('DOMContentLoaded', function () {
    const uploadLabel = document.getElementById('uploadLabel');
    const fileInput = document.getElementById('archivo');
    
    if (!uploadLabel || !fileInput) return;

    // Prevenir comportamiento por defecto
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadLabel.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Resaltar zona de drop
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadLabel.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadLabel.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        uploadLabel.classList.add('border-blue-500', 'bg-blue-100', 'scale-105');
    }

    function unhighlight(e) {
        uploadLabel.classList.remove('border-blue-500', 'bg-blue-100', 'scale-105');
    }

    // Manejar archivos arrastrados
    uploadLabel.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(fileInput);
        }
    }
});
</script>
@endsection