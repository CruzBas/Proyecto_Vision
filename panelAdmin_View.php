<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.html?redirect=' . urlencode(basename($_SERVER['PHP_SELF'])));
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Panel de Administración</title>

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;600;700;900"
    />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#13a4ec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101c22",
                    },
                    fontFamily: {
                        display: ["Inter", "system-ui", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "1rem",
                        xl: "1.5rem",
                        full: "9999px",
                    },
                },
            },
        };
    </script>

    <!-- Iconos Material -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"
    />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-100">
    <div class="relative flex min-h-screen w-full">
        <main class="flex-1 w-full">

            <!-- HEADER -->
            <header class="sticky top-0 z-20 border-b bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <!-- Logo + título -->
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 text-primary">
                            <svg
                                fill="none"
                                viewBox="0 0 48 48"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-full w-full"
                            >
                                <g clip-path="url(#clip0_6_319)">
                                    <path
                                        d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z"
                                        fill="currentColor"
                                    ></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_6_319">
                                        <rect width="48" height="48" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Vision</h1>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                             Panel de administración
                            </span>
                        </div>
                    </div>

                    <!-- Avatar / usuario -->
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-full bg-cover bg-center bg-no-repeat border border-white/50 shadow-sm"
                            aria-label="Avatar administrador"
                            style='background-image: url("https://cdn-icons-png.flaticon.com/512/11753/11753627.png");'
                        ></div>
                    </div>
                </div>
            </header>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-8 space-y-6">
                <!-- Título sección -->
                <div class="flex flex-col gap-1">
                    <h2 class="text-2xl font-semibold tracking-tight">Administración de usuarios</h2>
                </div>

                <!-- Filtros -->
                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <!-- Buscador -->
                    <div class="flex-1 min-w-[260px]">
                        <label class="flex flex-col w-full">
                            <span class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">
                                Buscar
                            </span>
                            <div
                                class="flex w-full items-stretch rounded-lg h-11 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 focus-within:ring-2 focus-within:ring-primary focus-within:border-transparent shadow-sm"
                            >
                                <div class="flex items-center justify-center pl-3 pr-1 text-gray-500">
                                    <span class="material-symbols-outlined text-xl">search</span>
                                </div>
                                <input
                                    id="f_nombre"
                                    type="text"
                                    class="form-input flex w-full border-none bg-transparent h-full px-3 text-sm focus:ring-0 focus:outline-none"
                                    placeholder="Buscar por nombre o correo..."
                                />
                            </div>
                        </label>
                    </div>

                    <!-- Filtro por rol -->
                    <div class="w-full sm:w-auto">
                        <label class="flex flex-col">
                            <span class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">
                                Tipo de usuario
                            </span>
                            <select
                                id="f_rol"
                                class="h-11 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            >
                                <option value="">Todos</option>
                                <option value="1">Paciente</option>
                                <option value="2">Doctor</option>
                                <option value="3">Administrador</option>
                            </select>
                        </label>
                    </div>
                </div>

                <!-- TABLA -->
                <div class="mt-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[1024px] text-left text-sm">
                                <thead class="bg-gray-100 dark:bg-slate-800/60">
                                    <tr class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                        <th class="p-4">ID Usuario</th>
                                        <th class="p-4">ID Paciente / ID Psicólogo</th>
                                        <th class="p-4">Nombre</th>
                                        <th class="p-4">Apellido</th>
                                        <th class="p-4">Correo</th>
                                        <th class="p-4">Teléfono</th>
                                        <th class="p-4">Rol</th>
                                        <th class="p-4 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_usuarios" class="divide-y divide-gray-100 dark:divide-slate-800">
                                    <!-- Se renderiza vía panelAdmin.js -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="flex items-center justify-between gap-4 px-4 py-3 border-t border-gray-100 dark:border-slate-800">
                            <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                Mostrando resultados
                            </span>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="flex h-8 items-center justify-center rounded-md border border-gray-200 dark:border-slate-700 px-3 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-primary/10 transition-colors"
                                >
                                    Anterior
                                </button>
                                <button
                                    type="button"
                                    class="flex h-8 items-center justify-center rounded-md border border-gray-200 dark:border-slate-700 px-3 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-primary/10 transition-colors"
                                >
                                    Siguiente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- jQuery (AJAX) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- JS del panel -->
    <script src="panelAdmin.js"></script>
</body>
</html>
