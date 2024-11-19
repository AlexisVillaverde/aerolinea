<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye la conexión a la base de datos
include 'conexion_bd.php';

// Definir la respuesta por defecto
$response = array('status' => 'error', 'message' => 'Error al cargar las reservas, inicia sesión primero', 'reservas' => array());
session_start();

$idPasajero = $_SESSION['ID_Pasajero'];
// Consulta SQL
$query = "
    SELECT 
        r.ID_Reserva, 
        r.ID_Vuelo, 
        a.Clase, 
        a.Numero_asiento, 
        a.Tipo_asiento, 
        CONCAT(p.Nombre, ' ', p.Primer_Apellido, ' ', p.Segundo_Apellido) AS Nombre_Completo,
        v.Fecha_Salida, 
        v.Hora_Salida, 
        v.Fecha_Llegada, 
        v.Hora_Llegada, 
        v.Tipo_Vuelo
    FROM 
        reserva r
    JOIN 
        pasajero p ON r.ID_Pasajero = p.ID_Pasajero
    JOIN 
        asiento a ON r.ID_Asiento = a.ID_Asiento
    JOIN 
        vuelo v ON r.ID_Vuelo = v.ID_Vuelo
    where p.ID_Pasajero = :id";



try {
    // Ejecutar la consulta
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $idPasajero, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener los resultados
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay reservas
    if ($reservas) {
        $response['status'] = 'success';
        $response['message'] = 'Reservas cargadas correctamente.';
        $response['reservas'] = $reservas;
    } else {
        $response['status'] = 'success';
        $response['message'] = 'No se encontraron reservas.';
        $response['reservas'] = [];
    }
} catch (PDOException $e) {
    $response['status'] = 'error';
    $response['message'] = 'Error al ejecutar la consulta: ' . $e->getMessage();
    $response['reservas'] = []; // Asegurarse de enviar un array vacío en caso de error
}

// Convertir la respuesta a formato JSON
header('Content-Type: application/json'); // Esto asegura que la respuesta se interprete como JSON
echo json_encode($response);
?>
