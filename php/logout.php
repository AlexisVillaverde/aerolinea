<?php
session_start();

// Establecer el mensaje de cierre de sesión
$_SESSION['logout_message'] = "Has cerrado la sesión exitosamente.";

// Guardar el mensaje en una variable de PHP para usarlo en HTML
$logout_message = $_SESSION['logout_message'];

// Destruir la sesión               //TB
session_unset();
session_destroy();
?>
                            
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script>
        // Redirigir automáticamente después de 2 segundos
        setTimeout(function() {
            window.location.href = "http://127.0.0.1:5500/index.html";
        }, 2000);
    </script>
</head>
<body>
    <!-- Mostrar el mensaje de cierre de sesión -->
    <p><?php echo $logout_message; ?></p>
</body>
</html>
