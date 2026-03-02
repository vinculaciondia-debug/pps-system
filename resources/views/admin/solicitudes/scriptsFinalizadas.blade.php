<script>
// Ver expediente completo
async function verExpediente(solicitudId) {
    const modal = document.getElementById('modalExpediente');
    const content = document.getElementById('expedienteContent');
    
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
            
            // Tipo de práctica
            const esNormal = s.tipo_practica === 'normal';
            const tipoPracticaLabel = esNormal ? 'Normal' : 'Por Trabajo';
            
            // Foto del estudiante para impresión
            const fotoEstudianteHTML = s.foto_estudiante_url ? `
                <div class="print-photo-container">
                    <img src="${s.foto_estudiante_url}" alt="Foto de ${s.user.name}" class="print-photo">
                    <p class="print-photo-caption">Fotografía del Estudiante</p>
                </div>
            ` : '';
            
            // Supervisor HTML
            const supervisorHTML = esNormal && s.supervisor ? `
                <tr>
                    <td class="print-label">Supervisor Asignado:</td>
                    <td class="print-value">${s.supervisor.user.name}</td>
                </tr>
                <tr>
                    <td class="print-label">Email del Supervisor:</td>
                    <td class="print-value">${s.supervisor.user.email}</td>
                </tr>
            ` : '';
            
            // Supervisiones para impresión
            let supervisionesHTML = '';
            if (esNormal && s.supervisiones && s.supervisiones.length > 0) {
                supervisionesHTML = `
                    <div class="print-section">
                        <h2 class="print-section-title">IV. SUPERVISIONES REALIZADAS (${s.supervisiones.length}/2)</h2>
                        ${s.supervisiones.map((sup, index) => `
                            <div class="print-supervision">
                                <h3 class="print-supervision-title">Supervisión #${sup.numero_supervision}</h3>
                                <p><strong>Fecha:</strong> ${new Date(sup.created_at).toLocaleDateString('es-HN', { 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric'
                                })}</p>
                                <p><strong>Comentario:</strong> ${sup.comentario || 'Sin comentarios'}</p>
                                ${sup.archivo ? '<p><strong>Archivo:</strong> Adjunto disponible en el sistema</p>' : '<p><em>Sin archivo adjunto</em></p>'}
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            // Documentos para impresión
            const tiposDocumentos = {
                'colegiacion': 'Colegiación',
                'documento_ia01': 'Formato IA-01',
                'documento_ia02': 'Formato IA-02',
                'carta_aceptacion': 'Carta de Aceptación',
                'carta_presentacion': 'Carta de Presentación',
                'constancia_trabajo': 'Constancia de Trabajo',
                'constancia_aprobacion': 'Constancia de 100% Clases',
                'carta_finalizacion': 'Carta de Finalización'
            };

            const seccionDocumentos = esNormal ? 'V' : 'IV';
            const documentosHTML = s.documentos && s.documentos.length > 0 ? `
                <div class="print-section">
                    <h2 class="print-section-title">${seccionDocumentos}. DOCUMENTOS ADJUNTOS (${s.documentos.length})</h2>
                    <table class="print-table-simple">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 50%;">Tipo de Documento</th>
                                <th style="width: 25%;">Fecha de Carga</th>
                                <th style="width: 20%;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${s.documentos.map((doc, index) => {
                                const tipoDoc = (doc.tipo || '').toLowerCase();
                                const nombreDoc = tiposDocumentos[tipoDoc] || doc.tipo.replace(/_/g, ' ').toUpperCase();
                                return `
                                <tr>
                                    <td style="text-align: center;">${index + 1}</td>
                                    <td>${nombreDoc}</td>
                                    <td>${new Date(doc.created_at).toLocaleDateString('es-HN')}</td>
                                    <td style="text-align: center;">✓ Cargado</td>
                                </tr>
                            `}).join('')}
                        </tbody>
                    </table>
                </div>
            ` : '';
            
            content.innerHTML = `
                <!-- VERSIÓN PANTALLA -->
                <div class="screen-only">
                    <!-- Alerta de Finalización -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-blue-800 font-bold text-lg mb-1">✓ Práctica Finalizada Exitosamente</p>
                                <p class="text-blue-700 text-sm">
                                    Finalizada el: ${new Date(s.updated_at).toLocaleDateString('es-HN', { 
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
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 sm:p-6 border border-blue-200 mb-6">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Información del Estudiante
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            ${s.foto_estudiante_url ? `
                            <div class="sm:col-span-2 flex justify-center">
                                <img src="${s.foto_estudiante_url}" alt="Foto de ${s.user.name}" class="w-32 h-32 object-cover rounded-full border-4 border-blue-200 shadow-lg">
                            </div>
                            ` : ''}
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

                    <!-- Información de la Empresa -->
                    <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-200 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 012 2z"/>
                            </svg>
                            Información de la Empresa
                        </h3>
                        <div class="space-y-3">
                            <p><strong>Empresa:</strong> ${s.nombre_empresa}</p>
                            <p><strong>Tipo:</strong> ${tipoPracticaLabel}</p>
                            <p><strong>Jefe Inmediato:</strong> ${s.nombre_jefe || 'N/A'}</p>
                            ${s.cargo_jefe ? `<p><strong>Cargo del Jefe:</strong> ${s.cargo_jefe}</p>` : ''}
                            ${s.nivel_academico_jefe ? `<p><strong>Nivel Académico del Jefe:</strong> <span class="capitalize">${s.nivel_academico_jefe}</span></p>` : ''}
                            ${esNormal ? `
                                <p><strong>Fecha inicio:</strong> ${s.fecha_inicio ? new Date(s.fecha_inicio).toLocaleDateString('es-HN') : 'N/A'}</p>
                                <p><strong>Fecha fin:</strong> ${s.fecha_fin ? new Date(s.fecha_fin).toLocaleDateString('es-HN') : 'N/A'}</p>
                            ` : `
                                <p><strong>Puesto:</strong> ${s.puesto_trabajo || 'N/A'}</p>
                                <p><strong>Años trabajando:</strong> ${s.anios_trabajando || 'N/A'}</p>
                            `}
                        </div>
                    </div>
                </div>

                <!-- VERSIÓN IMPRESIÓN -->
                <div class="print-only">
                    <!-- Encabezado SIN LOGO -->
                    <div class="print-header">
                        <div class="print-header-text">
                            <h1>UNIVERSIDAD NACIONAL AUTÓNOMA DE HONDURAS</h1>
                            <h2>Facultad de Ciencias Económicas, Administrativas y Contables</h2>
                            <h3>Carrera de Informática Administrativa</h3>
                        </div>
                    </div>

                    <div class="print-title">
                        <h1>EXPEDIENTE DE PRÁCTICA PROFESIONAL</h1>
                        <p class="print-subtitle">Estado: FINALIZADA</p>
                    </div>

                    <!-- Información del Estudiante CON FOTO -->
                    <div class="print-section">
                        <h2 class="print-section-title">I. INFORMACIÓN DEL ESTUDIANTE</h2>
                        
                        <div class="print-section-with-photo">
                            <!-- Foto a la derecha -->
                            ${fotoEstudianteHTML}
                            
                            <!-- Tabla de información -->
                            <table class="print-table">
                                <tr>
                                    <td class="print-label">Nombre Completo:</td>
                                    <td class="print-value">${s.user.name}</td>
                                </tr>
                                <tr>
                                    <td class="print-label">Número de Cuenta:</td>
                                    <td class="print-value">${s.numero_cuenta || 'N/A'}</td>
                                </tr>
                                ${s.dni_estudiante ? `
                                <tr>
                                    <td class="print-label">DNI (Identidad):</td>
                                    <td class="print-value">${s.dni_estudiante}</td>
                                </tr>
                                ` : ''}
                                <tr>
                                    <td class="print-label">Correo Electrónico:</td>
                                    <td class="print-value">${s.user.email}</td>
                                </tr>
                                ${s.telefono_alumno ? `
                                <tr>
                                    <td class="print-label">Teléfono:</td>
                                    <td class="print-value">${s.telefono_alumno}</td>
                                </tr>
                                ` : ''}
                                <tr>
                                    <td class="print-label">Carrera:</td>
                                    <td class="print-value">Informática Administrativa</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Información de la Empresa -->
                    <div class="print-section">
                        <h2 class="print-section-title">II. INFORMACIÓN DE LA EMPRESA</h2>
                        <table class="print-table">
                            <tr>
                                <td class="print-label">Nombre de la Empresa:</td>
                                <td class="print-value">${s.nombre_empresa || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Tipo de Empresa:</td>
                                <td class="print-value">${s.tipo_empresa ? (s.tipo_empresa === 'publica' ? 'Pública' : 'Privada') : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Dirección:</td>
                                <td class="print-value">${s.direccion_empresa || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Nombre del Jefe Inmediato:</td>
                                <td class="print-value">${s.nombre_jefe || 'N/A'}</td>
                            </tr>
                            ${s.cargo_jefe ? `
                            <tr>
                                <td class="print-label">Cargo del Jefe:</td>
                                <td class="print-value">${s.cargo_jefe}</td>
                            </tr>
                            ` : ''}
                            ${s.nivel_academico_jefe ? `
                            <tr>
                                <td class="print-label">Nivel Académico del Jefe:</td>
                                <td class="print-value capitalize">${s.nivel_academico_jefe}</td>
                            </tr>
                            ` : ''}
                            <tr>
                                <td class="print-label">Teléfono del Jefe:</td>
                                <td class="print-value">${s.numero_jefe || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Correo del Jefe:</td>
                                <td class="print-value">${s.correo_jefe || 'N/A'}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Información de la Práctica -->
                    <div class="print-section">
                        <h2 class="print-section-title">III. INFORMACIÓN DE LA PRÁCTICA</h2>
                        <table class="print-table">
                            <tr>
                                <td class="print-label">Tipo de Práctica:</td>
                                <td class="print-value">${tipoPracticaLabel}</td>
                            </tr>
                            ${esNormal ? `
                            <tr>
                                <td class="print-label">Modalidad:</td>
                                <td class="print-value">${s.modalidad ? s.modalidad.charAt(0).toUpperCase() + s.modalidad.slice(1) : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Fecha de Inicio:</td>
                                <td class="print-value">${s.fecha_inicio ? new Date(s.fecha_inicio).toLocaleDateString('es-HN', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Fecha de Finalización:</td>
                                <td class="print-value">${s.fecha_fin ? new Date(s.fecha_fin).toLocaleDateString('es-HN', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Horario:</td>
                                <td class="print-value">${s.horario || 'N/A'}</td>
                            </tr>
                            ${supervisorHTML}
                            ` : `
                            <tr>
                                <td class="print-label">Puesto de Trabajo:</td>
                                <td class="print-value">${s.puesto_trabajo || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Años Trabajando:</td>
                                <td class="print-value">${s.anios_trabajando || 'N/A'}</td>
                            </tr>
                            `}
                            <tr>
                                <td class="print-label">Fecha de Solicitud:</td>
                                <td class="print-value">${new Date(s.created_at).toLocaleDateString('es-HN', { year: 'numeric', month: 'long', day: 'numeric' })}</td>
                            </tr>
                            <tr>
                                <td class="print-label">Fecha de Finalización:</td>
                                <td class="print-value">${new Date(s.updated_at).toLocaleDateString('es-HN', { year: 'numeric', month: 'long', day: 'numeric' })}</td>
                            </tr>
                        </table>
                        ${s.observacion ? `
                        <div class="print-observation">
                            <p><strong>Observaciones:</strong></p>
                            <p>${s.observacion}</p>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Supervisiones -->
                    ${supervisionesHTML}

                    <!-- Documentos -->
                    ${documentosHTML}

                    <!-- Pie de página -->
                    <div class="print-footer">
                        <p>Expediente generado el: ${new Date().toLocaleDateString('es-HN', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                        <p class="print-page-number">Página <span class="page"></span> de <span class="pages"></span></p>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-600 font-semibold">Error al cargar el expediente</p>
            </div>
        `;
    }
}

// Función para imprimir el expediente
function imprimirExpediente() {
    window.print();
}

// Cerrar modal
function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Cerrar al hacer click fuera
document.querySelectorAll('[id^="modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal(this.id);
        }
    });
});
</script>


<style>
/* Estilos para pantalla */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

#modalExpediente > div {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.4) transparent;
}

#modalExpediente > div::-webkit-scrollbar {
    width: 6px;
}

#modalExpediente > div::-webkit-scrollbar-track {
    background: transparent;
}

#modalExpediente > div::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.4);
    border-radius: 10px;
}

#modalExpediente > div::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.6);
}

/* Ocultar versión de impresión en pantalla */
.print-only {
    display: none;
}

/* Capitalize helper */
.capitalize {
    text-transform: capitalize;
}

/* ========================================
   ESTILOS DE IMPRESIÓN PROFESIONALES
   ======================================== */
@media print {
    /* Configuración de página - Márgenes más pequeños */
    @page {
        size: letter;
        margin: 12mm 10mm;
    }
    
    /* Ocultar todo excepto el contenido de impresión */
    body * {
        visibility: hidden;
    }
    
    #modalExpediente,
    #modalExpediente * {
        visibility: visible;
    }
    
    .no-print,
    .screen-only {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    /* Configuración del modal */
    #modalExpediente {
        position: absolute;
        left: 0;
        top: 0;
        margin: 0;
        padding: 0;
        width: 100%;
        background: white !important;
    }
    
    #modalExpediente > div {
        max-width: 100% !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        overflow: visible !important;
        border-radius: 0 !important;
    }
    
    #expedienteContent {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Colores exactos */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* === FOTO DEL ESTUDIANTE === */
    .print-section-with-photo {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }
    
    .print-photo-container {
        flex-shrink: 0;
        width: 100px;
        text-align: center;
        float: right;
        margin-left: 15px;
        margin-bottom: 10px;
    }
    
    .print-photo {
        width: 100px;
        height: 120px;
        object-fit: cover;
        border: 2px solid #003366;
        border-radius: 5px;
        display: block;
        margin: 0 auto 5px;
    }
    
    .print-photo-caption {
        font-size: 7pt;
        color: #666;
        font-style: italic;
        margin: 0;
        line-height: 1.2;
    }
    
    /* Ajustar tabla cuando hay foto */
    .print-section-with-photo .print-table {
        flex: 1;
    }
    
    /* === ENCABEZADO SIN LOGO === */
   
    .print-header {
        text-align: center;
        border-bottom: 3px solid #003366;
        padding: 15px 0;
        margin-bottom: 20px;
        background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    }

    .print-header-text h1 {
        font-size: 16pt;
        font-weight: bold;
        color: #003366;
        margin: 8px 0;
        line-height: 1.4;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .print-header-text h2 {
        font-size: 13pt;
        font-weight: 600;
        color: #003366;
        margin: 6px 0;
        line-height: 1.3;
        letter-spacing: 0.3px;
    }

    .print-header-text h3 {
        font-size: 11pt;
        font-weight: 600;
        color: #003366;
        margin: 6px 0;
        line-height: 1.3;
        letter-spacing: 0.2px;
    }
    
    /* === TÍTULO === */
    .print-title {
        text-align: center;
        margin: 10px 0;
        padding: 10px;
        background: #f0f0f0;
        border: 2px solid #003366;
    }
    
    .print-title h1 {
        font-size: 15pt;
        font-weight: bold;
        color: #003366;
        margin: 0 0 3px 0;
        line-height: 1.2;
    }
    
    .print-subtitle {
        font-size: 10pt;
        font-weight: bold;
        color: #0066cc;
        margin: 0;
        line-height: 1.2;
    }
    
    /* === SECCIONES === */
    .print-section {
        margin: 12px 0;
        page-break-inside: avoid;
    }
    
    .print-section-title {
        font-size: 11pt;
        font-weight: bold;
        color: #003366;
        border-bottom: 2px solid #003366;
        padding-bottom: 3px;
        margin-bottom: 8px;
        line-height: 1.2;
    }
    
    /* === TABLAS === */
    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    
    .print-table tr {
        border-bottom: 1px solid #ddd;
    }
    
    .print-label {
        width: 35%;
        padding: 4px 6px;
        font-weight: bold;
        color: #333;
        font-size: 8.5pt;
        vertical-align: top;
        line-height: 1.3;
    }
    
    .print-value {
        width: 65%;
        padding: 4px 6px;
        color: #000;
        font-size: 8.5pt;
        vertical-align: top;
        line-height: 1.3;
    }
    
    .capitalize {
        text-transform: capitalize;
    }
    
    /* Tabla simple para documentos */
    .print-table-simple {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }
    
    .print-table-simple th {
        background: #003366;
        color: white;
        padding: 5px 6px;
        text-align: left;
        font-size: 8.5pt;
        border: 1px solid #003366;
        line-height: 1.2;
    }
    
    .print-table-simple td {
        padding: 4px 6px;
        font-size: 8pt;
        border: 1px solid #ddd;
        line-height: 1.3;
    }
    
    .print-table-simple tr:nth-child(even) {
        background: #f9f9f9;
    }
    
    /* === SUPERVISIONES === */
    .print-supervision {
        background: #f9f9f9;
        border-left: 3px solid #0066cc;
        padding: 8px;
        margin-bottom: 10px;
        page-break-inside: avoid;
    }
    
    .print-supervision-title {
        font-size: 9.5pt;
        font-weight: bold;
        color: #003366;
        margin-bottom: 5px;
        line-height: 1.2;
    }
    
    .print-supervision p {
        font-size: 8.5pt;
        margin: 3px 0;
        line-height: 1.3;
    }
    
    /* === OBSERVACIONES === */
    .print-observation {
        background: #fffef0;
        border: 1px solid #ddd;
        padding: 8px;
        margin-top: 10px;
        page-break-inside: avoid;
    }
    
    .print-observation p {
        font-size: 8.5pt;
        line-height: 1.4;
        margin: 3px 0;
    }
    
    /* === PIE DE PÁGINA === */
    .print-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 7pt;
        color: #666;
        border-top: 1px solid #ddd;
        padding: 5px 0;
        background: white;
        line-height: 1.2;
    }
    
    .print-footer p {
        margin: 1px 0;
    }
    
    /* Evitar saltos de página inapropiados */
    h1, h2, h3 {
        page-break-after: avoid;
    }
    
    table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    .print-supervision,
    .print-observation {
        page-break-inside: avoid;
    }
    
    /* Ajustar márgenes internos */
    .print-section:first-of-type {
        margin-top: 0;
    }
    
    /* Reducir espacios entre elementos */
    .print-section + .print-section {
        margin-top: 10px;
    }
}

/* Para vista previa en navegador */
@media screen {
    .print-only {
        display: none !important;
    }
}
</style>