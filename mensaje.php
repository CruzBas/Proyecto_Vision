<?php
session_start();
$Loggeado = isset($_SESSION['user_id']);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Vision Contacto</title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
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
  <style>
    .form-input,
    .form-textarea {
      border-color: #e2e8f0;
    }

    .dark .form-input,
    .dark .form-textarea {
      border-color: #334155;
    }

    .form-input:focus,
    .form-textarea:focus {
      border-color: #13a4ec;
      box-shadow: 0 0 0 1px #13a4ec;
    }
  </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#101c22] dark:text-[#f6f7f8]">
  <!--
    mensaje.php - formulario de contacto
    - Protegido por auth.js: si no estás autenticado se redirige a login.html con ?next=mensaje.php
    - El formulario (id="contactForm") valida en el cliente antes de "enviar".
    - La función showAlert muestra un mensaje temporal en el elemento #formAlert.
    - En producción deberías conectar el envío con un endpoint (fetch) para guardar/ enviar el mensaje.
  -->
  <div class="flex flex-col min-h-screen">
    <header class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
      <div class="container mx-auto flex items-center justify-between whitespace-nowrap px-6 py-4">
        <div class="flex items-center gap-3">
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
          <h2 class="text-xl font-bold"> Vision</h2>
        </div>
        <nav class="hidden md:flex items-center gap-8">
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="Index.html"><button>Pagina Principal</button></a>
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="Nosotros.html"><button>Sobre nosotros</button></a>
          <a class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary"
            href="Servicios.html"><button>Servicios</button></a>
        </nav>
        <script>

          document.addEventListener('DOMContentLoaded', function () {
            try {
              if (window.Auth && typeof window.Auth.isLoggedIn === 'function') {
                if (!window.Auth.isLoggedIn()) {
                  var next = 'mensaje.php';
                  location.href = 'login.html?next=' + encodeURIComponent(next);
                }
              }
            } catch (e) {/* ignore */ }
          });
        </script>
        <div class="flex items-center gap-4">
          <?php if (!$Loggeado): ?>
          <a href="login.html"><button
              class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-6 bg-primary text-white text-sm font-bold shadow-lg hover:bg-primary/90 transition-colors">Inicio de Sesion</button></a>
          <?php else: ?>
          <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10"
            style='background-image: url("https://cdn-icons-png.flaticon.com/512/11753/11753627.png");'>
          </div>
          <?php endif; ?>
        </div>
    </header>
    <main class="flex-grow container mx-auto px-6 py-16">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
          <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">Ponte en contacto</h1>
          <p class="text-lg text-black/60 dark:text-white/60 max-w-2xl mx-auto">Estamos aquí para apoyarte. Contáctanos
            si tienes alguna pregunta o para agendar una cita.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
          <div class="space-y-6">
            <h3 class="text-2xl font-bold">Formulario de contacto</h3>
            <form id="contactForm" class="space-y-6" novalidate>

              <div id="formAlert" role="status" aria-live="polite" class="hidden rounded-md p-3 text-sm"></div>

              <div>
                <label class="block text-sm font-medium mb-2" for="name">Nombre y apellidos</label>
                <input required name="name" aria-required="true" aria-describedby="nameHelp"
                  class="form-input w-full bg-background-light dark:bg-background-dark rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                  id="name" placeholder="Ingresa tu nombre" type="text" />
                <p id="nameHelp" class="sr-only">Tu nombre completo (requerido)</p>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" for="email">Correo electronico</label>
                <input required name="email" aria-required="true" aria-describedby="emailHelp"
                  class="form-input w-full bg-background-light dark:bg-background-dark rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                  id="email" placeholder="Ingresa tu email" type="email" />
                <p id="emailHelp" class="sr-only">Correo electrónico válido (requerido)</p>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" for="phone">Numero de telefono</label>
                <input required name="phone" aria-required="true" aria-describedby="phoneHelp"
                  pattern="[0-9+\-()\s]{6,}"
                  class="form-input w-full bg-background-light dark:bg-background-dark rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                  id="phone" placeholder="Ingresa tu numero de telefono" type="tel" />
                <p id="phoneHelp" class="sr-only">Número de teléfono (6+ dígitos). Puedes usar +, -, () y espacios.</p>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2" for="message">Mensaje</label>
                <textarea required name="message" aria-required="true" aria-describedby="messageHelp"
                  class="form-textarea w-full bg-background-light dark:bg-background-dark rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                  id="message" placeholder="Como te podemos ayudar?" rows="5"></textarea>
                <p id="messageHelp" class="sr-only">Describe brevemente cómo podemos ayudarte (requerido)</p>
              </div>
              <div>
                <button id="submitBtn"
                  class="w-full bg-primary text-white font-bold text-sm px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors"
                  type="submit">
                  Enviar mensaje
                </button>
              </div>
            </form>
          </div>
          <div class="space-y-8">
            <div>
              <h3 class="text-2xl font-bold mb-4">Otras formas de conectar</h3>
              <p class="text-black/60 dark:text-white/60 mb-6">Siguenos en nuestrsa redes sociales, tips, y más.</p>
              <div class="flex space-x-4">
                <a class="flex flex-col items-center gap-2 group" href="#">
                  <div class="bg-black/5 dark:bg-white/5 p-4 rounded-full group-hover:bg-primary/20 transition-colors">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M128,80a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160ZM176,24H80A56.06,56.06,0,0,0,24,80v96a56.06,56.06,0,0,0,56,56h96a56.06,56.06,0,0,0,56-56V80A56.06,56.06,0,0,0,176,24Zm40,152a40,40,0,0,1-40,40H80a40,40,0,0,1-40-40V80A40,40,0,0,1,80,40h96a40,40,0,0,1,40,40ZM192,76a12,12,0,1,1-12-12A12,12,0,0,1,192,76Z">
                      </path>
                    </svg>
                  </div>
                  <span
                    class="text-sm font-medium text-black/60 dark:text-white/60 group-hover:text-primary transition-colors">Instagram</span>
                </a>
                <a class="flex flex-col items-center gap-2 group" href="#">
                  <div class="bg-black/5 dark:bg-white/5 p-4 rounded-full group-hover:bg-primary/20 transition-colors">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm8,191.63V152h24a8,8,0,0,0,0-16H136V112a16,16,0,0,1,16-16h16a8,8,0,0,0,0-16H152a32,32,0,0,0-32,32v24H96a8,8,0,0,0,0,16h24v63.63a88,88,0,1,1,16,0Z">
                      </path>
                    </svg>
                  </div>
                  <span
                    class="text-sm font-medium text-black/60 dark:text-white/60 group-hover:text-primary transition-colors">Facebook</span>
                </a>
                <a class="flex flex-col items-center gap-2 group" href="#">
                  <div class="bg-black/5 dark:bg-white/5 p-4 rounded-full group-hover:bg-primary/20 transition-colors">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M247.39,68.94A8,8,0,0,0,240,64H209.57A48.66,48.66,0,0,0,168.1,40a46.91,46.91,0,0,0-33.75,13.7A47.9,47.9,0,0,0,120,88v6.09C79.74,83.47,46.81,50.72,46.46,50.37a8,8,0,0,0-13.65,4.92c-4.31,47.79,9.57,79.77,22,98.18a110.93,110.93,0,0,0,21.88,24.2c-15.23,17.53-39.21,26.74-39.47,26.84a8,8,0,0,0-3.85,11.93c.75,1.12,3.75,5.05,11.08,8.72C53.51,229.7,65.48,232,80,232c70.67,0,129.72-54.42,135.75-124.44l29.91-29.9A8,8,0,0,0,247.39,68.94Zm-45,29.41a8,8,0,0,0-2.32,5.14C196,166.58,143.28,216,80,216c-10.56,0-18-1.4-23.22-3.08,11.51-6.25,27.56-17,37.88-32.48A8,8,0,0,0,92,169.08c-.47-.27-43.91-26.34-44-96,16,13,45.25,33.17,78.67,38.79A8,8,0,0,0,136,104V88a32,32,0,0,1,9.6-22.92A30.94,30.94,0,0,1,167.9,56c12.66.16,24.49,7.88,29.44,19.21A8,8,0,0,0,204.67,80h16Z">
                      </path>
                    </svg>
                  </div>
                  <span
                    class="text-sm font-medium text-black/60 dark:text-white/60 group-hover:text-primary transition-colors">Twitter</span>
                </a>
              </div>
            </div>
            <div>
              <h3 class="text-2xl font-bold mb-4">Nuestra localizacion</h3>
              <p class="text-black/60 dark:text-white/60 mb-2">Visit our office at:</p>
              <address class="not-italic text-black/80 dark:text-white/80">
                El Alto de Guadalupe<br />
                El Alto, 100m al Sur de Novacentro<br />
              </address>
            </div>
            <div class="w-full aspect-video rounded-xl overflow-hidden">
              <div class="w-full h-full bg-cover bg-center"
                style='background-image: url("https://www.weather-forecast.com/locationmaps/Guadalupe-2.12.gif");'>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <footer class="border-t border-black/10 dark:border-white/10 mt-16">
      <div class="container mx-auto px-6 py-8 text-center text-black/60 dark:text-white/60">
        <div class="flex justify-center gap-6 mb-4">
          <a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary"
            href="Politica.php"><button>Politica de privacidad</button></a>
          <a class="text-sm hover:text-primary transition-colors" href="#">Terminos de servicio</a>
        </div>
        <p class="text-sm">© 2025 Vision. All rights reserved.</p>
      </div>
    </footer>
  </div>

  <script>
    (function () {
      // Elementos del formulario y del alert
      const form = document.getElementById('contactForm');
      const alertBox = document.getElementById('formAlert');

      // showAlert: muestra un mensaje temporal en #formAlert
      function showAlert(message, type = 'success') {
        alertBox.classList.remove('hidden');
        alertBox.textContent = message;
        if (type === 'error') {
          alertBox.classList.remove('bg-primary', 'text-white');
          alertBox.classList.add('bg-red-50', 'text-red-700');
        } else {
          alertBox.classList.remove('bg-red-50', 'text-red-700');
          alertBox.classList.add('bg-primary', 'text-white');
        }

        // Ocultar automáticamente después de 5 segundos
        clearTimeout(showAlert._t);
        showAlert._t = setTimeout(() => {
          alertBox.classList.add('hidden');
        }, 5000);
      }

      // validateForm: comprueba campos mínimos y formatos básicos
      function validateForm(data) {
        const errors = [];
        if (!data.get('name') || data.get('name').trim().length < 2) {
          errors.push('Ingresa un nombre válido.');
        }
        const email = data.get('email') || '';

        if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
          errors.push('Ingresa un correo electrónico válido.');
        }
        const phone = data.get('phone') || '';
        if (!/^[0-9+\-()\s]{6,}$/.test(phone)) {
          errors.push('Ingresa un número de teléfono válido (6+ dígitos).');
        }
        if (!data.get('message') || data.get('message').trim().length < 5) {
          errors.push('El mensaje debe tener al menos 5 caracteres.');
        }
        return errors;
      }

   form.addEventListener('submit', function (ev) {
  ev.preventDefault();
  const submitBtn = document.getElementById('submitBtn');
  submitBtn.disabled = true;
  submitBtn.setAttribute('aria-busy', 'true');

  const formData = new FormData(form);
  const errors = validateForm(formData);

  if (errors.length) {
    showAlert(errors.join(' '), 'error');
    submitBtn.disabled = false;
    submitBtn.removeAttribute('aria-busy');
    return;
  }

  // ENVÍO REAL DEL FORMULARIO
  fetch("enviar.php", {
    method: "POST",
    body: new FormData(form)
})
.then(res => res.json())
.then(data => {
    if (data.ok) {
        showAlert("Mensaje enviado correctamente");
        form.reset();
    } else {
        showAlert("Error: " + data.error, "error");
    }
})
.catch(() => showAlert("No se pudo conectar con el servidor", "error"));

});

    
    })();
  </script>

</body>

</html>