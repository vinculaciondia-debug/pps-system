// ============================================
// VARIABLES GLOBALES
// ============================================
let currentStep = 1;
const totalSteps = 3;
let feriadosArray = [];

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema iniciado');
    showStep(1);
    initFileUploads();
    initTipoPracticaListener();
    initFotoPreview();
    inicializarSistemaHorario();
    configurarListeners();
});

// ============================================
// INICIALIZAR SISTEMA DE HORARIO
// ============================================
function inicializarSistemaHorario() {
    console.log('✅ Sistema de horario flexible iniciado');
    
    const dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
    dias.forEach(dia => {
        const checkbox = document.querySelector(`.dia-checkbox[data-dia="${dia}"]`);
        const horarioDiv = document.getElementById(`horario_${dia}`);
        const desactivadoDiv = document.getElementById(`desactivado_${dia}`);
        
        if (checkbox && horarioDiv && desactivadoDiv) {
            if (!checkbox.checked) {
                horarioDiv.classList.add('hidden');
                desactivadoDiv.classList.remove('hidden');
            } else {
                horarioDiv.classList.remove('hidden');
                desactivadoDiv.classList.add('hidden');
                calcularHorasDia(dia);
            }
        }
    });
    
    setTimeout(() => calcularTotales(), 100);
}

// ============================================
// CONFIGURAR LISTENERS
// ============================================
function configurarListeners() {
    const fechaInicioInput = document.getElementById('fecha_inicio');
    if (fechaInicioInput) {
        fechaInicioInput.addEventListener('change', function() {
            console.log('Fecha de inicio cambiada:', this.value);
            calcularTotales();
        });
    }
}













// ============================================
// NAVEGACIÓN ENTRE PASOS
// ============================================
function showStep(step) {
    const steps = document.querySelectorAll('.step-content');
    steps.forEach((el, index) => {
        el.classList.toggle('hidden', index + 1 !== step);
    });

    document.getElementById('prevBtn').classList.toggle('hidden', step === 1);
    document.getElementById('nextBtn').classList.toggle('hidden', step === totalSteps);
    document.getElementById('submitBtn').classList.toggle('hidden', step !== totalSteps);

    updateStepIndicators(step);
}

function changeStep(n) {
    hideError();
    
    if (n === 1 && !validateCurrentStep()) {
        return;
    }
    
    currentStep += n;
    if (currentStep < 1) currentStep = 1;
    if (currentStep > totalSteps) currentStep = totalSteps;
    
    showStep(currentStep);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// ACTUALIZAR INDICADORES
// ============================================
function updateStepIndicators(step) {
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const stepNum = index + 1;
        const circle = indicator.querySelector('.step-circle');
        const text = indicator.querySelector('.step-text');
        const line = indicator.nextElementSibling;
        
        circle.classList.remove(
            'bg-gray-300', 'bg-gradient-to-br', 'from-blue-500', 'to-indigo-600',
            'bg-green-500', 'from-green-600', 'to-emerald-600', 'ring-4',
            'ring-blue-200', 'ring-green-200', 'shadow-lg', 'shadow-green-200'
        );
        
        if (text) text.classList.remove('text-gray-400', 'text-green-600', 'text-blue-700', 'text-gray-700');
        
        if (stepNum < step) {
            circle.classList.add('bg-gradient-to-br', 'from-green-600', 'to-emerald-600', 'ring-4', 'ring-green-200', 'shadow-lg');
            circle.innerHTML = `<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
            if (text) text.classList.add('text-green-600', 'font-bold');
            
            if (line && line.classList.contains('step-line')) {
                line.classList.remove('bg-gray-300');
                line.classList.add('bg-gradient-to-r', 'from-green-500', 'to-emerald-500');
            }
        } else if (stepNum === step) {
            circle.classList.add('bg-gradient-to-br', 'from-blue-500', 'to-indigo-600', 'ring-4', 'ring-blue-200', 'shadow-lg');
            circle.textContent = stepNum;
            if (text) text.classList.add('text-blue-700', 'font-bold');
            
            if (line && line.classList.contains('step-line')) {
                line.classList.add('bg-gray-300');
            }
        } else {
            circle.classList.add('bg-gray-300');
            circle.textContent = stepNum;
            if (text) text.classList.add('text-gray-400');
            
            if (line && line.classList.contains('step-line')) {
                line.classList.add('bg-gray-300');
            }
        }
    });
}

// ============================================
// VALIDACIÓN
// ============================================
function validateCurrentStep() {
    if (currentStep === 1) {
        const tipoPractica = document.querySelector('input[name="tipo_practica"]:checked');
        if (!tipoPractica) {
            showError('Por favor selecciona el tipo de práctica');
            return false;
        }
        
        if (tipoPractica.value === 'normal') {
            const modalidad = document.querySelector('input[name="modalidad"]:checked');
            if (!modalidad) {
                showError('Por favor selecciona la modalidad de trabajo');
                return false;
            }
        }
    }
    
    if (currentStep === 2) {
        const requiredFields = [
            { name: 'numero_cuenta', label: 'Número de Cuenta' },
            { name: 'telefono_alumno', label: 'Teléfono' },
            { name: 'nombre_empresa', label: 'Nombre de la Empresa' },
            { name: 'tipo_empresa', label: 'Tipo de Empresa' },
            { name: 'direccion_empresa', label: 'Dirección de la Empresa' },
            { name: 'nombre_jefe', label: 'Nombre del Jefe' },
            { name: 'numero_jefe', label: 'Teléfono del Jefe' },
            { name: 'correo_jefe', label: 'Correo del Jefe' }
        ];
        
        for (let field of requiredFields) {
            const input = document.querySelector(`[name="${field.name}"]`);
            if (!input || !input.value.trim()) {
                showError(`El campo "${field.label}" es obligatorio`);
                input?.focus();
                return false;
            }
        }
        
        const fotoInput = document.getElementById('foto_estudiante');
        if (!fotoInput || !fotoInput.files.length) {
            showError('Debes subir una foto del estudiante');
            return false;
        }
        
        const emailInput = document.querySelector('[name="correo_jefe"]');
        if (!isValidEmail(emailInput.value)) {
            showError('Por favor ingresa un correo electrónico válido');
            emailInput.focus();
            return false;
        }
        
        const tipoPractica = document.querySelector('input[name="tipo_practica"]:checked')?.value;
        
        if (tipoPractica === 'normal') {
            const fechaInicio = document.querySelector('[name="fecha_inicio"]');
            if (!fechaInicio || !fechaInicio.value.trim()) {
                showError('El campo "Fecha de Inicio" es obligatorio');
                fechaInicio?.focus();
                return false;
            }
            
            const diasMarcados = document.querySelectorAll('.dia-checkbox:checked').length;
            if (diasMarcados === 0) {
                showError('Debes seleccionar al menos un día laborable');
                return false;
            }
        }
        
        if (tipoPractica === 'trabajo') {
            const trabajoFields = [
                { name: 'puesto_trabajo', label: 'Puesto de Trabajo' },
                { name: 'anios_trabajando', label: 'Años Trabajando' }
            ];
            
            for (let field of trabajoFields) {
                const input = document.querySelector(`[name="${field.name}"]`);
                if (!input || !input.value.trim()) {
                    showError(`El campo "${field.label}" es obligatorio`);
                    input?.focus();
                    return false;
                }
            }
        }
    }
    
    return true;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showError(message) {
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    if (errorMessage) errorMessage.textContent = message;
    if (errorAlert) {
        errorAlert.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function hideError() {
    const errorAlert = document.getElementById('errorAlert');
    if (errorAlert) errorAlert.classList.add('hidden');
}

// ============================================
// TOGGLE DÍA
// ============================================
function toggleDia(dia) {
    const checkbox = document.querySelector(`.dia-checkbox[data-dia="${dia}"]`);
    const horarioDiv = document.getElementById(`horario_${dia}`);
    const desactivadoDiv = document.getElementById(`desactivado_${dia}`);
    const activoInput = document.querySelector(`input[name="dias_laborables[${dia}][activo]"]`);
    
    if (!checkbox || !horarioDiv || !desactivadoDiv || !activoInput) {
        console.error('❌ Elementos no encontrados para', dia);
        return;
    }
    
    if (checkbox.checked) {
        horarioDiv.classList.remove('hidden');
        desactivadoDiv.classList.add('hidden');
        activoInput.value = 'true';
        
        const entrada = document.querySelector(`.entrada-${dia}`);
        const salida = document.querySelector(`.salida-${dia}`);
        
        if (entrada && salida && !entrada.value && !salida.value) {
            entrada.value = '08:00';
            salida.value = '17:00';
        }
        
        calcularHorasDia(dia);
    } else {
        horarioDiv.classList.add('hidden');
        desactivadoDiv.classList.remove('hidden');
        activoInput.value = 'false';
        
        const horasInput = document.querySelector(`.horas-${dia}`);
        const horasDisplay = document.querySelector(`.horas-display-${dia}`);
        if (horasInput) horasInput.value = '0';
        if (horasDisplay) horasDisplay.textContent = '0.0 hrs';
    }
    
    calcularTotales();
}

// ============================================
// CALCULAR HORAS
// ============================================
function calcularHorasDia(dia) {
    const entrada = document.querySelector(`.entrada-${dia}`)?.value;
    const salida = document.querySelector(`.salida-${dia}`)?.value;
    
    if (!entrada || !salida) return;
    
    try {
        const [entradaHora, entradaMin] = entrada.split(':').map(Number);
        const [salidaHora, salidaMin] = salida.split(':').map(Number);
        
        let minutosEntrada = entradaHora * 60 + entradaMin;
        let minutosSalida = salidaHora * 60 + salidaMin;
        
        if (minutosSalida < minutosEntrada) {
            minutosSalida += 24 * 60;
        }
        
        const horasCorridas = (minutosSalida - minutosEntrada) / 60;
        const horasLaborales = horasCorridas >= 6.0 ? horasCorridas - 1.0 : horasCorridas;
        const horasRedondeadas = Math.round(horasLaborales * 10) / 10;
        
        const horasInput = document.querySelector(`.horas-${dia}`);
        const displayElement = document.querySelector(`.horas-display-${dia}`);
        
        if (horasInput) horasInput.value = horasRedondeadas;
        if (displayElement) displayElement.textContent = `${horasRedondeadas} hrs`;
        
        calcularTotales();
    } catch (error) {
        console.error(`❌ Error calculando horas para ${dia}:`, error);
    }
}

// ============================================
// APLICAR HORARIO A TODOS
// ============================================
function aplicarHorarioATodos() {
    const horaEntrada = document.getElementById('hora_base_entrada')?.value;
    const horaSalida = document.getElementById('hora_base_salida')?.value;
    
    if (!horaEntrada || !horaSalida) {
        alert('Por favor ingresa hora de entrada y salida');
        return;
    }
    
    const dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
    let diasAplicados = 0;
    
    dias.forEach(dia => {
        const checkbox = document.querySelector(`.dia-checkbox[data-dia="${dia}"]`);
        
        if (checkbox && checkbox.checked) {
            const entrada = document.querySelector(`.entrada-${dia}`);
            const salida = document.querySelector(`.salida-${dia}`);
            
            if (entrada && salida) {
                entrada.value = horaEntrada;
                salida.value = horaSalida;
                calcularHorasDia(dia);
                diasAplicados++;
            }
        }
    });
    
    if (diasAplicados > 0) {
        mostrarNotificacion(`Horario aplicado a ${diasAplicados} día(s)`, 'success');
    } else {
        mostrarNotificacion('Marca al menos un día primero', 'info');
    }
}

// ============================================
// CALCULAR TOTALES
// ============================================
function calcularTotales() {
    let totalSemanal = 0;
    let diasActivos = 0;

    document.querySelectorAll('.dia-container').forEach(container => {
        const dia = container.dataset.dia;
        const horasInput = document.querySelector(`.horas-${dia}`);
        
        if (horasInput) {
            const horas = parseFloat(horasInput.value) || 0;
            if (horas > 0) {
                totalSemanal += horas;
                diasActivos++;
            }
        }
    });

    const promedioDiario = diasActivos > 0 ? (totalSemanal / diasActivos).toFixed(1) : '0.0';

    const totalElement = document.getElementById('total_horas_semanales');
    const promedioElement = document.getElementById('promedio_horas_diarias');
    
    if (totalElement) {
        totalElement.textContent = totalSemanal.toFixed(1) + ' hrs';
    }
    
    if (promedioElement) {
        promedioElement.textContent = promedioDiario + ' hrs';
    }



     // 🆕 CALCULAR FECHA DE FINALIZACIÓN
    calcularFechaFinalizacion();
}





// ============================================
// CALCULAR FECHA DE FINALIZACIÓN EN TIEMPO REAL
// ============================================
// ============================================
// CALCULAR FECHA DE FINALIZACIÓN EN TIEMPO REAL
// ============================================
// ============================================
// CALCULAR FECHA DE FINALIZACIÓN EN TIEMPO REAL
// ============================================
function calcularFechaFinalizacion() {
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const displayFecha = document.getElementById('fecha_fin_display');
    const displaySemanas = document.getElementById('semanas_necesarias');
    
    if (!fechaInicioInput || !displayFecha || !displaySemanas) {
        console.log('⚠️ Elementos no encontrados');
        return;
    }
    
    const fechaInicio = fechaInicioInput.value;
    
    if (!fechaInicio) {
        displayFecha.textContent = 'Ingresa la fecha de inicio';
        displaySemanas.textContent = '0';
        return;
    }
    
    // Recolectar días laborables
    const diasLaborables = {};
    const dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
    
    dias.forEach(dia => {
        const checkbox = document.querySelector(`.dia-checkbox[data-dia="${dia}"]`);
        const entradaInput = document.querySelector(`.entrada-${dia}`);
        const salidaInput = document.querySelector(`.salida-${dia}`);
        const horasInput = document.querySelector(`.horas-${dia}`);
        
        diasLaborables[dia] = {
            activo: checkbox?.checked ? 'true' : 'false',
            hora_entrada: entradaInput?.value || null,
            hora_salida: salidaInput?.value || null,
            horas_laborales: horasInput?.value ? parseFloat(horasInput.value) : 0
        };
    });
    
    // Verificar al menos un día activo
    const hayDiaActivo = Object.values(diasLaborables).some(d => d.activo === 'true');
    
    if (!hayDiaActivo) {
        displayFecha.textContent = 'Selecciona al menos un día laborable';
        displaySemanas.textContent = '0';
        return;
    }
    
    // Mostrar "Calculando..."
    displayFecha.textContent = '⏳ Calculando...';
    
    // Preparar datos
    const datos = {
        fecha_inicio: fechaInicio,
        dias_laborables: diasLaborables,
        dias_feriados: JSON.stringify(feriadosArray)
    };
    
    console.log('📤 Enviando datos:', datos);
    
    // Hacer petición AJAX
    fetch('/estudiantes/solicitud/calcular-fecha', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify(datos)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📥 Respuesta:', data);
        
        if (data.success) {
            // ✅ ACTUALIZAR SOLO EL TEXTO, NO EL HTML
            displayFecha.innerHTML = `
                <span class="text-green-700"> ${data.fecha_fin_formateada}</span><br>
                <span class="text-xs text-gray-600">
                    ${data.horas_semanales} hrs/semana • ${data.dias_trabajados} días laborables
                </span>
            `;
            displaySemanas.textContent = data.semanas_necesarias;
        } else {
            displayFecha.textContent = '⚠️ ' + (data.message || 'Error al calcular');
            displaySemanas.textContent = '0';
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        displayFecha.textContent = '❌ Error de conexión';
        displaySemanas.textContent = '0';
    });
}

// ============================================
// FERIADOS
// ============================================
function agregarFeriado() {
    const inputFecha = document.getElementById('input_feriado');
    const listaFeriados = document.getElementById('lista_feriados');
    const jsonInput = document.getElementById('dias_feriados_json');
    
    if (!inputFecha || !inputFecha.value) {
        alert('Selecciona una fecha');
        return;
    }
    
    const fecha = inputFecha.value;
    
    // Verificar duplicados
    if (feriadosArray.includes(fecha)) {
        alert('Esta fecha ya fue agregada');
        return;
    }
    
    // Agregar al array
    feriadosArray.push(fecha);
    
    // Actualizar JSON
    if (jsonInput) {
        jsonInput.value = JSON.stringify(feriadosArray);
    }
    
    // Actualizar lista visual
    actualizarListaFeriados();
    
    // Limpiar input
    inputFecha.value = '';


     // 🆕 RECALCULAR FECHA
    calcularFechaFinalizacion();
    
    console.log('✅ Feriado agregado:', fecha);
}

function eliminarFeriado(fecha) {
    feriadosArray = feriadosArray.filter(f => f !== fecha);
    
    const jsonInput = document.getElementById('dias_feriados_json');
    if (jsonInput) {
        jsonInput.value = JSON.stringify(feriadosArray);
    }
    
    actualizarListaFeriados();
    console.log('❌ Feriado eliminado:', fecha);


        // 🆕 RECALCULAR FECHA
    calcularFechaFinalizacion();
}

function actualizarListaFeriados() {
    const lista = document.getElementById('lista_feriados');
    
    if (!lista) return;
    
    if (feriadosArray.length === 0) {
        lista.innerHTML = '<p class="text-gray-500 text-sm">No hay feriados agregados</p>';
        return;
    }
    
    lista.innerHTML = feriadosArray.map(fecha => {
        const fechaObj = new Date(fecha + 'T00:00:00');
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        return `
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <span class="text-sm">${fechaFormateada}</span>
                <button type="button" onclick="eliminarFeriado('${fecha}')" 
                        class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
    }).join('');
}

// ============================================
// TIPO PRÁCTICA
// ============================================
function initTipoPracticaListener() {
    const tipoInputs = document.querySelectorAll('input[name="tipo_practica"]');
    tipoInputs.forEach(input => {
        input.addEventListener('change', mostrarCampos);
    });
}

function mostrarCampos() {
    const tipo = document.querySelector('input[name="tipo_practica"]:checked')?.value;

    const modalidadFields = document.getElementById('modalidad_fields');
    const trabajoFields   = document.getElementById('trabajo_fields');
    const normalFields    = document.getElementById('normal_fields');
    const docsNormal      = document.getElementById('docs_normal');
    const docsTrabajo     = document.getElementById('docs_trabajo');

    // Ocultar todo
    modalidadFields?.classList.add('hidden');
    trabajoFields?.classList.add('hidden');
    normalFields?.classList.add('hidden');
    docsNormal?.classList.add('hidden');
    docsTrabajo?.classList.add('hidden');

    // 🔴 DESACTIVAR todos los file inputs al inicio
    document.querySelectorAll('#docs_normal input[type="file"], #docs_trabajo input[type="file"]').forEach(input => {
        input.disabled = true;
    });

    if (tipo === 'normal') {
        // Mostrar secciones de práctica normal
        modalidadFields?.classList.remove('hidden');
        normalFields?.classList.remove('hidden');
        docsNormal?.classList.remove('hidden');

        // 🟢 ACTIVAR solo los archivos del bloque normal
        document.querySelectorAll('#docs_normal input[type="file"]').forEach(input => {
            input.disabled = false;
        });

    } else if (tipo === 'trabajo') {
        // Mostrar secciones de práctica por trabajo
        trabajoFields?.classList.remove('hidden');
        docsTrabajo?.classList.remove('hidden');

        // 🟢 ACTIVAR solo los archivos del bloque trabajo
        document.querySelectorAll('#docs_trabajo input[type="file"]').forEach(input => {
            input.disabled = false;
        });
    }
}


function changeStep(n) {
    hideError();
    
    if (n === 1 && !validateCurrentStep()) {
        return;
    }
    
    currentStep += n;
    if (currentStep < 1) currentStep = 1;
    if (currentStep > totalSteps) currentStep = totalSteps;
    
    showStep(currentStep);
    
    // ✅ NUEVO: Ejecutar mostrarCampos al llegar al paso 3
    if (currentStep === 3) {
        mostrarCampos();
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ✅ NUEVO: Event listeners
document.querySelectorAll('input[name="tipo_practica"]').forEach(radio => {
    radio.addEventListener('change', mostrarCampos);
});

document.addEventListener('DOMContentLoaded', function() {
    mostrarCampos();
});

// ============================================
// FILE UPLOADS
// ============================================
function initFileUploads() {
    const dropAreas = document.querySelectorAll('.file-drop-area');
    
    dropAreas.forEach(dropArea => {
        const fileInput = dropArea.querySelector('.file-input');
        
        if (!fileInput) return;
        
        dropArea.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function() {
            handleFile(this.files[0], dropArea, fileInput);
        });
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('drag-over'), false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('drag-over'), false);
        });
        
        dropArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            handleFile(files[0], dropArea, fileInput);
        });
    });
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleFile(file, dropArea, fileInput) {
    if (!file) return;
    
    if (file.type !== 'application/pdf') {
        showError('Solo se permiten archivos PDF');
        fileInput.value = '';
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        showError('El archivo no debe superar los 5MB');
        fileInput.value = '';
        return;
    }
    
    const fileName = file.name;
    const fileSize = (file.size / 1024 / 1024).toFixed(2);
    
    dropArea.classList.add('has-file');
    
    let previewDiv = dropArea.querySelector('.file-preview');
    if (!previewDiv) {
        previewDiv = document.createElement('div');
        previewDiv.className = 'file-preview';
        dropArea.insertBefore(previewDiv, dropArea.firstChild);
    }
    
    const originalContent = dropArea.querySelectorAll('svg, p');
    originalContent.forEach(el => el.style.display = 'none');
    
    previewDiv.innerHTML = `
        <div class="flex items-center justify-center">
            <svg class="w-10 h-10 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-left">
                <p class="font-semibold text-gray-800">${fileName}</p>
                <p class="text-sm text-gray-600">${fileSize} MB</p>
            </div>
            <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="removeFile(this)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
}

function removeFile(button) {
    const dropArea = button.closest('.file-drop-area');
    const fileInput = dropArea.querySelector('.file-input');
    
    fileInput.value = '';
    dropArea.classList.remove('has-file');
    
    const previewDiv = dropArea.querySelector('.file-preview');
    if (previewDiv) {
        previewDiv.remove();
    }
    
    const originalContent = dropArea.querySelectorAll('svg, p');
    originalContent.forEach(el => el.style.display = '');
}

// ============================================
// FOTO PREVIEW
// ============================================
function initFotoPreview() {
    const fotoInput = document.getElementById('foto_estudiante');
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('fotoPreviewImg');
                    const preview = document.getElementById('fotoPreview');
                    const fileName = document.getElementById('fotoFileName');
                    
                    if (img) img.src = e.target.result;
                    if (preview) preview.classList.remove('hidden');
                    if (fileName) fileName.textContent = file.name;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// ============================================
// NOTIFICACIÓN
// ============================================
function mostrarNotificacion(mensaje, tipo = 'info') {
    const notif = document.createElement('div');
    const colores = {
        success: 'bg-green-500',
        info: 'bg-blue-500',
        error: 'bg-red-500'
    };
    
    notif.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${colores[tipo]} text-white font-medium`;
    notif.textContent = mensaje;
    
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.style.opacity = '0';
        notif.style.transition = 'opacity 0.3s';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}
(function empresaAutocomplete() {
  const input = document.getElementById('empresa_nombre_input');
  const dropdown = document.getElementById('empresa_dropdown');
  const list = document.getElementById('empresa_list');
  const hiddenId = document.getElementById('empresa_id');
  const addBtn = document.getElementById('empresa_add_btn');
  const addName = document.getElementById('empresa_add_name');

  if (!input || !dropdown || !list || !hiddenId || !addBtn || !addName) return;

  const searchUrl = window.PPS?.empresas?.searchUrl;
  const storeUrl  = window.PPS?.empresas?.storeUrl;
  const csrfToken = window.PPS?.empresas?.csrf;

  if (!searchUrl || !storeUrl || !csrfToken) {
    console.warn('Faltan URLs/CSRF para empresas. Revisa window.PPS.empresas en el Blade.');
    return;
  }

  let timer = null;
  let lastQuery = '';

  function openDropdown() { dropdown.classList.remove('hidden'); }
  function closeDropdown() { dropdown.classList.add('hidden'); }

  function clearSelection() {
    hiddenId.value = '';
  }

  function render(items, typed) {
    list.innerHTML = '';

    if (!items.length) {
      const li = document.createElement('li');
      li.className = "px-4 py-3 text-sm text-gray-500";
      li.textContent = "Sin coincidencias.";
      list.appendChild(li);
    } else {
      items.forEach((it) => {
        const li = document.createElement('li');
        li.className = "px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm";
        li.textContent = it.nombre;
        li.addEventListener('click', () => {
          input.value = it.nombre;
          hiddenId.value = it.id;
          closeDropdown();
        });
        list.appendChild(li);
      });
    }

    addName.textContent = typed || '';
  }

  async function doSearch(q) {
    const url = new URL(searchUrl, window.location.origin);
    url.searchParams.set('q', q);

    const res = await fetch(url.toString(), {
      headers: { 'Accept': 'application/json' }
    });

    if (!res.ok) return [];
    return await res.json();
  }

  async function createEmpresa(nombre) {
    const res = await fetch(storeUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({ nombre })
    });

    if (res.status === 422) {
      const data = await res.json().catch(() => ({}));
      throw new Error(data?.message || 'No se pudo crear (validación).');
    }

    if (!res.ok) throw new Error('No se pudo crear la empresa.');
    return await res.json();
  }

  input.addEventListener('input', () => {
    const q = input.value.trim();
    clearSelection();

    if (q.length < 2) {
      closeDropdown();
      return;
    }

    clearTimeout(timer);
    timer = setTimeout(async () => {
      try {
        lastQuery = q;
        const items = await doSearch(q);

        if (input.value.trim() !== lastQuery) return;

        render(items, q);
        openDropdown();
      } catch (e) {
        closeDropdown();
        console.error(e);
      }
    }, 250);
  });

  addBtn.addEventListener('click', async () => {
    const nombre = input.value.trim();
    if (nombre.length < 2) return;

    try {
      const created = await createEmpresa(nombre);
      input.value = created.nombre;
      hiddenId.value = created.id;
      closeDropdown();
    } catch (e) {
      alert(e.message);
    }
  });

  document.addEventListener('click', (ev) => {
    if (!dropdown.contains(ev.target) && ev.target !== input) {
      closeDropdown();
    }
  });

  input.addEventListener('keydown', (ev) => {
    if (ev.key === 'Backspace' || ev.key === 'Delete') {
      clearSelection();
    }
  });
})();

// ============================================
// SUBMIT
// ============================================
const form = document.getElementById('ppsForm');
if (form) {
    let formSubmitting = false;
    
    form.addEventListener('submit', function(e) {
        if (formSubmitting) {
            e.preventDefault();
            return false;
        }
        
        formSubmitting = true;
        
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enviando...
            `;
        }
        
        return true;
    });
}

console.log('✅ Sistema cargado completamente');




