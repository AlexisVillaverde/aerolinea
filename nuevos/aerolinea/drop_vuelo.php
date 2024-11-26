<?php
// Conexión a la base de datos
include('conexion_bd.php');

// Inicializamos la variable de búsqueda
$searchTerm = '';

// Verificamos si se ha enviado un término de búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_term'])) {
    $searchTerm = $_POST['search_term'];
}

// Consultar vuelos, filtrados por ciudad de origen o ciudad de destino
$query = "SELECT v.ID_Vuelo, v.Fecha_Salida, v.Hora_Salida, 
                 c_origen.Nombre AS Ciudad_Origen, c_destino.Nombre AS Ciudad_Destino
          FROM vuelo v
          JOIN aeropuerto a_origen ON v.ID_Origen = a_origen.ID_Aeropuerto
          JOIN ciudad c_origen ON a_origen.ID_Ciudad = c_origen.ID_Ciudad
          JOIN aeropuerto a_destino ON v.ID_Destino = a_destino.ID_Aeropuerto
          JOIN ciudad c_destino ON a_destino.ID_Ciudad = c_destino.ID_Ciudad
          WHERE c_origen.Nombre LIKE :searchTerm 
             OR c_destino.Nombre LIKE :searchTerm";

try {
    // Preparar la consulta y ejecutarla
    $vuelos_query = $conn->prepare($query);
    $vuelos_query->execute(['searchTerm' => '%' . $searchTerm . '%']);
    $vuelos = $vuelos_query->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay vuelos, muestra un mensaje
    if (empty($vuelos)) {
        $message = "No se encontraron vuelos para la ciudad ingresada.";
    }
} catch (Exception $e) {
    $message = "Error en la consulta: " . $e->getMessage();
}

// Lógica para eliminar el vuelo seleccionado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_vuelo'])) {
    $vuelo_id = $_POST['vuelo_id'];

    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Primero eliminamos los tickets asociados al pago
        $delete_tickets_stmt = $conn->prepare("DELETE FROM ticket WHERE ID_Pago IN 
                                                (SELECT ID_Pago FROM pago WHERE ID_Reserva IN 
                                                 (SELECT ID_Reserva FROM reserva WHERE ID_Vuelo = ?))");
        $delete_tickets_stmt->execute([$vuelo_id]);

        // Eliminamos los pagos asociados a las reservas del vuelo
        $delete_pagos_stmt = $conn->prepare("DELETE p FROM pago p 
                                             JOIN reserva r ON p.ID_Reserva = r.ID_Reserva 
                                             WHERE r.ID_Vuelo = ?");
        $delete_pagos_stmt->execute([$vuelo_id]);

        // Ahora eliminamos las reservas asociadas al vuelo
        $delete_reservas_stmt = $conn->prepare("DELETE FROM reserva WHERE ID_Vuelo = ?");
        $delete_reservas_stmt->execute([$vuelo_id]);

        // Ahora eliminamos el vuelo
        $delete_vuelo_stmt = $conn->prepare("DELETE FROM vuelo WHERE ID_Vuelo = ?");
        $delete_vuelo_stmt->execute([$vuelo_id]);

        // Confirmamos la transacción
        $conn->commit();

        // Si se eliminó el vuelo y las reservas, mostramos mensaje y redirigimos
        if ($delete_vuelo_stmt->rowCount() > 0) {
            echo "<script>alert('Vuelo eliminado correctamente');</script>";
            echo "<script>window.location.href = 'http://localhost/si/pry/Aerolinea/admin.html';</script>"; // Redirigir a la nueva ruta
        } else {
            echo "<script>alert('Error al eliminar el vuelo');</script>";
        }

    } catch (Exception $e) {
        // Si ocurre un error, deshacemos la transacción
        $conn->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Vuelo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('wallpaper_flight.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-container {
            width: 30%;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: inline-block;
        }

        input[type="text"], input[type="date"], input[type="time"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        .hidden {
            display: none;
        }

        .suggestions-dropdown {
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
        }

        .suggestions-dropdown li {
            cursor: pointer;
        }

        .suggestions-dropdown li:hover {
            background-color: #f5b0b0;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="my-4">Eliminar Vuelo</h2>

    <!-- Formulario de búsqueda -->
    <div class="form-container">
        <form method="POST" action="drop_vuelo.php" class="mb-4">
            <div class="form-group">
                <label for="search_term">Buscar por ciudad de origen o ciudad de destino:</label>
                <input type="text" class="form-control" id="search_term" name="search_term" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Ingrese ciudad de origen o destino" required>
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <!-- Mensaje si no se encuentran vuelos -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <!-- Tabla de vuelos encontrados -->
        <?php if (!empty($vuelos)): ?>
            <form method="POST" action="drop_vuelo.php">
                <div class="form-group">
                    <label for="vuelo">Selecciona el vuelo a eliminar:</label>
                    <select class="form-control" id="vuelo" name="vuelo_id" required>
                        <option value="">Selecciona un vuelo</option>
                        <?php foreach ($vuelos as $vuelo): ?>
                            <option value="<?= $vuelo['ID_Vuelo']; ?>">
                                <?= "Vuelo desde " . $vuelo['Ciudad_Origen'] . " a " . $vuelo['Ciudad_Destino'] . 
                                    " - Fecha: " . $vuelo['Fecha_Salida'] . " " . $vuelo['Hora_Salida']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-danger" name="eliminar_vuelo">Eliminar Vuelo</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
