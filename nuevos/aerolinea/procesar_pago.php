<?php
session_start();
include 'conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idReserva = $_POST['id_reserva'];
    $monto = $_POST['monto'];
    $metodoPago = $_POST['metodo_pago'];

    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Registrar el pago
        $queryPago = "INSERT INTO pago (Fecha_Pago, Monto, Metodo_Pago, ID_Reserva) VALUES (NOW(), ?, ?, ?)";
        $stmtPago = $conn->prepare($queryPago);

        // Asignar los parámetros individualmente con el tipo correcto
        $stmtPago->bindParam(1, $monto, PDO::PARAM_STR);
        $stmtPago->bindParam(2, $metodoPago, PDO::PARAM_STR);
        $stmtPago->bindParam(3, $idReserva, PDO::PARAM_INT);

        if ($stmtPago->execute()) {
            // Obtener el último ID insertado en la tabla de pagos
            $idPago = $conn->lastInsertId();

            // Generar el ticket
            $queryTicket = "INSERT INTO ticket (Fecha_Emision, Total, ID_Pago) VALUES (NOW(), ?, ?)";
            $stmtTicket = $conn->prepare($queryTicket);

            // Asignar los parámetros para el ticket
            $stmtTicket->bindParam(1, $monto, PDO::PARAM_STR);
            $stmtTicket->bindParam(2, $idPago, PDO::PARAM_INT);

            if ($stmtTicket->execute()) {
                // Confirmar la transacción
                $conn->commit();
                echo "<script>alert('Pago realizado exitosamente. Su ticket ha sido emitido.'); window.location.href = 'ver_ticket.php?id_pago=$idPago';</script>";
            } else {
                // Revertir la transacción en caso de error
                $conn->rollBack();
                echo "Error al generar el ticket.";
            }
        } else {
            // Revertir la transacción en caso de error
            $conn->rollBack();
            echo "Error al registrar el pago.";
        }

    } catch (Exception $e) {
        // Revertir la transacción en caso de excepción
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
