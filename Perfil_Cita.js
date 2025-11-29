(function () {
    const lb = document.getElementById('logoutBtn');
    const Logout = document.getElementById('btnLogout');
    const overlay = document.getElementById('confirmarOverlay');
    const modalWrap = document.getElementById('confirmaModal');
    const btnCancelar = document.getElementById('cancelarLogout');
    const btnConfirmar = document.getElementById('confirmarLogout');

    
    function openConfirm() {
        if (overlay && modalWrap) {
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            modalWrap.classList.remove('opacity-0', 'pointer-events-none');
            modalWrap.classList.add('opacity-100');
        }
    }
    function closeConfirm() {
        if (overlay && modalWrap) {
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            modalWrap.classList.remove('opacity-100');
            modalWrap.classList.add('opacity-0', 'pointer-events-none');
        }
    }

    if (lb) lb.addEventListener('click', function (e) { e.preventDefault(); openConfirm(); });
    if (Logout) Logout.addEventListener('click', function (e) { e.preventDefault(); openConfirm(); });
    if (btnCancelar) btnCancelar.addEventListener('click', closeConfirm);
    if (overlay) overlay.addEventListener('click', closeConfirm);

    if (btnConfirmar) btnConfirmar.addEventListener('click', function () {
        if (window.Auth && window.Auth.logout) window.Auth.logout();
    });


    
    let ID_cita_selecionada = null;
    const apptOverlay = document.getElementById('appOpcionesOverlay');
    const apptModal = document.getElementById('apptOpcionesModal');
    const apptModalCard = document.getElementById('apptModalCard');
    const apptInfoText = document.getElementById('modalCita_Info');

    window.abrir_Cita_Modal = function (id, Nombre_Especialidad) {
        ID_cita_selecionada = id;


        if (apptInfoText && Nombre_Especialidad) {
            apptInfoText.textContent = `Cita: ${Nombre_Especialidad}`;
        }

        
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

    window.handleEditAppointment = function () {
        if (ID_cita_selecionada) {
            window.location.href = `view_appointment.php?id=${ID_cita_selecionada}`;
        }
    };

    window.Eliminar_cita = async function () {
        if (ID_cita_selecionada) {
            if (confirm('¿Seguro que deseas cancelar esta cita?')) {

                try {
                    const resp = await fetch("Eliminar_cita.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
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
        }
    };

    // --- LÓGICA DE CARGA DE CITAS DESDE BD ---


   function makeCard(app) {

    
    let fechaTexto = "";
    let citaPasada = false;

    if (app.date) {
        const dateParts = app.date.split("-");
        const d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
        fechaTexto = d.toLocaleDateString();

        // Comparar si es pasada
        const citaDate = new Date(`${app.date}T${app.time}:00`);
        const now = new Date();
        citaPasada = citaDate < now; 
    }


    let statusColor = "bg-gray-100 text-gray-800";
    if (app.Estado === "Pendiente") statusColor = "bg-yellow-100 text-yellow-800";
    if (app.Estado === "Completada") statusColor = "bg-green-100 text-green-800";
    if (app.Estado === "Cancelada") statusColor = "bg-red-100 text-red-800";

    const estadoBadge = app.Estado
        ? `<span class="text-[10px] uppercase tracking-wider font-bold ${statusColor} px-2 py-0.5 rounded ml-auto">${app.Estado}</span>`
        : "";

    const el = document.createElement("div");

    
    const hasDate = !!app.date;
    const notClickable = citaPasada || !hasDate;

    el.innerHTML = `
    <div
        class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md transition duration-200 ease-in-out 
        ${notClickable ? "cursor-default" : "cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-lg transform hover:scale-[1.01]"}
        border border-gray-100 dark:border-gray-700 group">

        <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg w-20 h-20 flex-shrink-0 bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary/20 transition-colors">
            <span class="material-symbols-outlined text-3xl">calendar_month</span>
        </div>

        <div class="flex-grow min-w-0 ml-4">

            <div class="flex justify-between items-start mb-1">
                ${fechaTexto
                    ? `<p class="text-primary font-semibold text-sm">${fechaTexto} • ${app.time}</p>`
                    : `<p class="text-primary font-semibold text-sm">Sin fecha asignada</p>`
                }
                ${estadoBadge}
            </div>

            <h3 class="text-gray-900 dark:text-gray-100 font-bold text-base truncate" title="${app.Nombre_Especialidad}">
                ${app.Nombre_Especialidad}
            </h3>

            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1" title="${app.nombre_completo_psicologo}">
                Psicólogo asignado: ${app.nombre_completo_psicologo}
            </p>
        </div>

    </div>
    `;


    if (!notClickable) {
        el.addEventListener('click', function () {
            window.abrir_Cita_Modal(app.id, app.Nombre_Especialidad);
        });
    }

    return el;
}


    const container = document.getElementById('appointmentsContainer');
    const pastContainer = document.getElementById('pastContainer');

    fetch('Obtener_Cita.php')
        .then(response => response.json())
        .then(data => {
            const now = new Date();
            const upcoming = [];
            const past = [];

            data.forEach(cita => {
                const fullDateStr = `${cita.date}T${cita.time}:00`;
                const citaDate = new Date(fullDateStr);

                if (citaDate >= now) {
                    upcoming.push(cita);
                } else {
                    past.push(cita);
                }
            });

            // Renderizar citas Próximas
            if (container) {
                container.innerHTML = '';
                if (upcoming.length === 0) {
                    container.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">No tienes citas programadas próximamente.</div>';
                } else {
                    upcoming.forEach(a => container.appendChild(makeCard(a)));
                }
            }

            // Renderizar citas Pasadas
            if (pastContainer) {
                pastContainer.innerHTML = '';
                if (past.length === 0) {
                    pastContainer.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">No hay historial de citas.</div>';
                } else {
                    // Invierte el orden para ver las más recientes primero
                    past.reverse().forEach(a => pastContainer.appendChild(makeCard(a)));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (container) container.innerHTML = '<p class="text-red-500">Error cargando citas.</p>';
        });

})();

(function () {
    const overviewBtn = document.getElementById('overviewBtn');
    const settingsBtn = document.getElementById('settingsBtn');
    const dashboardPanel = document.getElementById('dashboardPanel');
    const settingsPanel = document.getElementById('settingsPanel');
    const settingsEmail = document.getElementById('settingsEmail');
    function setActive(btn) {
        [overviewBtn, settingsBtn].forEach(b => {
            if (!b) return;
            b.classList.remove('bg-primary', 'text-white');
            b.classList.add('text-slate-700');
        });
        if (btn) {
            btn.classList.add('bg-primary', 'text-white');
            btn.classList.remove('text-slate-700');
        }
    }

    if (overviewBtn) overviewBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (dashboardPanel) dashboardPanel.classList.remove('hidden');
        if (settingsPanel) settingsPanel.classList.add('hidden');
        setActive(overviewBtn);
    });
    if (settingsBtn) settingsBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (dashboardPanel) dashboardPanel.classList.add('hidden');
        if (settingsPanel) settingsPanel.classList.remove('hidden');
        setActive(settingsBtn);

        if (window.Auth) {
            const u = window.Auth.getUser();
            if (settingsEmail) settingsEmail.textContent = u.email;
        }
    });

    
    // Inicializar estado activo
    setActive(overviewBtn);
})();