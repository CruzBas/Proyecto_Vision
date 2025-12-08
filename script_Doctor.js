(function () {


    const tbody = document.getElementById('tbody_citas');
    const loader = document.getElementById('loaderCitas');
    const btnRecargarCitas = document.getElementById('btnRecargarCitas');
    const alertaExitoGlobal = document.getElementById('alertaExitoGlobal');
    const alertaGeneralGlobal = document.getElementById('alertaGeneralGlobal');

    const filtroNombrePaciente = document.getElementById('f_paciente');
    const filtroEstado = document.getElementById('f_estado');
    const filtroOrden = document.getElementById('f_ordenamiento');

    // Modal Estado Cita
    const modalCambiarEstadoOverlay = document.getElementById('modalCambiarEstadoOverlay');
    const modalEstadoSelect = document.getElementById('modalEstadoSelect');
    const modalCitaInfo = document.getElementById('modalCitaInfo');
    const btnCancelarEstado = document.getElementById('btnCancelarEstado');
    const btnGuardarEstado = document.getElementById('btnGuardarEstado');

    let citasData = [];
    let citaActualEnEdicion = null;



    // LOGOUT Y MODALES DE CONFIRMACIÓN

    const logoutBtn = document.getElementById('logoutBtn');
    const Logout = document.getElementById('btnLogout'); // Sidebar bottom
    const overlay = document.getElementById('confirmarOverlay');
    const modalWrap = document.getElementById('confirmaModal');
    const btnCancelar = document.getElementById('cancelarLogout');
    const btnConfirmar = document.getElementById('confirmarLogout');

    function toggleModal(show) {
        if (!overlay || !modalWrap) return;
        const add = show ? ['opacity-100'] : ['opacity-0', 'pointer-events-none'];
        const remove = show ? ['opacity-0', 'pointer-events-none'] : ['opacity-100'];

        overlay.classList.remove(...remove);
        overlay.classList.add(...add);
        modalWrap.classList.remove(...remove);
        modalWrap.classList.add(...add);
        modalWrap.classList.toggle('scale-95', !show);
        modalWrap.classList.toggle('scale-100', show);
    }

    const openConfirm = () => toggleModal(true);
    const closeConfirm = () => toggleModal(false);

    if (logoutBtn) logoutBtn.addEventListener('click', (e) => { e.preventDefault(); openConfirm(); });
    if (Logout) Logout.addEventListener('click', (e) => { e.preventDefault(); openConfirm(); }); // Para botón extra si hubiera
    if (btnCancelar) btnCancelar.addEventListener('click', closeConfirm);
    if (overlay) overlay.addEventListener('click', closeConfirm);

    if (btnConfirmar) btnConfirmar.addEventListener('click', function () {
        if (window.Auth && window.Auth.logout) window.Auth.logout();
    });

    //  NAVEGACIÓN Y PANELES

    const CitasBtn = document.getElementById('CitasBtn');
    const Configuracion_Btn = document.getElementById('Configuracion_Btn');

    // Paneles
    const dashboardPanel = document.getElementById('dashboardPanel');
    const CitasPanel = document.getElementById('CitasPanel');
    const Configuracion_Panel = document.getElementById('Configuracion_Panel');
    const Configuracion_Email = document.getElementById('Configuracion_Email');

    function setActive(btn) {
        [CitasBtn, Configuracion_Btn].forEach(b => {
            if (b) {
                b.classList.remove('bg-primary', 'text-white');
                b.classList.add('text-slate-700', 'dark:text-slate-300');
            }
        });
        if (btn) {
            btn.classList.add('bg-primary', 'text-white');
            btn.classList.remove('text-slate-700', 'dark:text-slate-300');
        }
    }

    function Cambio_modal(ModalActivo) {
        [dashboardPanel, CitasPanel, Configuracion_Panel].forEach(p => {
            if (p) p.classList.add('hidden');
        });
        if (ModalActivo) ModalActivo.classList.remove('hidden');
    }


    if (CitasBtn) CitasBtn.addEventListener('click', (e) => {
        e.preventDefault();
        Cambio_modal(CitasPanel);
        setActive(CitasBtn);
        cargarCitas();
    });


    if (Configuracion_Btn) Configuracion_Btn.addEventListener('click', (e) => {
        e.preventDefault();
        Cambio_modal(Configuracion_Panel);
        setActive(Configuracion_Btn);
        if (window.Auth && Configuracion_Email) Configuracion_Email.textContent = window.Auth.getUser().email;
    });


    setActive(CitasBtn);
    Cambio_modal(CitasPanel);
    cargarCitas();

    // LOGICA MODAL ACTUALIZAR PERFIL

    const profOpenBtn = document.getElementById('openProfileModalBtn');
    const profOverlay = document.getElementById('profileModalOverlay');
    const perfilContainer = document.getElementById('perfileModalContainer');
    const perfilCloseBtn = document.getElementById('closeProfileBtn');
    const profCancelBtn = document.getElementById('cancelProfileBtn');
    const profSaveBtn = document.getElementById('saveProfileBtn');

    const Nuevo_Nombre = document.getElementById('edit_nombre');
    const Nuevo_Apellido = document.getElementById('edit_apellido');
    const Nueva_Edad = document.getElementById('edit_edad');
    const Nuevo_Telefono = document.getElementById('edit_telefono');
    const modalAlert = document.getElementById('modalAlert');

    let Info_ActualPerfil = { id_paciente: null, id_usuario: null };

    function Mostar_Alert(msg, type = 'error') {
        if (!modalAlert) return;
        modalAlert.textContent = msg;
        modalAlert.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
        if (type === 'success') {
            modalAlert.classList.add('bg-green-100', 'text-green-700');
        } else {
            modalAlert.classList.add('bg-red-100', 'text-red-700');
        }
        modalAlert.classList.remove('hidden');
    }

    function Ocultar_Alert() {
        if (modalAlert) modalAlert.classList.add('hidden');
    }

    async function Cargar_InfoPerfil() {
        try {
            const resp = await fetch('actualizarPerfil.php?action=get_profile');
            const data = await resp.json();

            if (data.success && data.data) {
                const p = data.data;
                Info_ActualPerfil.id_paciente = p.ID_Paciente;
                Info_ActualPerfil.id_usuario = p.ID_Usuario;

                if (Nuevo_Nombre) Nuevo_Nombre.value = p.Nombre_Paciente || '';
                if (Nuevo_Apellido) Nuevo_Apellido.value = p.Apellido_Paciente || '';
                if (Nueva_Edad) Nueva_Edad.value = p.Edad || '';
                if (Nuevo_Telefono) Nuevo_Telefono.value = p.Telefono_Paciente || '';
            } else {
                Mostar_Alert('Error cargando datos: ' + (data.message || 'Desconocido'));
            }
        } catch (e) {
            console.error(e);
            Mostar_Alert('Error de conexión al cargar perfil');
        }
    }

    async function guardar_Info_Perfil() {
        Ocultar_Alert();

        if (!Nuevo_Nombre.value.trim() || !Nuevo_Apellido.value.trim()) {
            Mostar_Alert('Nombre y Apellido son obligatorios');
            return;
        }

        const payload = {
            id_paciente: Info_ActualPerfil.id_paciente,
            id_usuario: Info_ActualPerfil.id_usuario,
            nombre: Nuevo_Nombre.value.trim(),
            apellido: Nuevo_Apellido.value.trim(),
            edad: parseInt(Nueva_Edad.value),
            telefono: Nuevo_Telefono.value.trim()
        };

        try {
            const resp = await fetch('actualizarPerfil.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            });
            const res = await resp.json();

            if (res.success) {
                Mostar_Alert('Datos actualizados correctamente', 'success');
                const nameDisplay = document.getElementById('perfilName');
                const welcomeTitle = document.getElementById('welcomeTitle');
                if (nameDisplay) nameDisplay.textContent = `${payload.nombre} ${payload.apellido}`;
                if (welcomeTitle) welcomeTitle.textContent = `¡Bienvenido, ${payload.nombre} ${payload.apellido}!`;

                setTimeout(Cerrar_PerfilModal, 1500);
            } else {
                Mostar_Alert(res.message || 'Error al guardar');
            }
        } catch (e) {
            console.error(e);
            Mostar_Alert('Error de conexión al guardar');
        }
    }

    function Abrir_PerfilModal() {
        if (!profOverlay || !perfilContainer) return;
        Ocultar_Alert();
        profOverlay.classList.remove('opacity-0', 'pointer-events-none');
        perfilContainer.classList.remove('opacity-0', 'pointer-events-none');
        perfilContainer.querySelector('div').classList.remove('scale-95');
        perfilContainer.querySelector('div').classList.add('scale-100');
        Cargar_InfoPerfil();
    }

    function Cerrar_PerfilModal() {
        if (!profOverlay || !perfilContainer) return;
        profOverlay.classList.add('opacity-0', 'pointer-events-none');
        perfilContainer.classList.add('opacity-0', 'pointer-events-none');
        perfilContainer.querySelector('div').classList.add('scale-95');
        perfilContainer.querySelector('div').classList.remove('scale-100');
    }

    if (profOpenBtn) profOpenBtn.addEventListener('click', Abrir_PerfilModal);
    if (perfilCloseBtn) perfilCloseBtn.addEventListener('click', Cerrar_PerfilModal);
    if (profCancelBtn) profCancelBtn.addEventListener('click', Cerrar_PerfilModal);
    if (profSaveBtn) profSaveBtn.addEventListener('click', guardar_Info_Perfil);
    if (profOverlay) profOverlay.addEventListener('click', Cerrar_PerfilModal);


    // LOGICA CARGAR CITAS


    function mostrarAlertaGlobal(mensaje, tipo = 'error') {
        const el = tipo === 'success' ? alertaExitoGlobal : alertaGeneralGlobal;
        if (!el) return;
        el.textContent = mensaje;
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 4000);
    }


    async function cargarCitas() {
        if (!tbody) return;
        try {
            loader.classList.remove('hidden');
            tbody.innerHTML = '';

            const response = await fetch('Citas_Psicologo.php?action=get_citas', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await response.json();

            if (!data.success) {
                mostrarAlertaGlobal((data.message || 'Error cargando citas'), 'error');
                citasData = [];
            } else {
                citasData = data.data;
            }

            aplicarFiltros();

        } catch (err) {
            console.error("Error:", err);
            mostrarAlertaGlobal('Error de conexión con el servidor', 'error');
        } finally {
            loader.classList.add('hidden');
        }
    }

    if (btnRecargarCitas) btnRecargarCitas.addEventListener('click', cargarCitas);

    // RENDERIZAR CITAS
    function renderizarCitas(citasAMostrar) {
        if (!tbody) return;
        if (citasAMostrar.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-gray-500 py-8">No se encontraron citas.</td></tr>';
            return;
        }

        tbody.innerHTML = citasAMostrar.map(cita => {
            const fechaObj = new Date(cita.Fecha_Cita);
            const fecha = fechaObj.toLocaleDateString();
            const hora = fechaObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            let claseEstado = '';
            if (cita.Estado === 'Pendiente') claseEstado = 'estado-pendiente';
            else if (cita.Estado === 'Confirmada') claseEstado = 'estado-confirmada';
            else if (cita.Estado === 'Completada') claseEstado = 'estado-completada';
            else if (cita.Estado === 'Cancelada') claseEstado = 'estado-cancelada';

            const nombrePaciente = `${cita.Nombre_Paciente} ${cita.Apellido_Paciente}`;

            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-100 dark:border-gray-800">
                    <td class="p-4 text-sm font-mono text-gray-500">#${cita.ID_Cita}</td>
                    <td class="p-4 text-sm font-medium text-gray-900 dark:text-gray-100">${nombrePaciente}</td>
                    <td class="p-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>${cita.Telefono_Paciente || 'N/A'}</div>
                        <div class="text-xs text-gray-400">${cita.Correo_Paciente || ''}</div>
                    </td>
                    <td class="p-4 text-sm text-gray-600 dark:text-gray-400">${fecha} <br> <span class="text-xs">${hora}</span></td>
                    <td class="p-4 text-sm text-gray-600 dark:text-gray-400 truncate max-w-[150px]" title="${cita.Motivo}">${cita.Motivo}</td>
                    <td class="p-4 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-bold ${claseEstado}">${cita.Estado}</span>
                    </td>
                    <td class="p-4 text-sm text-right">
                        <button class="text-primary hover:text-blue-700 text-xs font-semibold px-3 py-1 border border-primary/20 rounded hover:bg-primary/10 transition"
                            onclick="window.abrirModalEstado(${cita.ID_Cita}, '${cita.Estado}', '${nombrePaciente}')">
                            Cambiar
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // FILTROS
    function aplicarFiltros() {
        let res = [...citasData];

        const txt = filtroNombrePaciente ? filtroNombrePaciente.value.toLowerCase().trim() : '';
        if (txt) {
            res = res.filter(c =>
                (c.Nombre_Paciente + ' ' + c.Apellido_Paciente).toLowerCase().includes(txt)
            );
        }

        const st = filtroEstado ? filtroEstado.value : '';
        if (st) {
            res = res.filter(c => c.Estado === st);
        }

        const ord = filtroOrden ? filtroOrden.value : '';
        if (ord) {
            res.sort((a, b) => {
                const dA = new Date(a.Fecha_Cita);
                const dB = new Date(b.Fecha_Cita);
                if (ord === 'fecha_asc') return dA - dB;
                if (ord === 'fecha_desc') return dB - dA;
                if (ord === 'id_asc') return a.ID_Cita - b.ID_Cita;
                if (ord === 'id_desc') return b.ID_Cita - a.ID_Cita;
                return 0;
            });
        }
        renderizarCitas(res);
    }

    if (filtroNombrePaciente) filtroNombrePaciente.addEventListener('input', aplicarFiltros);
    if (filtroEstado) filtroEstado.addEventListener('change', aplicarFiltros);
    if (filtroOrden) filtroOrden.addEventListener('change', aplicarFiltros);



    window.abrirModalEstado = function (id, estado, nombre) {
        citaActualEnEdicion = id;
        if (modalCitaInfo) modalCitaInfo.textContent = `Paciente: ${nombre} — Estado actual: ${estado}`;
        if (modalEstadoSelect) modalEstadoSelect.value = estado;
        if (modalCambiarEstadoOverlay) modalCambiarEstadoOverlay.classList.remove('hidden');
    };

    function cerrarModalEstado() {
        if (modalCambiarEstadoOverlay) modalCambiarEstadoOverlay.classList.add('hidden');
        citaActualEnEdicion = null;
    }

    async function guardarEstado() {
        if (!citaActualEnEdicion || !modalEstadoSelect) return;
        const nuevoEstado = modalEstadoSelect.value;

        try {
            btnGuardarEstado.textContent = 'Guardando...';
            btnGuardarEstado.disabled = true;

            const response = await fetch('Citas_Psicologo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_cita: citaActualEnEdicion, estado: nuevoEstado })
            });
            const data = await response.json();

            if (data.success) {
                mostrarAlertaGlobal('Estado actualizado correctamente', 'success');
                cerrarModalEstado();
                cargarCitas();
            } else {
                alert(data.message || 'Error al actualizar');
            }
        } catch (e) {
            console.error(e);
            alert('Error de conexión');
        } finally {
            btnGuardarEstado.textContent = 'Guardar';
            btnGuardarEstado.disabled = false;
        }
    }

    if (btnCancelarEstado) btnCancelarEstado.addEventListener('click', cerrarModalEstado);
    if (btnGuardarEstado) btnGuardarEstado.addEventListener('click', guardarEstado);
    if (modalCambiarEstadoOverlay) modalCambiarEstadoOverlay.addEventListener('click', (e) => {
        if (e.target === modalCambiarEstadoOverlay) cerrarModalEstado();
    });

})();