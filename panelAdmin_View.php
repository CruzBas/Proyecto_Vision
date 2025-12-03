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
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Panel de Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
    <link as="style" href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter:wght@400;500;700;900"
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
      .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
<div class="relative flex min-h-screen w-full">
<main class="flex-1 p-8 w-full">
    <!-- Nuevo header responsivo bonito -->
    <header class="bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm sticky top-0 z-10 border-b">
        <div class="container mx-auto px-10 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
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
                    <h1 class="text-xl font-bold text-neutral-text-light dark:text-neutral-text-dark">Administración</h1>
                </div>

                <nav class="hidden md:flex items-center gap-8 ml-6">
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Index.html">Página principal</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Servicios.html">Servicios</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="Nosotros.html">Sobre nosotros</a>
                    <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" href="mensaje.html">Contacto</a>
                </nav>
            </div>

            <div class="flex flex-1 justify-end gap-4 items-center">
                <label class="relative flex h-10 w-full max-w-64 items-center">
                    <span class="material-symbols-outlined absolute left-3 text-gray-500 dark:text-gray-400 text-xl">search</span>
                    <input class="form-input h-full w-full rounded-lg border-neutral-border-light bg-white dark:border-neutral-border-dark dark:bg-neutral-border-dark/30 pl-10 text-base font-normal leading-normal placeholder:text-gray-400 focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Buscar" value=""/>
                </label>
                <button class="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg border border-neutral-border-light bg-white dark:border-neutral-border-dark dark:bg-neutral-border-dark/30">
                    <span class="material-symbols-outlined text-xl text-gray-600 dark:text-gray-300">notifications</span>
                </button>
                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10" data-alt="Admin avatar" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCVwv-zS-uQ4jQkoGZ114abTXXqEX-c3xiMOh4s9_EPt3GoQHd2WOeJroq3oiNMJ5KbtQTAAOn3wilUGvp35adPvzlib0BCn49l08Y2GYRjAgMMB33pGCdMy3aH7BkrVr0zOMB7JBdAMbPVcwVbPmszNA3ZAPvuVoXQl6KpwehiIbxoBrP88-Pn3ersPqFfletB5gpscpKA2UzFNq6fD5hl5rscKhRFMCGk0b_mTq6GuUVUy_7PJmi8Mrle6oVB8KXkA79J6SO6FbA");'></div>
            </div>
        </div>
    </header>

    <!-- el resto del contenido queda igual -->

    <!-- Filtros -->
    <div class="mt-8 flex flex-wrap items-center gap-4">
    <div class="flex-grow">
        <label class="flex flex-col w-full min-w-72">
            <div class="flex w-full items-stretch rounded-lg h-12 bg-white border focus-within:ring-2 focus-within:ring-primary">
                <div class="flex items-center justify-center pl-4 text-gray-500">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input id="f_nombre" class="form-input flex w-full border-none bg-transparent h-full px-4 pl-2 text-base"
                    placeholder="Buscar por nombre o correo..."/>
            </div>
        </label>
    </div>
    <!-- Combobox cambiado a Rol -->
    <select id="f_rol" class="flex h-10 items-center rounded-lg border px-4 text-sm font-medium">
      <option value="">Tipo de usuario: Todos</option>
      <option value="1">Paciente</option>
      <option value="2">Doctor</option>
      <option value="3">Administrador</option>
    </select>
</div>

    <!-- Tabla -->
<div class="mt-6">
  <div class="overflow-hidden rounded-lg border bg-white">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[1024px] text-left">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-4 text-sm font-semibold">ID Usuario</th>
            <th class="p-4 text-sm font-semibold">ID Paciente / ID Psicólogo</th>
            <th class="p-4 text-sm font-semibold">Nombre</th>
            <th class="p-4 text-sm font-semibold">Apellido</th>
            <th class="p-4 text-sm font-semibold">Correo</th>
            <th class="p-4 text-sm font-semibold">Teléfono</th>
            <th class="p-4 text-sm font-semibold">Rol</th>
            <th class="p-4 text-sm font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbody_usuarios" class="divide-y">
          <!-- Se renderiza via panelAdmin.js -->
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between p-4 border-t">
      <span class="text-sm text-gray-500">Mostrando resultados</span>
      <div class="flex items-center gap-2">
        <button class="flex h-8 items-center justify-center rounded-md border px-3 text-sm font-medium hover:bg-primary/10">Anterior</button>
        <button class="flex h-8 items-center justify-center rounded-md border px-3 text-sm font-medium hover:bg-primary/10">Siguiente</button>
      </div>
    </div>
  </div>
</div>
</main>
</div>
<!-- Import de AJAX-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Import de js -->
 <script src="panelAdmin.js"></script>
</body>
</html>