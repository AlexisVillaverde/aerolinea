<?php
session_start();
include 'conexion_bd.php';

if (!isset($_GET['id_reserva'])) {
    die("ID de reserva no especificado.");
}

$idReserva = $_GET['id_reserva'];

// Obtener detalles de la reserva
$queryReserva = "
    SELECT r.ID_Reserva, p.Nombre AS NombrePasajero,  a.Numero_asiento, 
           v.Fecha_Salida, v.Hora_Salida, v.Fecha_Llegada, v.Hora_Llegada, v.Tipo_Vuelo, 
           ao.Nombre AS Origen, ad.Nombre AS Destino, v.Costo
    FROM reserva r 
    JOIN vuelo v ON r.ID_Vuelo = v.ID_Vuelo
    JOIN pasajero p ON r.ID_Pasajero = p.ID_Pasajero
    JOIN aeropuerto ao ON v.ID_Origen = ao.ID_Aeropuerto
    JOIN aeropuerto ad ON v.ID_Destino = ad.ID_Aeropuerto
    JOIN asiento a ON r.ID_Asiento = a.ID_Asiento 
    WHERE r.ID_Reserva = ?";

$stmt = $conn->prepare($queryReserva);
$stmt->bindValue(1, $idReserva, PDO::PARAM_INT); // Parámetro 1 para ID_Reserva
$stmt->execute();
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    die("Reserva no encontrada.");
}

// Obtener el costo del vuelo
$montoTotal = $reserva['Costo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago</title>
    <link rel="stylesheet" href="principal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

        body{
          height: 100%;
          width: 100%;
            margin: 0;
            padding: 0;
          background-color: lightskyblue;
        }

        .container-data{
            margin: 0 auto;
            margin-top: 150px;
            width: 50%;
            padding: 10px;
            border: 3px solid #007bff;
            border-radius: 10px;
            background-color: lightskyblue;
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body>
<?php include 'header.php'?>
<div class="principal">
    <div class="container-data">


    <h2>Pagar Reserva</h2>
    <p><strong>Pasajero:</strong> <?php echo htmlspecialchars($reserva['NombrePasajero']); ?></p>
    <p><strong>Vuelo:</strong> <?php echo htmlspecialchars($reserva['Origen']); ?> → <?php echo htmlspecialchars($reserva['Destino']); ?></p>
    <p><strong>Fecha de Salida:</strong> <?php echo htmlspecialchars($reserva['Fecha_Salida'] . ' ' . $reserva['Hora_Salida']); ?></p>
    <p><strong>Fecha de Llegada:</strong> <?php echo htmlspecialchars($reserva['Fecha_Llegada'] . ' ' . $reserva['Hora_Llegada']); ?></p>
    <p><strong>Tipo de Vuelo:</strong> <?php echo htmlspecialchars($reserva['Tipo_Vuelo']); ?></p>
    <p><strong>Asiento:</strong> <?php echo htmlspecialchars($reserva['Numero_asiento']); ?></p>
    <p><strong>Total a pagar:</strong> $<?php echo number_format($montoTotal, 2); ?></p>

    <form action="procesar_pago.php" method="POST">
        <input type="hidden" name="id_reserva" value="<?php echo $idReserva; ?>">
        <input type="hidden" name="monto" value="<?php echo $montoTotal; ?>">

        <label for="metodo_pago">Método de Pago:</label>
        <select name="metodo_pago" id="metodo_pago" required>
            <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
            <option value="PayPal">PayPal</option>
            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
        </select>

        <button type="submit">Confirmar Pago</button>
    </form>
    </div>
</div>

</body>
</html>
