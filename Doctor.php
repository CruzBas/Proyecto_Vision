<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.html?redirect=' . urlencode(basename($_SERVER['PHP_SELF'])));
    exit;
}

require_once 'db.php';

$Psicologo = null;
$idPsicologo = null;

// Obtenemos datos básicos para la vista inicial
$query = "SELECT 
PS.ID_Psicologo, ES.Nombre_Especialidad, PS.Nombre_Psicologo,PS.Apellido_Psicologo 
FROM psicologos AS PS
 JOIN especialidades AS ES ON PS.ID_Especialidad = ES.ID_Especialidad
 WHERE ID_Usuario = ?";

if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $Psicologo = $row;
        $idPsicologo = $row['ID_Psicologo'];
    }
    $stmt->close();
}

$nombreMostrar = $Psicologo['Nombre_Psicologo'] ?? 'Usuario';
$apellidoMostrar = $Psicologo['Apellido_Psicologo'] ?? '';
$nombreCompleto = trim($nombreMostrar . ' ' . $apellidoMostrar);
$Especialidad = $Psicologo['Nombre_Especialidad'] ?? '';

$jsData = [
    'nombre' => $nombreMostrar,
    'apellido' => $apellidoMostrar,
    'idPsicologo' => $idPsicologo,
    'Especialidad' => $Especialidad
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Vision - Panel de Usuario</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
    <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;700;900"
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

        .fc-event {
            cursor: pointer;
        }
    </style>

    <script>
        const usuarioSesion = <?php echo json_encode($jsData); ?>;
        window.Auth = {
            getUser: function () {
                return {
                    firstName: usuarioSesion.nombre,
                    lastName: usuarioSesion.apellido,
                    idPsicologo: usuarioSesion.idPsicologo,
                    especialidadPsi: usuarioSesion.especialidadPsi
                };
            },
            logout: function (options) {
                window.location.href = 'logout.php';
            }
        };
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">

    <header
        class="bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="text-primary h-8 w-8">
                        <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_6_319)">
                                <path
                                    d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z"
                                    fill="currentColor"></path>
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
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors"
                        href="Index.php">Página principal</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors"
                        href="Servicios.php">Servicios</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors"
                        href="Nosotros.php">Sobre nosotros</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors"
                        href="mensaje.php">Contacto</a>
                </nav>
                <div class="flex items-center">
                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 ml-4"></div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <aside
            class="w-64 bg-background-light dark:bg-background-dark border-r border-gray-200 dark:border-gray-700/50 flex flex-col p-6">
            <div class="flex items-center gap-3 mb-10">
                <div id="perfilAvatar" class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12"
                    style='background-image: url("https://cdn-icons-png.flaticon.com/512/11753/11753627.png");'></div>
                <div class="flex flex-col">
                    <h1 id="perfilName" class="text-gray-900 dark:text-white font-bold text-lg">
                        <?php echo htmlspecialchars($nombreCompleto); ?>
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo htmlspecialchars($Especialidad); ?>
                    </p>
                </div>
            </div>

            <nav class="flex flex-col gap-2">
                <a id="overviewBtn"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary text-white font-medium cursor-pointer">
                    <span class="material-symbols-outlined">event_list</span>
                    <span>Citas</span>
                </a>
                <a id="CalendarioBtn"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer"
                    href="calendarioCitas_View.php">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <span>Calendario</span>
                </a>
                <a id="NotasBtn"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer"
                    href="VerNotas.php">
                    <span class="material-symbols-outlined">sticky_note_2</span>
                    <span>Notas</span>
                </a>
                <a id="PdfBtn"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer"
                    href="ResumenPdf.php">
                    <span class="material-symbols-outlined">edit_document</span>
                    <span>Generar PDF</span>
                </a>
                <a id="Configuracion_Btn"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Ajustes</span>
                </a>
            </nav>

            <div class="mt-auto">
                <a id="logoutBtn"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium cursor-pointer">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-4xl mx-auto">

                <div id="dashboardPanel">
                    <h1 id="welcomeTitle" class="text-4xl font-bold text-gray-900 dark:text-white mb-8">¡Bienvenido,
                        <?php echo htmlspecialchars($nombreCompleto); ?>!
                    </h1>
                </div>
            </div>
    </div>


    <div id="Configuracion_Panel"
        class="hidden bg-white dark:bg-background-dark/50 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700/50">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Ajustes</h2>
        <div class="space-y-6">
            <div
                class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Información Personal</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">Datos del Perfil</p>
                    <p class="text-xs text-gray-400">Nombre, edad, teléfono</p>
                </div>
                <button id="openProfileModalBtn"
                    class="px-4 py-2 bg-primary/10 text-primary hover:bg-primary/20 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">edit</span> Editar
                </button>
            </div>

            <div
                class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div>
                    <p class="text-sm text-gray-500">Correo electrónico</p>
                    <p id="settingsEmail" class="font-semibold text-gray-900 dark:text-gray-100">
                        <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-8 border-t pt-4 flex justify-between items-center">
            <div class="text-sm text-gray-500">¿Necesitas ayuda? <a href="mensaje.php"
                    class="text-primary">Contáctanos</a></div>
        </div>
    </div>

    </div>
    </main>
    </div>

    <button id="appOpcionesOverlay" onclick="window.Cerrar_CitaModal()"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-40"></button>
    <div id="apptOpcionesModal" class="fixed inset-0 flex items-center justify-center pointer-events-none z-50 px-4">
        <div id="apptModalCard"
            class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-sm shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 opacity-0 transition-all duration-300">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Opciones de Cita</h3>
                <p id="modalCita_Info" class="text-sm text-gray-500 dark:text-gray-400 mt-1">Selecciona una acción</p>
            </div>
            <div class="space-y-3">
                <button onclick="window.handleEditAppointment()"
                    class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg">
                    <span class="material-symbols-outlined text-sm">edit</span> Editar
                </button>
                <button onclick="window.Eliminar_cita()"
                    class="w-full flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-red-200 text-red-600 hover:bg-red-50 font-medium py-2.5 px-4 rounded-lg">
                    <span class="material-symbols-outlined text-sm">delete</span> Cancelar cita
                </button>
                <button onclick="window.Cerrar_CitaModal()"
                    class="w-full text-gray-500 hover:text-gray-700 text-sm font-medium py-2">Volver atrás</button>
            </div>
        </div>
    </div>

    <div id="profileModalOverlay"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    </div>
    <div id="perfileModalContainer"
        class="fixed inset-0 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 z-[60] p-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-2xl shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">

            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">person_edit</span>
                    Editar Información Personal
                </h2>
                <button id="closeProfileBtn"
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors text-gray-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                <div id="modalAlert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                        <input id="edit_nombre" type="text"
                            class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_nombre" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellido</label>
                        <input id="edit_apellido" type="text"
                            class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_apellido" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Edad</label>
                        <input id="edit_edad" type="number"
                            class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_edad" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                        <input id="edit_telefono" type="text"
                            class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p id="err_telefono" class="text-red-500 text-xs mt-1"></p>
                    </div>
                </div>
            </div>

            <div
                class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <button id="cancelProfileBtn"
                    class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button id="saveProfileBtn"
                    class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:opacity-90 transition-opacity flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span> Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <script src="script_Doctor.js"></script>
</body>

</html>