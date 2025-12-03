(function () {
  
    const calendarGrid = document.getElementById('calendarGrid');
    const monthLabel = document.getElementById('calendarMonthLabel');
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    const selectedDateLabel = document.getElementById('selectedDateLabel');
    const timeSlots = document.getElementById('timeSlots');

    const formReserva = document.getElementById('formReserva');
    const confirmBtn = document.getElementById('confirmBtn');

    const therapyTypeSelect = document.getElementById('therapyType');
    const divPsicologo = document.getElementById('divPsicologo');
    const comboPsicologo = document.getElementById('comboPsicologo');

    const inputFecha = document.getElementById('inputFecha');
    const inputHora = document.getElementById('inputHora');
    const inputPsicologo = document.getElementById('inputPsicologo');
    const inputTipoServicio = document.getElementById('inputTipoServicio');

    // Nuevos Campos
    const inputMotivo = document.getElementById('inputMotivo');
    const inputDuracion = document.getElementById('inputDuracion');

    const timeOptions = ['09:00', '10:00', '11:00', '12:00','13:00','14:00', '15:00', '16:00','17:00','18:00'];

    const baseDayClass = 'h-12 rounded-xl border border-transparent bg-white dark:bg-slate-900 shadow-sm text-sm font-semibold text-gray-700 dark:text-gray-200 transition transform hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/60';
    const selectedDayClasses = ['bg-primary', 'text-white', 'border-primary', 'shadow-lg'];
    const todayRingClasses = ['ring-2', 'ring-primary/50'];
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayKey = `${today.getFullYear()}-${today.getMonth()}-${today.getDate()}`;

    let viewDate = new Date();
    let selectedDate = null;
    let selectedTime = null;
    let selectedDayBtn = null;

    therapyTypeSelect.addEventListener('change', async function () {
        const idEspecialidad = this.value;
        inputTipoServicio.value = idEspecialidad;

        comboPsicologo.innerHTML = '<option value="">-- Selecciona un psicologo --</option>';
        inputPsicologo.value = '';

        if (!idEspecialidad) {
            divPsicologo.classList.add('hidden');
            return;
        }

        divPsicologo.classList.remove('hidden');

        try {
            const response = await fetch(`get_psicologos.php?especialidad=${encodeURIComponent(idEspecialidad)}`);
            const data = await response.json();

            if (data.success && data.psicologos && data.psicologos.length > 0) {
                data.psicologos.forEach((doc) => {
                    const option = document.createElement('option');
                    option.value = doc.id;
                    option.textContent = doc.nombre;
                    comboPsicologo.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.textContent = 'No hay psicologos disponibles';
                comboPsicologo.appendChild(option);
            }
        } catch (error) {
            console.error('Error cargando psicologos', error);
            const option = document.createElement('option');
            option.textContent = 'Error al cargar psicologos';
            comboPsicologo.appendChild(option);
        }
    });

    comboPsicologo.addEventListener('change', function () {
        inputPsicologo.value = this.value;
    });

    function sameDay(a, b) {
        return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
    }

    function resetDayStyles(btn, isToday) {
        btn.className = baseDayClass;
        if (isToday) {
            btn.classList.add(...todayRingClasses);
        }
    }

    function applySelectedStyles(btn) {
        btn.classList.remove('bg-white', 'dark:bg-slate-900', 'text-gray-700', 'dark:text-gray-200');
        btn.classList.add(...selectedDayClasses);
    }

    function renderMonth() {
        calendarGrid.innerHTML = '';
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();

        monthLabel.textContent = viewDate.toLocaleString('es-ES', { month: 'long', year: 'numeric' });

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const placeholder = document.createElement('div');
            placeholder.className = 'h-12';
            calendarGrid.appendChild(placeholder);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = day;
            btn.className = baseDayClass;

            const dateObj = new Date(year, month, day);
            const dateKey = `${dateObj.getFullYear()}-${dateObj.getMonth()}-${dateObj.getDate()}`;
            btn.dataset.dateKey = dateKey;

            if (sameDay(dateObj, today)) {
                btn.classList.add(...todayRingClasses);
            }
            if (sameDay(dateObj, selectedDate)) {
                applySelectedStyles(btn);
                selectedDayBtn = btn;
            }

            btn.addEventListener('click', () => selectDate(dateObj, btn));
            calendarGrid.appendChild(btn);
        }
    }

    function selectDate(date, btn) {
        selectedDate = date;
        selectedTime = null;
        inputHora.value = '';

        if (selectedDayBtn) {
            const isToday = selectedDayBtn.dataset.dateKey === todayKey;
            resetDayStyles(selectedDayBtn, isToday);
        }
        selectedDayBtn = btn;
        applySelectedStyles(btn);

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const dayName = date.toLocaleDateString('es-ES', { weekday: 'long' });
        inputFecha.value = `${year}-${month}-${day}`;
        selectedDateLabel.textContent = `Selecciona un horario para el ${dayName} ${day}/${month}/${year}`;

        renderTimeSlots();
    }

    function renderTimeSlots() {
        timeSlots.innerHTML = '';

        if (!selectedDate) {
            timeSlots.innerHTML = '<p class="col-span-2 text-sm text-gray-500 dark:text-gray-400">Selecciona una fecha.</p>';
            return;
        }

        timeOptions.forEach((time) => {
            const label = document.createElement('label');
            label.className = 'flex cursor-pointer items-center gap-2 rounded-xl border border-transparent bg-white dark:bg-slate-900 px-3 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:-translate-y-0.5 hover:shadow-md hover:border-primary transition transform';

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'slot';
            input.value = time;
            input.className = 'sr-only';

            const span = document.createElement('span');
            span.textContent = time;

            if (selectedTime === time) {
                input.checked = true;
                label.classList.add('border-primary', 'bg-primary/10', 'shadow-md');
            }

            input.addEventListener('change', () => {
                selectedTime = time;
                inputHora.value = time;
                document.querySelectorAll('#timeSlots label').forEach((l) => {
                    l.classList.remove('border-primary', 'bg-primary/10', 'shadow-md');
                });
                label.classList.add('border-primary', 'bg-primary/10', 'shadow-md');
            });

            label.appendChild(input);
            label.appendChild(span);
            timeSlots.appendChild(label);
        });
    }

    renderMonth();
    renderTimeSlots();

    
    formReserva.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!inputFecha.value) { alert('Por favor, selecciona una fecha.'); return; }
        if (!inputHora.value) { alert('Por favor, selecciona una hora.'); return; }
        if (!inputPsicologo.value) { alert('Por seguro, selecciona un psicólogo.'); return; }

        confirmBtn.disabled = true;
        const originalText = confirmBtn.textContent;
        confirmBtn.textContent = 'Procesando...';

        try {
            const resp = await fetch('Reserva_Cita.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    fecha: inputFecha.value,
                    hora: inputHora.value,
                    id_psicologo: inputPsicologo.value,
                    tipo_servicio: inputTipoServicio.value,
                    motivo: inputMotivo ? (inputMotivo.value || null) : null,
                    duracion: inputDuracion && inputDuracion.value
                        ? parseInt(inputDuracion.value, 10) : 60
                })
            });

            const data = await resp.json();

            if (data.success) {
                alert('Cita reservada con éxito');
                window.location.href = 'Perfil.php';
            } else {
                alert(data.message || 'Error al reservar');
                confirmBtn.disabled = false;
                confirmBtn.textContent = originalText;
            }
        } catch (err) {
            console.error(err);
            alert('Error de red');
            confirmBtn.disabled = false;
            confirmBtn.textContent = originalText;
        }
    });

    prevBtn.addEventListener('click', () => {
        viewDate.setMonth(viewDate.getMonth() - 1);
        renderMonth();
    });

    nextBtn.addEventListener('click', () => {
        viewDate.setMonth(viewDate.getMonth() + 1);
        renderMonth();
    });
})();
