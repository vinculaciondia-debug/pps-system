<script>
// ============================================
// ABRIR MODAL DE SUPERVISIÓN
// ============================================
function abrirModalSupervision(solicitudId, numeroSupervision) {
    const modal = document.getElementById('modalSupervision');
    const form = document.getElementById('formSupervision');
    
    // Configurar formulario
    form.action = `/supervisor/alumnos/${solicitudId}/supervision`;
    form.reset();
    
    // Configurar número de supervisión
    document.getElementById('numero_supervision').value = numeroSupervision;
    document.getElementById('numeroSupervisionTexto').textContent = numeroSupervision;
    
    // Ocultar errores
    document.getElementById('erroresSupervision').classList.add('hidden');
    document.getElementById('nombreArchivo').classList.add('hidden');
    
    // Mostrar modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
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
// MOSTRAR NOMBRE DEL ARCHIVO SELECCIONADO
// ============================================
function mostrarNombreArchivo(input) {
    const nombreArchivo = document.getElementById('nombreArchivo');
    if (input.files && input.files[0]) {
        nombreArchivo.textContent = `Archivo seleccionado: ${input.files[0].name}`;
        nombreArchivo.classList.remove('hidden');
    } else {
        nombreArchivo.classList.add('hidden');
    }
}

// ============================================
// VALIDACIÓN DEL FORMULARIO
// ============================================
function validarFormularioSupervision() {
    const comentario = document.getElementById('comentario').value.trim();
    const archivo = document.getElementById('archivo').files[0];
    const fechaSupervision = document.getElementById('fecha_supervision').value;  // ✅ NUEVA LÍNEA
    
    const errores = [];
    
    // ✅ AGREGAR TODO ESTE BLOQUE DE VALIDACIÓN DE FECHA
    if (!fechaSupervision) {
        errores.push('La fecha de supervisión es obligatoria');
    } else {
        const fecha = new Date(fechaSupervision);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fecha > hoy) {
            errores.push('La fecha de supervisión no puede ser futura');
        }
    }
    // ✅ FIN DEL BLOQUE DE VALIDACIÓN DE FECHA
    
    // Comentario opcional: solo validar tope
    if (comentario.length > 1000) {
        errores.push('El comentario no puede exceder 1000 caracteres');
    }
    
    // Validar archivo
    if (!archivo) {
        errores.push('Debes seleccionar un archivo');
    } else {
        const extension = archivo.name.split('.').pop().toLowerCase();
        const extensionesValidas = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!extensionesValidas.includes(extension)) {
            errores.push('El archivo debe ser PDF, JPG, JPEG o PNG');
        }
        if (archivo.size > 5242880) {
            errores.push('El archivo no puede superar 5MB');
        }
    }
    
    return errores;
}

function mostrarErrores(errores) {
    const contenedor = document.getElementById('erroresSupervision');
    const lista = document.getElementById('listaErroresSupervision');
    
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
// VALIDACIÓN AL ENVIAR FORMULARIO
// ============================================
document.getElementById('formSupervision').addEventListener('submit', function(e) {
    const errores = validarFormularioSupervision();
    
    if (errores.length > 0) {
        e.preventDefault();
        mostrarErrores(errores);
        return false;
    }
    
    // Deshabilitar botón para evitar doble envío
    const btn = document.getElementById('btnSubirSupervision');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Subiendo...
    `;
});

// ============================================
// CERRAR MODAL CON TECLA ESC
// ============================================
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal('modalSupervision');
    }
});

// ============================================
// DRAG & DROP DE ARCHIVOS
// ============================================
const dropZone = document.querySelector('.border-dashed');
const fileInput = document.getElementById('archivo');

if (dropZone && fileInput) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-green-500', 'bg-green-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-green-500', 'bg-green-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            mostrarNombreArchivo(fileInput);
        }
    }
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>