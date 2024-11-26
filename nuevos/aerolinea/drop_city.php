<?php
require_once 'conexion_bd.php';

$mensaje = ''; // Variable para almacenar el mensaje
$ciudad = null; // Variable para almacenar los detalles de la ciudad

// Manejo del formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_ciudad = $_POST['nombre_ciudad'] ?? '';

    // Validar que el nombre de la ciudad no esté vacío
    if (empty($nombre_ciudad)) {
        $mensaje = 'El nombre de la ciudad es obligatorio.';
    } else {
        try {
            // Verificar si la ciudad existe
            $sql_check = "SELECT * FROM Ciudad WHERE Nombre = :nombre_ciudad";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bindParam(':nombre_ciudad', $nombre_ciudad, PDO::PARAM_STR);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                // Obtener los detalles de la ciudad encontrada
                $ciudad = $stmt_check->fetch(PDO::FETCH_ASSOC);
                $id_ciudad = $ciudad['ID_Ciudad'];

                // Si se ha solicitado eliminar, proceder
                if (isset($_POST['eliminar']) && $_POST['eliminar'] == 'Sí') {
                    $conn->beginTransaction();

                    try {
                        // Eliminar los tickets relacionados con los pagos
                        $sql_ticket = "DELETE FROM Ticket WHERE ID_Pago IN (SELECT ID_Pago FROM Pago WHERE ID_Reserva IN (SELECT ID_Reserva FROM Reserva WHERE ID_Vuelo IN (SELECT ID_Vuelo FROM Vuelo WHERE ID_Origen IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad) OR ID_Destino IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad))))";
                        $stmt_ticket = $conn->prepare($sql_ticket);
                        $stmt_ticket->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_ticket->execute();

                        // Eliminar los pagos relacionados con las reservas
                        $sql_pago = "DELETE FROM Pago WHERE ID_Reserva IN (SELECT ID_Reserva FROM Reserva WHERE ID_Vuelo IN (SELECT ID_Vuelo FROM Vuelo WHERE ID_Origen IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad) OR ID_Destino IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad)))";
                        $stmt_pago = $conn->prepare($sql_pago);
                        $stmt_pago->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_pago->execute();

                        // Eliminar las reservas relacionadas con los vuelos de la ciudad
                        $sql_reserva = "DELETE FROM Reserva WHERE ID_Vuelo IN (SELECT ID_Vuelo FROM Vuelo WHERE ID_Origen IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad) OR ID_Destino IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad))";
                        $stmt_reserva = $conn->prepare($sql_reserva);
                        $stmt_reserva->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_reserva->execute();

                        // Eliminar los vuelos relacionados con los aeropuertos de la ciudad
                        $sql_vuelo_origen = "DELETE FROM Vuelo WHERE ID_Origen IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad)";
                        $stmt_vuelo_origen = $conn->prepare($sql_vuelo_origen);
                        $stmt_vuelo_origen->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_vuelo_origen->execute();

                        $sql_vuelo_destino = "DELETE FROM Vuelo WHERE ID_Destino IN (SELECT ID_Aeropuerto FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad)";
                        $stmt_vuelo_destino = $conn->prepare($sql_vuelo_destino);
                        $stmt_vuelo_destino->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_vuelo_destino->execute();

                        // Eliminar los aeropuertos asociados a la ciudad
                        $sql_aeropuerto = "DELETE FROM Aeropuerto WHERE ID_Ciudad = :id_ciudad";
                        $stmt_aeropuerto = $conn->prepare($sql_aeropuerto);
                        $stmt_aeropuerto->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_aeropuerto->execute();

                        // Eliminar la ciudad
                        $sql_ciudad = "DELETE FROM Ciudad WHERE ID_Ciudad = :id_ciudad";
                        $stmt_ciudad = $conn->prepare($sql_ciudad);
                        $stmt_ciudad->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_ciudad->execute();

                        $conn->commit();
                        $mensaje = 'Tickets, pagos, reservas, vuelos, aeropuertos y ciudad eliminados correctamente.';
                        $ciudad = null; // Restablecer la variable ciudad
                        // Redirigir a la página de administración después de la eliminación exitosa
                        header("Location: http://localhost/si/pry/Aerolinea/admin.html");
                        exit(); // Asegúrate de que el script se detenga después de la redirección
                    } catch (PDOException $e) {
                        $conn->rollBack();
                        $mensaje = 'Error al eliminar los datos: ' . $e->getMessage();
                    }
                }
            } else {
                $mensaje = 'La ciudad no existe.';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error al buscar la ciudad: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar y Eliminar Ciudad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('wallpaper_city.jpg');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }

        .container {
            width: 40%;
            margin: 50px auto;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #ecf0f1;
        }

        label {
            color: #ecf0f1;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #34495e;
            color: #ecf0f1;
        }

        button {
            background-color: #2ecc71; /* Color verde */
            color: #ecf0f1;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #27ae60; /* Un verde más oscuro al pasar el cursor */
        }

        .mensaje {
            text-align: center;
            color: #e74c3c;
            font-weight: bold;
        }

        .ciudad-info {
            margin-top: 20px;
            background-color: #34495e;
            padding: 10px;
            border-radius: 5px;
        }

        .ciudad-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Buscar y Eliminar Ciudad</h1>
        <form method="post" action="">
            <label for="nombre_ciudad">Nombre de la ciudad:</label>
            <input type="text" id="nombre_ciudad" name="nombre_ciudad" value="<?php echo htmlspecialchars($nombre_ciudad ?? ''); ?>" required>

            <button type="submit">Buscar</button>

            <?php if ($ciudad): ?>
                <div class="ciudad-info">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($ciudad['ID_Ciudad']); ?></p>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($ciudad['Nombre']); ?></p>
                    <p><strong>País:</strong> <?php echo htmlspecialchars($ciudad['Pais']); ?></p>
                    <form method="post" action="">
                        <input type="hidden" name="nombre_ciudad" value="<?php echo htmlspecialchars($ciudad['Nombre']); ?>">
                        <button type="submit" name="eliminar" value="Sí" onclick="return confirm('¿Estás seguro de eliminar esta ciudad?')">Eliminar Ciudad</button>
                    </form>
                </div>
            <?php endif; ?>

            <p class="mensaje"><?php echo $mensaje; ?></p>
        </form>
    </div>
</body>
</html>
