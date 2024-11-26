<?php
// Conexión a la base de datos
include 'conexion_bd.php'; 

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $edad = intval($_POST['edad']);
    $telefono = trim($_POST['telefono']);
    $correo_electronico = trim($_POST['correo_electronico']);
    $contrasenia = $_POST['contrasenia'];

    // Validar campos obligatorios
    if (empty($nombre) || empty($primer_apellido) || empty($edad) || empty($telefono) || empty($correo_electronico) || empty($contrasenia)) {
        $mensaje = "Todos los campos son obligatorios.";
        $mensaje_clase = "error";
    } elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo electrónico no es válido.";
        $mensaje_clase = "error";
    } elseif ($edad <= 0) {
        $mensaje = "La edad debe ser un número positivo.";
        $mensaje_clase = "error";
    } elseif (!preg_match('/^\d{10,12}$/', $telefono)) {
        $mensaje = "El teléfono debe contener entre 10 y 12 dígitos.";
        $mensaje_clase = "error";
    } else {
        // Cifrar la contraseña
        $hashed_password = password_hash($contrasenia, PASSWORD_BCRYPT);

        // Insertar el usuario administrador en la base de datos
        try {
            $sql = "INSERT INTO pasajero 
                    (Nombre, Primer_Apellido, Segundo_Apellido, Estado, Edad, Telefono, Correo_Electronico, contrasenia, perfil) 
                    VALUES (:nombre, :primer_apellido, :segundo_apellido, 1, :edad, :telefono, :correo_electronico, :contrasenia, 'admin')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':primer_apellido', $primer_apellido);
            $stmt->bindParam(':segundo_apellido', $segundo_apellido);
            $stmt->bindParam(':edad', $edad);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo_electronico', $correo_electronico);
            $stmt->bindParam(':contrasenia', $hashed_password);

            $stmt->execute();
            $mensaje = "Administrador registrado con éxito.";
            $mensaje_clase = "mensaje";
        } catch (PDOException $e) {
            $mensaje = "Error al registrar el administrador: " . htmlspecialchars($e->getMessage());
            $mensaje_clase = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('wallpaper-register.jpg'); 
            background-size: cover;
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

        h1 {
            text-align: center;
            color: #ecf0f1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #ecf0f1; 
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
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
            font-size: 18px;
            color: #27ae60;
        }

        .error {
            text-align: center;
            font-size: 18px;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registrar Administrador</h1>
        <?php if (isset($mensaje)): ?>
            <div class="<?php echo $mensaje_clase; ?>">
                <p><?php echo $mensaje; ?></p>
            </div>
            <?php if ($mensaje_clase === "mensaje"): ?>
                <script>
                    setTimeout(function() {
                        window.location.href = "http://localhost/si/pry/Aerolinea/admin.html";
                    }, 1500); // Redirige después de 1.5 segundos
                </script>
            <?php endif; ?>
        <?php endif; ?>
        <form action="" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="primer_apellido">Primer Apellido:</label>
            <input type="text" id="primer_apellido" name="primer_apellido" required>

            <label for="segundo_apellido">Segundo Apellido:</label>
            <input type="text" id="segundo_apellido" name="segundo_apellido">

            <label for="edad">Edad:</label>
            <input type="number" id="edad" name="edad" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="correo_electronico">Correo Electrónico:</label>
            <input type="email" id="correo_electronico" name="correo_electronico" required>

            <label for="contrasenia">Contraseña:</label>
            <input type="password" id="contrasenia" name="contrasenia" required>

            <button type="submit">Registrar Administrador</button>
        </form>
    </div>
</body>
</html>
