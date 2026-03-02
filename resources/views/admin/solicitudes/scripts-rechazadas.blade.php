<script>
// ============================================
// FUNCIÓN: Ver detalle de solicitud
// ============================================
async function verDetalle(solicitudId) {
    const modal = document.getElementById('modalDetalle');
    const content = document.getElementById('detalleContent');
    
    // Mostrar modal con loading
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    `;
    
    try {
        const response = await fetch(`{{ url('/admin/solicitudes') }}/${solicitudId}`);
        const data = await response.json();
        
        if (data.success) {
            const s = data.solicitud;
            
            // Determinar tipo de práctica
            const esNormal = s.tipo_practica === 'normal';
            const tipoPracticaLabel = esNormal ? 'Normal' : 'Por Trabajo';
            const tipoPracticaColor = esNormal ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800';
            
            // Modalidad (solo para normal)
            const modalidadHTML = s.modalidad ? `
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold mb-2">Modalidad</p>
                    <span class="inline-flex px-3 py-1 text-xs sm:text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 capitalize">
                        ${s.modalidad}
                    </span>
                </div>
            ` : '';
            
            // Campos específicos según tipo
            const camposEspecificos = esNormal ? `
                <div class="bg-white rounded-lg p-3 sm:p-4 border border-gray-200">
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold mb-3">Información de la Práctica</p>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Fecha de inicio</p>
                            <p class="text-sm text-gray-900 font-medium">${s.fecha_inicio ? new Date(s.fecha_inicio).toLocaleDateString('es-HN') : 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Fecha de finalización</p>
                            <p class="text-sm text-gray-900 font-medium">${s.fecha_fin ? new Date(s.fecha_fin).toLocaleDateString('es-HN') : 'N/A'}</p>
                        </div>
                        ${s.fecha_finalizacion_calculada ? `
                        <div>
                            <p class="text-xs text-gray-500">Fecha finalización calculada</p>
                            <p class="text-sm text-blue-600 font-bold">${new Date(s.fecha_finalizacion_calculada).toLocaleDateString('es-HN')}</p>
                        </div>
                        ` : ''}
                        <div>
                            <p class="text-xs text-gray-500">Horario</p>
                            <p class="text-sm text-gray-900 break-words">${s.horario || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            ` : `
                <div class="bg-white rounded-lg p-3 sm:p-4 border border-gray-200">
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold mb-3">Información Laboral</p>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Puesto de trabajo</p>
                            <p class="text-sm text-gray-900 font-medium break-words">${s.puesto_trabajo || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Años trabajando</p>
                            <p class="text-sm text-gray-900 font-medium">${s.anios_trabajando || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // HTML para la foto del estudiante
            const fotoHTML = s.foto_estudiante_url ? `
                <div class="sm:col-span-2 flex justify-center">
                    <div class="relative">
                        <img src="${s.foto_estudiante_url}" 
                             alt="Foto de ${s.user.name}"
                             class="w-32 h-32 sm:w-40 sm:h-40 object-cover rounded-full border-4 border-blue-200 shadow-lg">
                        <div class="absolute bottom-0 right-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center border-4 border-white">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            ` : '';
            
            content.innerHTML = `
                <!-- Alerta de Rechazo -->
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-red-800 font-bold text-lg mb-2">Solicitud Rechazada</p>
                            <p class="text-red-700 font-semibold text-sm mb-1">Motivo del rechazo:</p>
                            <p class="text-red-700 text-sm whitespace-pre-wrap">${s.observaciones || 'No especificado'}</p>
                            <p class="text-red-600 text-xs mt-2">
                                Rechazada el: ${new Date(s.updated_at).toLocaleDateString('es-HN', { 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información del Estudiante -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 sm:p-6 border border-blue-200">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Información del Estudiante
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        ${fotoHTML}
                        <div>
                            <p class="text-xs sm:text-sm text-gray-600 font-semibold">Nombre completo</p>
                            <p class="text-sm sm:text-base text-gray-900 font-bold break-words">${s.user.name}</p>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-600 font-semibold">Número de cuenta</p>
                            <p class="text-sm sm:text-base text-gray-900 font-bold">${s.numero_cuenta || 'N/A'}</p>
                        </div>
                        ${s.dni_estudiante ? `
                        <div>
                            <p class="text-xs sm:text-sm text-gray-600 font-semibold">DNI (Identidad)</p>
                            <p class="text-sm sm:text-base text-gray-900 font-bold">${s.dni_estudiante}</p>
                        </div>
                        ` : ''}
                        ${s.telefono_alumno ? `
                        <div>
                            <p class="text-xs sm:text-sm text-gray-600 font-semibold">Teléfono</p>
                            <p class="text-sm sm:text-base text-gray-900">${s.telefono_alumno}</p>
                        </div>
                        ` : ''}
                        <div class="sm:col-span-2">
                            <p class="text-xs sm:text-sm text-gray-600 font-semibold">Correo electrónico</p>
                            <p class="text-sm sm:text-base text-gray-900 break-all">${s.user.email}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs sm:text-sm text-gray-600 font-semibold">Carrera</p>
                            <p class="text-sm sm:text-base text-gray-900">Informática Administrativa</p>
                        </div>
                    </div>
                </div>

                <!-- Información de la Empresa -->
                <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-200">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 012 2z"/>
                        </svg>
                        Información de la Empresa
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Columna 1: Tipo y Empresa -->
                        <div class="space-y-3 sm:space-y-4">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 font-semibold mb-2">Tipo de práctica</p>
                                <span class="inline-flex px-3 py-1 text-xs sm:text-sm font-semibold rounded-full ${tipoPracticaColor}">
                                    ${tipoPracticaLabel}
                                </span>
                            </div>

                            ${s.tipo_empresa ? `
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 font-semibold mb-2">Tipo de empresa</p>
                                <span class="inline-flex px-3 py-1 text-xs sm:text-sm font-semibold rounded-full ${s.tipo_empresa === 'publica' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">
                                    ${s.tipo_empresa === 'publica' ? 'Pública' : 'Privada'}
                                </span>
                            </div>
                            ` : ''}

                            ${modalidadHTML}
                            
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 font-semibold">Nombre de la empresa</p>
                                <p class="text-sm sm:text-base text-gray-900 font-medium break-words">${s.nombre_empresa || 'N/A'}</p>
                            </div>
                            
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 font-semibold">Dirección</p>
                                <p class="text-sm sm:text-base text-gray-900 break-words">${s.direccion_empresa || 'N/A'}</p>
                            </div>
                        </div>

                        <!-- Columna 2: Jefe y Fechas/Horarios -->
                        <div class="space-y-3 sm:space-y-4">
                            <div class="bg-white rounded-lg p-3 sm:p-4 border border-gray-200">
                                <p class="text-xs sm:text-sm text-gray-600 font-semibold mb-3">Jefe Inmediato</p>
                                <p class="text-sm sm:text-base text-gray-900 font-bold mb-3">${s.nombre_jefe || 'N/A'}</p>
                                
                                <div class="grid grid-cols-1 gap-3">
                                    ${s.cargo_jefe ? `
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 012 2z"/>
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-500">Cargo</p>
                                            <p class="text-sm text-gray-900 font-medium break-words">${s.cargo_jefe}</p>
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                                    ${s.nivel_academico_jefe ? `
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-500">Nivel académico</p>
                                            <p class="text-sm text-gray-900 font-medium capitalize">${s.nivel_academico_jefe}</p>
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-500">Teléfono</p>
                                            <p class="text-sm text-gray-900 break-all">${s.numero_jefe || 'N/A'}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-xs text-gray-500">Correo electrónico</p>
                                            <p class="text-sm text-gray-900 break-all">${s.correo_jefe || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ${camposEspecificos}
                        </div>
                    </div>

                    ${s.observacion ? `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold mb-2">Observaciones</p>
                        <p class="text-sm sm:text-base text-gray-700 whitespace-pre-wrap break-words bg-white rounded-lg p-3">${s.observacion}</p>
                    </div>
                    ` : ''}

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs sm:text-sm text-gray-600 font-semibold">Fecha de solicitud</p>
                        <p class="text-sm sm:text-base text-gray-900">${new Date(s.created_at).toLocaleDateString('es-HN', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="bg-white rounded-xl p-4 sm:p-6 border-2 border-gray-200">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Documentos Adjuntos (${s.documentos.length})
                    </h3>
                    ${s.documentos.length > 0 ? `
                        <div class="space-y-2 sm:space-y-3">
                            ${s.documentos.map(doc => {
                                const tiposDocumentos = {
                                    'colegiacion': 'Colegiación',
                                    'documento_ia01': 'Formato IA-01',
                                    'documento_ia02': 'Formato IA-02',
                                    'carta_aceptacion': 'Carta de Aceptación',
                                    'carta_presentacion': 'Carta de Presentación',
                                    'constancia_trabajo': 'Constancia de Trabajo',
                                    'constancia_aprobacion': 'Constancia de 100% Clases'
                                };
                                const nombreDoc = tiposDocumentos[doc.tipo] || doc.tipo.replace(/_/g, ' ');
                                return `
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 text-sm sm:text-base break-words">${nombreDoc}</p>
                                            <p class="text-xs text-gray-500">${new Date(doc.created_at).toLocaleDateString('es-HN')}</p>
                                        </div>
                                    </div>
                                    <a href="{{ url('/estudiantes/documentos') }}/${doc.id}/ver" target="_blank"
                                       class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold text-center flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                </div>
                            `}).join('')}
                        </div>
                    ` : '<p class="text-gray-500 text-center py-8 text-sm">No hay documentos adjuntos</p>'}
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-600 font-semibold text-sm sm:text-base">Error al cargar los detalles</p>
            </div>
        `;
    }
}

// ============================================
// FUNCIÓN: Ver motivo completo
// ============================================
async function verMotivo(solicitudId) {
    const modal = document.getElementById('modalMotivo');
    const motivoTexto = document.getElementById('motivoTexto');
    
    try {
        const response = await fetch(`{{ url('/admin/solicitudes') }}/${solicitudId}`);
        const data = await response.json();
        
        if (data.success && data.solicitud.observaciones) {
            motivoTexto.textContent = data.solicitud.observaciones;
            modal.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar el motivo del rechazo');
    }
}

// ============================================
// FUNCIÓN: Cerrar modal
// ============================================
function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// ============================================
// EVENTO: Cerrar modal al hacer clic fuera
// ============================================
document.querySelectorAll('[id^="modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal(this.id);
        }
    });
});
</script>

<style>
/* Scrollbar personalizada */
#modalDetalle > div,
#modalMotivo > div {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.4) transparent;
}

#modalDetalle > div::-webkit-scrollbar,
#modalMotivo > div::-webkit-scrollbar {
    width: 6px;
}

#modalDetalle > div::-webkit-scrollbar-track,
#modalMotivo > div::-webkit-scrollbar-track {
    background: transparent;
}

#modalDetalle > div::-webkit-scrollbar-thumb,
#modalMotivo > div::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.4);
    border-radius: 10px;
}

/* Line clamp para texto largo */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animación fade-in */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>