// ===============================================
// ELEMENTOS DEL DOM
// ===============================================
const tbody = document.getElementById('tbody_citas');
const loader = document.getElementById('loader');
const alertaGeneral = document.getElementById('alertaGeneral');
const alertaExito = document.getElementById('alertaExito');
const filtroNombrePaciente = document.getElementById('f_paciente');
const filtroEstado = document.getElementById('f_estado');
const modalCambiarEstado = document.getElementById('modalCambiarEstado');
const modalEstadoSelect = document.getElementById('modalEstadoSelect');

// Variables
let citas = [];
let citaActualEnEdicion = null;

// ===============================================
// FUNCIONES DE ALERTAS
// ===============================================

function mostrarAlertaExito(mensaje) {
    alertaExito.textContent = mensaje;
    alertaExito.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => {
        alertaExito.classList.add('hidden');
    }, 3000);
}

function mostrarAlertaGeneral(mensaje, tipo = 'error') {
    alertaGeneral.textContent = mensaje;
    alertaGeneral.classList.remove('hidden', 'bg-red-100', 'bg-yellow-100', 'border-red-400', 'border-yellow-400', 'text-red-700', 'text-yellow-700');
    
    if (tipo === 'error') {
        alertaGeneral.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
    } else if (tipo === 'warning') {
        alertaGeneral.classList.add('bg-yellow-100', 'border', 'border-yellow-400', 'text-yellow-700');
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => {
        alertaGeneral.classList.add('hidden');
    }, 4000);
}

// ===============================================
// CARGAR CITAS
// ===============================================

async function cargarCitas() {
    try {
        console.log("Cargando citas del psicólogo...");
        loader.classList.remove('hidden');
        tbody.innerHTML = '';
        
        const response = await fetch('Citas_Psicologo.php?action=get_citas', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const text = await response.text();
        
        console.log("Respuesta RAW:", text);
        console.log("Status HTTP:", response.status);
        
        const data = JSON.parse(text);
        console.log("Datos parseados:", data);
        
        if (!response.ok || !data.success) {
            mostrarAlertaGeneral((data.message || 'Error al cargar las citas'), 'error');
            loader.classList.add('hidden');
            return;
        }
        
        citas = data.data;
        console.log("Citas cargadas:", citas);
        
        if (citas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-gray-500 py-8">No tienes citas programadas</td></tr>';
        } else {
            renderizarCitas(citas);
        }
        
    } catch (err) {
        console.error("Error:", err);
        mostrarAlertaGeneral('Error al cargar las citas: ' + err.message, 'error');
    } finally {
        loader.classList.add('hidden');
    }
}

// ===============================================
// RENDERIZAR CITAS
// ===============================================

function renderizarCitas(citasAMostrar) {
    if (citasAMostrar.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-gray-500 py-8">No hay resultados</td></tr>';
        return;
    }
    
    tbody.innerHTML = citasAMostrar.map(cita => {
        const fecha = new Date(cita.Fecha_Cita);
        const fechaFormato = fecha.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
        const horaFormato = fecha.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const nombrePaciente = `${cita.Nombre_Paciente} ${cita.Apellido_Paciente}`;
        const telefonoPaciente = cita.Paciente_Telefono || 'N/A';
        const correoPaciente = cita.Correo_Paciente || 'N/A';
        
        let claseEstado = '';
        if (cita.Estado === 'Pendiente') claseEstado = 'estado-pendiente';
        else if (cita.Estado === 'Confirmada') claseEstado = 'estado-confirmada';
        else if (cita.Estado === 'Completada') claseEstado = 'estado-completada';
        else if (cita.Estado === 'Cancelada') claseEstado = 'estado-cancelada';
        
        return `
            <tr class="hover:bg-gray-100 dark:hover:bg-neutral-border-dark/50">
                <td class="p-4 text-sm">#${cita.ID_Cita}</td>
                <td class="p-4 text-sm font-medium">${nombrePaciente}</td>
                <td class="p-4 text-sm">${telefonoPaciente}</td>
                <td class="p-4 text-sm">${correoPaciente}</td>
                <td class="p-4 text-sm">${fechaFormato} ${horaFormato}</td>
                <td class="p-4 text-sm">${cita.Motivo}</td>
                <td class="p-4 text-sm text-center">${cita.Duracion}</td>
                <td class="p-4 text-sm">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${claseEstado}">
                        ${cita.Estado}
                    </span>
                </td>
                <td class="p-4 text-sm text-right">
                    <button onclick="abrirModalCambiarEstado(${cita.ID_Cita}, '${cita.Estado}', '${nombrePaciente}')" 
                            class="text-primary hover:text-primary/80 font-medium text-xs">
                        Cambiar estado
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// ===============================================
// FILTROS
// ===============================================

function aplicarFiltros() {
    let citasFiltradas = citas;
    
    // Filtro por nombre de paciente
    const nombrePaciente = filtroNombrePaciente.value.toLowerCase().trim();
    if (nombrePaciente) {
        citasFiltradas = citasFiltradas.filter(cita => {
            const nombreCompleto = `${cita.Nombre_Paciente} ${cita.Apellido_Paciente}`.toLowerCase();
            return nombreCompleto.includes(nombrePaciente);
        });
    }
    
    // Filtro por estado
    const estado = filtroEstado.value.trim();
    if (estado) {
        citasFiltradas = citasFiltradas.filter(cita => cita.Estado === estado);
    }
    
    renderizarCitas(citasFiltradas);
}

filtroNombrePaciente.addEventListener('input', aplicarFiltros);
filtroEstado.addEventListener('change', aplicarFiltros);

// ===============================================
// MODAL Y CAMBIO DE ESTADO
// ===============================================

function abrirModalCambiarEstado(idCita, estadoActual, nombrePaciente) {
    citaActualEnEdicion = idCita;
    document.getElementById('modalCitaInfo').textContent = `Paciente: ${nombrePaciente} | Estado actual: ${estadoActual}`;
    modalEstadoSelect.value = estadoActual;
    modalCambiarEstado.classList.remove('hidden');
}

function cerrarModal() {
    modalCambiarEstado.classList.add('hidden');
    citaActualEnEdicion = null;
}

async function guardarCambioEstado() {
    if (!citaActualEnEdicion) return;
    
    const nuevoEstado = modalEstadoSelect.value;
    
    try {
        loader.classList.remove('hidden');
        
        const response = await fetch('Citas_Psicologo.php', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_cita: citaActualEnEdicion,
                estado: nuevoEstado
            })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            mostrarAlertaGeneral(data.message, 'error');
            return;
        }
        
        mostrarAlertaExito('Estado de cita actualizado correctamente');
        cerrarModal();
        cargarCitas();
        
    } catch (err) {
        console.error("Error:", err);
        mostrarAlertaGeneral('Error al actualizar la cita: ' + err.message, 'error');
    } finally {
        loader.classList.add('hidden');
    }
}

// Cerrar modal al hacer clic fuera
modalCambiarEstado.addEventListener('click', (e) => {
    if (e.target === modalCambiarEstado) {
        cerrarModal();
    }
});

// ===============================================
// INICIALIZACIÓN
// ===============================================

document.addEventListener('DOMContentLoaded', () => {
    cargarCitas();
});

// ===============================================
// FUNCIONES DE ORDENAMIENTO
// ===============================================

function aplicarOrdenamiento() {
    const selectOrdenamiento = document.getElementById('f_ordenamiento');
    const tipoOrdenamiento = selectOrdenamiento.value;
    
    if (!tipoOrdenamiento || citas.length === 0) {
        renderizarCitas(citas);
        return;
    }
    
    let citasOrdenadas = [...citas];
    
    if (tipoOrdenamiento === 'fecha_asc') {
        citasOrdenadas.sort((a, b) => new Date(a.Fecha_Cita) - new Date(b.Fecha_Cita));
    } else if (tipoOrdenamiento === 'fecha_desc') {
        citasOrdenadas.sort((a, b) => new Date(b.Fecha_Cita) - new Date(a.Fecha_Cita));
    } else if (tipoOrdenamiento === 'id_asc') {
        citasOrdenadas.sort((a, b) => a.ID_Cita - b.ID_Cita);
    } else if (tipoOrdenamiento === 'id_desc') {
        citasOrdenadas.sort((a, b) => b.ID_Cita - a.ID_Cita);
    }
    
    renderizarCitas(citasOrdenadas);
}

function ordenarPorFecha(direccion) {
    if (citas.length === 0) {
        mostrarAlertaGeneral('Error: Primero debes cargar las citas', 'error');
        return;
    }
    
    const citasOrdenadas = [...citas].sort((a, b) => {
        const fechaA = new Date(a.Fecha_Cita);
        const fechaB = new Date(b.Fecha_Cita);
        
        if (direccion === 'asc') {
            return fechaA - fechaB;
        } else {
            return fechaB - fechaA;
        }
    });
    
    renderizarCitas(citasOrdenadas);
}

function ordenarPorID(direccion) {
    if (citas.length === 0) {
        mostrarAlertaGeneral('Error: Primero debes cargar las citas', 'error');
        return;
    }
    
    const citasOrdenadas = [...citas].sort((a, b) => {
        if (direccion === 'asc') {
            return a.ID_Cita - b.ID_Cita;
        } else {
            return b.ID_Cita - a.ID_Cita;
        }
    });
    
    renderizarCitas(citasOrdenadas);
}
