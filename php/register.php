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

    // Validar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Si ya existe un usuario con el correo proporcionado
        echo json_encode([
            'status' => 'error',
            'message' => 'El correo ya está registrado'
        ]);
    } else {
        // Si el usuario no existe, se procede a crear uno nuevo

        // Hashear la contraseña antes de guardarla
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insertar el nuevo usuario en la base de datos
        $stmt = $pdo->prepare("INSERT INTO usuarios (email, password) VALUES (:email, :password)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario registrado con éxito'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en la conexión o consulta: ' . $e->getMessage()
    ]);
}
?>
