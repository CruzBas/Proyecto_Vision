<?php

session_start();

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html?redirect=' . urldecode(basename($_SERVER['PHP_SELF'])));
    exit;
}

$especialidades = [];

$query = "SELECT ID_Especialidad, Nombre_Especialidad FROM especialidades";
if ($result = $mysqli->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $especialidades[] = $row;
}
}

if (isset($_REQUEST['id'])) {
  $citaId = $_REQUEST['id'];
} else {
  
  header('Location: Index.html');
  exit;
}
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
  <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Inter%3Awght%40400%3B500%3B700%3B900&family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" onload="this.rel='stylesheet'" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: { "primary": "#13a4ec", "background-light": "#f6f7f8", "background-dark": "#101c22" },
          fontFamily: { "display": ["Inter"] },
        },
      },
    }
  </script>
  <title>Reserva tu Cita</title>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#333] dark:text-[#ccc]">
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

    <main class="flex-1">
      <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">Reprograma tu cita</h1>
        </div>

        <div class="rounded-xl border border-primary/20 bg-background-light dark:bg-background-dark p-4 sm:p-6 lg:p-8">
          <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            
            <div class="space-y-4">
               <div class="flex justify-between"><button id="prevMonth"><</button><p id="calendarMonthLabel"></p><button id="nextMonth">></button></div>
               <div class="grid grid-cols-7 text-center font-semibold text-gray-500 dark:text-gray-400"><div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div></div>
               <div id="calendarGrid" class="grid grid-cols-7 text-center gap-2"></div>
            </div>

            <div class="space-y-6">
              <h3 id="selectedDateLabel" class="text-xl font-bold text-gray-800 dark:text-white">Selecciona un día</h3>
              
              <div class="space-y-2">
                <label for="therapyType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Especialidad</label>
                <select id="therapyType" class="mt-1 block w-full rounded-md border border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:border-primary focus:ring-primary dark:bg-background-dark dark:border-slate-700 dark:text-gray-200">
                  <option value=""> Selecciona especialidad </option>
                  <?php foreach ($especialidades as $esp): ?>
                      <option value="<?php echo $esp['ID_Especialidad']; ?>">
                          <?php echo htmlspecialchars($esp['Nombre_Especialidad']); ?>
                      </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div id="divPsicologo" class="space-y-2 hidden transition-all duration-300">
                <label for="comboPsicologo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selecciona Psicólogo</label>
                <select id="comboPsicologo" class="mt-1 block w-full rounded-md border border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:border-primary focus:ring-primary dark:bg-background-dark dark:border-slate-700 dark:text-gray-200">
                  <option value=""> Cargando psicólogos... </option>
                </select>
              </div>

              <div id="timeSlots" class="grid grid-cols-2 gap-4 sm:grid-cols-3"></div>

              <div id="reservaAlert" class="hidden rounded p-3 text-sm mb-4"></div>

              <form id="formReservaEditar" method="post" action="Editar_Cita.php">
                <input type="hidden" name="id_cita" id="id_cita" value="<?php echo htmlspecialchars($citaId); ?>">
                  <input type="hidden" name="fecha" id="inputFecha">
                  <input type="hidden" name="hora" id="inputHora">
                  <input type="hidden" name="tipo_servicio" id="inputTipoServicio">
                  <input type="hidden" name="id_psicologo" id="inputPsicologo">
                  
                  <button type="submit" id="confirmBtn" class="w-full rounded-lg bg-primary py-3 px-4 text-base font-bold text-white shadow-md transition-transform hover:scale-[1.02]">
                    Confirmar Nueva Cita
                  </button>
              </form>
            </div>
          </div>
        </div>
      </div>
<script src="Editar_cita_Calendario.js"></script>
</body>
</html>
<?php
?>