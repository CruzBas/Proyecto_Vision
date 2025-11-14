<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
  <link as="style"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900"
    onload="this.rel='stylesheet'" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#13a4ec",
            "background-light": "#f6f7f8",
            "background-dark": "#101c22",
          },
          fontFamily: {
            "display": ["Inter"]
          },
          borderRadius: { "DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px" },
        },
      },
    }
  </script>
  <link href="data:image/x-icon;base64," rel="icon" type="image/x-icon" />
  <title>Reserva</title>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#333] dark:text-[#ccc]">
  <!--
    Protección de la página: si el usuario no está autenticado se redirige a login.html
    con el parámetro ?next=View_Reserva.php para volver después de iniciar sesión.
    Este check depende de window.Auth (definido en auth.js). En producción el
    servidor debería hacer la protección, aquí es solo una medida en el cliente.
  -->
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      try{
        if(window.Auth && typeof window.Auth.isLoggedIn === 'function'){
          if(!window.Auth.isLoggedIn()){
            var next = 'View_Reserva.php';
            location.href = 'login.html?next=' + encodeURIComponent(next);
          }
        }
      }catch(e){/* ignore */}
    });
  </script>
  <div class="flex min-h-screen flex-col">
    <header class="border-b border-primary/20 dark:border-primary/10">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center gap-4">
         <a href="Index.html">
          <button>
            <div class="w-8 h-8 text-primary">
              <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z"
                  fill="currentColor"></path>
              </svg>
            </div>
          </button>
        </a>
          <h2 class="text-xl font-bold text-gray-800 dark:text-white">Vision</h2>
        </div>
        <nav class="hidden items-center gap-8 md:flex">
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="Index.html"><button>Pagina Principal</button></a>
            <a class="text-sm font-medium text-slate-700 hover:text-primary dark:text-slate-300 dark:hover:text-primary"
            href="Servicios.html"><button>Servicios</button></a>
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="Nosotros.html"><button>Sobre Nosotros</button></a>
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="mensaje.html"><button>Contactacto</button></a>
        </nav>
        <div class="flex items-center gap-4">
          <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10"
            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCVwv-zS-uQ4jQkoGZ114abTXXqEX-c3xiMOh4s9_EPt3GoQHd2WOeJroq3oiNMJ5KbtQTAAOn3wilUGvp35adPvzlib0BCn49l08Y2GYRjAgMMB33pGCdMy3aH7BkrVr0zOMB7JBdAMbPVcwVbPmszNA3ZAPvuVoXQl6KpwehiIbxoBrP88-Pn3ersPqFfletB5gpscpKA2UzFNq6fD5hl5rscKhRFMCGk0b_mTq6GuUVUy_7PJmi8Mrle6oVB8KXkA79J6SO6FbA");'>
          </div>
        </div>
      </div>
    </header>
  <!-- Contenido principal: calendario y selección de horarios -->
  <main class="flex-1">
      <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">Programa tu cita</h1>
          <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Selecciona la fecha y hora que mejor te convenga.
            Nuestro equipo está aquí para apoyar tu bienestar mental.</p>
        </div>
    <!-- Caja principal donde se ubica el calendario y el selector de horarios -->
    <div class="rounded-xl border border-primary/20 dark:border-primary/10 bg-background-light dark:bg-background-dark p-4 sm:p-6 lg:p-8">
          <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <button id="prevMonth" class="rounded-full p-2 text-gray-600 dark:text-gray-300 hover:bg-primary/10 dark:hover:bg-primary/20" aria-label="Mes anterior">
                  <svg fill="currentColor" height="20" viewBox="0 0 256 256" width="20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M165.66,202.34a8,8,0,0,1-11.32,11.32l-80-80a8,8,0,0,1,0-11.32l80-80a8,8,0,0,1,11.32,11.32L91.31,128Z"></path>
                  </svg>
                </button>
                <p id="calendarMonthLabel" class="text-lg font-bold text-gray-800 dark:text-white">&nbsp;</p>
                <button id="nextMonth" class="rounded-full p-2 text-gray-600 dark:text-gray-300 hover:bg-primary/10 dark:hover:bg-primary/20" aria-label="Mes siguiente">
                  <svg fill="currentColor" height="20" viewBox="0 0 256 256" width="20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"></path>
                  </svg>
                </button>
              </div>

              <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-500 dark:text-gray-400">
                <div>S</div>
                <div>M</div>
                <div>T</div>
                <div>W</div>
                <div>T</div>
                <div>F</div>
                <div>S</div>
              </div>

              
              <!-- Aquí se renderizan los días del mes (botones) -->
              <div id="calendarGrid" class="grid grid-cols-7 text-center gap-2"></div>
            </div>

            <div class="space-y-6">
              <h3 id="selectedDateLabel" class="text-xl font-bold text-gray-800 dark:text-white">Selecciona un día</h3>
              
              <div class="space-y-2">
                <label for="therapyType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de servicio</label>
                <select id="therapyType" class="mt-1 block w-full rounded-md border border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:border-primary focus:ring-primary dark:bg-background-dark dark:border-slate-700 dark:text-gray-200">
                  <option value="">-- Selecciona tipo de servicio --</option>
                  <option value="Terapia individual">Terapia individual</option>
                  <option value="Terapia familiar">Terapia familiar</option>
                  <option value="Terapia grupal">Terapia grupal</option>
                  <option value="Evaluaciones y diagnósticos">Evaluaciones y diagnósticos</option>
                  <option value="Consultoría y apoyo">Consultoría y apoyo</option>
                </select>
              </div>
              <!-- timeSlots: las franjas horarias generadas por JS (9:00 a 18:00) -->
              <div id="timeSlots" class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                <!-- Time slots (9:00 - 18:00) will be rendered here -->
              </div>
              <button id="confirmBtn" class="w-full rounded-lg bg-primary py-3 px-4 text-base font-bold text-white shadow-md transition-transform hover:scale-[1.02]">
                Confirmar Cita
              </button>
              <p id="confirmationMessage" class="text-sm text-gray-600 dark:text-gray-300"></p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

</body>

</html>

<script>
  // Simple calendar + hourly times (9:00 - 18:00) implementation
  // Comentarios en español:
  // - renderMonth(): dibuja los botones del mes actual en #calendarGrid
  // - selectDate(date, btn): selecciona un día y actualiza la UI
  // - renderTimeSlots(): genera inputs tipo radio para cada hora entre 9 y 18
  // - confirmBtn: al confirmar crea un objeto de cita y lo guarda en localStorage
  (function(){
    const calendarGrid = document.getElementById('calendarGrid');
    const monthLabel = document.getElementById('calendarMonthLabel');
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    const selectedDateLabel = document.getElementById('selectedDateLabel');
    const timeSlots = document.getElementById('timeSlots');
    const confirmBtn = document.getElementById('confirmBtn');
    const confirmationMessage = document.getElementById('confirmationMessage');

    let viewDate = new Date(); // month being viewed
    let selectedDate = null; // Date object
    let selectedTime = null; // string like '09:00'

  // Helpers para calcular inicio/fin de mes
  function startOfMonth(d){ return new Date(d.getFullYear(), d.getMonth(), 1); }
  function endOfMonth(d){ return new Date(d.getFullYear(), d.getMonth()+1, 0); }

  function renderMonth(){
      calendarGrid.innerHTML = '';
      const start = startOfMonth(viewDate);
      const end = endOfMonth(viewDate);
      const monthName = viewDate.toLocaleString(undefined, { month: 'long', year: 'numeric' });
      monthLabel.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

  // Day of week for the 1st (0 = Sunday)
      const startDow = start.getDay();

      // Render empty cells before first day
      // Render empty cells before primer día para alinear la cuadrícula
      for(let i=0;i<startDow;i++){
        const empty = document.createElement('div');
        empty.className = 'h-10';
        calendarGrid.appendChild(empty);
      }

      for(let day=1; day<=end.getDate(); day++){
        const date = new Date(viewDate.getFullYear(), viewDate.getMonth(), day);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'flex h-10 w-10 items-center justify-center rounded-full text-sm hover:bg-primary/10 dark:hover:bg-primary/20';
        btn.textContent = day;
        btn.addEventListener('click', ()=> selectDate(date, btn));

        // mark today
        const today = new Date();
        if(date.toDateString() === today.toDateString()){
          btn.classList.add('bg-primary','text-white');
        }

        calendarGrid.appendChild(btn);
      }
    }

  // selectDate: marca la fecha seleccionada y prepara los time slots
  function selectDate(date, btn){
      selectedDate = date;
      selectedTime = null;
      // update label
      const label = date.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
      selectedDateLabel.textContent = 'Horarios disponibles para el ' + label;

      // highlight selected day in grid
      Array.from(calendarGrid.querySelectorAll('button')).forEach(b=>{
        b.classList.remove('ring-2','ring-primary','bg-primary','text-white');
        // leave today's styling alone unless it's selected
      });
      btn.classList.add('ring-2','ring-primary');

      renderTimeSlots();
      confirmationMessage.textContent = '';
    }

  // renderTimeSlots: crea radios para las franjas horarias (9:00 - 18:00)
  function renderTimeSlots(){
      timeSlots.innerHTML = '';
      if(!selectedDate){
        timeSlots.innerHTML = '<p class="col-span-2 text-sm text-gray-600 dark:text-gray-300">Selecciona un día para ver horarios</p>';
        return;
      }

      // generate hours from 9 to 18 inclusive start times (9..18) => last slot starts at 18:00
  for(let hour=9; hour<=18; hour++){
        const label = formatHour(hour);
        const id = 'ts-' + hour;
        const labelEl = document.createElement('label');
        labelEl.className = 'cursor-pointer';

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = 'time';
        input.className = 'peer sr-only';
        input.value = hour.toString().padStart(2,'0') + ':00';
        input.id = id;
        // Al cambiar la franja seleccionada actualizamos selectedTime
        input.addEventListener('change', ()=>{
          selectedTime = input.value;
          confirmationMessage.textContent = '';
        });

        const slot = document.createElement('div');
        slot.className = 'rounded-lg border-2 border-primary/20 bg-background-light dark:bg-background-dark p-4 text-center text-gray-700 dark:text-gray-300 peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary dark:peer-checked:bg-primary/20';
        const p = document.createElement('p');
        p.className = 'font-semibold';
        p.textContent = label;
        slot.appendChild(p);

        labelEl.appendChild(input);
        labelEl.appendChild(slot);
        timeSlots.appendChild(labelEl);
      }
    }

  // formatHour: convierte una hora 0-23 a formato 12h con AM/PM
  function formatHour(h){
      const ampm = h<12 ? 'AM' : 'PM';
      const hour12 = ((h+11)%12)+1; // convert 0..23 to 12h
      return hour12 + ':00 ' + ampm;
    }

    prevBtn.addEventListener('click', ()=>{
      viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth()-1, 1);
      renderMonth();
    });
    nextBtn.addEventListener('click', ()=>{
      viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth()+1, 1);
      renderMonth();
    });

  // Al confirmar la cita se valida selección y se guarda en localStorage
  confirmBtn.addEventListener('click', ()=>{
      if(!selectedDate){
        confirmationMessage.textContent = 'Por favor selecciona primero una fecha.';
        return;
      }
      if(!selectedTime){
        confirmationMessage.textContent = 'Por favor selecciona una franja horaria (ej. 09:00).';
        return;
      }
      const therapyTypeEl = document.getElementById('therapyType');
      const therapyType = therapyTypeEl ? therapyTypeEl.value : '';
      if(!therapyType){
        confirmationMessage.textContent = 'Por favor selecciona el tipo de servicio (ej. Terapia individual).';
        return;
      }
      // Create a readable confirmation including the therapy type
      const dateStr = selectedDate.toLocaleDateString(undefined, { year:'numeric', month:'long', day:'numeric' });
      confirmationMessage.textContent = `Cita confirmada: ${dateStr} a las ${selectedTime} — ${therapyType}`;
      // store appointment in localStorage for overview
      try{
        const key = 'vision_appointments';
        const raw = localStorage.getItem(key);
        let arr = raw ? JSON.parse(raw) : [];
        arr.unshift({
          date: selectedDate.toISOString(),
          time: selectedTime,
          type: therapyType,
          createdAt: new Date().toISOString()
        });
        
        localStorage.setItem(key, JSON.stringify(arr));
      }catch(e){ console.error('save appointment', e); }

      try {
  const usuario = JSON.parse(localStorage.getItem("usuarioActivo"));
  if (usuario && usuario.id) {
    fetch("guardar_cita.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        user_id: usuario.id,
        fecha: selectedDate.toISOString().split("T")[0],
        hora: selectedTime + ":00",
        tipo_servicio: therapyType
      })
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) console.error("Error guardando cita:", data.error);
    })
    .catch(err => console.error("Error de conexión:", err));
  }
} catch (err) {
  console.error("Error al obtener usuario:", err);
}
      // redirect to profile overview so the user sees their appointment
      setTimeout(()=>{ location.href = 'Perfil.php'; }, 700);
    });

    // initialize
    renderMonth();
    renderTimeSlots();
  })();
</script>
  <script src="auth.js"></script>