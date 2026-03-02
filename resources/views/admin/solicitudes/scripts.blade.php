<script>
// ============================================
// VARIABLES GLOBALES
// ============================================
let supervisoresData = [];

// ============================================
// FUNCIÓN: Mostrar notificación toast
// ============================================
function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.createElement('div');
    const iconos = {
        success: `<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`,
        error: `<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>`,
        warning: `<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>`
    };
    
    const colores = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500'
    };
    
    toast.className = `fixed top-4 right-4 ${colores[tipo]} text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 z-[9999] transform transition-all duration-300 ease-out`;
    toast.style.transform = 'translateX(400px)';
    toast.innerHTML = `
        ${iconos[tipo]}
        <span class="font-semibold text-sm sm:text-base">${mensaje}</span>
    `;
    
    document.body.appendChild(toast);
    
    // Animación de entrada
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Animación de salida y eliminación
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

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
            actualizarSelectSupervisores();
        } else {
            console.error('Error al cargar supervisores:', data.message);
        }
    } catch (error) {
        console.error('Error en la petición:', error);
    }
}

// ============================================
// FUNCIÓN: Actualizar select de supervisores
// ============================================
function actualizarSelectSupervisores() {
    const select = document.getElementById('supervisorSelect');
    select.innerHTML = '<option value="">Selecciona un supervisor</option>';
    
    // Separar supervisores: disponibles y llenos
    const disponibles = supervisoresData.filter(s => !s.lleno);
    const llenos = supervisoresData.filter(s => s.lleno);
    
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
}

// ============================================
// EVENTO: Mostrar info del supervisor
// ============================================
document.addEventListener('change', function(e) {
    if (e.target.id === 'supervisorSelect') {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const infoDiv = document.getElementById('infoSupervisor');
        
        if (selectedOption.value && selectedOption.dataset.supervisor) {
            const supervisor = JSON.parse(selectedOption.dataset.supervisor);
            
            document.getElementById('supervisorEmail').textContent = supervisor.email;
            document.getElementById('supervisorCapacidad').textContent = 
                `${supervisor.asignados} de ${supervisor.max_estudiantes} estudiantes`;
            
            infoDiv.classList.remove('hidden');
        } else {
            infoDiv.classList.add('hidden');
        }
    }
});

// ============================================
// EVENTO: Cambiar entre modo automático y manual
// ============================================
document.addEventListener('change', function(e) {
    // Mostrar/Ocultar selector de supervisor según el modo
    if (e.target.name === 'modo_asignacion') {
        const contenedorManual = document.getElementById('contenedorSupervisorManual');
        const infoSupervisor = document.getElementById('infoSupervisor');
        const infoAutomatico = document.getElementById('infoAutomatico');
        const supervisorSelect = document.getElementById('supervisorSelect');
        
        if (e.target.value === 'manual') {
            contenedorManual.classList.remove('hidden');
            infoAutomatico.classList.add('hidden');
            supervisorSelect.required = true;
        } else {
            contenedorManual.classList.add('hidden');
            infoSupervisor.classList.add('hidden');
            infoAutomatico.classList.remove('hidden');
            supervisorSelect.required = false;
            supervisorSelect.value = '';
        }
    }
    
    // Mostrar info del supervisor seleccionado manualmente
    if (e.target.id === 'supervisorSelect') {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const infoDiv = document.getElementById('infoSupervisor');
        
        if (selectedOption.value && selectedOption.dataset.supervisor) {
            const supervisor = JSON.parse(selectedOption.dataset.supervisor);
            
            document.getElementById('supervisorEmail').textContent = supervisor.email;
            document.getElementById('supervisorCapacidad').textContent = 
                `${supervisor.asignados} de ${supervisor.max_estudiantes} estudiantes`;
            
            infoDiv.classList.remove('hidden');
        } else {
            infoDiv.classList.add('hidden');
        }
    }
});

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
            <svg class="animate-spin h-8 w-8 sm:h-10 sm:w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
// FUNCIÓN: Abrir modal de aprobar o aprobar directamente
// ============================================
async function abrirModalAprobar(solicitudId) {
    try {
        const response = await fetch(`{{ url('/admin/solicitudes') }}/${solicitudId}`);
        const data = await response.json();
        
        if (data.success) {
            const solicitud = data.solicitud;
            
            if (solicitud.tipo_practica === 'trabajo') {
                // Crear modal de confirmación personalizado
                const confirmarModal = document.createElement('div');
                confirmarModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]';
                confirmarModal.innerHTML = `
                    <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl transform transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Aprobar Práctica por Trabajo</h3>
                                <p class="text-sm text-gray-600">Sin asignación de supervisor</p>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-6">¿Confirmas que deseas aprobar esta práctica por trabajo?</p>
                        <div class="flex gap-3">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold transition">
                                Cancelar
                            </button>
                            <button onclick="aprobarPracticaTrabajo(${solicitudId}); this.closest('.fixed').remove();" 
                                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold transition">
                                Aprobar
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(confirmarModal);
            } else {
                const modal = document.getElementById('modalAprobar');
                const form = document.getElementById('formAprobar');
                
                form.action = `{{ url('/admin/solicitudes') }}/${solicitudId}/aprobar`;
                modal.classList.remove('hidden');
            }
        }
    } catch (error) {
        console.error('Error al obtener solicitud:', error);
        mostrarToast('Error al procesar la solicitud', 'error');
    }
}

// ============================================
// FUNCIÓN: Aprobar práctica por trabajo (sin supervisor)
// ============================================
async function aprobarPracticaTrabajo(solicitudId) {
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'POST');
        
        const response = await fetch(`{{ url('/admin/solicitudes') }}/${solicitudId}/aprobar`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarToast(data.message, 'success');
            
            // Recargar después de 1 segundo
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            mostrarToast(data.message || 'No se pudo aprobar la solicitud', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al conectar con el servidor', 'error');
    }
}

// ============================================
// FUNCIÓN: Abrir modal de rechazar
// ============================================
function abrirModalRechazar(solicitudId) {
    const modal = document.getElementById('modalRechazar');
    const form = document.getElementById('formRechazar');
    
    form.action = `{{ url('/admin/solicitudes') }}/${solicitudId}/rechazar`;
    modal.classList.remove('hidden');
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

// ============================================
// FUNCIÓN: Enviar formulario de aprobar con supervisor
// ============================================
document.addEventListener('submit', async function(e) {
    if (e.target.id === 'formAprobar') {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (!submitBtn) {
            console.error('Botón de submit no encontrado');
            return;
        }
        
        // Buscar el span o usar el botón directamente
        const btnText = submitBtn.querySelector('span') || submitBtn;
        const originalText = btnText.textContent || btnText.innerText;
        const originalHTML = submitBtn.innerHTML;
        
        // Deshabilitar botón y mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">Aprobando...</span>
        `;
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarToast(data.message, 'success');
                cerrarModal('modalAprobar');
                
                // Recargar después de 1 segundo
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                mostrarToast(data.message || 'No se pudo aprobar la solicitud', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarToast('Error al conectar con el servidor', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    }
});

// ============================================
// PREVENIR SCROLL DEL BODY
// ============================================
const modales = document.querySelectorAll('[id^="modal"]');
modales.forEach(modal => {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                if (!modal.classList.contains('hidden')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        });
    });
    observer.observe(modal, { attributes: true });
});
</script>

{{-- ============================================
     ESTILOS CSS
     ============================================ --}}
<style>
/* Scrollbar personalizada en modales */
#modalDetalle > div,
#modalAprobar > div,
#modalRechazar > div {
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.4) transparent;
}

#modalDetalle > div::-webkit-scrollbar,
#modalAprobar > div::-webkit-scrollbar,
#modalRechazar > div::-webkit-scrollbar {
    width: 6px;
}

#modalDetalle > div::-webkit-scrollbar-track,
#modalAprobar > div::-webkit-scrollbar-track,
#modalRechazar > div::-webkit-scrollbar-track {
    background: transparent;
}

#modalDetalle > div::-webkit-scrollbar-thumb,
#modalAprobar > div::-webkit-scrollbar-thumb,
#modalRechazar > div::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.4);
    border-radius: 10px;
}

#modalDetalle > div::-webkit-scrollbar-thumb:hover,
#modalAprobar > div::-webkit-scrollbar-thumb:hover,
#modalRechazar > div::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.6);
}

/* Animación fade-in */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>