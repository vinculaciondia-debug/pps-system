{{-- ============================================
     MODAL: VER DETALLE COMPLETO
     ============================================ --}}
<div id="modalDetalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        
        {{-- Header --}}
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-t-xl sm:rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-lg sm:text-2xl font-bold">Detalle de Solicitud</h2>
            <button onclick="cerrarModal('modalDetalle')" class="text-white hover:text-gray-200 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div id="detalleContent" class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 sm:h-10 sm:w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

    </div>
</div>

{{-- ============================================
     MODAL: APROBAR Y ASIGNAR SUPERVISOR
     ============================================ --}}
<div id="modalAprobar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-t-xl sm:rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center gap-2 sm:gap-3">
                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-lg sm:text-2xl font-bold">Aprobar Solicitud</h2>
            </div>
            <button onclick="cerrarModal('modalAprobar')" class="text-white hover:text-gray-200 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form id="formAprobar" method="POST" class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            @csrf
            
            <div class="bg-green-50 border-l-4 border-green-500 p-3 sm:p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-green-800 text-sm sm:text-base">Aprobación de solicitud</p>
                        <p class="text-xs sm:text-sm text-green-700 mt-1">Esta acción aprobará la solicitud y asignará un supervisor</p>
                    </div>
                </div>
            </div>

            {{-- Selector de Modo de Asignación --}}
            <div>
                <label class="block text-sm font-bold text-gray-800 mb-3">
                    Modo de asignación <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    {{-- Opción: Asignación Automática --}}
                    <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-500 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                        <input type="radio" name="modo_asignacion" value="automatico" id="modoAutomatico" checked
                               class="mt-1 w-4 h-4 text-green-600 focus:ring-green-500">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-800">Asignación Automática</span>
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Recomendado</span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                El sistema asignará automáticamente al supervisor con menos estudiantes para balancear la carga
                            </p>
                        </div>
                    </label>

                    {{-- Opción: Asignación Manual --}}
                    <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <input type="radio" name="modo_asignacion" value="manual" id="modoManual"
                               class="mt-1 w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <div class="ml-3 flex-1">
                            <span class="font-semibold text-gray-800">Asignación Manual</span>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1">
                                Selecciona manualmente el supervisor que desees asignar
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Select de Supervisor (Solo visible en modo manual) --}}
            <div id="contenedorSupervisorManual" class="hidden">
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Seleccionar Supervisor <span class="text-red-500">*</span>
                </label>
                <select name="supervisor_id" id="supervisorSelect"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">Cargando supervisores...</option>
                </select>
                <p class="mt-2 text-xs sm:text-sm text-gray-600">
                    Solo se muestran supervisores con cupo disponible
                </p>
            </div>

            {{-- Información del Supervisor Manual --}}
            <div id="infoSupervisor" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 sm:p-4">
                <p class="text-xs sm:text-sm font-semibold text-blue-800 mb-2">Información del supervisor:</p>
                <div class="space-y-1 text-xs sm:text-sm text-blue-700">
                    <p><span class="font-semibold">Email:</span> <span id="supervisorEmail" class="break-all">-</span></p>
                    <p><span class="font-semibold">Capacidad:</span> <span id="supervisorCapacidad">-</span></p>
                </div>
            </div>

            {{-- Información de Asignación Automática --}}
            <div id="infoAutomatico" class="bg-green-50 border border-green-200 rounded-xl p-3 sm:p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-green-800">Asignación inteligente activada</p>
                        <p class="text-xs text-green-700 mt-1">
                            El sistema seleccionará automáticamente al supervisor con menor carga de trabajo para mantener un balance equitativo
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                <button type="button" onclick="cerrarModal('modalAprobar')"
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition text-sm sm:text-base">
                    Cancelar
                </button>
                <button type="submit"
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition shadow-lg text-sm sm:text-base">
                    ✓ Aprobar y Asignar
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ============================================
     MODAL: RECHAZAR SOLICITUD
     ============================================ --}}
<div id="modalRechazar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-t-xl sm:rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center gap-2 sm:gap-3">
                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-lg sm:text-2xl font-bold">Rechazar Solicitud</h2>
            </div>
            <button onclick="cerrarModal('modalRechazar')" class="text-white hover:text-gray-200 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form id="formRechazar" method="POST" class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            @csrf
            
            <div class="bg-red-50 border-l-4 border-red-500 p-3 sm:p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-red-800 font-semibold text-xs sm:text-sm">Esta acción notificará al estudiante</p>
                        <p class="text-red-700 text-xs sm:text-sm mt-1">El estudiante podrá ver el motivo del rechazo en su dashboard</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Motivo del rechazo <span class="text-red-500">*</span>
                </label>
                <textarea name="observaciones" rows="6" required
                          placeholder="Explica claramente por qué se rechaza la solicitud...&#10;&#10;Ejemplo:&#10>- Documentos incompletos&#10>- Información incorrecta en la carta de presentación&#10>- No cumple con los requisitos del reglamento"
                          class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition resize-none"></textarea>
                <p class="mt-2 text-xs sm:text-sm text-gray-600">
                    Sé específico para que el estudiante pueda corregir los errores
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                <button type="button" onclick="cerrarModal('modalRechazar')"
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition text-sm sm:text-base">
                    Cancelar
                </button>
                <button type="submit"
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold hover:from-red-600 hover:to-pink-700 transition shadow-lg text-sm sm:text-base">
                    Rechazar Solicitud
                </button>
            </div>
        </form>

    </div>
</div>