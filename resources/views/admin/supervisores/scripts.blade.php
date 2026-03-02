<script>
// ============================================
// FUNCIONES PARA MOSTRAR/OCULTAR CONTRASEÑAS
// ============================================
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}

// ============================================
// VALIDACIONES DEL FORMULARIO
// ============================================
function validarFormularioCrear() {
    const nombre = document.getElementById('crear_nombre').value.trim();
    const email = document.getElementById('crear_email').value.trim();
    const password = document.getElementById('crear_password').value;
    const passwordConfirm = document.getElementById('crear_password_confirmation').value;
    const maxEstudiantes = document.getElementById('crear_max_estudiantes').value;
    
    const errores = [];
    
    // Validar nombre
    if (nombre.length < 3) {
        errores.push('El nombre debe tener al menos 3 caracteres');
    }
    
    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        errores.push('El correo electrónico no es válido');
    }
    
    // Validar contraseña
    if (password.length < 8) {
        errores.push('La contraseña debe tener al menos 8 caracteres');
    }
    
    // Validar confirmación de contraseña
    if (password !== passwordConfirm) {
        errores.push('Las contraseñas no coinciden');
    }
    
    // Validar capacidad
    if (maxEstudiantes < 1 || maxEstudiantes > 100) {
        errores.push('La capacidad debe estar entre 1 y 100');
    }
    
    return errores;
}

function validarFormularioEditar() {
    const nombre = document.getElementById('edit_nombre').value.trim();
    const email = document.getElementById('edit_email').value.trim();
    const password = document.getElementById('edit_password').value;
    const passwordConfirm = document.getElementById('edit_password_confirmation').value;
    const maxEstudiantes = document.getElementById('edit_max_estudiantes').value;
    
    const errores = [];
    
    // Validar nombre
    if (nombre.length < 3) {
        errores.push('El nombre debe tener al menos 3 caracteres');
    }
    
    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        errores.push('El correo electrónico no es válido');
    }
    
    // Validar contraseña solo si se ingresó
    if (password.length > 0) {
        if (password.length < 8) {
            errores.push('La contraseña debe tener al menos 8 caracteres');
        }
        
        if (password !== passwordConfirm) {
            errores.push('Las contraseñas no coinciden');
        }
    }
    
    // Validar capacidad
    if (maxEstudiantes < 1 || maxEstudiantes > 100) {
        errores.push('La capacidad debe estar entre 1 y 100');
    }
    
    return errores;
}

function mostrarErrores(errores, contenedorId, listaId) {
    const contenedor = document.getElementById(contenedorId);
    const lista = document.getElementById(listaId);
    
    if (errores.length > 0) {
        lista.innerHTML = '<ul class="list-disc list-inside space-y-1">' + 
            errores.map(error => `<li>${error}</li>`).join('') + 
            '</ul>';
        contenedor.classList.remove('hidden');
    } else {
        contenedor.classList.add('hidden');
        lista.innerHTML = '';
    }
}

// ============================================
// ABRIR MODAL CREAR
// ============================================
function abrirModalCrear() {
    const modal = document.getElementById('modalCrear');
    const form = document.getElementById('formCrear');
    
    // Limpiar formulario
    form.reset();
    
    // Ocultar errores
    document.getElementById('erroresCrear').classList.add('hidden');
    
    // Mostrar modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// ============================================
// ABRIR MODAL EDITAR
// ============================================
async function abrirModalEditar(id) {
    const modal = document.getElementById('modalEditar');
    const form = document.getElementById('formEditar');
    const btnEditar = document.getElementById('btnEditar');
    
    // Ocultar errores
    document.getElementById('erroresEditar').classList.add('hidden');
    
    // Deshabilitar botón mientras carga
    btnEditar.disabled = true;
    btnEditar.innerHTML = `
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Cargando...
    `;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    try {
        const response = await fetch(`{{ url('/admin/supervisores') }}/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const supervisor = data.supervisor;
            
            // Actualizar action del form
            form.action = `{{ url('/admin/supervisores') }}/${id}`;
            
            // Llenar campos
            document.getElementById('edit_nombre').value = supervisor.user.name;
            document.getElementById('edit_email').value = supervisor.user.email;
            document.getElementById('edit_max_estudiantes').value = supervisor.max_estudiantes;
            
            // Limpiar campos de contraseña
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            
            // Habilitar botón
            btnEditar.disabled = false;
            btnEditar.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Cambios
            `;
        } else {
            throw new Error('Error al cargar datos');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar datos del supervisor');
        cerrarModal('modalEditar');
    }
}

// ============================================
// VER DETALLE DEL SUPERVISOR
// ============================================
async function verDetalle(id) {
    const modal = document.getElementById('modalDetalle');
    const content = document.getElementById('detalleContent');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <svg class="animate-spin h-10 w-10 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    `;
    
    try {
        const response = await fetch(`{{ url('/admin/supervisores') }}/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const s = data.supervisor;
            const asignados = data.estudiantes_asignados || 0;
            const disponibles = data.cupos_disponibles || 0;
            const porcentaje = s.max_estudiantes > 0 ? Math.round((asignados / s.max_estudiantes) * 100) : 0;
            
            const colorBarra = porcentaje >= 90 ? 'bg-red-500' : (porcentaje >= 70 ? 'bg-yellow-500' : 'bg-green-500');
            const estadoBadge = s.activo 
                ? '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Activo</span>'
                : '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Inactivo</span>';
            
            content.innerHTML = `
                <!-- Información básica -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                                ${s.user.name.substring(0, 2).toUpperCase()}
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">${s.user.name}</h3>
                                <p class="text-gray-600 flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    ${s.user.email}
                                </p>
                            </div>
                        </div>
                        ${estadoBadge}
                    </div>
                </div>

                <!-- Capacidad y ocupación -->
                <div class="bg-white rounded-xl p-6 border-2 border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Capacidad y Ocupación
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 font-semibold">Capacidad Total</p>
                            <p class="text-3xl font-bold text-blue-800">${s.max_estudiantes}</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-purple-600 font-semibold">Asignados</p>
                            <p class="text-3xl font-bold text-purple-800">${asignados}</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600 font-semibold">Disponibles</p>
                            <p class="text-3xl font-bold text-green-800">${disponibles}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700">Nivel de ocupación</span>
                            <span class="text-sm font-bold text-gray-900">${porcentaje}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="${colorBarra} h-4 rounded-full transition-all flex items-center justify-end pr-2" style="width: ${porcentaje}%">
                                <span class="text-xs font-bold text-white">${porcentaje}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estudiantes asignados -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Estudiantes Asignados (${asignados})
                    </h3>
                    ${s.solicitudes_asignadas && s.solicitudes_asignadas.length > 0 ? `
                        <div class="space-y-3">
                            ${s.solicitudes_asignadas.map((sol, index) => `
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold">
                                            ${index + 1}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">${sol.user.name}</p>
                                            <p class="text-sm text-gray-600 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                ${sol.user.email}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                        ${sol.estado_solicitud}
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-gray-600 font-semibold">Sin estudiantes asignados</p>
                            <p class="text-gray-500 text-sm">Este supervisor aún no tiene estudiantes a cargo</p>
                        </div>
                    `}
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
                <p class="text-red-600 font-semibold">Error al cargar los datos</p>
            </div>
        `;
    }
}

// ============================================
// CERRAR MODAL
// ============================================
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// ============================================
// VALIDACIÓN AL ENVIAR FORMULARIOS
// ============================================
document.getElementById('formCrear').addEventListener('submit', function(e) {
    const errores = validarFormularioCrear();
    
    if (errores.length > 0) {
        e.preventDefault();
        mostrarErrores(errores, 'erroresCrear', 'listaErroresCrear');
        return false;
    }
    
    // Deshabilitar botón para evitar doble envío
    const btn = document.getElementById('btnCrear');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Creando...
    `;
});

document.getElementById('formEditar').addEventListener('submit', function(e) {
    const errores = validarFormularioEditar();
    
    if (errores.length > 0) {
        e.preventDefault();
        mostrarErrores(errores, 'erroresEditar', 'listaErroresEditar');
        return false;
    }
    
    // Deshabilitar botón para evitar doble envío
    const btn = document.getElementById('btnEditar');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Guardando...
    `;
});

// ============================================
// CERRAR MODAL CON TECLA ESC
// ============================================
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal('modalCrear');
        cerrarModal('modalEditar');
        cerrarModal('modalDetalle');
    }
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* ============================================
   OCULTAR OJITO NATIVO DEL NAVEGADOR
   ============================================ */
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear {
    display: none;
}

input[type="password"]::-webkit-contacts-auto-fill-button,
input[type="password"]::-webkit-credentials-auto-fill-button {
    visibility: hidden;
    pointer-events: none;
    position: absolute;
    right: 0;
}
</style>