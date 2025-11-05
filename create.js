
// create.js
(function () {
    const form = document.getElementById('createForm');
    const alertEl = document.getElementById('formAlert');
    const submitBtn = document.getElementById('createSubmit');

    function showAlert(message, type = 'error') {
        alertEl.className = '';
        alertEl.classList.add('rounded', 'p-3', 'text-sm');
        if (type === 'error') {
            alertEl.classList.add('bg-red-50', 'text-red-700');
        } else {
            alertEl.classList.add('bg-green-50', 'text-green-700');
        }
        alertEl.textContent = message;
        alertEl.classList.remove('hidden');
    }

    function clearAlert() {
        alertEl.classList.add('hidden');
        alertEl.textContent = '';
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validatePhone(phone) {
        return /^[0-9+\-()\s]{6,}$/.test(phone);
    }

    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearAlert();

        const first = document.getElementById('first-name').value.trim();
        const last = document.getElementById('last-name').value.trim();
        const ageVal = document.getElementById('age').value;
        const age = parseInt(ageVal, 10);
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const password = document.getElementById('password').value;

        if (!first || !last) { showAlert('Debe ingresar nombre y apellido', 'error'); return; }
        if (!ageVal || Number.isNaN(age)) { showAlert('Ingrese una edad válida', 'error'); return; }
        if (age < 18) { showAlert('Debes ser mayor de 18 años para crear una cuenta', 'error'); return; }
        if (!validateEmail(email)) { showAlert('Ingrese un correo electrónico válido', 'error'); return; }
        if (!validatePhone(phone)) { showAlert('Ingrese un teléfono válido', 'error'); return; }
        if (password.length <= 6) { showAlert('La contraseña debe tener al menos 6 caracteres', 'error'); return; }

        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creando...';

        try {
            const resp = await fetch('register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ first, last, age, phone, email, password })
            });

            const data = await resp.json();

            if (resp.ok && data.success) {
                showAlert(data.message || 'Cuenta creada correctamente.', 'success');
                form.reset();
            } else {
                // Mensajes del servidor (400,409,500, etc.)
                showAlert(data.message || 'Ocurrió un error al crear la cuenta', 'error');
            }
        } catch (err) {
            showAlert('Error de red. Intenta nuevamente.', 'error');
            console.error(err);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Crear cuenta';
        }
    });
})();
