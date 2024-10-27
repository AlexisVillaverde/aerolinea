<?php

// Habilitar CORS para solicitudes desde React
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Configura la conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "Grogu123/";
$db = "pry_aerolinea";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos enviados desde el frontend (React)
    $data = json_decode(file_get_contents("php://input"));

    $email = $data->email;
    $password = $data->password;

    // Consulta para verificar las credenciales
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y la contraseña es correcta
    if ($user && password_verify($password, $user['password'])) {
        // Si es correcto, devolver respuesta exitosa
        echo json_encode([
            'status' => 'success',
            'message' => 'Login exitoso',
            'user' => $user
        ]);
    } else {
        // Si no es correcto, devolver un error
        echo json_encode([
            'status' => 'error',
            'message' => 'Credenciales inválidas'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en la conexión o consulta: ' . $e->getMessage()
    ]);
}
?>

