<?php

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), false);

$email = $data['email'];
$password = $data['password'];

include 'conexion_bd.php';

try {
    // Consulta para verificar el usuario en la base de datos
    $sql = "SELECT contrasenia FROM pasajero WHERE correo_electronico = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();              //Este
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['contrasenia'])) {
        echo json_encode(['status' => 'success', 'message' => 'Login exitoso']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Correo o contraseña incorrectos']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos']);
}
?>
