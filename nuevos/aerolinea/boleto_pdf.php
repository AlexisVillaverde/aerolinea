<?php
require('fpdf/fpdf.php');
include 'conexion_bd.php';

if (!isset($_GET['id_reserva'])) {
    die("ID de reserva no especificado.");
}

$idReserva = $_GET['id_reserva'];

$query = "SELECT r.ID_Reserva, p.Nombre, p.Primer_Apellido, p.Segundo_Apellido, v.Fecha_Salida, v.Hora_Salida,
                 a.Modelo AS Avion, v.ID_Vuelo, ae1.Nombre AS Origen, ae2.Nombre AS Destino, 
                 s.Numero_Asiento, s.Clase
          FROM reserva r
          JOIN pasajero p ON r.ID_Pasajero = p.ID_Pasajero
          JOIN vuelo v ON r.ID_Vuelo = v.ID_Vuelo
          JOIN asiento s ON r.ID_Asiento = s.ID_Asiento
          JOIN aeropuerto ae1 ON v.ID_Origen = ae1.ID_Aeropuerto
          JOIN aeropuerto ae2 ON v.ID_Destino = ae2.ID_Aeropuerto
          JOIN avion a ON v.ID_Avion = a.ID_Avion
          WHERE r.ID_Reserva = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $idReserva, PDO::PARAM_INT);
$stmt->execute();
$boleto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$boleto) {
    die("Reserva no encontrada.");
}

$pdf = new FPDF('L', 'mm', array(210, 90)); // Formato horizontal tipo boleto
$pdf->AddPage();

// Logo de la aerolínea
$pdf->Image('skywings_logo.png', 10, 5, 30);

// Título del boleto
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'PASE DE ABORDAJE', 0, 1, 'C');

// Información principal
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, 'Pasajero: ' . $boleto['Nombre'] . ' ' . $boleto['Primer_Apellido'], 0, 0, 'L');
$pdf->Cell(0, 8, 'ID Vuelo: ' . $boleto['ID_Vuelo'], 0, 1, 'R');


// Sección con datos del vuelo
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, 'Origen: ' . $boleto['Origen'], 0, 0, 'L');
$pdf->Image('airplane.png', 100, 30, 20); // Reemplaza con la ruta de tu código de barras
$pdf->Cell(95, 8, 'Destino: ' . $boleto['Destino'], 0, 1, 'R');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, 'Fecha de Salida: ' . $boleto['Fecha_Salida'], 0, 0, 'L');
$pdf->Cell(95, 8, 'Hora de Salida: ' . $boleto['Hora_Salida'], 0, 1, 'R');

// Información del asiento y clase
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, 'Asiento: ' . $boleto['Numero_Asiento'], 0, 0, 'L');
$pdf->Cell(95, 8, 'Clase: ' . utf8_decode($boleto['Clase']), 0, 1, 'R');

// Código de barras (simulado con una imagen)
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Buen viaje!', 0, 1, 'C');

$pdf->Image('codigo_barras.png', 80, 60, 50); // Reemplaza con la ruta de tu código de barras
// Salida del PDF
$pdf->Output('I', 'Pase_Abordaje.pdf');

?>
