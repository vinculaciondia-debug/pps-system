{{-- Modal: Subir Supervisión --}}
<div id="modalSupervision" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-2xl font-bold">Subir Supervisión</h2>
            <button onclick="cerrarModal('modalSupervision')" type="button" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="formSupervision" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-lg flex items-start gap-2">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                </svg>
                <div>
                    <p class="text-green-800 text-sm font-semibold">
                        Estás subiendo la Supervisión #<span id="numeroSupervisionTexto"></span>
                    </p>
                    <p class="text-green-700 text-xs mt-1">
                        Una vez subida, el estudiante podrá ver el comentario y descargar el archivo.
                    </p>
                </div>
            </div>

            {{-- Errores de validación --}}
            <div id="erroresSupervision" class="hidden bg-red-50 border-l-4 border-red-500 p-3 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div id="listaErroresSupervision" class="text-red-800 text-sm"></div>
                </div>
            </div>

           <input type="hidden" name="numero_supervision" id="numero_supervision">

<!-- ✅ AGREGAR TODO ESTE BLOQUE -->
<div>
    <label class="block text-sm font-bold text-gray-800 mb-2">
        Fecha de Supervisión <span class="text-red-500">*</span>
    </label>
    <input type="date" 
           name="fecha_supervision" 
           id="fecha_supervision" 
           required
           max="{{ date('Y-m-d') }}"
           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
    <p class="mt-1 text-xs text-gray-600">
        Fecha en que realizaste la supervisión (no puede ser futura)
    </p>
</div>
<!-- ✅ FIN DEL BLOQUE NUEVO -->


<!-- ✅ AGREGAR ESTE BLOQUE COMPLETO -->
<div class="border-2 border-gray-200 rounded-xl p-4 bg-gray-50">
    <label class="block text-sm font-bold text-gray-800 mb-2">
        <svg class="w-4 h-4 inline-block text-gray-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        Justificación 
    </label>
    <p class="text-xs text-gray-600 mb-3">Marca uno o ambas </p>
    
    <div class="space-y-2">
        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-2 rounded transition">
            <input type="checkbox" name="ausencia_entrada" value="1"
                   class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
            <span class="text-sm text-gray-700">Entrada</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-2 rounded transition">
            <input type="checkbox" name="ausencia_salida" value="1"
                   class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
            <span class="text-sm text-gray-700">Salida</span>
        </label>
    </div>
</div>
<!-- ✅ FIN DEL BLOQUE -->



<div>
    <label class="block text-sm font-bold text-gray-800 mb-2">
        Comentarios y Observaciones <span class="text-gray-500 font-normal">(opcional)</span>
    </label>
                <textarea name="comentario" id="comentario" rows="6" maxlength="1000"
                          placeholder="Opcional: describe el desempeño del estudiante, observaciones relevantes, áreas de mejora, etc."
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition resize-none"></textarea>
                <p class="mt-1 text-xs text-gray-600">
                    Opcional. Máximo 1000 caracteres.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Archivo de Supervisión <span class="text-red-500">*</span>
                </label>
                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-green-400 transition">
                    <div class="space-y-2 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="text-sm text-gray-600">
                            <label for="archivo" class="relative cursor-pointer bg-white rounded-md font-semibold text-green-600 hover:text-green-500 focus-within:outline-none">
                                <span>Subir archivo</span>
                                <input id="archivo" name="archivo" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="sr-only" onchange="mostrarNombreArchivo(this)">
                            </label>
                            <p class="pl-1 inline">o arrastrar y soltar</p>
                        </div>
                        <p class="text-xs text-gray-500">PDF, JPG, PNG hasta 5MB</p>
                        <p id="nombreArchivo" class="text-sm font-semibold text-green-600 hidden"></p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="cerrarModal('modalSupervision')"
                        class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition text-sm">
                    Cancelar
                </button>
                <button type="submit" id="btnSubirSupervision"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-teal-700 transition shadow-lg flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Subir Supervisión
                </button>
            </div>
        </form>
    </div>
</div>