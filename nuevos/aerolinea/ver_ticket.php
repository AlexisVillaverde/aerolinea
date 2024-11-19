<?php
include 'conexion_bd.php';

if (!isset($_GET['id_pago'])) {
    die("ID de pago no especificado.");
}

$idPago = $_GET['id_pago'];

// Obtener datos del ticket
$queryTicket = "SELECT t.ID_Ticket, t.Fecha_Emision, t.Total, p.Metodo_Pago, p.ID_Reserva
                FROM ticket t
                JOIN pago p ON t.ID_Pago = p.ID_Pago
                WHERE t.ID_Pago = ?";
$stmt = $conn->prepare($queryTicket);
$stmt->bindParam(1, $idPago, PDO::PARAM_INT);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Ticket no encontrado.");
}

// Obtener ID de la reserva
$idReserva = $ticket['ID_Reserva'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Vuelo</title>
    <link rel="stylesheet" href="principal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            border-radius: 10px;
            background-color: #fff;
        }

        .container-data {
            margin: 0 auto;
            width: 50%;
            padding-top: 50px;
            justify-items: center;
        }

        h2 {
            color: #003366;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
        }

        .button-container {
            margin-top: 20px;
        }

        .button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include 'header.php' ?>
<div class="principal">

    <div class="container-data">
        <h2>Ticket de Vuelo</h2>
        <p><strong>ID Ticket:</strong> <?php echo $ticket['ID_Ticket']; ?></p>
        <p><strong>Fecha de Emisión:</strong> <?php echo $ticket['Fecha_Emision']; ?></p>
        <p><strong>Total Pagado:</strong> $<?php echo number_format($ticket['Total'], 2); ?></p>
        <p><strong>Método de Pago:</strong> <?php echo $ticket['Metodo_Pago']; ?></p>

        <div class="button-container">
            <!-- Botón para generar PDF de factura -->
            <a href="generar_pdf.php?id_pago=<?php echo $idPago; ?>" class="button">Imprimir Factura PDF</a>
            <!-- Botón para imprimir el boleto de vuelo -->
            <a href="boleto_pdf.php?id_reserva=<?php echo $idReserva; ?>" class="button">Imprimir Pase de Abordaje</a>
            <!-- Botón para volver al inicio -->
            <a href="http://localhost/si/pry/Aerolinea/" class="button">Volver a la Página Principal</a>
        </div>
    </div>

</div>
</body>
</html>
