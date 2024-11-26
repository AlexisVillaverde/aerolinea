<?php
require_once 'conexion_bd.php';

$mensaje = '';
$matricula = '';
$id_avion = '';
$estado_avion = '';

// Verificar si se recibió la matrícula
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['matricula'])) {
    $matricula = $_POST['matricula'] ?? '';

    if (!empty($matricula)) {
        $sql = "SELECT ID_Avion, Matricula, Estado_Avion FROM avion WHERE Matricula = :matricula";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':matricula', $matricula, PDO::PARAM_STR);
        $stmt->execute();
        $avion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($avion) {
            $id_avion = $avion['ID_Avion'];
            $matricula = $avion['Matricula'];
            $estado_avion = $avion['Estado_Avion'];
        } else {
            $mensaje = "No se encontró un avión con esa matrícula.";
        }
    }
}

// Si se recibe una solicitud de actualización de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_avion']) && isset($_POST['nuevo_estado'])) {
    $id_avion = $_POST['id_avion'];
    $nuevo_estado = $_POST['nuevo_estado'];
    $response = array('success' => false);

    try {
        // Actualizar el estado del avión en la base de datos
        $sql = "UPDATE avion SET Estado_Avion = :nuevo_estado WHERE ID_Avion = :id_avion";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
        $stmt->bindParam(':id_avion', $id_avion, PDO::PARAM_INT);
        $stmt->execute();

        // Devolver respuesta con el nuevo estado
        $response['success'] = true;
        $response['estado'] = $nuevo_estado;

        echo json_encode($response);
        exit; // Terminar la ejecución aquí para evitar la carga de la página adicionalmente
    } catch (PDOException $e) {
        $response['error'] = "Error al actualizar el estado: " . $e->getMessage();
        echo json_encode($response);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Avión</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('wallpaper_avion.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 35%;
            background-color: rgba(44, 62, 80, 0.8);
            color: #ecf0f1;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        h1 {
            color: #ecf0f1;
            font-size: 2em;
        }

        label {
            color: #ecf0f1;
            font-size: 1.1em;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #34495e;
            color: #ecf0f1;
        }

        button {
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
        }

        button.habilitar {
            background-color: #27ae60;
            color: #ecf0f1;
        }

        button.deshabilitar {
            background-color: #e74c3c;
            color: #ecf0f1;
        }

        button:hover {
            opacity: 0.8;
        }

        .mensaje {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modificar Estado del Avión</h1>

        <form method="post" action="" id="form-buscar">
            <label for="matricula">Matrícula del avión:</label>
            <input type="text" id="matricula" name="matricula" required value="<?php echo htmlspecialchars($matricula); ?>">
            <button type="submit">Buscar</button>
        </form>

        <?php if ($id_avion): ?>
            <p><strong>ID del avión:</strong> <?php echo $id_avion; ?></p>
            <p><strong>Matrícula del avión:</strong> <?php echo $matricula; ?></p>
            <p><strong>Estado del avión:</strong> <span id="estado-avion"><?php echo ($estado_avion == 1) ? 'Habilitado' : 'Deshabilitado'; ?></span></p>

            <button type="button" onclick="actualizarEstado(<?php echo $id_avion; ?>, 1)" class="habilitar">Habilitar</button>
            <button type="button" onclick="actualizarEstado(<?php echo $id_avion; ?>, 0)" class="deshabilitar">Deshabilitar</button>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>
    </div>

    <script>
        function actualizarEstado(idAvion, nuevoEstado) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Enviar a la misma página
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    var respuesta = JSON.parse(xhr.responseText);
                    if (respuesta.success) {
                        var estadoTexto = (respuesta.estado == 1) ? 'Habilitado' : 'Deshabilitado';
                        document.getElementById('estado-avion').textContent = estadoTexto;

                        // Redirigir a la página admin.html después de 1 segundo
                        setTimeout(function() {
                            window.location.href = 'http://localhost/si/pry/Aerolinea/admin.html';
                        }, 1000);
                    } else {
                        alert('Error: ' + respuesta.error);
                    }
                } else {
                    alert('Error al conectar con el servidor.');
                }
            };

            xhr.send('id_avion=' + idAvion + '&nuevo_estado=' + nuevoEstado); // Enviar los parámetros
        }
    </script>
</body>
</html>
