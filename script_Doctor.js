(function () {

    // LOGOUT Y NAVEGACIÓN

    const logoutBtn = document.getElementById('logoutBtn');
    const Logout = document.getElementById('btnLogout');
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
    if (Logout) Logout.addEventListener('click', (e) => { e.preventDefault(); openConfirm(); });
    if (btnCancelar) btnCancelar.addEventListener('click', closeConfirm);
    if (overlay) overlay.addEventListener('click', closeConfirm);

    if (btnConfirmar) btnConfirmar.addEventListener('click', function () {
        if (window.Auth && window.Auth.logout) window.Auth.logout();
    });


    const overviewBtn = document.getElementById('overviewBtn');
    const Configuracion_Btn = document.getElementById('Configuracion_Btn');
    const HistorialBtn = document.getElementById('HistorialBtn');
    const dashboardPanel = document.getElementById('dashboardPanel');
    const Configuracion_Panel = document.getElementById('Configuracion_Panel');
    const HistorialPanel = document.getElementById('HistorialPanel');
    const Configuracion_Email = document.getElementById('Configuracion_Email');

    function setActive(btn) {
        [overviewBtn, Configuracion_Btn, HistorialBtn].forEach(b => {
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
        [dashboardPanel, Configuracion_Panel].forEach(p => {
            if (p) p.classList.add('hidden');
        });
        if (ModalActivo) ModalActivo.classList.remove('hidden');
    }

    if (overviewBtn) overviewBtn.addEventListener('click', (e) => {
        e.preventDefault();
        Cambio_modal(dashboardPanel);
        setActive(overviewBtn);
    });

    if (Configuracion_Btn) Configuracion_Btn.addEventListener('click', (e) => {
        e.preventDefault();
        Cambio_modal(Configuracion_Panel);
        setActive(Configuracion_Btn);
        if (window.Auth && Configuracion_Email) Configuracion_Email.textContent = window.Auth.getUser().email;
    });

    setActive(overviewBtn);

    
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
        modalAlert.classList.add('hidden');
    }

    async function Cargar_InfoPerfil() {
        try {
            const resp = await fetch('actualizarPerfil.php?action=get_profile');
            const data = await resp.json();

            if (data.success && data.data) {
                const p = data.data;
                Info_ActualPerfil.id_paciente = p.ID_Paciente;
                Info_ActualPerfil.id_usuario = p.ID_Usuario;

                Nuevo_Nombre.value = p.Nombre_Paciente || '';
                Nuevo_Apellido.value = p.Apellido_Paciente || '';
                Nueva_Edad.value = p.Edad || '';
                Nuevo_Telefono.value = p.Telefono_Paciente || '';
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
        if (Nueva_Edad.value < 18 || Nueva_Edad.value > 120) {
            Mostar_Alert('Edad inválida (18-120)');
            return;
        }
        if (!Nuevo_Telefono.value.trim() || Nuevo_Telefono.value.length < 6) {
            Mostar_Alert('Teléfono inválido');
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
                // Actualizar interfaz principal
                const nameDisplay = document.getElementById('perfilName');
                const welcomeTitle = document.getElementById('welcomeTitle');
                if (nameDisplay) nameDisplay.textContent = `${payload.nombre} ${payload.apellido}`;
                if (welcomeTitle) welcomeTitle.textContent = `¡Bienvenido, ${payload.nombre} ${payload.apellido}!`;

                setTimeout(Cerrar_CitaModal, 1500);
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

    function Cerrar_CitaModal() {
        if (!profOverlay || !perfilContainer) return;
        profOverlay.classList.add('opacity-0', 'pointer-events-none');
        perfilContainer.classList.add('opacity-0', 'pointer-events-none');
        perfilContainer.querySelector('div').classList.add('scale-95');
        perfilContainer.querySelector('div').classList.remove('scale-100');
    }

    if (profOpenBtn) profOpenBtn.addEventListener('click', Abrir_PerfilModal);
    if (perfilCloseBtn) perfilCloseBtn.addEventListener('click', Cerrar_CitaModal);
    if (profCancelBtn) profCancelBtn.addEventListener('click', Cerrar_CitaModal);
    if (profSaveBtn) profSaveBtn.addEventListener('click', guardar_Info_Perfil);
    if (profOverlay) profOverlay.addEventListener('click', Cerrar_CitaModal);

})();