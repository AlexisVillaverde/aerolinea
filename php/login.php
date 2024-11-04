<?php

// Habilitar CORS para solicitudes desde el cliente
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once ('conexion_aerolinea.php');

// Obtener los datos enviados desde el frontend
$data = json_decode(file_get_contents("php://input"));

// Verificar que los datos se recibieron correctamente y que no están vacíos
if (empty($data->email) || empty($data->password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email y contraseña son requeridos.'
    ]);
    exit;
}

$email = $data->email;
$password = $data->password;

// Consulta para verificar las credenciales en la tabla 'pasajero'
$stmt = $pdo->prepare("SELECT * FROM pasajero WHERE Correo_Electronico = :email");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el usuario existe y la contraseña es correcta
if ($user && password_verify($password, $user['contrasenia'])) {
    // Aquí puedes manejar la sesión del usuario o cualquier lógica necesaria
    session_start();
    $_SESSION['user_id'] = $user['ID_Pasajero']; // Guarda el ID del usuario en la sesión

    echo json_encode([
        'status' => 'success',
        'message' => 'Login exitoso'
    ]);
    exit();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Credenciales incorrectas'
    ]);
}
?>








