<?php
// Habilitar CORS para permitir solicitudes desde localhost:3000 y 127.0.0.1:5500
$allowedOrigins = ['http://localhost:3000', 'http://127.0.0.1:5500'];

if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}
include 'conexion_bd.php'; // Incluye el archivo de conexión

try {

    // Obtener el parámetro de búsqueda de la URL
    $query = isset($_GET['query']) ? $_GET['query'] : '';

    // Preparar la consulta SQL con un parámetro preparado para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT Nombre FROM ciudad WHERE Nombre LIKE :query ");

    // Bindear el parámetro de búsqueda con el valor que incluye comodines para LIKE
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados y almacenarlos en un array
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los resultados como JSON
    echo json_encode($cities);

} catch (PDOException $e) {
    // Manejar errores de conexión o ejecución
    die("Error en la conexión o consulta: " . $e->getMessage());
}

// No es necesario cerrar la conexión manualmente con PDO; se cierra al final del script
?>
