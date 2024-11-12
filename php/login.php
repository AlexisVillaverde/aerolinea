<?php

header('Content-Type: application/json');

// Obtener datos JSON enviados en la solicitud
$data = json_decode(file_get_contents('php://input'), false);

$email = trim($data->email ?? '');
$passwordusr = trim($data->password ?? '');

include 'conexion_bd.php';

try {
    // Consulta para verificar el usuario en la base de datos
    $sql = "SELECT contrasenia FROM pasajero WHERE correo_electronico = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $storedPassword = $user['contrasenia'];         //

        // Confirmar si el hash está presente y probar la verificación
        if (password_verify($passwordusr, $storedPassword)) {
            echo json_encode(['status' => 'success', 'message' => 'Login exitoso']);
        } else {
            error_log("Contraseña incorrecta para el usuario con correo: $email");
            echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
        }
    } else {
        error_log("Correo electrónico no encontrado: $email");
        echo json_encode(['status' => 'error', 'message' => 'Correo electrónico no encontrado']);
    }
} catch (PDOException $e) {
    error_log("Error en login.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error en la consulta a la base de datos']);
}

?>
