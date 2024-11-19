<?php
// conexion_db.php

$servername = "localhost"; // Cambia esto si tu servidor de base de datos es diferente
$username = "root";
$password = "Grogu123/";
$dbname = "pry_aerolinea";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
