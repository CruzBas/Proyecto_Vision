document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");

  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {
      const response = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
      });

      const data = await response.json();

      if (data.success) {
        localStorage.setItem("usuarioActivo", JSON.stringify(data.usuario));
        alert("Bienvenido " + data.usuario.nombre);
        window.location.href = "Perfil.html"; 
      } else {
        alert(data.error || "Credenciales incorrectas.");
      }
    } catch (error) {
      console.error("Error:", error);
      alert("No se pudo conectar con el servidor.");
    }
  });
});