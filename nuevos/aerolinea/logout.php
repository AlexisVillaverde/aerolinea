<?php
session_start(); // Iniciar la sesión

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la sesión por completo
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Destruir la sesión

// Redirigir al usuario a la página principal o de inicio de sesión
header("Location: http://localhost/si/pry/Aerolinea/");
exit();
?>
