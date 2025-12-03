// ELEMENTOS DEL DOM
const contenedorCitas = document.getElementById('contenedorCitas');
const loader = document.getElementById('loader');
const alertaGeneral = document.getElementById('alertaGeneral');
const alertaExito = document.getElementById('alertaExito');

// Variable para almacenar las citas
let citas = [];

// ===============================================
// FUNCIONES DE ALERTAS
// ===============================================

function mostrarAlertaGeneral(mensaje, tipo) {
    alertaGeneral.textContent = mensaje;
    alertaGeneral.classList.remove('hidden', 'bg-red-100', 'bg-yellow-100', 'border', 'border-red-400', 'border-yellow-400', 'text-red-700', 'text-yellow-700');
    
    if (tipo === 'error') {
        alertaGeneral.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
    } else if (tipo === 'warning') {
        alertaGeneral.classList.add('bg-yellow-100', 'border', 'border-yellow-400', 'text-yellow-700');
    }
    
    // Scroll a la parte superior
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Ocultar después de 4 segundos
    setTimeout(() => {
        alertaGeneral.classList.add('hidden');
    }, 4000);
}

function mostrarAlertaExito(mensaje) {
    alertaExito.textContent = '✅ ' + mensaje;
    alertaExito.classList.remove('hidden');
    
    // Scroll a la parte superior
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
        alertaExito.classList.add('hidden');
    }, 3000);
}

// ===============================================
// CARGAR CITAS
// ===============================================

async function cargarCitas() {
    try {
        console.log("🔄 Cargando citas...");
        loader.classList.remove('hidden');
        contenedorCitas.innerHTML = '';
        
        const response = await fetch('Citas.php?action=get_citas');
        const text = await response.text();
        
        console.log("📦 Respuesta RAW:", text);
        console.log("📊 Status HTTP:", response.status);
        console.log("📋 Headers:", response.headers);
        
        if (!response.ok) {
            console.error("❌ Error HTTP:", response.status);
            console.error("❌ Respuesta:", text);
            throw new Error(`Error HTTP ${response.status}: ${text}`);
        }
        
        const data = JSON.parse(text);
        console.log("✅ Datos parseados:", data);
        
        if (!data.success) {
            mostrarAlertaGeneral('❌ ' + data.message, 'error');
            loader.classList.add('hidden');
            return;
        }
        
        citas = data.data;
        console.log("✅ Citas cargadas:", citas);
        
        if (citas.length === 0) {
            contenedorCitas.innerHTML = '<p class="text-center text-muted-light dark:text-muted-dark py-8">No tienes citas programadas</p>';
        } else {
            renderizarCitas();
        }
        
    } catch (err) {
        console.error("❌ Error completo:", err);
        console.error("❌ Stack:", err.stack);
        mostrarAlertaGeneral('❌ Error al cargar las citas: ' + err.message, 'error');
    } finally {
        loader.classList.add('hidden');
    }
}

// ===============================================
// RENDERIZAR CITAS
// ===============================================

function renderizarCitas() {
    contenedorCitas.innerHTML = '';
    
    citas.forEach(cita => {
        const fechaCita = new Date(cita.Fecha_Cita);
        const fechaFormato = fechaCita.toLocaleDateString('es-ES', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const horaFormato = fechaCita.toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const estadoColor = obtenerColorEstado(cita.Estado);
        const citatarjeta = document.createElement('div');
        citatarjeta.className = 'p-6 rounded-lg bg-background-light dark:bg-subtle-dark/50 border border-subtle-light dark:border-subtle-dark hover:shadow-md transition-shadow';
        citatarjeta.innerHTML = `
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Información de la cita -->
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-primary">calendar_today</span>
                        <h3 class="text-lg font-bold">${fechaFormato}</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 ml-8">
                        <div>
                            <p class="text-sm text-muted-light dark:text-muted-dark">Hora</p>
                            <p class="font-medium">${horaFormato}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-light dark:text-muted-dark">Duración</p>
                            <p class="font-medium">${cita.Duracion} min</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-light dark:text-muted-dark">Motivo</p>
                            <p class="font-medium">${cita.Motivo}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-light dark:text-muted-dark">Estado Actual</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium ${estadoColor}">
                                ${cita.Estado}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Dropdown de Estado -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium">Cambiar Estado</label>
                    <select class="px-3 py-2 rounded-md border border-subtle-light dark:border-subtle-dark bg-white dark:bg-subtle-dark text-foreground-light dark:text-foreground-dark" onchange="actualizarEstadoCita(${cita.ID_Cita}, ${cita.ID_Paciente}, this.value)">
                        <option value="">-- Seleccionar --</option>
                        ${cita.Estado !== 'Pendiente' ? `<option value="Pendiente">Pendiente</option>` : ''}
                        ${cita.Estado !== 'Confirmada' ? `<option value="Confirmada">Confirmada</option>` : ''}
                        ${cita.Estado !== 'Completada' ? `<option value="Completada">Completada</option>` : ''}
                        ${cita.Estado !== 'Cancelada' ? `<option value="Cancelada">Cancelada</option>` : ''}
                    </select>
                </div>
            </div>
        `;
        
        contenedorCitas.appendChild(citatarjeta);
    });
}

// ===============================================
// OBTENER COLOR DE ESTADO
// ===============================================

function obtenerColorEstado(estado) {
    switch(estado) {
        case 'Pendiente':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300';
        case 'Confirmada':
            return 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300';
        case 'Completada':
            return 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300';
        case 'Cancelada':
            return 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-700 dark:bg-gray-900/50 dark:text-gray-300';
    }
}

// ===============================================
// ACTUALIZAR ESTADO DE CITA
// ===============================================

async function actualizarEstadoCita(id_cita, id_paciente, nuevo_estado) {
    if (!nuevo_estado) {
        return;
    }
    
    try {
        console.log("📤 Actualizando cita:", { id_cita, nuevo_estado });
        
        const response = await fetch('Citas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id_cita: id_cita,
                id_paciente: id_paciente,
                estado: nuevo_estado
            })
        });
        
        const text = await response.text();
        console.log("📥 Respuesta:", text);
        
        if (!response.ok) {
            throw new Error(`Error HTTP ${response.status}`);
        }
        
        const result = JSON.parse(text);
        
        if (result.success) {
            // Actualizar en el array local
            const indexCita = citas.findIndex(c => c.ID_Cita === id_cita);
            if (indexCita !== -1) {
                citas[indexCita].Estado = nuevo_estado;
            }
            
            // Rerenderizar
            renderizarCitas();
            
            mostrarAlertaExito('Estado de cita actualizado correctamente');
        } else {
            mostrarAlertaGeneral('❌ ' + result.message, 'error');
        }
    } catch (err) {
        console.error("❌ Error:", err);
        mostrarAlertaGeneral('❌ Error al actualizar la cita: ' + err.message, 'error');
    }
}

// ===============================================
// INICIALIZACIÓN
// ===============================================

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cargarCitas);
} else {
    cargarCitas();
}
