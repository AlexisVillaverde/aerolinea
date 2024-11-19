<?php
require('fpdf/fpdf.php');
include 'conexion_bd.php';

if (!isset($_GET['id_pago'])) {
    die("ID de pago no especificado.");
}

$idPago = $_GET['id_pago'];

$query = "SELECT t.ID_Ticket, t.Fecha_Emision, t.Total, p.Metodo_Pago
          FROM ticket t
          JOIN pago p ON t.ID_Pago = p.ID_Pago
          WHERE t.ID_Pago = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $idPago, PDO::PARAM_INT);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Ticket no encontrado.");
}

$pdf = new FPDF('P', 'mm', array(80, 120)); // Tamaño pequeño para simular ticket
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 10, 'Ticket de Pago', 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(40, 5, 'ID Ticket: ' . $ticket['ID_Ticket'], 0, 1);
$pdf->Cell(40, 5, 'Fecha de Emision: ' . $ticket['Fecha_Emision'], 0, 1);
$pdf->Cell(40, 5, 'Total Pagado: $' . number_format($ticket['Total'], 2), 0, 1);
$pdf->Cell(40, 5, 'Metodo de Pago: ' . $ticket['Metodo_Pago'], 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'Gracias por su compra', 0, 1, 'C');

$pdf->Output('I', 'Ticket_Pago.pdf');
?>
