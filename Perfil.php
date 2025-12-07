<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.html?redirect=' . urlencode(basename($_SERVER['PHP_SELF'])));
    exit;
}

require_once 'db.php';

$paciente = null;
$idPaciente = null;

// Obtenemos datos básicos para la vista inicial
$query = "SELECT ID_Paciente, Nombre_Paciente, Apellido_Paciente FROM pacientes WHERE ID_Usuario = ?";

if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $paciente = $row;
        $idPaciente = $row['ID_Paciente'];
    }
    $stmt->close();
}

$nombreMostrar = $paciente['Nombre_Paciente'] ?? 'Usuario';
$apellidoMostrar = $paciente['Apellido_Paciente'] ?? '';
$nombreCompleto = trim($nombreMostrar . ' ' . $apellidoMostrar);

$jsData = [
    'nombre' => $nombreMostrar,
    'apellido' => $apellidoMostrar,
    'email' => $_SESSION['user_email'] ?? '',
    'idPaciente' => $idPaciente
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Vision - Panel de Usuario</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
    <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;700;900" onload="this.rel='stylesheet'" rel="stylesheet" />
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
                },
            },
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

    <style>
        .fc .fc-button {
            background-color: #13a4ec !important;
            border: none;
            color: white;
            text-transform: capitalize;
        }
        .fc-event { cursor: pointer; }
    </style>

    <script>
        const usuarioSesion = <?php echo json_encode($jsData); ?>;
        window.Auth = {
            getUser: function() {
                return {
                    firstName: usuarioSesion.nombre,
                    lastName: usuarioSesion.apellido,
                    email: usuarioSesion.email,
                    idPaciente: usuarioSesion.idPaciente
                };
            },
            logout: function(options) {
                window.location.href = 'logout.php';
            }
        };
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">

    <header class="bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="text-primary h-8 w-8">
                        <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_6_319)">
                                <path d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z" fill="currentColor"></path>
                            </g>
                            <defs>
                                <clipPath id="clip0_6_319">
                                    <rect fill="white" height="48" width="48"></rect>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Vision</h1>
                </div>
                <nav class="hidden md:flex items-center gap-8">
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Index.php">Página principal</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Servicios.php">Servicios</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Nosotros.php">Sobre nosotros</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="mensaje.php">Contacto</a>
                </nav>
                <div class="flex items-center">
                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 ml-4"></div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <aside class="w-64 bg-background-light dark:bg-background-dark border-r border-gray-200 dark:border-gray-700/50 flex flex-col p-6">
            <div class="flex items-center gap-3 mb-10">
                <div id="perfilAvatar" class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12" style='background-image: url("https://cdn-icons-png.flaticon.com/512/11753/11753627.png");'></div>
                <div class="flex flex-col">
                    <h1 id="perfilName" class="text-gray-900 dark:text-white font-bold text-lg">
                        <?php echo htmlspecialchars($nombreCompleto); ?>
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Paciente</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                    </p>
                </div>
            </div>

            <nav class="flex flex-col gap-2">
                <a id="overviewBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary text-white font-medium cursor-pointer">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Resumen</span>
                </a>
                <a id="CalendarioBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer" href="#">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <span>Calendario</span>
                </a>
                <a id="ReservarBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer" href="View_Reserva.php">
                    <span class="material-symbols-outlined">calendar_add_on</span>
                    <span>Reserva cita</span>
                </a>
                <a id="HistorialBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer">
                    <span class="material-symbols-outlined">history</span>
                    <span>Historial</span>
                </a>
                <a id="Configuracion_Btn" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Ajustes</span>
                </a>
            </nav>

            <div class="mt-auto">
                <a id="logoutBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-4xl mx-auto">

                <div id="dashboardPanel">
                    <h1 id="welcomeTitle" class="text-4xl font-bold text-gray-900 dark:text-white mb-8">¡Bienvenido, <?php echo htmlspecialchars($nombreCompleto); ?>!</h1>
                    
                </div>

                <div id="HistorialPanel" class="hidden bg-white dark:bg-background-dark/50 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700/50">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Historial</h2>
                    <div class="space-y-6">
                        <section>
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Tus citas</h2>
                            <div id="appointmentsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
                        </section>
                        <section class="mt-12">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Citas pasadas</h2>
                            <div id="pastContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
                        </section>
                    </div>
                </div>

                <div id="Configuracion_Panel" class="hidden bg-white dark:bg-background-dark/50 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700/50">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Ajustes</h2>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Información Personal</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">Datos del Perfil</p>
                                <p class="text-xs text-gray-400">Nombre, edad, teléfono</p>
                            </div>
                            <button id="openProfileModalBtn" class="px-4 py-2 bg-primary/10 text-primary hover:bg-primary/20 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">edit</span> Editar
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-500">Correo electrónico</p>
                                <p id="Configuracion_Email" class="font-semibold text-gray-900 dark:text-gray-100"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 border-t pt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-500">¿Necesitas ayuda? <a href="mensaje.php" class="text-primary">Contáctanos</a></div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="calendarModalOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-md opacity-0 pointer-events-none transition-opacity duration-300 z-40"></div>
    <div id="calendarModalContainer" class="fixed inset-0 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 z-50 p-4">
        <div class="bg-white dark:bg-gray-900 w-full max-w-5xl h-[85vh] rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col transform scale-95 transition-transform duration-300">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">calendar_month</span>
                    Mi Calendario
                </h2>
                <button id="closeCalendarBtn" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-gray-500">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-hidden p-4 bg-background-light dark:bg-background-dark/50">
                <div id="calendar" class="h-full w-full bg-white dark:bg-gray-900 rounded-lg shadow-inner p-2"></div>
            </div>
        </div>
    </div>
    <div id="calDetailModal" class="fixed inset-0 flex items-center justify-center z-[60] hidden">
         <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="calDetailBackdrop"></div>
         <div class="relative bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-sm shadow-2xl border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Detalles de la Cita</h3>
            <div id="calDetailContent" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></div>
            <div class="mt-6 flex justify-end">
                <button id="calDetailCloseBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition">Cerrar</button>
            </div>
         </div>
    </div>

    <div id="confirmarOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-50"></div>
    <div id="confirmaModal" class="fixed inset-0 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-[90%] max-w-md shadow-xl border border-gray-200 dark:border-gray-700 transform scale-95">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Confirmar cierre de sesión</h3>
            <div class="flex justify-end gap-3 mt-4">
                <button id="cancelarLogout" class="px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-700">Cancelar</button>
                <button id="confirmarLogout" class="px-4 py-2 rounded-md bg-red-600 text-white">Cerrar sesión</button>
            </div>
        </div>
    </div>

    <button id="appOpcionesOverlay" onclick="window.Cerrar_CitaModal()" class="fixed inset-0 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-40"></button>
    <div id="apptOpcionesModal" class="fixed inset-0 flex items-center justify-center pointer-events-none z-50 px-4">
        <div id="apptModalCard" class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-sm shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 opacity-0 transition-all duration-300">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Opciones de Cita</h3>
                <p id="modalCita_Info" class="text-sm text-gray-500 dark:text-gray-400 mt-1">Selecciona una acción</p>
            </div>
            <div class="space-y-3">
                <button onclick="window.Editar_cita()" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg">
                    <span class="material-symbols-outlined text-sm">edit</span> Editar
                </button>
                <button onclick="window.Eliminar_cita()" class="w-full flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-red-200 text-red-600 hover:bg-red-50 font-medium py-2.5 px-4 rounded-lg">
                    <span class="material-symbols-outlined text-sm">delete</span> Cancelar cita
                </button>
                <button onclick="window.Cerrar_CitaModal()" class="w-full text-gray-500 hover:text-gray-700 text-sm font-medium py-2">Volver atrás</button>
            </div>
        </div>
    </div>

    <div id="profileModalOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-50"></div>
    <div id="perfileModalContainer" class="fixed inset-0 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 z-[60] p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-2xl shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">
            
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">person_edit</span>
                    Editar Información Personal
                </h2>
                <button id="closeProfileBtn" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors text-gray-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                <div id="modalAlert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                        <input id="edit_nombre" type="text" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_nombre" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellido</label>
                        <input id="edit_apellido" type="text" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_apellido" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Edad</label>
                        <input id="edit_edad" type="number" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_edad" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                        <input id="edit_telefono" type="text" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_telefono" class="text-red-500 text-xs mt-1"></p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button id="cancelProfileBtn" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button id="saveProfileBtn" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:opacity-90 transition-opacity flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span> Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <script src="Perfil_Cita.js"></script>
</body>
</html>