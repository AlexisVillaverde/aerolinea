<?php
    echo("Hola Mundo");

    // Habilitar CORS para permitir solicitudes desde localhost:3000 y 127.0.0.1:5500
    $allowedOrigins = ['http://localhost:3000', 'http://127.0.0.1:5500'];

    if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }


    // Configura la conexión a la base de datos
    $host = "localhost";
    $user = "root";
    $pass = "Grogu123/";
    $db = "pry_aerolinea";


    // Recibir los datos del formulario
    $origin = $_POST['origin'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $departureDate = $_POST['departureDate'] ?? '';
    $returnDate = $_POST['returnDate'] ?? '';

    try{

        $mysqli = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        // Configurar PDO para que muestre excepciones en caso de error
        $mysqli->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Preparar la consulta SQL con un parámetro preparado para evitar inyecciones SQL
        $stmt = $mysqli->prepare("SELECT 
            co.Nombre AS ciudad_origen,
            c.Nombre AS ciudad_destino,
            a.Nombre AS aeropuerto_destino,
            ao.Nombre AS aeropuerto_origen,
            av.Modelo AS modelo_avion,
            v.Fecha_Salida,
            v.Fecha_Llegada 
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
        $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los resultados como JSON
        echo json_encode($cities);

    } catch (PDOException $e) {
        // Manejar errores de conexión o ejecución
        echo json_encode(['error' => "Error en la conexión o consulta: " . $e->getMessage()]);
    }
?>
