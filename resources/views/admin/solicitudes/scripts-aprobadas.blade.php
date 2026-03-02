<script>
// ============================================
// VARIABLES GLOBALES
// ============================================
let supervisoresData = [];
let supervisorActualId = null;
let solicitudActual = null;

// ============================================
// CARGAR SUPERVISORES AL INICIAR
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    cargarSupervisores();
});

// ============================================
// FUNCIÓN: Cargar supervisores disponibles
// ============================================
async function cargarSupervisores() {
    try {
        const response = await fetch('{{ route("admin.supervisores.disponibles") }}');
        const data = await response.json();
        
        if (data.success) {
            supervisoresData = data.supervisores;
        } else {
            console.error('Error al cargar supervisores:', data.message);
        }
    } catch (error) {
        console.error('Error en la petición:', error);
    }
}

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
            const esPorTrabajo = s.tipo_practica === 'trabajo';
            const tipoPracticaLabel = esNormal ? 'Normal' : 'Por Trabajo';
            const tipoPracticaColor = esNormal ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800';
            
            // Modalidad (solo para normal)
            const modalidadHTML = s.modalidad ? `
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Modalidad</p>
                    <p class="text-sm sm:text-base text-gray-900 capitalize">${s.modalidad}</p>
                </div>
            ` : '';
            
            // Campos específicos según tipo
            const camposEspecificos = esNormal ? `
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Fecha de inicio</p>
                    <p class="text-sm sm:text-base text-gray-900">${s.fecha_inicio ? new Date(s.fecha_inicio).toLocaleDateString('es-HN') : 'N/A'}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Fecha de finalización</p>
                    <p class="text-sm sm:text-base text-gray-900">${s.fecha_fin ? new Date(s.fecha_fin).toLocaleDateString('es-HN') : 'N/A'}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Fecha finalización calculada</p>
                    <p class="text-sm sm:text-base text-gray-900 font-semibold ${s.fecha_finalizacion_calculada ? 'text-blue-600' : ''}">${s.fecha_finalizacion_calculada ? new Date(s.fecha_finalizacion_calculada).toLocaleDateString('es-HN') : 'No calculada'}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Horario</p>
                    <p class="text-sm sm:text-base text-gray-900">${s.horario || 'N/A'}</p>
                </div>
            ` : `
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Puesto de trabajo</p>
                    <p class="text-sm sm:text-base text-gray-900">${s.puesto_trabajo || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Años trabajando</p>
                    <p class="text-sm sm:text-base text-gray-900">${s.anios_trabajando || 'N/A'} años</p>
                </div>
            `;

            // HTML para la foto del estudiante
            const fotoHTML = s.foto_estudiante_url ? `
                <div class="sm:col-span-2 flex justify-center mb-4">
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

            // Información del supervisor - SOLO PARA PRÁCTICAS NORMALES
            const supervisorHTML = esNormal ? (
                s.supervisor ? `
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 sm:p-6 border border-purple-200">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Supervisor Asignado
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 font-semibold">Nombre</p>
                                <p class="text-sm sm:text-base text-gray-900 font-bold">${s.supervisor.user.name}</p>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 font-semibold">Email</p>
                                <p class="text-sm sm:text-base text-gray-900 break-all">${s.supervisor.user.email}</p>
                            </div>
                        </div>
                    </div>
                ` : `
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <p class="text-yellow-800 font-semibold">⚠️ No hay supervisor asignado</p>
                    </div>
                `
            ) : ''; // Para prácticas por trabajo, no mostrar nada

            // Supervisiones (SOLO para prácticas normales)
            let supervisionesHTML = '';
            if (esNormal) {
                if (s.supervisiones && s.supervisiones.length > 0) {
                    supervisionesHTML = `
                        <div class="bg-green-50 rounded-xl p-4 sm:p-6 border border-green-200">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Supervisiones Realizadas (${s.supervisiones.length}/2)
                            </h3>
                            <div class="space-y-3 sm:space-y-4">
                            ${s.supervisiones.map((sup) => `
                                <div class="bg-white rounded-lg p-3 sm:p-4 border-l-4 border-green-500">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                        <h4 class="font-bold text-gray-900 text-sm sm:text-base">Supervisión #${sup.numero_supervision}</h4>
                                        <span class="text-xs text-gray-500 self-start">${new Date(sup.created_at).toLocaleDateString('es-HN')}</span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-700 mb-3">${sup.comentario || 'Sin comentarios'}</p>
                                    ${sup.archivo ? `
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <a href="/admin/supervisiones/${sup.id}/ver" target="_blank"
                                            class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Ver
                                            </a>
                                            <a href="/admin/supervisiones/${sup.id}/descargar" download
                                            class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Descargar
                                            </a>
                                        </div>
                                    ` : '<p class="text-xs text-gray-500 italic">Sin archivo adjunto</p>'}
                                </div>
                            `).join('')}
                            </div>
                            ${s.supervisiones.length >= 2 ? `
                                <div class="mt-4 p-3 sm:p-4 bg-green-100 border-l-4 border-green-600 rounded-lg">
                                    <p class="text-green-800 font-bold text-sm sm:text-base">✓ Supervisiones completadas</p>
                                    <p class="text-green-700 text-xs sm:text-sm">El estudiante ya puede subir su carta de finalización.</p>
                                </div>
                            ` : `
                                <div class="mt-4 p-3 sm:p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                                    <p class="text-yellow-800 font-semibold text-sm sm:text-base">⏳ Faltan ${2 - s.supervisiones.length} supervisión(es)</p>
                                </div>
                            `}
                        </div>
                    `;
                } else {
                    supervisionesHTML = `
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                            <p class="text-yellow-800 font-semibold text-sm sm:text-base">⚠ No hay supervisiones registradas</p>
                            <p class="text-yellow-700 text-xs sm:text-sm">El supervisor debe realizar las 2 supervisiones requeridas.</p>
                        </div>
                    `;
                }
            }

            // Tipos de documentos
            const tiposDocumentos = {
                'colegiacion': 'Colegiación',
                'documento_ia01': 'Formato IA-01',
                'ia-01': 'Formato IA-01',
                'documento_ia02': 'Formato IA-02',
                'ia-02': 'Formato IA-02',
                'carta_aceptacion': 'Carta de Aceptación',
                'carta_presentacion': 'Carta de Presentación',
                'constancia_trabajo': 'Constancia de Trabajo',
                'constancia_aprobacion': 'Constancia de 100% Clases',
                'carta_finalizacion': 'Carta de Finalización',
                'documento_actualizacion': 'Documento de Actualización',
                'actualizacion': 'Documento de Actualización'
            };

            // Filtrar documentos normalizando a minúsculas SOLO para comparar
            const docsIniciales = s.documentos.filter(d => {
                const tipoLower = (d.tipo || '').toLowerCase();
                return [
                    'carta_presentacion', 
                    'carta_aceptacion', 
                    'documento_ia01', 
                    'ia-01',
                    'documento_ia02',
                    'ia-02',
                    'colegiacion', 
                    'constancia_trabajo', 
                    'constancia_aprobacion',
                    'documento_actualizacion',
                    'actualizacion'
                ].includes(tipoLower);
            });

            const docsFinalizacion = s.documentos.filter(d => {
                const tipoLower = (d.tipo || '').toLowerCase();
                return tipoLower === 'carta_finalizacion';
            });
            
            content.innerHTML = `
                <!-- Alerta de Estado -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-yellow-800 font-bold text-lg mb-1">Práctica en Proceso</p>
                            <p class="text-yellow-700 text-sm">Aprobada el: ${s.fecha_aprobacion ? new Date(s.fecha_aprobacion).toLocaleDateString('es-HN') : new Date(s.created_at).toLocaleDateString('es-HN')}</p>
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
                    </div>
                </div>

                <!-- Supervisor -->
                ${supervisorHTML}

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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 012 2z"/>
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

                <!-- Supervisiones (SOLO para prácticas normales) -->
                ${supervisionesHTML}

                <!-- Documentos Iniciales -->
                <div class="bg-white rounded-xl p-4 sm:p-6 border-2 border-gray-200">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Documentos Adjuntos (${docsIniciales.length})
                    </h3>
                    ${docsIniciales.length > 0 ? `
                        <div class="space-y-2 sm:space-y-3">
                            ${docsIniciales.map(doc => {
                                const tipoLower = (doc.tipo || '').toLowerCase();
                                const nombreDoc = tiposDocumentos[tipoLower] || doc.tipo.replace(/_/g, ' ');
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
                                    <a href="/estudiantes/documentos/${doc.id}/ver" target="_blank"
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

                <!-- Carta de Finalización (si existe) -->
                ${docsFinalizacion.length > 0 ? `
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 sm:p-6 border-2 border-green-200">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        ✓ Carta de Finalización Recibida
                    </h3>
                    ${docsFinalizacion.map(carta => {
                        const estadoRevision = carta.estado_revision || 'PENDIENTE';
                        
                        // Determinar el badge de estado
                        let estadoBadge = '';
                        let botonesAccion = '';
                        
                        if (estadoRevision === 'APROBADA') {
                            estadoBadge = `
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Aprobada
                                </span>
                            `;
                        } else if (estadoRevision === 'RECHAZADA') {
                            estadoBadge = `
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Rechazada
                                </span>
                                ${carta.observacion_revision ? `
                                    <div class="mt-2 p-3 bg-red-50 border-l-4 border-red-400 rounded">
                                        <p class="text-xs text-red-800"><strong>Motivo:</strong> ${carta.observacion_revision}</p>
                                    </div>
                                ` : ''}
                            `;
                        } else {
                            // PENDIENTE - Mostrar botón de rechazar
                            estadoBadge = `
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Pendiente de Revisión
                                </span>
                            `;
                        botonesAccion = `
                            <div class="mt-4 pt-4 border-t border-yellow-200">
                                <button onclick="rechazarCarta(${carta.id}, ${s.id}, '${s.user.name.replace(/'/g, "\\'")}')"
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold flex items-center justify-center gap-2 shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Rechazar Carta
                                </button>
                            </div>
                        `;
                        }
                        
                        return `
                            <div class="bg-white rounded-lg p-3 sm:p-4 border-2 ${estadoRevision === 'APROBADA' ? 'border-green-300' : estadoRevision === 'RECHAZADA' ? 'border-red-300' : 'border-yellow-300'}">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <p class="font-bold text-gray-900 text-sm sm:text-base">Carta de Finalización</p>
                                                ${estadoBadge}
                                            </div>
                                            <p class="text-xs text-gray-600">Subida el ${new Date(carta.created_at).toLocaleDateString('es-HN')}</p>
                                        </div>
                                    </div>
                                    <a href="/estudiantes/documentos/${carta.id}/ver" target="_blank"
                                       class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold text-center flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver Carta
                                    </a>
                                </div>
                                ${botonesAccion}
                            </div>
                        `;
                    }).join('')}
                </div>
                ` : ''}
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-600 font-semibold">Error al cargar los detalles</p>
            </div>
        `;
    }
}

// ============================================
// FUNCIÓN: Cerrar modal
// ============================================
function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}


// ============================================
// FUNCIÓN: Abrir modal finalizar
// ============================================
async function finalizarSolicitud(solicitudId) {
    // Obtener datos de la solicitud
    try {
        const response = await fetch(`{{ url('/admin/solicitudes') }}/${solicitudId}`);
        const data = await response.json();
        
        if (data.success) {
            const s = data.solicitud;
            
            // Verificar requisitos SOLO para prácticas normales
            if (s.tipo_practica === 'normal') {
                const supervisionesCount = s.supervisiones ? s.supervisiones.length : 0;
                const tieneCartaFinalizacion = s.documentos.some(d => {
                    const tipoLower = (d.tipo || '').toLowerCase();
                    return tipoLower === 'carta_finalizacion';
                });
                
                if (supervisionesCount < 2) {
                    alert('⚠ No se puede finalizar: El supervisor debe completar 2 supervisiones.\nActualmente tiene: ' + supervisionesCount);
                    return;
                }
                
                if (!tieneCartaFinalizacion) {
                    alert('⚠ No se puede finalizar: El estudiante debe subir su carta de finalización.');
                    return;
                }
            }
            
            // Configurar el modal según el tipo de práctica
            const esPorTrabajo = s.tipo_practica === 'trabajo';
            
            // Mostrar/ocultar requisitos
            const requisitosDiv = document.getElementById('requisitosFinalizacion');
            if (requisitosDiv) {
                if (esPorTrabajo) {
                    requisitosDiv.classList.add('hidden');
                } else {
                    requisitosDiv.classList.remove('hidden');
                }
            }
            
            // Actualizar nombre del estudiante
            document.getElementById('nombreEstudianteFinalizar').textContent = s.user.name;
            
            // Mostrar mensaje diferente según el tipo de práctica
            const mensajeModal = document.getElementById('mensajeFinalizacion');
            if (mensajeModal) {
                if (esPorTrabajo) {
                    mensajeModal.textContent = '¿Estás seguro de finalizar esta práctica por trabajo?';
                } else {
                    mensajeModal.textContent = '¿Estás seguro de finalizar esta práctica?';
                }
            }
            
            // Configurar acción del formulario
            document.getElementById('formFinalizar').action = `{{ url('/admin/solicitudes') }}/${solicitudId}/finalizar`;
            
            // Mostrar modal
            document.getElementById('modalFinalizar').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al verificar los requisitos de finalización');
    }
}

// ============================================
// EVENTO: Enviar formulario de finalizar con AJAX
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const formFinalizar = document.getElementById('formFinalizar');
    
    if (formFinalizar) {
        formFinalizar.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('span') || submitBtn;
            const originalHTML = submitBtn.innerHTML;
            
            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2">Finalizando...</span>
            `;
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Cerrar modal
                    cerrarModal('modalFinalizar');
                    
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'fixed top-4 right-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-lg z-50 animate-fade-in';
                    alertDiv.innerHTML = `
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-green-800 font-medium">${data.message}</p>
                        </div>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Recargar después de 1.5 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Error al finalizar la práctica');
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
                
                // Mostrar error
                alert('❌ ' + (error.message || 'Error al finalizar la práctica'));
            }
        });
    }
    
    // Evento para el formulario de cambiar supervisor
    const formCambiarSupervisor = document.getElementById('formCambiarSupervisor');
    
    if (formCambiarSupervisor) {
        formCambiarSupervisor.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            
            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2">Cambiando...</span>
            `;
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Cerrar modal
                    cerrarModal('modalCambiarSupervisor');
                    
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'fixed top-4 right-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-lg z-50 animate-fade-in';
                    alertDiv.innerHTML = `
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-green-800 font-medium">${data.message}</p>
                        </div>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Recargar después de 1.5 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Error al cambiar supervisor');
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
                
                // Mostrar error
                alert('❌ ' + (error.message || 'Error al cambiar supervisor'));
            }
        });
    }
});

// ============================================
// FUNCIÓN: Abrir modal cambiar supervisor
// ============================================
function abrirModalCambiarSupervisor(solicitudId, supervisorActual) {
    solicitudActual = solicitudId;
    supervisorActualId = supervisorActual;
    
    // Actualizar form action
    document.getElementById('formCambiarSupervisor').action = `{{ url('/admin/solicitudes') }}/${solicitudId}/cambiar-supervisor`;
    
    // Cargar select de supervisores
    const select = document.getElementById('supervisorSelectCambio');
    select.innerHTML = '<option value="">Selecciona un supervisor...</option>';
    
    supervisoresData.forEach(supervisor => {
        const option = document.createElement('option');
        option.value = supervisor.id;
        option.textContent = `${supervisor.user.name} (${supervisor.asignados}/${supervisor.max_estudiantes})`;
        option.dataset.supervisor = JSON.stringify(supervisor);
        
        if (supervisor.id === supervisorActual) {
            option.textContent += ' (Actual)';
            option.disabled = true;
        }
        
        select.appendChild(option);
    });
    
    document.getElementById('modalCambiarSupervisor').classList.remove('hidden');
}

// ============================================
// FUNCIÓN: Cambiar supervisor
// ============================================
async function cambiarSupervisor(solicitudId) {
    const modal = document.getElementById('modalCambiarSupervisor');
    const form = document.getElementById('formCambiarSupervisor');
    
    // Obtener datos de la solicitud actual
    try {
        const response = await fetch(`{{ url('/admin/solicitudes') }}/${solicitudId}`);
        const data = await response.json();
        
        if (data.success) {
            solicitudActual = data.solicitud;
            supervisorActualId = solicitudActual.supervisor_id;
            
            // Verificar que sea una práctica normal
            if (solicitudActual.tipo_practica === 'trabajo') {
                alert('⚠ Las prácticas por trabajo no tienen supervisor asignado.');
                return;
            }
            
            // Mostrar supervisor actual
            if (solicitudActual.supervisor) {
                document.getElementById('supervisorActualNombre').textContent = solicitudActual.supervisor.user.name;
                document.getElementById('supervisorActualEmail').textContent = solicitudActual.supervisor.user.email;
            } else {
                document.getElementById('supervisorActualNombre').textContent = 'Sin asignar';
                document.getElementById('supervisorActualEmail').textContent = '-';
            }
            
            // Actualizar select excluyendo el supervisor actual
            actualizarSelectCambioSupervisor();
            
            form.action = `{{ url('/admin/solicitudes') }}/${solicitudId}/cambiar-supervisor`;
            modal.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error al cargar solicitud:', error);
        alert('Error al cargar la información de la solicitud');
    }
}

// ============================================
// FUNCIÓN: Actualizar select de cambio (excluyendo actual)
// ============================================
function actualizarSelectCambioSupervisor() {
    const select = document.getElementById('supervisorSelectCambio');
    if (!select) return;
    
    select.innerHTML = '<option value="">Selecciona un supervisor</option>';
    
    // Filtrar supervisores: disponibles y que NO sea el actual
    const disponibles = supervisoresData.filter(s => !s.lleno && s.id !== supervisorActualId);
    const llenos = supervisoresData.filter(s => s.lleno && s.id !== supervisorActualId);
    
    // Agregar supervisores disponibles
    disponibles.forEach(supervisor => {
        const option = document.createElement('option');
        option.value = supervisor.id;
        option.textContent = `${supervisor.nombre} (${supervisor.disponibles} cupos disponibles)`;
        option.dataset.supervisor = JSON.stringify(supervisor);
        select.appendChild(option);
    });
    
    // Agregar supervisores llenos (deshabilitados)
    if (llenos.length > 0) {
        const optgroup = document.createElement('optgroup');
        optgroup.label = 'Sin cupo disponible';
        
        llenos.forEach(supervisor => {
            const option = document.createElement('option');
            option.value = supervisor.id;
            option.textContent = `${supervisor.nombre} (LLENO)`;
            option.disabled = true;
            optgroup.appendChild(option);
        });
        
        select.appendChild(optgroup);
    }
    
    // Si no hay supervisores disponibles
    if (disponibles.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No hay supervisores disponibles para cambio';
        option.disabled = true;
        select.appendChild(option);
    }
}

// ============================================
// EVENTO: Mostrar info del nuevo supervisor seleccionado
// ============================================
document.addEventListener('change', function(e) {
    if (e.target.id === 'supervisorSelectCambio') {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const infoDiv = document.getElementById('nuevoSupervisorInfo');
        
        if (selectedOption.value && selectedOption.dataset.supervisor) {
            const supervisor = JSON.parse(selectedOption.dataset.supervisor);
            
            document.getElementById('nuevoSupervisorEmail').textContent = supervisor.email;
            document.getElementById('nuevoSupervisorCapacidad').textContent = 
                `${supervisor.asignados} de ${supervisor.max_estudiantes} estudiantes`;
            
            infoDiv.classList.remove('hidden');
        } else {
            infoDiv.classList.add('hidden');
        }
    }
});

// ============================================
// CERRAR MODAL AL HACER CLIC FUERA
// ============================================
document.querySelectorAll('[id^="modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal(this.id);
        }
    });
});

// ============================================
// PREVENIR SCROLL DEL BODY CUANDO HAY MODAL ABIERTO
// ============================================
const modales = document.querySelectorAll('[id^="modal"]');
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.attributeName === 'class') {
            const modal = mutation.target;
            if (!modal.classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
            } else {
                const hayModalAbierto = Array.from(modales).some(m => !m.classList.contains('hidden'));
                if (!hayModalAbierto) {
                    document.body.style.overflow = '';
                }
            }
        }
    });
});

modales.forEach(modal => {
    observer.observe(modal, { attributes: true });
});

// ============================================
// FUNCIÓN: Rechazar Carta de Finalización
// ============================================
function rechazarCarta(documentoId, solicitudId, nombreEstudiante) {
    const modal = document.getElementById('modalRechazarCarta');
    const form = document.getElementById('formRechazarCarta');
    
    if (!modal || !form) {
        console.error('Modal o formulario no encontrado');
        return;
    }
    
    // Configurar la acción del formulario
    form.action = `/admin/solicitudes/${solicitudId}/carta-finalizacion/${documentoId}/rechazar`;
    
    // Actualizar el nombre del estudiante
    const nombreElement = document.getElementById('nombreEstudianteCarta');
    if (nombreElement) {
        nombreElement.textContent = nombreEstudiante;
    }
    
    // Limpiar el textarea
    const textarea = form.querySelector('textarea[name="observacion"]');
    if (textarea) {
        textarea.value = '';
    }
    
    // Mostrar el modal
    modal.classList.remove('hidden');
    
    // Hacer scroll al inicio del modal
    const modalContent = modal.querySelector('.bg-white');
    if (modalContent) {
        modalContent.scrollTop = 0;
    }
}

// ============================================
// CERRAR MODAL AL HACER CLIC FUERA
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal de rechazar carta al hacer clic fuera
    const modalRechazar = document.getElementById('modalRechazarCarta');
    if (modalRechazar) {
        modalRechazar.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal('modalRechazarCarta');
            }
        });
    }
    
    // Manejar el envío del formulario de rechazo
    const formRechazar = document.getElementById('formRechazarCarta');
    if (formRechazar) {
        formRechazar.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            
            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2">Rechazando...</span>
            `;
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Cerrar modal
                    cerrarModal('modalRechazarCarta');
                    
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'fixed top-4 right-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-lg z-50 animate-fade-in';
                    alertDiv.innerHTML = `
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-green-800 font-medium">${data.message || 'Carta rechazada correctamente'}</p>
                        </div>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Recargar después de 1.5 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Error al rechazar la carta');
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
                
                // Mostrar error
                alert('❌ ' + (error.message || 'Error al rechazar la carta. Por favor, intenta de nuevo.'));
            }
        });
    }
});
</script>