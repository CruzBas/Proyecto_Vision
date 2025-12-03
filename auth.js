document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");

  if (!form) return;

  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const submitButton = form.querySelector("button[type='submit']");

    // Deshabilitar botón para evitar doble envío
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerText = "Cargando...";
    }

    try {
      const response = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
      });

      const data = await response.json();
      
      if (data.success) {
        if(data.usuario.Rol===1){
          window.location.href = "Perfil.php";
        }
        if(data.usuario.Rol===2){
          window.location.href = "Doctor.php";
        }
        if(data.usuario.Rol===3){
          window.location.href = "panelAdmin_View.php";
        }
      
      } else {
        // ERROR: Credenciales incorrectas
        alert(data.error || "Credenciales incorrectas.");
      }
    } catch (error) {
      console.error("Error:", error);
      alert("No se pudo conectar con el servidor. Intente más tarde.");
    } finally {
        
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerText = "Iniciar Sesión";
        }
    }
  });
});