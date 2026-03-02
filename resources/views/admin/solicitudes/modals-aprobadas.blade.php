
{{-- Modal: Ver Detalle --}}
<div id="modalDetalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-t-xl sm:rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-bold">Detalle de Solicitud</h2>
            <button onclick="cerrarModal('modalDetalle')" class="text-white hover:text-gray-200 transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="detalleContent" class="p-3 sm:p-4 lg:p-6 space-y-4 sm:space-y-6">
            {{-- Aquí se carga el contenido dinámicamente --}}
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 sm:h-10 sm:w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Rechazar Carta de Finalización --}}
<div id="modalRechazarCarta" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-4 rounded-t-xl">
            <h2 class="text-xl font-bold">Rechazar Carta de Finalización</h2>
        </div>
        <form id="formRechazarCarta" method="POST" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded">
                <p class="text-red-800 text-sm">
                    <strong>Atención:</strong> Al rechazar esta carta, el estudiante deberá subir una nueva versión.
                </p>
            </div>

            <div id="infoCarta" class="mb-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Estudiante:</p>
                <p class="font-semibold text-gray-900" id="nombreEstudianteCarta">-</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Motivo del Rechazo <span class="text-red-600">*</span>
                </label>
                <textarea name="observacion" required rows="4"
                          placeholder="Explica por qué se rechaza la carta (firma faltante, datos incorrectos, etc.)"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition resize-none"></textarea>
                <p class="text-xs text-gray-600 mt-2">El estudiante verá este mensaje y podrá subir una nueva carta.</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="cerrarModal('modalRechazarCarta')"
                        class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl font-semibold hover:from-red-700 hover:to-pink-700 transition shadow-lg">
                    Rechazar Carta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Finalizar Práctica --}}
<div id="modalFinalizar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-t-xl sm:rounded-t-2xl">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-bold">Finalizar Práctica Profesional</h2>
        </div>
        <form id="formFinalizar" method="POST" class="p-4 sm:p-6">
            @csrf
            @method('PATCH')
            
            <div id="requisitosFinalizacion" class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-blue-800 mb-2">Requisitos verificados:</p>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• 2 supervisiones completadas ✓</li>
                            <li>• Carta de finalización recibida ✓</li>
                        </ul>
                    </div>
                </div>
            </div>

            <p class="text-sm sm:text-base text-gray-700 mb-4">
                ¿Confirmas que deseas finalizar la práctica profesional de <strong id="nombreEstudianteFinalizar" class="break-words"></strong>?
            </p>
            
            <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-lg mb-4">
                <p class="text-green-800 text-xs sm:text-sm">
                    <strong>Esta acción:</strong><br>
                    • Marcará la práctica como completada<br>
                    • El estudiante será notificado<br>
                    • El proceso quedará archivado
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="cerrarModal('modalFinalizar')" 
                        class="w-full sm:flex-1 px-4 py-2.5 sm:py-2 bg-gray-200 text-gray-700 rounded-lg sm:rounded-xl font-semibold hover:bg-gray-300 transition text-sm sm:text-base">
                    Cancelar
                </button>
                <button type="submit" 
                        class="w-full sm:flex-1 px-4 py-2.5 sm:py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg sm:rounded-xl font-semibold hover:from-green-700 hover:to-teal-700 transition shadow-lg text-sm sm:text-base">
                    Finalizar Práctica
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Cambiar Supervisor --}}
<div id="modalCambiarSupervisor" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-t-xl sm:rounded-t-2xl flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold truncate">Cambiar Supervisor</h2>
            </div>
            <button onclick="cerrarModal('modalCambiarSupervisor')" class="text-white hover:text-gray-200 transition flex-shrink-0 ml-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form id="formCambiarSupervisor" method="POST" class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            @csrf
            
            {{-- Alerta informativa --}}
            <div class="bg-purple-50 border-l-4 border-purple-500 p-3 sm:p-4 rounded">
                <div class="flex items-start gap-2 sm:gap-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-xs sm:text-sm text-purple-800">
                        <p class="font-semibold mb-1">Cambiar el supervisor reasignará esta práctica a otro docente</p>
                        <p>El nuevo supervisor podrá gestionar esta solicitud desde su panel</p>
                    </div>
                </div>
            </div>

            {{-- Supervisor actual --}}
            <div id="supervisorActualInfo" class="bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-2 sm:mb-3">Supervisor actual:</p>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p id="supervisorActualNombre" class="font-semibold text-gray-900 text-sm sm:text-base truncate">-</p>
                        <p id="supervisorActualEmail" class="text-xs text-gray-600 truncate">-</p>
                    </div>
                </div>
            </div>

            {{-- Selector de nuevo supervisor --}}
            <div>
                <label class="block text-xs sm:text-sm font-bold text-gray-800 mb-2">
                    Nuevo Supervisor <span class="text-red-600">*</span>
                </label>
                <select name="supervisor_id" id="supervisorSelectCambio" required 
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition">
                    <option value="">Selecciona un supervisor</option>
                </select>
                <p class="mt-2 text-xs text-gray-600">
                    Solo se muestran supervisores con cupo disponible
                </p>
            </div>

            {{-- Info del nuevo supervisor seleccionado --}}
            <div id="nuevoSupervisorInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg sm:rounded-xl p-3 sm:p-4">
                <p class="text-xs sm:text-sm font-semibold text-blue-800 mb-2">Información del nuevo supervisor:</p>
                <div class="text-xs sm:text-sm text-blue-900 space-y-1">
                    <p><span class="font-semibold">Email:</span> <span id="nuevoSupervisorEmail" class="break-all">-</span></p>
                    <p><span class="font-semibold">Capacidad:</span> <span id="nuevoSupervisorCapacidad">-</span></p>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-2 sm:pt-4">
                <button type="button" onclick="cerrarModal('modalCambiarSupervisor')"
                        class="w-full sm:flex-1 px-4 py-2.5 sm:py-2 bg-gray-200 text-gray-700 rounded-lg sm:rounded-xl font-semibold hover:bg-gray-300 transition text-sm sm:text-base">
                    Cancelar
                </button>
                <button type="submit"
                        class="w-full sm:flex-1 px-4 py-2.5 sm:py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg sm:rounded-xl font-semibold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg text-sm sm:text-base">
                    Cambiar Supervisor
                </button>
            </div>
        </form>

    </div>
</div>

<style>
/* Focus states para el select */
#supervisorSelectCambio:focus {
    outline: none;
    border-color: #9333ea;
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
}

/* Hover effects */
#supervisorSelectCambio:hover {
    border-color: #c4b5fd;
}

/* Scrollbar personalizada */
#modalDetalle > div,
#modalCambiarSupervisor > div,
#modalFinalizar > div,
#modalRechazarCarta > div {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.4) transparent;
}

#modalDetalle > div::-webkit-scrollbar,
#modalCambiarSupervisor > div::-webkit-scrollbar,
#modalFinalizar > div::-webkit-scrollbar,
#modalRechazarCarta > div::-webkit-scrollbar {
    width: 6px;
}

#modalDetalle > div::-webkit-scrollbar-track,
#modalCambiarSupervisor > div::-webkit-scrollbar-track,
#modalFinalizar > div::-webkit-scrollbar-track,
#modalRechazarCarta > div::-webkit-scrollbar-track {
    background: transparent;
}

#modalDetalle > div::-webkit-scrollbar-thumb,
#modalCambiarSupervisor > div::-webkit-scrollbar-thumb,
#modalFinalizar > div::-webkit-scrollbar-thumb,
#modalRechazarCarta > div::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.4);
    border-radius: 10px;
}

#modalDetalle > div::-webkit-scrollbar-thumb:hover,
#modalCambiarSupervisor > div::-webkit-scrollbar-thumb:hover,
#modalFinalizar > div::-webkit-scrollbar-thumb:hover,
#modalRechazarCarta > div::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.6);
}

/* Animación de entrada */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

#modalCambiarSupervisor > div,
#modalFinalizar > div,
#modalRechazarCarta > div {
    animation: fadeIn 0.2s ease-out;
}
</style>