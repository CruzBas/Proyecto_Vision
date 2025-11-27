(function() {
    const lb = document.getElementById('logoutBtn');
    const settingsLogout = document.getElementById('settingsLogout');
    const overlay = document.getElementById('confirmOverlay');
    const modalWrap = document.getElementById('confirmModal');
    const btnCancel = document.getElementById('cancelLogout');
    const btnConfirm = document.getElementById('confirmLogout');

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

    if (lb) lb.addEventListener('click', function(e) { e.preventDefault(); openConfirm(); });
    if (settingsLogout) settingsLogout.addEventListener('click', function(e) { e.preventDefault(); openConfirm(); });
    if (btnCancel) btnCancel.addEventListener('click', closeConfirm);
    if (overlay) overlay.addEventListener('click', closeConfirm);
    
    if (btnConfirm) btnConfirm.addEventListener('click', function() {
        if (window.Auth && window.Auth.logout) window.Auth.logout();
    });

    // --- LÓGICA DE CARGA DE CITAS DESDE BD ---

    // Función para crear la tarjeta visual
    function makeCard(app) {
    
        const dateParts = app.date.split('-'); 
        
        const d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
        
        const el = document.createElement('div');
        el.className = 'bg-white dark:bg-background-dark/50 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700/50 flex items-start gap-4 transition hover:shadow-md';
        
        // Badge de estado (Pendiente vs Completada)
        let statusColor = 'bg-gray-100 text-gray-800';
        if (app.Estado === 'Pendiente') statusColor = 'bg-yellow-100 text-yellow-800';
        if (app.Estado === 'Completada') statusColor = 'bg-green-100 text-green-800';
        if(app.Estado === 'Cancelada') statusColor ='bg-red-100 text-red-800';
        
        const estadoBadge = app.Estado ? `<span class="text-[10px] uppercase tracking-wider font-bold ${statusColor} px-2 py-0.5 rounded ml-auto">${app.Estado}</span>` : '';

        el.innerHTML = `
        <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg w-20 h-20 flex-shrink-0 bg-primary/10 text-primary flex items-center justify-center">
             <span class="material-symbols-outlined text-3xl">calendar_month</span>
        </div>
        <div class="flex-grow min-w-0">
            <div class="flex justify-between items-start mb-1">
                <p class="text-primary font-semibold text-sm">${d.toLocaleDateString()} &bull; ${app.time}</p>
                ${estadoBadge}
            </div>
            <h3 class="text-gray-900 dark:text-gray-100 font-bold text-base truncate" title="${app.type}">${app.type}</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1" title="${app.nombre_completo_psicologo}">Psicólogo asignado: ${app.nombre_completo_psicologo} </p>
        </div>`;
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
            if(container) container.innerHTML = '<p class="text-red-500">Error cargando citas.</p>';
        });

})();

(function() {
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

    if (overviewBtn) overviewBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (dashboardPanel) dashboardPanel.classList.remove('hidden');
        if (settingsPanel) settingsPanel.classList.add('hidden');
        setActive(overviewBtn);
    });
    if (settingsBtn) settingsBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (dashboardPanel) dashboardPanel.classList.add('hidden');
        if (settingsPanel) settingsPanel.classList.remove('hidden');
        setActive(settingsBtn);
        
        if(window.Auth) {
            const u = window.Auth.getUser();
            if(settingsEmail) settingsEmail.textContent = u.email;
        }
    });
    
    // Inicializar estado activo
    setActive(overviewBtn);
})();