<?php

session_start();
// Habilitar CORS para permitir solicitudes desde localhost:3000 y 127.0.0.1:5500
$allowedOrigins = ['http://localhost:3000', 'http://127.0.0.1:5500'];

if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}


include 'conexion_bd.php'; // Incluye el archivo de conexión


// Recibir los datos del formulario
$origin = $_POST['origin'] ;
$destination = $_POST['destination'] ;
$departureDate = $_POST['departureDate'] ;
$returnDate = $_POST['returnDate'] ;

// Convertir las fechas al formato `yyyy-mm-dd` si están en `mm/dd/yyyy`
if (!empty($departureDate)) {
    $departureDate = date("Y-m-d", strtotime($departureDate));
}

if (!empty($returnDate)) {
    $returnDate = date("Y-m-d", strtotime($returnDate));
}

try{

    // Preparar la consulta SQL con un parámetro preparado para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT 
            c.Nombre AS ciudad_destino,
            co.Nombre AS ciudad_origen,
            a.Codigo_IATA AS aeropuerto_destino,
            ao.Codigo_IATA AS aeropuerto_origen,
            av.Modelo AS modelo_avion,
            v.Fecha_Salida,
            v.Hora_Salida, 
            v.Fecha_Llegada,
            v.Hora_Llegada,
            v.Costo as Costo
            FROM 
                ciudad c 
            JOIN 
                Aeropuerto a ON c.ID_Ciudad = a.ID_Ciudad
            JOIN 
                Vuelo v ON a.ID_Aeropuerto = v.ID_Destino
            JOIN 
                Avion av ON av.ID_Avion = v.ID_Avion
            JOIN 
                Aeropuerto ao ON v.ID_Origen = ao.ID_Aeropuerto
            JOIN 
                Ciudad co ON ao.ID_Ciudad = co.ID_Ciudad
            WHERE 
                co.Nombre = :ciudad_origen
                AND c.Nombre = :ciudad_destino
                AND v.Fecha_Salida = :fecha_salida
                AND v.Fecha_Llegada = :fecha_regreso");

    // Bindear el parámetro de búsqueda
    $stmt->bindParam(':ciudad_origen', $origin, PDO::PARAM_STR);
    $stmt->bindParam(':ciudad_destino', $destination, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_salida', $departureDate, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_regreso', $returnDate, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados y almacenarlos en un array
    $fligths = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los resultados como JSON
    echo json_encode($fligths);

    // Guardar los resultados en una variable de sesión
    $_SESSION['resultados'] =$fligths;

    // Redirigir a la página HTML donde se mostrarán los resultados
    //header("Location: http://127.0.0.1:5500/vuelos.html?origin=$origin&destination=$destination&departureDate=$departureDate&returnDate=$returnDate");
    header("Location: http://localhost/si/aerolinea/get_Flights.php");

    exit;

} catch (PDOException $e) {
    // Manejar errores de conexión o ejecución
    echo json_encode(['error' => "Error en la conexión o consulta: " . $e->getMessage()]);
}
?>