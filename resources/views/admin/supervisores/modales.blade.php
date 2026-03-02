<style>
/* Scrollbar bonita solo para elementos con .nice-scroll */
.nice-scroll{
  scrollbar-gutter: stable;
  overscroll-behavior: contain;
  scrollbar-width: thin;                 /* Firefox */
  scrollbar-color: #94a3b8 #f1f5f9;      /* thumb, track */
}
.nice-scroll::-webkit-scrollbar{ width: 10px; }           /* Chrome/Edge */
.nice-scroll::-webkit-scrollbar-track{
  background: #f1f5f9;
  border-radius: 12px;
}
.nice-scroll::-webkit-scrollbar-thumb{
  background: linear-gradient(180deg,#93c5fd,#60a5fa);
  border-radius: 12px;
  border: 2px solid #f1f5f9;             /* separa el thumb del contenido */
}
.nice-scroll::-webkit-scrollbar-thumb:hover{
  background: linear-gradient(180deg,#60a5fa,#3b82f6);
}
</style>

{{-- Modal: Crear Supervisor --}}
<div id="modalCrear" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto nice-scroll" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-2xl font-bold">Crear Nuevo Supervisor</h2>
            <button onclick="cerrarModal('modalCrear')" type="button" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="formCrear" method="POST" action="{{ route('admin.supervisores.store') }}" class="p-6 space-y-4">
            @csrf
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-lg flex items-start gap-2">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                </svg>
                <p class="text-blue-800 text-sm font-semibold">
                    Se creará automáticamente un usuario con rol "supervisor"
                </p>
            </div>

            {{-- Errores de validación --}}
            <div id="erroresCrear" class="hidden bg-red-50 border-l-4 border-red-500 p-3 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div id="listaErroresCrear" class="text-red-800 text-sm"></div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Nombre completo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre" id="crear_nombre" required
                       placeholder="Ej: Juan Pérez"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="crear_email" required
                       placeholder="ejemplo@unah.edu.hn"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Contraseña <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" id="crear_password" required minlength="8"
                           placeholder="Mínimo 8 caracteres"
                           class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    <button type="button" onclick="togglePassword('crear_password', 'iconCrearPassword')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition focus:outline-none">
                        <svg id="iconCrearPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Confirmar contraseña <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="crear_password_confirmation" required minlength="8"
                           placeholder="Repite la contraseña"
                           class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    <button type="button" onclick="togglePassword('crear_password_confirmation', 'iconCrearPasswordConfirm')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition focus:outline-none">
                        <svg id="iconCrearPasswordConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Capacidad máxima de estudiantes <span class="text-red-500">*</span>
                </label>
                <input type="number" name="max_estudiantes" id="crear_max_estudiantes" required min="1" max="100" value="10"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                <p class="mt-1 text-xs text-gray-600">
                    Número máximo de estudiantes que puede supervisar simultáneamente (1-100)
                </p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="cerrarModal('modalCrear')"
                        class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition text-sm">
                    Cancelar
                </button>
                <button type="submit" id="btnCrear"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Supervisor
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Supervisor --}}
<div id="modalEditar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto nice-scroll" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-2xl font-bold">Editar Supervisor</h2>
            <button onclick="cerrarModal('modalEditar')" type="button" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="formEditar" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            {{-- Errores de validación --}}
            <div id="erroresEditar" class="hidden bg-red-50 border-l-4 border-red-500 p-3 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div id="listaErroresEditar" class="text-red-800 text-sm"></div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Nombre completo <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit_nombre" name="nombre" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" id="edit_email" name="email" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded-lg flex items-start gap-2">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                </svg>
                <p class="text-yellow-800 text-sm font-semibold">
                    Deja la contraseña en blanco si no deseas cambiarla
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Nueva contraseña (opcional)
                </label>
                <div class="relative">
                    <input type="password" name="password" id="edit_password" minlength="8"
                           placeholder="Dejar en blanco para no cambiar"
                           class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
                    <button type="button" onclick="togglePassword('edit_password', 'iconEditPassword')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition focus:outline-none">
                        <svg id="iconEditPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Confirmar nueva contraseña
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="edit_password_confirmation" minlength="8"
                           placeholder="Repetir nueva contraseña"
                           class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
                    <button type="button" onclick="togglePassword('edit_password_confirmation', 'iconEditPasswordConfirm')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition focus:outline-none">
                        <svg id="iconEditPasswordConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">
                    Capacidad máxima de estudiantes <span class="text-red-500">*</span>
                </label>
                <input type="number" id="edit_max_estudiantes" name="max_estudiantes" required min="1" max="100"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
                <p class="mt-1 text-xs text-gray-600">
                    Número máximo de estudiantes que puede supervisar simultáneamente (1-100)
                </p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="cerrarModal('modalEditar')"
                        class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition text-sm">
                    Cancelar
                </button>
                <button type="submit" id="btnEditar"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl font-semibold hover:from-yellow-600 hover:to-orange-600 transition shadow-lg flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Ver Detalle --}}
<div id="modalDetalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto nice-scroll" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-2xl font-bold">Detalle del Supervisor</h2>
            <button onclick="cerrarModal('modalDetalle')" type="button" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="detalleContent" class="p-6 space-y-6">
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-10 w-10 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>