<?php
/**
 * calendarioPaciente_View.php
 * 
 * Vista principal que muestra el calendario de citas de un paciente.
 * - Verifica sesión y obtiene ID_Paciente.
 * - Layout con Tailwind y FullCalendar.
 * - Incluye modal para mostrar detalles de citas.
 */
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html?redirect=' . urldecode(basename($_SERVER['PHP_SELF'])));
    exit;
}

$idUsuario = (int)$_SESSION['user_id'];
$idPaciente = null;

$query = "SELECT ID_Paciente FROM pacientes WHERE ID_Usuario = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($idPaciente);
$stmt->fetch();
$stmt->close();

if (!$idPaciente) {
    die("No se encontró un paciente asociado a este usuario.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
  <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900" onload="this.rel='stylesheet'" rel="stylesheet" />
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
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <!-- Importar traducciones -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
  <!-- jQuery para AJAX -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- .js -->
  <script defer src="calendarioPaciente.js"></script>
  <style>
  /* Estilos personalizados para los botones de FullCalendar */
  .fc .fc-button {
    background-color: #13a4ec !important;
    color: #fff !important;
    font-weight: bold;
    border: none;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, opacity 0.2s ease;
  }
  .fc .fc-button:hover {
    transform: scale(1.05);
    opacity: 0.9;
  }
  .fc .fc-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  .fc .fc-button:focus,
  .fc .fc-button:active {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
  }
  </style>
  <title>Calendario del Paciente</title>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#333] dark:text-[#ccc]">
  <div class="flex min-h-screen flex-col">
    <!-- Header -->
    <header class="border-b border-primary/20 dark:border-primary/10">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center gap-4">
         <a href="Index.php">
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
            href="Index.php"><button>Pagina Principal</button></a>
            <a class="text-sm font-medium text-slate-700 hover:text-primary dark:text-slate-300 dark:hover:text-primary"
            href="Servicios.php"><button>Servicios</button></a>
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="Nosotros.php"><button>Sobre Nosotros</button></a>
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="mensaje.php"><button>Contactacto</button></a>
        </nav>
        <div class="flex items-center gap-4">
          <a href="perfil.php">
              <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10"
                style='background-image: url("https://cdn-icons-png.flaticon.com/512/11753/11753627.png");'>
              </div>
            </a>
        </div>
      </div>
    </header>

    <!-- Main -->
    <main class="flex-1">
      <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">Calendario del Paciente</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">Haz clic en una cita para ver sus detalles.</p>
        </div>

        <div class="rounded-xl border border-primary/20 bg-background-light dark:bg-background-dark p-4 sm:p-6 lg:p-8">
          <!-- Contenedor de FullCalendar -->
          <div id="calendar"
               data-paciente="<?php echo htmlspecialchars($idPaciente, ENT_QUOTES, 'UTF-8'); ?>"
               class="bg-white dark:bg-[#0f172a] rounded-lg p-2"></div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal de detalles -->
  <div id="modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative mx-auto mt-24 w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-[#0f172a]">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detalles de la Cita</h2>
        <button id="closeModal" class="rounded-md px-3 py-1 text-sm font-semibold text-white bg-primary hover:opacity-90">Cerrar</button>
      </div>
      <div id="modal-content" class="space-y-2 text-sm text-gray-800 dark:text-gray-200">
        <!-- Se rellena dinámicamente desde calendarioPaciente.js -->
      </div>
    </div>
  </div>
</body>
</html>
