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

    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearAlert();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        
        if (!validateEmail(email)) { showAlert('Ingrese un correo electrónico válido', 'error'); return; }
        if (password.length <= 6) { showAlert('La contraseña debe tener al menos 6 caracteres', 'error'); return; }

        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creando...';

        try {
            const resp = await fetch('register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
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
