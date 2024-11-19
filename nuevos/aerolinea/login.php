<?php

header('Content-Type: application/json');
session_start(); // Iniciar sesión para guardar el ID del pasajero

// Obtener datos JSON enviados en la solicitud
$data = json_decode(file_get_contents('php://input'), false);

$email = trim($data->email ?? '');
$passwordusr = trim($data->password ?? '');
$redirectUrl = trim($data->redirectUrl ?? ''); // URL a la que redirigir después del login


include 'conexion_bd.php';

try {
    // Consulta para verificar el usuario y obtener su ID
    $sql = "SELECT ID_Pasajero, contrasenia, perfil FROM pasajero WHERE correo_electronico = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $storedPassword = $user['contrasenia'];
        $perfil = $user['perfil']; // Perfil del usuario (admin o cliente)

        // Verificar la contraseña
        if (password_verify($passwordusr, $storedPassword)) {
            // Guardar el ID del pasajero en la sesión
            $_SESSION['ID_Pasajero'] = $user['ID_Pasajero'];

            // Almacenar la sesión
            $_SESSION['email'] = $email;
            $_SESSION['perfil'] = $perfil;  // Guardar el perfil del usuario en la sesión

             // Redirigir según el perfil
            if ($perfil === 'admin') {
                echo json_encode(['status' => 'success', 'message' => 'Login exitoso', 'redirect' => 'http://127.0.0.1:5500/admin.html']);
            } else {

            // Respuesta exitosa
            echo json_encode([
                'status' => 'success',
                'message' => 'Login exitoso',
                'id_pasajero' => $user['ID_Pasajero'],
                'redirect' => $redirectUrl ?: 'confirmar_reserva.php' // Redirigir si hay URL
            ]);}
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

