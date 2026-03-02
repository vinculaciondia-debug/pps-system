@extends('layouts.estudiantes')

@section('content')

@php
    $activa = isset($activa)
        ? $activa
        : (isset($solicitud) && $solicitud && in_array($solicitud->estado_solicitud, ['SOLICITADA','APROBADA']));
@endphp

@if($activa)
    {{-- MENSAJE DE SOLICITUD EN PROCESO --}}
    <div class="min-h-screen bg-gray-100 py-12 px-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-blue-100">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-center text-gray-800 mb-3">Solicitud Enviada</h1>
                <p class="text-center text-gray-600 mb-8">Tu solicitud está en proceso de revisión por el equipo administrativo.</p>
                
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                        </svg>
                        <p class="text-sm text-amber-700">Por favor espera la validación de tus documentos. Te notificaremos cuando haya actualizaciones.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('estudiantes.dashboard') }}" 
                       class="flex-1 text-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transform transition hover:scale-105 shadow-lg">
                        Ver Dashboard
                    </a>
                    @if(!empty($solicitud?->id))
                        <a href="{{ route('estudiantes.solicitudes.documentos', $solicitud->id) }}"
                           class="flex-1 text-center px-6 py-3 bg-white border-2 border-blue-600 text-blue-600 rounded-xl font-medium hover:bg-blue-50 transform transition hover:scale-105">
                            Ver Documentos
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    {{-- FORMULARIO MODERNO --}}
    <div class="min-h-screen bg-gray-100 py-12 px-4">
        <div class="max-w-5xl mx-auto">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-unahblue mb-3">Solicitud de Práctica Profesional</h1>
                <p class="text-gray-600">Completa todos los campos para enviar tu solicitud</p>
            </div>

        {{-- Progress Steps --}}
        <div class="mb-12">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-2 md:space-x-4">
                    <div class="flex items-center step-indicator" data-step="1">
                        <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full text-white font-bold shadow-lg step-circle border-4 border-white">
                            1
                        </div>
                        <span class="hidden md:inline ml-3 text-sm font-semibold text-gray-700 step-text">Tipo de Práctica</span>
                    </div>
                    <div class="w-6 md:w-24 h-1 bg-gray-300 step-line transition-all"></div>
                    <div class="flex items-center step-indicator" data-step="2">
                        <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-gray-300 rounded-full text-white font-bold step-circle border-4 border-white shadow">
                            2
                        </div>
                        <span class="hidden md:inline ml-3 text-sm font-semibold text-gray-400 step-text">Información</span>
                    </div>
                    <div class="w-6 md:w-24 h-1 bg-gray-300 step-line transition-all"></div>
                    <div class="flex items-center step-indicator" data-step="3">
                        <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-gray-300 rounded-full text-white font-bold step-circle border-4 border-white shadow">
                            3
                        </div>
                        <span class="hidden md:inline ml-3 text-sm font-semibold text-gray-400 step-text">Documentos</span>
                    </div>
                </div>
            </div>
        </div>

            {{-- Error Alert --}}
            <div id="errorAlert" class="hidden max-w-5xl mx-auto mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-red-800">Error en el formulario</p>
                        <p id="errorMessage" class="text-sm text-red-700 mt-1"></p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('estudiantes.solicitud.store') }}" enctype="multipart/form-data" id="ppsForm">
                @csrf

                {{-- STEP 1: Tipo de Práctica --}}
                <div class="step-content" id="step1">
                    <div class="bg-white rounded-2xl shadow-xl p-8 border border-blue-100 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <span class="w-6 h-6 md:w-8 md:h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white mr-2 md:mr-3">1</span>
                            Tipo de Práctica
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Opción Normal --}}
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="tipo_practica" value="normal" class="peer sr-only" required>
                                <div class="border-2 border-gray-300 rounded-xl p-6 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg hover:border-blue-300">
                                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4 mx-auto">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-center mb-2 text-gray-800">Práctica Normal</h3>
                                    <p class="text-sm text-center text-gray-600">Realizarás tu práctica en una empresa o institución sin vínculo laboral previo.</p>
                                </div>
                            </label>

                            {{-- Opción Trabajo --}}
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="tipo_practica" value="trabajo" class="peer sr-only">
                                <div class="border-2 border-gray-300 rounded-xl p-6 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-lg hover:border-green-300">
                                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-4 mx-auto">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-center mb-2 text-gray-800">Práctica por Trabajo</h3>
                                    <p class="text-sm text-center text-gray-600">Ya trabajas en una empresa y validarás tu experiencia laboral como práctica.</p>
                                </div>
                            </label>
                        </div>

                        {{-- Modalidad (solo normal) --}}
                        <div id="modalidad_fields" class="hidden mt-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Modalidad de Trabajo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <label class="cursor-pointer">
                                    <input type="radio" name="modalidad" value="Presencial" class="peer sr-only">
                                    <div class="border-2 border-gray-300 rounded-xl p-6 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg hover:border-blue-300">
                                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4 mx-auto">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-gray-800">Presencial</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="modalidad" value="Semipresencial" class="peer sr-only">
                                    <div class="border-2 border-gray-300 rounded-xl p-6 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg hover:border-blue-300">
                                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4 mx-auto">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-gray-800">Semipresencial</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="modalidad" value="Teletrabajo" class="peer sr-only">
                                    <div class="border-2 border-gray-300 rounded-xl p-6 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg hover:border-blue-300">
                                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4 mx-auto">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-gray-800">Teletrabajo</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

             {{-- STEP 2: Información --}}
        <div class="step-content hidden" id="step2">
            <div class="bg-white rounded-2xl shadow-xl p-4 md:p-8 border border-blue-100 mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center">
                    <span class="w-6 h-6 md:w-8 md:h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white mr-2 md:mr-3">2</span>
                    Información Personal y Empresa
                </h2>

        {{-- Información Personal --}}
        <div class="mb-6 md:mb-8">
            <h3 class="text-base md:text-lg font-bold text-gray-800 mb-3 md:mb-4 pb-2 border-b">Datos del Estudiante</h3>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Cuenta *</label>
                    <input type="text" name="numero_cuenta" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: 20191000001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                    <input type="text" name="telefono_alumno" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: 9999-9999">
                </div>
                {{-- DNI --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">DNI (Identidad) *</label>
                    <input type="text" name="dni_estudiante" required maxlength="15"
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: 0801-1999-12345">
                </div>
            </div>

            {{-- Foto del Estudiante --}}
            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto del Estudiante *</label>
                <div class="flex items-center space-x-3 md:space-x-4">
                    <div id="fotoPreview" class="hidden w-16 h-16 md:w-24 md:h-24 rounded-lg overflow-hidden border-2 border-gray-300">
                        <img id="fotoPreviewImg" src="" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <label for="foto_estudiante" class="cursor-pointer inline-flex items-center px-3 md:px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span id="fotoFileName" class="text-xs md:text-sm text-gray-600">Seleccionar foto (JPG, PNG - Max 2MB)</span>
                        </label>
                        <input type="file" id="foto_estudiante" name="foto_estudiante" accept="image/jpeg,image/png,image/jpg" required class="hidden">
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Sube una foto reciente tipo carnet (fondo claro preferiblemente)</p>
            </div>
        </div>

        {{-- Información Empresa --}}
        <div class="mb-6 md:mb-8">
            <h3 class="text-base md:text-lg font-bold text-gray-800 mb-3 md:mb-4 pb-2 border-b">Datos de la Empresa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
<div class="relative">
  <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Empresa *</label>

  <input
    type="text"
    id="empresa_nombre_input"
    name="nombre_empresa"
    required
    autocomplete="off"
    class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
    placeholder="Ej: Tecnología y Desarrollo S.A."
  >

  <input type="hidden" id="empresa_id" name="empresa_id" value="">

  <div id="empresa_dropdown"
       class="hidden absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
    <ul id="empresa_list" class="max-h-56 overflow-y-auto"></ul>

    <div class="border-t border-gray-100 p-3 bg-gray-50">
      <button type="button"
              id="empresa_add_btn"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        + Agregar empresa: <span id="empresa_add_name"></span>
      </button>
      <p class="text-xs text-gray-500 mt-2">
        Si no aparece en la lista, puedes registrarla.
      </p>
    </div>
  </div>

  <p class="text-xs text-gray-500 mt-2">Escribe al menos 2 letras para buscar.</p>
</div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Empresa *</label>
                    <select name="tipo_empresa" required
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccione...</option>
                        <option value="publica">Pública</option>
                        <option value="privada">Privada</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección de la Empresa *</label>
                    <textarea name="direccion_empresa" required rows="2"
                              class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Dirección completa de la empresa"></textarea>
                </div>
            </div>
        </div>

        {{-- Información Jefe --}}
        <div class="mb-6 md:mb-8">
            <h3 class="text-base md:text-lg font-bold text-gray-800 mb-3 md:mb-4 pb-2 border-b">Datos del Jefe Inmediato</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Jefe *</label>
                    <input type="text" name="nombre_jefe" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nombre completo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono del Jefe *</label>
                    <input type="text" name="numero_jefe" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: 9999-9999">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Correo del Jefe *</label>
                    <input type="email" name="correo_jefe" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="ejemplo@empresa.com">
                </div>

                {{-- Cargo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cargo del Jefe *</label>
                    <input type="text" name="cargo_jefe" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: Gerente de Sistemas">
                </div>

                {{-- Nivel Académico --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nivel Académico del Jefe *</label>
                    <select name="nivel_academico_jefe" required
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccione...</option>
                        <option value="Bachillerato">Bachillerato</option>
                        <option value="Licenciado">Licenciado</option>
                        <option value="Ingeniero">Ingeniero</option>
                        <option value="Abogado">Abogado</option>
                        <option value="Máster">Máster</option>
                        <option value="Doctor">Doctor</option>
                    </select>
                </div>

                
            </div>
        </div>

        {{-- Campos específicos TRABAJO --}}
        <div id="trabajo_fields" class="hidden mb-6 md:mb-8">
            <h3 class="text-base md:text-lg font-bold text-gray-800 mb-3 md:mb-4 pb-2 border-b">Información Laboral</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Puesto de Trabajo *</label>
                    <input type="text" name="puesto_trabajo"
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: Desarrollador Junior">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Años Trabajando *</label>
                    <input type="number" name="anios_trabajando" min="0" max="100"
                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: 2">
                </div>
            </div>
        </div>


{{-- Campos específicos NORMAL --}}
<div id="normal_fields" class="hidden mb-6 md:mb-8">
    <h3 class="text-base md:text-lg font-bold text-gray-800 mb-3 md:mb-4 pb-2 border-b">Información de la Práctica</h3>
    
    {{-- Fecha de Inicio --}}
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio *</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio"
               class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               min="{{ date('Y-m-d') }}">
    </div>

    {{-- HORARIO FLEXIBLE POR DÍA --}}
    <div class="border-2 border-dashed border-blue-300 rounded-xl p-4 md:p-6 bg-blue-50 mb-6">
        <h4 class="text-base md:text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Horario de Trabajo
        </h4>

        {{-- Configuración Rápida --}}
        <div class="bg-white rounded-lg p-3 md:p-4 mb-4 border-2 border-blue-200">
            <p class="text-xs md:text-sm font-semibold text-gray-700 mb-3"> Atajo Rápido (si todos los días son iguales):</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Hora Entrada</label>
                    <input type="time" id="hora_base_entrada" value="08:00"
                           class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Hora Salida</label>
                    <input type="time" id="hora_base_salida" value="17:00"
                           class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 transition text-sm">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="aplicarHorarioATodos()" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Aplicar
                    </button>
                </div>
            </div>
            <p class="text-xs text-gray-500 italic"> Horas laborales</p>
        </div>

        {{-- Días de la Semana --}}
        <div class="space-y-2 md:space-y-3">
            @php
                $diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
            @endphp

            @foreach($diasSemana as $dia)
                <div class="bg-white rounded-lg p-3 md:p-4 border-2 border-gray-200 hover:border-blue-300 transition dia-container" data-dia="{{ $dia }}">
                    <div class="flex items-center gap-3 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" 
                                   class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-200 dia-checkbox" 
                                   data-dia="{{ $dia }}"
                                   {{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? 'checked' : '' }}
                                   onchange="toggleDia('{{ $dia }}')">
                            <span class="text-sm md:text-base font-bold text-gray-900 capitalize">{{ $dia }}</span>
                        </label>
                    </div>

                    <input type="hidden" 
                           name="dias_laborables[{{ $dia }}][activo]" 
                           value="{{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? 'true' : 'false' }}" 
                           class="activo-{{ $dia }}">

                    <div class="dia-horario {{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? '' : 'hidden' }}" 
                         id="horario_{{ $dia }}">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 md:gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Entrada</label>
                                <input type="time" 
                                       name="dias_laborables[{{ $dia }}][hora_entrada]" 
                                       value="{{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? '08:00' : '' }}"
                                       class="w-full px-2 md:px-3 py-1 md:py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 transition text-sm entrada-{{ $dia }}"
                                       onchange="calcularHorasDia('{{ $dia }}')">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Salida</label>
                                <input type="time" 
                                       name="dias_laborables[{{ $dia }}][hora_salida]" 
                                       value="{{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? '17:00' : '' }}"
                                       class="w-full px-2 md:px-3 py-1 md:py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 transition text-sm salida-{{ $dia }}"
                                       onchange="calcularHorasDia('{{ $dia }}')">
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Horas Laborales</label>
                                <div class="flex items-center gap-2 h-8 md:h-10">
                                    <span class="text-base md:text-lg font-bold text-blue-600 horas-display-{{ $dia }}">
                                        {{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? '8.0' : '0.0' }} hrs
                                    </span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" 
                               name="dias_laborables[{{ $dia }}][horas_laborales]" 
                               value="{{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? '8' : '0' }}" 
                               class="horas-{{ $dia }}">
                    </div>

                    <div class="dia-desactivado {{ in_array($dia, ['lunes', 'martes', 'miércoles', 'jueves', 'viernes']) ? 'hidden' : '' }}" 
                         id="desactivado_{{ $dia }}">
                        <p class="text-xs md:text-sm text-gray-500 italic">Marca la casilla si trabajas este día</p>
                    </div>
                </div>
            @endforeach
        </div>

     {{-- Resumen Semanal --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 md:p-4 mt-4 border-2 border-blue-200">
            <p class="text-xs md:text-sm font-semibold text-gray-700 mb-2">Resumen Semanal:</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-lg p-2 md:p-3 text-center">
                    <p class="text-xs text-gray-600">Total Semanal</p>
                    <p class="text-lg md:text-xl font-bold text-blue-600" id="total_horas_semanales">40.0 hrs</p>
                </div>
                <div class="bg-white rounded-lg p-2 md:p-3 text-center">
                    <p class="text-xs text-gray-600">Promedio Diario</p>
                    <p class="text-lg md:text-xl font-bold text-blue-600" id="promedio_horas_diarias">8.0 hrs</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 🆕 DÍAS FERIADOS (AHORA ANTES DE FECHA FINALIZACIÓN) --}}
    <div class="mt-4 md:mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Días Feriados (Opcional)</label>
        <p class="text-xs text-gray-500 mb-3">Agrega días no laborables (feriados, vacaciones, etc.) para un cálculo más preciso</p>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 md:p-4">
            <div class="flex gap-3 mb-3">
                <input type="date" id="input_feriado" 
                       class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 transition text-sm">
                <button type="button" onclick="agregarFeriado()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition">
                    + Agregar
                </button>
            </div>
            <div id="lista_feriados" class="space-y-2 max-h-32 md:max-h-40 overflow-y-auto">
                <p class="text-gray-500 text-sm">No hay feriados agregados</p>
            </div>
            <input type="hidden" name="dias_feriados" id="dias_feriados_json" value="[]">
        </div>
    </div>

    {{-- FECHA DE FINALIZACIÓN (AHORA DESPUÉS DE FERIADOS) --}}
    <div class="mt-4 md:mt-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-2 border-green-200">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800 mb-1">Fecha Estimada de Finalización</p>
                <p class="text-lg md:text-xl font-bold text-green-700" id="fecha_fin_display">
                    Ingresa fecha de inicio y horario
                </p>
                <p class="text-xs text-gray-600 mt-2">
                    <span class="font-semibold">Semanas necesarias:</span> <span id="semanas_necesarias" class="font-bold text-green-700">0</span>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Observaciones --}}
<div>
    <h3 class="text-base md:text-lg font-bold text-gray-800 mb-3 md:mb-4 pb-2 border-b">Observaciones (Opcional)</h3>
    <textarea name="observacion" rows="3"
              class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Información adicional que consideres relevante"></textarea>
</div>
            </div>
        </div>

   {{-- STEP 3: Documentos --}}
                <div class="step-content hidden" id="step3">
                    <div class="bg-white rounded-2xl shadow-xl p-8 border border-blue-100 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white mr-3">3</span>
                            Documentos de Respaldo
                        </h2>

                        <p class="text-gray-600 mb-6"><span class="text-red-500 font-semibold">Importante:</span> Debes subir al menos un documento para continuar.</p>

                        {{-- Docs Normal --}}
                        <div id="docs_normal" class="hidden space-y-6">
                            <div class="file-upload-box" data-name="colegiacion">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Colegiación (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
                                    
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-blue-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="colegiacion" accept="application/pdf" class="file-input">
                                </div>
                            </div>

                            <div class="file-upload-box" data-name="documento_ia01">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Formato IA-01 (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-blue-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="documento_ia01" accept="application/pdf" class="file-input">
                                </div>
                            </div>

                            <div class="file-upload-box" data-name="carta_aceptacion">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Carta de Aceptación (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-blue-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="carta_aceptacion" accept="application/pdf" class="file-input">
                                </div>
                            </div>

                            <div class="file-upload-box" data-name="carta_presentacion">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Carta de Presentación (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-blue-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="carta_presentacion" accept="application/pdf" class="file-input">
                                </div>
                            </div>
                        </div>

                        {{-- Docs Trabajo --}}
                        <div id="docs_trabajo" class="hidden space-y-6">
                            <div class="file-upload-box" data-name="colegiacion">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Colegiación (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-purple-400 hover:bg-purple-50 cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-purple-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="colegiacion" accept="application/pdf" class="file-input">
                                </div>
                            </div>

                            <div class="file-upload-box" data-name="documento_ia02">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Formato IA-02 (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-purple-400 hover:bg-purple-50 cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-purple-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="documento_ia02" accept="application/pdf" class="file-input">
                                </div>
                            </div>

                            <div class="file-upload-box" data-name="constancia_trabajo">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Constancia de Trabajo (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-purple-400 hover:bg-purple-50 cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-purple-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="constancia_trabajo" accept="application/pdf" class="file-input">
                                </div>
                            </div>

                            <div class="file-upload-box" data-name="constancia_aprobacion">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Constancia de 100% Clases (PDF) <span class="text-red-500">*</span></label>
                                <div class="file-drop-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all hover:border-purple-400 hover:bg-purple-50 cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-600 mb-1">Arrastra tu archivo aquí o <span class="text-purple-600 font-medium">haz clic para seleccionar</span></p>
                                    <p class="text-sm text-gray-400">PDF - Máximo 5MB</p>
                                    <input type="file" name="constancia_aprobacion" accept="application/pdf" class="file-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex justify-between items-center max-w-5xl mx-auto mt-8">
                    <button type="button" id="prevBtn" onclick="changeStep(-1)" 
                            class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all hidden">
                        Anterior
                    </button>
                    <button type="button" id="nextBtn" onclick="changeStep(1)" 
                            class="ml-auto px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
                        Siguiente
                    </button>
                    <button type="submit" id="submitBtn" 
                            class="ml-auto px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-medium hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hidden disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="submitBtnText">Enviar Solicitud</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<style>
.file-input {
    position: absolute;
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
    pointer-events: none;
    z-index: -1;
}

.file-drop-area.drag-over {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
}

.file-drop-area.has-file {
    border-color: #10b981 !important;
    background-color: #d1fae5 !important;
}

/* Mejorar visibilidad de los steps */
.step-circle {
    transition: all 0.3s ease;
    font-size: 1.125rem;
}

.step-text {
    transition: all 0.3s ease;
}

.step-line {
    transition: all 0.3s ease;
    height: 4px;
}

/* Animación al completar step */
@keyframes checkmark {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

.step-circle svg {
    animation: checkmark 0.4s ease;
}
</style>
<script>
  window.PPS = window.PPS || {};
  window.PPS.empresas = {
    searchUrl: @json(route('estudiantes.empresas.search')),
    storeUrl: @json(route('estudiantes.empresas.store')),
    csrf: @json(csrf_token()),
  };
</script>

<script src="{{ asset('js/solicitud-form.js') }}?v={{ time() }}"></script>

@endsection