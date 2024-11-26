<?php

include('conexion_bd.php');

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $modelo = $_POST['modelo'];
    $matricula = $_POST['matricula'];

    // Definir valores fijos
    $capacidad = 192;
    $asientos_disponibles = 192;
    $estado_avion = 1;  // El estado por defecto será 1 (activo)

    try {
        // Preparar la consulta SQL utilizando PDO
        $sql = "INSERT INTO avion (Modelo, Estado_Avion, Asientos_Disponibles, Capacidad, Matricula) 
                VALUES (:modelo, :estado_avion, :asientos_disponibles, :capacidad, :matricula)";

        $stmt = $conn->prepare($sql);
        
        // Bind de los parámetros
        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':estado_avion', $estado_avion);
        $stmt->bindParam(':asientos_disponibles', $asientos_disponibles);
        $stmt->bindParam(':capacidad', $capacidad);
        $stmt->bindParam(':matricula', $matricula);

        // Ejecutar la consulta
        $stmt->execute();

        // Mensaje de éxito
        $mensaje = "Avión agregado exitosamente.";
    } catch (PDOException $e) {
        // Mensaje de error
        $mensaje = "Error al agregar avión: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Avión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('wallpaper_avion.jpg'); 
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

        input[type="text"], select {
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

        p {
            text-align: center;
            color: #e74c3c; 
        }

        .mensaje {
            text-align: center;
            font-size: 18px;
            color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Nuevo avión</h2>
        
        <?php if (isset($mensaje)): ?>
            <div class="mensaje">
                <p><?php echo $mensaje; ?></p>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "http://localhost/si/pry/Aerolinea/admin.html";
                }, 1500); // Redirige después de 1 segundo
            </script>
        <?php endif; ?>
        
        <form action="add_avion.php" method="POST">
            <label for="modelo">Modelo:</label><br>
            <select id="modelo" name="modelo" required>
                <option value="Airbus A320">Airbus A320</option>
                <option value="Boeing 757">Boeing 757</option>
                <option value="Boeing 737">Boeing 737</option>
            </select><br><br>

            <label for="matricula">Matrícula:</label><br>
            <input type="text" id="matricula" name="matricula" required><br><br>

            <button type="submit">Agregar</button>
        </form>
    </div>
</body>
</html>
