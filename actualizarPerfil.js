// INPUTS - Coinciden con los IDs del HTML
const nombre = document.getElementById("nombre");
const apellido = document.getElementById("apellido");
const edad = document.getElementById("edad");
const telefono = document.getElementById("telefono");
const email = document.getElementById("email");
const fecha = document.getElementById("fecha");

// ELEMENTOS DE ERROR
const errorNombre = document.getElementById("errorNombre");
const errorApellido = document.getElementById("errorApellido");
const errorEdad = document.getElementById("errorEdad");
const errorTelefono = document.getElementById("errorTelefono");

// ALERTAS GENERALES
const alertaGeneral = document.getElementById("alertaGeneral");
const alertaExito = document.getElementById("alertaExito");

// BOTONES
const btnEditar = document.getElementById("btnEditar");
const btnGuardar = document.getElementById("btnGuardar");
const btnCancelar = document.getElementById("btnCancelar");

// Variable para guardar los IDs necesarios y los datos originales
let datosActuales = {};
let datosOriginales = {};
let modoEdicion = false;

// Función para limpiar todos los errores
function limpiarErrores() {
    errorNombre.textContent = '';
    errorApellido.textContent = '';
    errorEdad.textContent = '';
    errorTelefono.textContent = '';
}

// Función para limpiar alerta de éxito
function limpiarExitos() {
    alertaExito.classList.add('hidden');
    alertaExito.textContent = '';
}

// Función para mostrar alerta general
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

// Función para mostrar alerta de éxito general
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
// 1. OBTENER DATOS DEL PHP VÍA AJAX
// ===============================================
async function cargarDatos() {
    try {
        console.log("🔄 Solicitando datos del perfil...");
        
        // Hacer petición GET al PHP para obtener los datos
        const response = await fetch("actualizarPerfil.php?action=get_profile");
        
        const text = await response.text();
        console.log(" Respuesta RAW del servidor:", text);
        console.log("Status code:", response.status);
        
        if (!response.ok) {
            console.error("❌ El servidor respondió con error:", response.status);
            alert("Error del servidor: " + text);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = JSON.parse(text);
        console.log(" Respuesta parseada:", data);
        
        if (!data.success || !data.data) {
            console.error(" Error:", data.message);
            alert(data.message || "No se pudieron cargar los datos del perfil");
            return;
        }
        
        const p = data.data;
        
        // Guardar IDs para el UPDATE
        datosActuales = {
            id_paciente: p.ID_Paciente || 0, // 0 si no existe paciente asociado
            id_usuario: p.ID_Usuario // Siempre debe existir
        };
        
        // Llenar los campos
        nombre.value = p.Nombre_Paciente || '';
        apellido.value = p.Apellido_Paciente || '';
        edad.value = p.Edad || '';
        telefono.value = p.Telefono_Paciente || '';
        email.value = p.email || '';
        fecha.value = p.Fecha_Registro ? p.Fecha_Registro.split(' ')[0] : '';
        
        // Guardar los datos originales para el botón Cancelar
        datosOriginales = {
            nombre: nombre.value,
            apellido: apellido.value,
            edad: edad.value,
            telefono: telefono.value
        };
        
        console.log(" Datos cargados correctamente");
        console.log("IDs guardados:", datosActuales);
        
    } catch (err) {
        console.error(" Error cargando datos:", err);
        alert("Error al cargar los datos del perfil. Por favor, recarga la página.");
    }
}

// Cargar datos cuando la página esté lista
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cargarDatos);
} else {
    cargarDatos();
}

// ===============================================
// 2. BOTONES: EDITAR, GUARDAR, CANCELAR
// ===============================================

// BOTÓN EDITAR - Activa el modo de edición
btnEditar.addEventListener("click", () => {
    // Limpiar mensajes previos
    limpiarErrores();
    limpiarExitos();
    
    // Habilitar campos editables
    [nombre, apellido, edad, telefono].forEach(campo => {
        campo.disabled = false;
        campo.classList.remove('bg-subtle-light', 'dark:bg-subtle-dark');
        campo.classList.add('bg-white', 'dark:bg-gray-800', 'ring-2', 'ring-primary');
    });
    
    // Cambiar visibilidad de botones
    btnEditar.classList.add('hidden');      // Ocultar Editar
    btnGuardar.classList.remove('hidden');  // Mostrar Guardar
    btnCancelar.classList.remove('hidden'); // Mostrar Cancelar
    
    modoEdicion = true;
    console.log(" Modo edición activado");
});

// BOTÓN GUARDAR - Envía los cambios
btnGuardar.addEventListener("click", async () => {
    // Limpiar errores previos
    limpiarErrores();
    
    // Validaciones
    let hayError = false;
    
    if (!nombre.value.trim()) {
        errorNombre.textContent = 'El nombre es obligatorio';
        hayError = true;
    }
    
    if (!apellido.value.trim()) {
        errorApellido.textContent = 'El apellido es obligatorio';
        hayError = true;
    }
    
    const edadNum = parseInt(edad.value);
    if (edadNum < 18 || edadNum > 120) {
        errorEdad.textContent = 'Edad inválida (debe ser 18-120 años)';
        hayError = true;
    }
    
    if (!telefono.value.trim() || telefono.value.trim().length < 6) {
        errorTelefono.textContent = 'Teléfono inválido (mínimo 6 caracteres)';
        hayError = true;
    }
    
    if (hayError) {
        return;
    }
    
    if (!datosActuales.id_paciente || !datosActuales.id_usuario) {
        alert("Error: No se han cargado los datos del paciente correctamente");
        console.error("Datos actuales:", datosActuales);
        return;
    }
    
    const datos = {
        id_paciente: datosActuales.id_paciente || 0, // 0 si no existe paciente asociado
        id_usuario: datosActuales.id_usuario,
        nombre: nombre.value.trim(),
        apellido: apellido.value.trim(),
        edad: edadNum,
        telefono: telefono.value.trim()
    };
    
    console.log("📤 Enviando datos:", datos);

    try {
        const response = await fetch("actualizarPerfil.php", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify(datos)
        });

        console.log("📥 Status de respuesta:", response.status);
        const text = await response.text();
        console.log("📥 Respuesta RAW del servidor:", text);

        if (!response.ok) {
            console.error("❌ Error HTTP:", response.status, text);
            throw new Error(`Error HTTP ${response.status}: ${text}`);
        }

        const result = JSON.parse(text);
        console.log("📥 Respuesta parseada:", result);
        
        if (result.success) {
            // Limpiar errores
            limpiarErrores();
            
            // Mostrar alerta de éxito general
            mostrarAlertaExito('Datos actualizados correctamente');
            
            // Actualizar datos originales
            datosOriginales = {
                nombre: nombre.value,
                apellido: apellido.value,
                edad: edad.value,
                telefono: telefono.value
            };
            
            // Deshabilitar campos
            [nombre, apellido, edad, telefono].forEach(campo => {
                campo.disabled = true;
                campo.classList.remove('bg-white', 'dark:bg-gray-800', 'ring-2', 'ring-primary');
                campo.classList.add('bg-subtle-light', 'dark:bg-subtle-dark');
            });
            
            // Cambiar visibilidad de botones
            btnEditar.classList.remove('hidden');    // Mostrar Editar
            btnGuardar.classList.add('hidden');      // Ocultar Guardar
            btnCancelar.classList.add('hidden');     // Ocultar Cancelar
            
            modoEdicion = false;
            console.log("🔒 Modo edición desactivado - Cambios guardados");
        } else {
            // Mostrar error general si no hay éxito
            if (result.message.includes('No se realizaron cambios')) {
                mostrarAlertaGeneral("⚠️ " + result.message, 'warning');
            } else {
                mostrarAlertaGeneral("❌ " + result.message, 'error');
            }
        }
    } catch (err) {
        console.error("❌ Error completo:", err);
        mostrarAlertaGeneral("❌ Error al guardar: " + err.message, 'error');
    }
});

// BOTÓN CANCELAR - Descarta los cambios y vuelve al modo normal
btnCancelar.addEventListener("click", () => {
    // Restaurar los datos originales
    nombre.value = datosOriginales.nombre;
    apellido.value = datosOriginales.apellido;
    edad.value = datosOriginales.edad;
    telefono.value = datosOriginales.telefono;
    
    // Limpiar mensajes
    limpiarErrores();
    limpiarExitos();
    
    // Deshabilitar campos
    [nombre, apellido, edad, telefono].forEach(campo => {
        campo.disabled = true;
        campo.classList.remove('bg-white', 'dark:bg-gray-800', 'ring-2', 'ring-primary');
        campo.classList.add('bg-subtle-light', 'dark:bg-subtle-dark');
    });
    
    // Cambiar visibilidad de botones
    btnEditar.classList.remove('hidden');    // Mostrar Editar
    btnGuardar.classList.add('hidden');      // Ocultar Guardar
    btnCancelar.classList.add('hidden');     // Ocultar Cancelar
    
    modoEdicion = false;
    console.log("❌ Edición cancelada - Datos restaurados");
});