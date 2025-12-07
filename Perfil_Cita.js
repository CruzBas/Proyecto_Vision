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
        [dashboardPanel, Configuracion_Panel, HistorialPanel].forEach(p => {
            if (p) p.classList.add('hidden');
        });
        if (ModalActivo) ModalActivo.classList.remove('hidden');
    }

    if (overviewBtn) overviewBtn.addEventListener('click', (e) => {
        e.preventDefault();
        Cambio_modal(dashboardPanel);
        setActive(overviewBtn);
    });

    if (HistorialBtn) HistorialBtn.addEventListener('click', (e) => {
        e.preventDefault();
        Cambio_modal(HistorialPanel);
        setActive(HistorialBtn);
    });

    if (Configuracion_Btn) Configuracion_Btn.addEventListener('click', (e) => {
        e.preventDefault();
        Cambio_modal(Configuracion_Panel);
        setActive(Configuracion_Btn);
        if (window.Auth && Configuracion_Email) Configuracion_Email.textContent = window.Auth.getUser().email;
    });

    setActive(overviewBtn);

    // LÓGICA DEL CALENDARIO 
    const calBtn = document.getElementById('CalendarioBtn');
    const calOverlay = document.getElementById('calendarModalOverlay');
    const calContainer = document.getElementById('calendarModalContainer');
    const calCloseBtn = document.getElementById('closeCalendarBtn');
    const calEl = document.getElementById('calendar');

    // Modal de Detalles del Calendario
    const calDetailModal = document.getElementById('calDetailModal');
    const calDetailContent = document.getElementById('calDetailContent');
    const calDetailCloseBtn = document.getElementById('calDetailCloseBtn');
    const calDetailBackdrop = document.getElementById('calDetailBackdrop');

    let calendarInstance = null;
    let idPaciente = window.Auth ? window.Auth.getUser().idPaciente : null;

    function openCalendarModal() {
        if (!calOverlay || !calContainer) return;

        calOverlay.classList.remove('opacity-0', 'pointer-events-none');
        calContainer.classList.remove('opacity-0', 'pointer-events-none');
        calContainer.querySelector('div').classList.remove('scale-95');
        calContainer.querySelector('div').classList.add('scale-100');

        if (!calendarInstance && calEl && idPaciente) {
            initFullCalendar();
        } else if (calendarInstance) {
            setTimeout(() => {
                calendarInstance.updateSize();
                calendarInstance.refetchEvents();
            }, 200);
        }
    }

    function closeCalendarModal() {
        if (!calOverlay || !calContainer) return;
        calOverlay.classList.add('opacity-0', 'pointer-events-none');
        calContainer.classList.add('opacity-0', 'pointer-events-none');
        calContainer.querySelector('div').classList.add('scale-95');
        calContainer.querySelector('div').classList.remove('scale-100');
    }

    if (calBtn) calBtn.addEventListener('click', (e) => { e.preventDefault(); openCalendarModal(); });
    if (calCloseBtn) calCloseBtn.addEventListener('click', closeCalendarModal);
    if (calOverlay) calOverlay.addEventListener('click', closeCalendarModal);

    function initFullCalendar() {
        calendarInstance = new FullCalendar.Calendar(calEl, {
            initialView: 'timeGridWeek',
            locale: 'es',
            nowIndicator: true,
            slotMinTime: '06:00:00',
            slotMaxTime: '21:00:00',
            firstDay: 1,
            height: '100%',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día'
            },
            events: function (fetchInfo, successCallback, failureCallback) {
                const url = `calendarioPaciente.php?idPaciente=${idPaciente}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            eventDidMount: function (info) {
                const estado = (info.event.extendedProps.estado || '').toLowerCase();
                let bg = '#9ca3af';
                switch (estado) {
                    case 'pendiente': bg = '#f59e0b'; break;
                    case 'confirmada': bg = '#10b981'; break;
                    case 'completada': bg = '#3b82f6'; break;
                    case 'cancelada': bg = '#ef4444'; break;
                }
                info.el.style.backgroundColor = bg;
                info.el.style.borderColor = bg;
                info.el.style.color = 'white';
            },
            eventClick: function (info) {
                const props = info.event.extendedProps;
                const html = `
                    <p><strong>📅 Fecha:</strong> ${props.fecha}</p>
                    <p><strong>⏰ Hora:</strong> ${props.hora}</p>
                    <p><strong>👨‍⚕️ Psicólogo:</strong> ${props.psicologo}</p>
                    <p><strong>📝 Motivo:</strong> ${props.motivo}</p>
                    <p><strong>📌 Estado:</strong> <span class="capitalize">${props.estado}</span></p>
                `;
                calDetailContent.innerHTML = html;
                calDetailModal.classList.remove('hidden');
            }
        });
        calendarInstance.render();
    }
    const closeDetail = () => calDetailModal.classList.add('hidden');
    if (calDetailCloseBtn) calDetailCloseBtn.addEventListener('click', closeDetail);
    if (calDetailBackdrop) calDetailBackdrop.addEventListener('click', closeDetail);

    
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


    // LOGICA DE HISTORIAL
 
    let ID_cita_selecionada = null;
    const apptOverlay = document.getElementById('appOpcionesOverlay');
    const apptModal = document.getElementById('apptOpcionesModal');
    const apptModalCard = document.getElementById('apptModalCard');
    const apptInfoText = document.getElementById('modalCita_Info');

    window.abrir_Cita_Modal = function (id, Nombre_Especialidad) {
        ID_cita_selecionada = id;
        if (apptInfoText && Nombre_Especialidad) apptInfoText.textContent = `Cita: ${Nombre_Especialidad}`;
        if (apptOverlay && apptModal && apptModalCard) {
            apptOverlay.classList.remove('opacity-0', 'pointer-events-none');
            apptModal.classList.remove('pointer-events-none');
            apptModalCard.classList.remove('scale-95', 'opacity-0');
            apptModalCard.classList.add('scale-100', 'opacity-100');
        }
    };

    window.Cerrar_CitaModal = function () {
        ID_cita_selecionada = null;
        if (apptOverlay && apptModal && apptModalCard) {
            apptOverlay.classList.add('opacity-0', 'pointer-events-none');
            apptModal.classList.add('pointer-events-none');
            apptModalCard.classList.remove('scale-100', 'opacity-100');
            apptModalCard.classList.add('scale-95', 'opacity-0');
        }
    };

    window.Editar_cita = function () {
        if (ID_cita_selecionada) {
            window.location.href = "View_Editar_Cita.php?id=" + encodeURIComponent(ID_cita_selecionada);
        } else {
            alert("Por favor, selecciona una cita primero.");
        }
    };

    window.Eliminar_cita = async function () {
        if (ID_cita_selecionada && confirm('¿Seguro que deseas cancelar esta cita?')) {
            try {
                const resp = await fetch("Eliminar_cita.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(ID_cita_selecionada)
                });
                const data = await resp.json();
                if (data.success) {
                    alert("Cita cancelada correctamente");
                    window.Cerrar_CitaModal();
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (e) {
                alert("Error al eliminar Cita");
            }
        }
    };

    function makeCard(app) {
        let fechaTexto = "";
        let citaPasada = false;
        if (app.date) {
            const dateParts = app.date.split("-");
            const d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
            fechaTexto = d.toLocaleDateString();
            const citaDate = new Date(`${app.date}T${app.time}:00`);
            citaPasada = citaDate < new Date();
        }

        const statusMap = {
            "Pendiente": "bg-yellow-100 text-yellow-800",
            "Completada": "bg-green-100 text-green-800",
            "Cancelada": "bg-red-100 text-red-800"
        };
        const statusColor = statusMap[app.Estado] || "bg-gray-100 text-gray-800";
        const estadoBadge = app.Estado ? `<span class="text-[10px] uppercase tracking-wider font-bold ${statusColor} px-2 py-0.5 rounded ml-auto">${app.Estado}</span>` : "";

        const el = document.createElement("div");
        const hasDate = !!app.date;
        const notClickable = citaPasada || !hasDate;

        el.innerHTML = `
        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md transition duration-200 ease-in-out 
            ${notClickable ? "cursor-default" : "cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-lg transform hover:scale-[1.01]"}
            border border-gray-100 dark:border-gray-700 group">
            <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg w-20 h-20 flex-shrink-0 bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-3xl">calendar_month</span>
            </div>
            <div class="flex-grow min-w-0 ml-4">
                <div class="flex justify-between items-start mb-1">
                    <p class="text-primary font-semibold text-sm">${fechaTexto ? `${fechaTexto} • ${app.time}` : "Sin fecha asignada"}</p>
                    ${estadoBadge}
                </div>
                <h3 class="text-gray-900 dark:text-gray-100 font-bold text-base truncate" title="${app.Nombre_Especialidad}">${app.Nombre_Especialidad}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Psicólogo asignado: ${app.nombre_completo_psicologo}</p>
            </div>
        </div>`;

        if (!notClickable) {
            el.addEventListener('click', () => window.abrir_Cita_Modal(app.id, app.Nombre_Especialidad));
        }
        return el;
    }

    const container = document.getElementById('appointmentsContainer');
    const pastContainer = document.getElementById('pastContainer');

    fetch('Obtener_Cita.php')
        .then(r => r.json())
        .then(data => {
            const now = new Date();
            const upcoming = [];
            const pasada = [];
            if (Array.isArray(data)) {
                data.forEach(cita => {
                    const citaDate = new Date(`${cita.date}T${cita.time}:00`);
                    (citaDate >= now ? upcoming : pasada).push(cita);
                });
            }
            if (container) {
                container.innerHTML = upcoming.length ? '' : '<div class="col-span-full text-center py-8 text-gray-500">No tienes citas programadas próximamente.</div>';
                upcoming.forEach(a => container.appendChild(makeCard(a)));
            }
            if (pastContainer) {
                pastContainer.innerHTML = pasada.length ? '' : '<div class="col-span-full text-center py-8 text-gray-500">No hay historial de citas.</div>';
                pasada.reverse().forEach(a => pastContainer.appendChild(makeCard(a)));
            }
        })
        .catch(e => {
            console.error('Error cargando historial:', e);
            if (container) container.innerHTML = '<p class="text-red-500">Error cargando citas.</p>';
        });

})();