<?php

header('Content-Type: application/json');
session_start();

// Obtener datos JSON enviados en la solicitud
$data = json_decode(file_get_contents('php://input'), false);

$email = trim($data->email ?? '');
$passwordusr = trim($data->password ?? '');

include 'conexion_bd.php';

try {
    // Consulta para verificar si el usuario existe en la base de datos
    $sql = "SELECT contrasenia, perfil FROM pasajero WHERE correo_electronico = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $storedPassword = $user['contrasenia'];
        $perfil = $user['perfil']; // Perfil del usuario (admin o cliente)

        // Confirmar si el hash está presente y probar la verificación
        if (password_verify($passwordusr, $storedPassword)) {
            // Almacenar la sesión
            $_SESSION['email'] = $email;
            $_SESSION['perfil'] = $perfil;  // Guardar el perfil del usuario en la sesión

            // Redirigir según el perfil
            if ($perfil === 'admin') {
                echo json_encode(['status' => 'success', 'message' => 'Login exitoso', 'redirect' => 'http://127.0.0.1:5500/admin.html']);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Login exitoso', 'redirect' => 'http://127.0.0.1:5500/index.html']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Correo electrónico o contraseña incorrectos']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Correo electrónico o contraseña incorrectos']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la consulta a la base de datos']);
}

?>
