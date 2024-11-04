
<?php

$host = "localhost";
$user = "root";
$pass = "SaulGhost04";
$db = "pry_aerolinea";

try {
    // Crear una instancia de PDO para la conexión
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si ocurre un error, detener el script y mostrar un mensaje de error
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

?>
