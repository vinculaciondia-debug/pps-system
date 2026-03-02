{{-- resources/views/estudiantes/solicitud_cancelacion.blade.php --}}

@extends('layouts.estudiantes')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-unahblue">Cancelar Práctica</h1>
                <p class="text-gray-600 mt-1">Adjunta el justificante y confirma la cancelación</p>
            </div>

            @if(isset($solicitud))
                @php
                    $statusConfig = [
                        'SOLICITADA' => [
                            'bg' => 'bg-gradient-to-r from-yellow-400 to-orange-400',
                            'text' => 'text-white',
                            'label' => 'En Proceso'
                        ],
                        'APROBADA' => [
                            'bg' => 'bg-gradient-to-r from-green-400 to-emerald-500',
                            'text' => 'text-white',
                            'label' => 'Aprobada'
                        ],
                        'RECHAZADA' => [
                            'bg' => 'bg-gradient-to-r from-red-400 to-pink-500',
                            'text' => 'text-white',
                            'label' => 'Rechazada'
                        ],
                        'CANCELADA' => [
                            'bg' => 'bg-gradient-to-r from-gray-400 to-gray-500',
                            'text' => 'text-white',
                            'label' => 'Cancelada'
                        ],
                        'FINALIZADA' => [
                            'bg' => 'bg-gradient-to-r from-blue-500 to-indigo-600',
                            'text' => 'text-white',
                            'label' => 'Finalizada'
                        ],
                    ];
                    $status = $statusConfig[$solicitud->estado_solicitud] ?? $statusConfig['SOLICITADA'];
                @endphp

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">

                    @if(!empty($solicitud->created_at))
                        <span class="text-sm text-gray-600">
                            Creada: {{ \Illuminate\Support\Carbon::parse($solicitud->created_at)->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- MENSAJES DE ALERTA --}}
        @if (session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-md">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                    </svg>
                    <p class="text-yellow-800 font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('status') }}</p>
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
                            @foreach($errors->all() as $e)
                                <li class="text-red-800 font-medium">{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- CARD PRINCIPAL --}}
        <div class="bg-white rounded-2xl shadow-xl p-6  border border-blue-100">
            @if(!isset($solicitud))
                {{-- Sin solicitud --}}
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">No hay solicitud activa</h2>
                    <p class="text-gray-600 mb-8">Aún no tienes una solicitud registrada en el sistema.</p>
                    <a href="{{ route('estudiantes.solicitud') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transform transition hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Crear Nueva Solicitud</span>
                    </a>
                </div>
            @else
                {{-- Con solicitud --}}
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-gray-800">Solicitud #{{ $solicitud->id }}</h2>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                            {{ $solicitud->estado_solicitud }}
                        </span>
                    </div>
                    <p class="text-gray-600">Estado actual de tu práctica profesional</p>
                </div>

                @php
                    $cancelable = in_array($solicitud->estado_solicitud, ['SOLICITADA','APROBADA'], true);
                    $action = \Illuminate\Support\Facades\Route::has('estudiantes.cancelacion.store.con-id')
                        ? route('estudiantes.cancelacion.store.con-id', $solicitud->id)
                        : route('estudiantes.cancelacion.store');
                @endphp

                @if(!$cancelable)
                    {{-- No cancelable --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">No se puede cancelar</h3>
                        <p class="text-gray-700 mb-6">
                            La solicitud #{{ $solicitud->id }} está en estado <strong>{{ $solicitud->estado_solicitud }}</strong>,
                            por lo que no puede cancelarse desde aquí.
                        </p>
                        <a href="{{ route('estudiantes.dashboard') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver al Dashboard
                        </a>
                    </div>
                @else
                    {{-- Formulario de cancelación --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">Tu solicitud está en proceso</p>
                            <p class="text-sm text-gray-700">Si deseas cancelarla, completa el formulario indicando el motivo y adjunta el documento PDF requerido.</p>
                        </div>
                    </div>

                    <form id="cancelForm" method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Motivo --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">
                                Motivo de cancelación
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="motivo"
                                rows="5"
                                class="w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-gray-800 placeholder-gray-400
                                       focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors"
                                placeholder="Describe brevemente el motivo de la cancelación..."
                                required>{{ old('motivo') }}</textarea>
                            @error('motivo')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Archivo --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">
                                Adjuntar justificante (PDF)
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
                                        <p class="text-xs text-gray-600 mt-1">Formato: PDF • Tamaño máximo: 2 MB</p>
                                    </div>
                                </label>
                            </div>

                            {{-- Preview del archivo (oculto inicialmente) --}}
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

                            @error('archivo')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <a href="{{ route('estudiantes.dashboard') }}"
                               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Volver al Panel
                            </a>

                            <button type="submit"
                                    id="btnConfirmarCancelacion"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Confirmar Cancelación
                            </button>
                        </div>
                    </form>
                @endif
            @endif
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
        
        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('El archivo excede el tamaño máximo de 2 MB');
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

    // Confirmación de cancelación
    const form = document.getElementById('cancelForm');
    const btn = document.getElementById('btnConfirmarCancelacion');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        // Crear modal de confirmación personalizado
        const modalHTML = `
            <div id="confirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all scale-95 opacity-0">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">¿Confirmar cancelación?</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">Esta acción ocultará tu solicitud del panel y podrás crear una nueva. ¿Estás seguro de continuar?</p>
                    <div class="flex gap-3">
                        <button onclick="closeModal()" 
                                class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                            No, volver
                        </button>
                        <button onclick="confirmCancel()" 
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition">
                            Sí, cancelar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Animar entrada
        setTimeout(() => {
            const modal = document.querySelector('#confirmModal > div');
            modal.classList.remove('scale-95', 'opacity-0');
            modal.classList.add('scale-100', 'opacity-100');
        }, 10);
    });

    // Función para cerrar modal
    window.closeModal = function() {
        const modal = document.getElementById('confirmModal');
        const inner = modal.querySelector('div');
        inner.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.remove(), 200);
    };

    // Función para confirmar
    window.confirmCancel = function() {
        closeModal();
        
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cancelando...
            `;
        }
        
        form.submit();
    };
});
</script>
@endsection