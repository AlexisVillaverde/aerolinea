<?php
require_once 'conexion_bd.php';

$mensaje = '';  // Variable para almacenar el mensaje

// Manejo del formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_ciudad = $_POST['nombre_ciudad'] ?? '';
    $pais = $_POST['pais'] ?? '';
    $nombre_aeropuerto = $_POST['nombre_aeropuerto'] ?? '';
    $codigo_iata = $_POST['codigo_iata'] ?? '';

    try {
        $conn->beginTransaction();

        // Validar y agregar la ciudad
        if (!empty($nombre_ciudad) && !empty($pais)) {
            $sql_ciudad = "INSERT INTO Ciudad (Nombre, Pais) VALUES (:nombre_ciudad, :pais)";
            $stmt_ciudad = $conn->prepare($sql_ciudad);
            $stmt_ciudad->bindParam(':nombre_ciudad', $nombre_ciudad);
            $stmt_ciudad->bindParam(':pais', $pais);
            $stmt_ciudad->execute();

            // Obtener el ID de la ciudad recién agregada
            $id_ciudad = $conn->lastInsertId();

            // Validar y agregar el aeropuerto relacionado
            if (!empty($nombre_aeropuerto) && !empty($codigo_iata)) {
                $sql_aeropuerto = "INSERT INTO Aeropuerto (Nombre, Codigo_IATA, ID_Ciudad) 
                                   VALUES (:nombre_aeropuerto, :codigo_iata, :id_ciudad)";
                $stmt_aeropuerto = $conn->prepare($sql_aeropuerto);
                $stmt_aeropuerto->bindParam(':nombre_aeropuerto', $nombre_aeropuerto);
                $stmt_aeropuerto->bindParam(':codigo_iata', $codigo_iata);
                $stmt_aeropuerto->bindParam(':id_ciudad', $id_ciudad);
                $stmt_aeropuerto->execute();
            }

            $conn->commit();

            // Mostrar mensaje de éxito
            $mensaje = 'Ciudad y Aeropuerto agregados correctamente. Redirigiendo...';
        } else {
            $mensaje = 'El nombre de la ciudad y el país son obligatorios.';
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $mensaje = 'Error al agregar los datos: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Ciudad y Aeropuerto</title>
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
            width: 30%;
            margin: 50px auto;
            background-color: #2c3e50; 
            color: #ecf0f1; 
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        h1, h2 {
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
            background-color: #27ae60; 
            color: #ecf0f1; 
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #2ecc71; 
        }

        .mensaje {
            text-align: center;
            color: #e74c3c; 
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Nueva Ciudad y Aeropuerto</h1>
        <form method="post" action="">
            <h2>Datos de la Ciudad</h2>
            <label for="nombre_ciudad">Nombre de la ciudad:</label>
            <input type="text" id="nombre_ciudad" name="nombre_ciudad" required>

            <label for="pais">País:</label>
            <input type="text" id="pais" name="pais" required>

            <h2>Datos del Aeropuerto</h2>
            <label for="nombre_aeropuerto">Nombre del aeropuerto:</label>
            <input type="text" id="nombre_aeropuerto" name="nombre_aeropuerto" required>

            <label for="codigo_iata">Código IATA:</label>
            <input type="text" id="codigo_iata" name="codigo_iata" required>

            <button type="submit">Agregar</button>

            <?php if ($mensaje): ?>
                <div class="mensaje"><?php echo $mensaje; ?></div>
                <script>
                    // Redirigir después de 1.5 segundos si se mostró el mensaje de éxito
                    setTimeout(function() {
                        window.location.href = 'http://localhost/si/pry/Aerolinea/admin.html';
                    }, 1500); // Espera de 1.5 segundos antes de redirigir
                </script>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
