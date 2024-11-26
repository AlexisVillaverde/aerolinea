<?php

include 'conexion_bd.php'; // Incluye el archivo de conexión

try {

    // Obtener el parámetro de búsqueda (si existe)
    $query = isset($_GET['query']) ? $_GET['query'] : '';
    $origin = isset($_GET['origin']) ? $_GET['origin'] : '';


    // Preparar la consulta SQL
    if ($query) {
        // Si hay una búsqueda, aplicar un filtro `LIKE`
        $stmt = $conn->prepare("SELECT Nombre FROM ciudad WHERE Nombre LIKE :query");
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    } elseif ($origin) {
        // Si no hay búsqueda, obtener todas las ciudades
        $stmt = $conn->prepare("SELECT DISTINCT c.Nombre AS Nombre
                            FROM Vuelo v
                            JOIN Aeropuerto a_dest ON v.ID_Destino = a_dest.ID_Aeropuerto
                            JOIN Ciudad c ON a_dest.ID_Ciudad = c.ID_Ciudad
                            JOIN Aeropuerto a_orig ON v.ID_Origen = a_orig.ID_Aeropuerto
                            JOIN Ciudad co ON a_orig.ID_Ciudad = co.ID_Ciudad
                            WHERE co.Nombre = :origin");
        $stmt->bindParam(':origin', $origin, PDO::PARAM_STR);
     } else {
        // Si no hay búsqueda ni origen, devolver todas las ciudades
        $stmt = $conn->prepare("SELECT Nombre FROM ciudad");
    }

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

?>
