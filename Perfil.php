<?php
session_start();


if (empty($_SESSION['user_id'])) {
    header('Location: login.html?redirect=' . urlencode(basename($_SERVER['PHP_SELF'])));
    exit;
}

require_once 'db.php';

$paciente = null; 

$query = "SELECT Nombre_Paciente, Apellido_Paciente FROM pacientes WHERE ID_Usuario = ?";

if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $_SESSION['user_id']); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $paciente = $row;
    }
    
    $stmt->close();
}


$nombreMostrar = $paciente['Nombre_Paciente'] ?? 'Usuario';
$apellidoMostrar = $paciente['Apellido_Paciente'] ?? '';
$nombreCompleto = trim($nombreMostrar . ' ' . $apellidoMostrar);

$jsData = [
    'nombre' => $nombreMostrar,
    'apellido' => $apellidoMostrar,
    'email' => $_SESSION['user_email'] ?? ''
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
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

      <script>
        // Pasamos los datos limpios de PHP a JS
        const usuarioSesion = <?php echo json_encode($jsData); ?>;

        window.Auth = {
            getUser: function() {
                return {
                    firstName: usuarioSesion.nombre,
                    lastName: usuarioSesion.apellido,
                    email: usuarioSesion.email
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
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Index.html">Página principal</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Servicios.html">Servicios</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Nosotros.html">Sobre nosotros</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="mensaje.html">Contacto</a>
                </nav>
                <div class="flex items-center">
                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 ml-4" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCVwv-zS-uQ4jQkoGZ114abTXXqEX-c3xiMOh4s9_EPt3GoQHd2WOeJroq3oiNMJ5KbtQTAAOn3wilUGvp35adPvzlib0BCn49l08Y2GYRjAgMMB33pGCdMy3aH7BkrVr0zOMB7JBdAMbPVcwVbPmszNA3ZAPvuVoXQl6KpwehiIbxoBrP88-Pn3ersPqFfletB5gpscpKA2UzFNq6fD5hl5rscKhRFMCGk0b_mTq6GuUVUy_7PJmi8Mrle6oVB8KXkA79J6SO6FbA");'></div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <aside class="w-64 bg-background-light dark:bg-background-dark border-r border-gray-200 dark:border-gray-700/50 flex flex-col p-6">
            <div class="flex items-center gap-3 mb-10">
                <div id="perfilAvatar" class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAYXKK9-gR1zZ7av7idsVNcs5cGFmHKUC73uZ9p1SSe4_6M80Tvct9QFixH_FeguMfeWMPFeL3wXUf2qrqVh37say64QsR0uiqBJHIPz0nUJ6XurcZkZbhoTrb5FCQuCQr4xJOXHYpswluSY8n6ClWqZya8qWy4JcPhsj93zV12ovYKmymqKNBd5iBjyg_PSENrLm7DtNbf5S4Y5830tu7xQJZKo1xJAkPAkR4tkCAG-C8cVKENR1fyUVS--ZDQFeUCKX3WKcr1zWs");'></div>
                <div class="flex flex-col">
                    <h1 id="perfilName" class="text-gray-900 dark:text-white font-bold text-lg">
                     <?php echo htmlspecialchars($nombreCompleto); ?>

                    </h1>
                    <p id="perfilRole" class="text-gray-500 dark:text-gray-400 text-sm">Paciente</p>
                    <p id="perfilEmail" class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                    </p>
                    <p id="perfilPhone" class="text-sm text-gray-500 dark:text-gray-400"></p>
                </div>
            </div>

            <nav class="flex flex-col gap-2">
                <a id="overviewBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary text-white font-medium" href="#">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Resumen</span>
                </a>
                <a id="settingsBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium" href="#">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Ajustes</span>
                </a>
            </nav>

            <div class="mt-auto">
                <a id="logoutBtn" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-primary/10 dark:hover:bg-primary/20 font-medium" href="#">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-4xl mx-auto">
                
                <div id="dashboardPanel">
                    <h1 id="welcomeTitle" class="text-4xl font-bold text-gray-900 dark:text-white mb-8">¡Bienvenido,  <?php echo htmlspecialchars($nombreCompleto); ?>
!</h1>
                    <section>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Tus citas</h2>
                            <a href="View_Reserva.php">
                            <button id="bookNewBtn" class="bg-primary text-white font-semibold py-2 px-6 rounded-lg hover:bg-primary/90 transition-colors">
                                Reservar Cita
                            </button>
                            </a>
                        </div>
                        <div id="appointmentsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
                    </section>
                    <section class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Citas pasadas</h2>
                        <div id="pastContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
                    </section>
                </div>

                <div id="settingsPanel" class="hidden bg-white dark:bg-background-dark/50 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700/50">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Ajustes</h2>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border rounded-lg">
                            <div>
                                <p class="text-sm text-gray-500">Correo electrónico</p>
                                <div class="flex items-center gap-3">
                                    <p id="settingsEmail" class="font-semibold text-gray-900 dark:text-gray-100">
                                        <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                                    </p>
                                    <input id="settingsEmailInput" type="email" class="hidden px-2 py-1 rounded-md border" placeholder="tu@correo.com" />
                                </div>
                            </div>
                            <div>
                                <button id="emailEditBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">Edit</button>
                                <button id="emailSaveBtn" class="px-3 py-1 bg-primary text-white rounded text-sm hidden">Save</button>
                                <button id="emailCancelBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm hidden">Cancel</button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border rounded-lg">
                            <div>
                                <p class="text-sm text-gray-500">Contraseña</p>
                                <div class="flex items-center gap-3">
                                    <p id="settingsPasswordDisplay" class="font-semibold text-gray-900 dark:text-gray-100">••••••••</p>
                                    <div id="settingsPasswordInputs" class="hidden space-y-1">
                                        <input id="settingsPasswordInput" type="password" placeholder="Nueva contraseña" class="px-2 py-1 rounded-md border w-full" />
                                        <input id="settingsPasswordConfirm" type="password" placeholder="Confirmar contraseña" class="px-2 py-1 rounded-md border w-full" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button id="passwordEditBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">Edit</button>
                                <button id="passwordSaveBtn" class="px-3 py-1 bg-primary text-white rounded text-sm hidden">Save</button>
                                <button id="passwordCancelBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm hidden">Cancel</button>
                            </div>
                        </div>

                        <div class="p-4 bg-white dark:bg-gray-900 border rounded-lg">
                            <p class="text-sm text-gray-500 mb-2">Preferencias</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Idioma</p>
                                        <p id="settingsLanguage" class="font-semibold text-gray-900 dark:text-gray-100">English</p>
                                        <select id="settingsLanguageSelect" class="hidden px-2 py-1 rounded-md border">
                                            <option value="en">English</option>
                                            <option value="es">Español</option>
                                        </select>
                                    </div>
                                    <div>
                                        <button id="languageEditBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">Edit</button>
                                        <button id="languageSaveBtn" class="px-3 py-1 bg-primary text-white rounded text-sm hidden">Save</button>
                                        <button id="languageCancelBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm hidden">Cancel</button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Zona horaria</p>
                                        <p id="settingsTimezone" class="font-semibold text-gray-900 dark:text-gray-100">(GMT-4) La Paz</p>
                                        <select id="settingsTimezoneSelect" class="hidden px-2 py-1 rounded-md border">
                                            <option value="-4">(GMT-4) La Paz</option>
                                            <option value="-5">(GMT-5) Nueva York</option>
                                            <option value="1">(GMT+1) Madrid</option>
                                        </select>
                                    </div>
                                    <div>
                                        <button id="timezoneEditBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">Edit</button>
                                        <button id="timezoneSaveBtn" class="px-3 py-1 bg-primary text-white rounded text-sm hidden">Save</button>
                                        <button id="timezoneCancelBtn" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm hidden">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 border-t pt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-500">¿Necesitas ayuda? <a href="mensaje.html" class="text-primary">Contáctanos</a></div>
                        <button id="settingsLogout" class="bg-red-600 text-white px-4 py-2 rounded">Log Out</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="confirmOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"></div>
    <div id="confirmModal" class="fixed inset-0 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-[90%] max-w-md shadow-xl border border-gray-200 dark:border-gray-700 transform scale-95">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Confirmar cierre de sesión</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">¿Estás seguro que deseas cerrar sesión? Serás redirigido a la página principal.</p>
            <div class="flex justify-end gap-3">
                <button id="cancelLogout" class="px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-700">Cancelar</button>
                <button id="confirmLogout" class="px-4 py-2 rounded-md bg-red-600 text-white">Cerrar sesión</button>
            </div>
        </div>
    </div>

    <script src="Perfil_Cita.js"></script>
</body>
</html>