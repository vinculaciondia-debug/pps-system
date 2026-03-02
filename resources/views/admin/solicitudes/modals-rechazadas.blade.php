{{-- Modal: Ver Detalle --}}
<div id="modalDetalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-2xl font-bold">Detalle de Solicitud Rechazada</h2>
            <button onclick="cerrarModal('modalDetalle')" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="detalleContent" class="p-6 space-y-6">
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Ver Motivo Completo --}}
<div id="modalMotivo" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl">
        
        {{-- Header --}}
        <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);" class="text-white px-6 py-4 rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h2 class="text-2xl font-bold">Motivo del Rechazo</h2>
            </div>
            <button onclick="cerrarModal('modalMotivo')" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 8px;">
                <p id="motivoTexto" style="color: #991b1b; font-size: 15px; line-height: 1.6; white-space: pre-wrap;"></p>
            </div>
            
            <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                <button onclick="cerrarModal('modalMotivo')" 
                        style="padding: 12px 24px; background-color: #e5e7eb; color: #374151; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; font-size: 15px;"
                        onmouseover="this.style.backgroundColor='#d1d5db'" onmouseout="this.style.backgroundColor='#e5e7eb'">
                    Cerrar
                </button>
            </div>
        </div>

    </div>
</div>