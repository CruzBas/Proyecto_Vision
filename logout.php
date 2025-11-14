<?php
// Iniciar la sesión para poder acceder a ella
session_start();

// Limpiar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página principal o de login
header('Location: Index.html');
exit;
?>