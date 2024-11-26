<?php
// Conexión a la base de datos
include('conexion_bd.php');

// Variables para controlar la lógica
$show_return_form = false;
$flight_data = []; // Datos para el vuelo de vuelta

// Procesar el formulario de ida
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_vuelo'])) {
    // Recoger los datos del formulario de ida
    $fecha_salida = $_POST['fecha_salida'];
    $hora_salida = $_POST['hora_salida'];
    $fecha_llegada = $_POST['fecha_llegada'];
    $hora_llegada = $_POST['hora_llegada'];
    $id_origen = $_POST['id_origen'];
    $id_destino = $_POST['id_destino'];
    $costo = $_POST['costo'];
    $tipo_vuelo = $_POST['tipo_vuelo'];
    $opcion_vuelo = $_POST['opcion_vuelo'];

    // Verificar si los aeropuertos de origen y destino existen
    $origen_query = $conn->prepare("SELECT * FROM aeropuerto WHERE Nombre = ?");
    $origen_query->execute([$id_origen]);
    $origen_result = $origen_query->fetch(PDO::FETCH_ASSOC);

    $destino_query = $conn->prepare("SELECT * FROM aeropuerto WHERE Nombre = ?");
    $destino_query->execute([$id_destino]);
    $destino_result = $destino_query->fetch(PDO::FETCH_ASSOC);

    if ($origen_result && $destino_result) {
        $id_origen_db = $origen_result['ID_Aeropuerto'];
        $id_destino_db = $destino_result['ID_Aeropuerto'];

        // Verificar los aviones activos
        $aviones_result = $conn->query("SELECT * FROM avion WHERE Estado_Avion = 1");
        $avion = $aviones_result->fetch(PDO::FETCH_ASSOC); // Primer avión activo disponible

        // Insertar el vuelo de ida
        $stmt = $conn->prepare("INSERT INTO vuelo (Estado, Fecha_Salida, Hora_Salida, Fecha_Llegada, Hora_Llegada, Tipo_Vuelo, Opcion_Vuelo, ID_Origen, ID_Destino, ID_Avion, Costo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $estado = 1; // Suponemos que el vuelo está activo
        $stmt->execute([$estado, $fecha_salida, $hora_salida, $fecha_llegada, $hora_llegada, $tipo_vuelo, $opcion_vuelo, $id_origen_db, $id_destino_db, $avion['ID_Avion'], $costo]);

        if ($stmt->rowCount() > 0) {
            if ($opcion_vuelo === "Redondo") {
                // Preparar datos para el vuelo de vuelta
                $flight_data = [
                    'id_origen' => $id_destino_db,
                    'id_destino' => $id_origen_db,
                    'fecha_salida' => $fecha_llegada,
                    'hora_salida' => $hora_llegada,
                    'fecha_llegada' => $fecha_salida,
                    'hora_llegada' => $hora_salida,
                    'avion' => $avion['ID_Avion'],
                    'costo' => $costo
                ];
                $show_return_form = true;
            } else {
                // Vuelo sencillo, redirigir
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'http://localhost/si/pry/Aerolinea/admin.html';
                        }, 1500);
                      </script>";
            }
        }
    } else {
        echo "El aeropuerto de origen o destino no existe.";
    }
}

// Procesar el formulario de vuelta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_vuelo_vuelta'])) {
    // Recoger los datos del formulario de vuelta
    $id_origen = $_POST['id_origen'];
    $id_destino = $_POST['id_destino'];
    $fecha_salida = $_POST['fecha_salida'];
    $hora_salida = $_POST['hora_salida'];
    $fecha_llegada = $_POST['fecha_llegada'];
    $hora_llegada = $_POST['hora_llegada'];
    $costo = $_POST['costo'];
    $id_avion = $_POST['avion'];

    // Insertar el vuelo de vuelta
    $stmt = $conn->prepare("INSERT INTO vuelo (Estado, Fecha_Salida, Hora_Salida, Fecha_Llegada, Hora_Llegada, Tipo_Vuelo, Opcion_Vuelo, ID_Origen, ID_Destino, ID_Avion, Costo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $estado = 1;
    $tipo_vuelo = "Nacional"; // Ajustar según requisitos
    $opcion_vuelo = "Redondo";

    $stmt->execute([$estado, $fecha_salida, $hora_salida, $fecha_llegada, $hora_llegada, $tipo_vuelo, $opcion_vuelo, $id_origen, $id_destino, $id_avion, $costo]);

    if ($stmt->rowCount() > 0) {
        // Vuelo de vuelta registrado correctamente, redirigir
        echo "<script>
                
                setTimeout(function() {
                    window.location.href = 'http://localhost/si/pry/Aerolinea/admin.html';
                }, 100);
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vuelo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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

<!-- Mostrar formulario según el contexto -->
<?php if (!$show_return_form): ?>
    <!-- Formulario de vuelo de ida -->
    <div class="form-container">
        <h2>Registrar Vuelo de Ida</h2>
        <form method="POST" action="add_vuelo.php">
            <label for="avion">Avión: </label>
            <input type="number" name="avion" value="<?= $avion['ID_Avion']; ?>" required><br>

            <label for="id_origen">Origen: </label>
            <input type="text" id="origen" name="id_origen" required><br>
            <ul id="origin-suggestions" class="list-group suggestions-dropdown"></ul>

            <label for="id_destino">Destino: </label>
            <input type="text" name="id_destino" required><br>

            <label for="fecha_salida">Fecha de Salida: </label>
            <input type="date" name="fecha_salida" required><br>

            <label for="hora_salida">Hora de Salida: </label>
            <input type="time" name="hora_salida" required><br>

            <label for="fecha_llegada">Fecha de Llegada: </label>
            <input type="date" name="fecha_llegada" required><br>

            <label for="hora_llegada">Hora de Llegada: </label>
            <input type="time" name="hora_llegada" required><br>

            <label for="tipo_vuelo">Tipo de Vuelo: </label>
            <select name="tipo_vuelo" required>
                <option value="Nacional">Nacional</option>
                <option value="Internacional">Internacional</option>
            </select><br>

            <label for="opcion_vuelo">Opción de Vuelo: </label>
            <select name="opcion_vuelo" required>
                <option value="Sencillo">Sencillo</option>
                <option value="Redondo">Redondo</option>
            </select><br>

            <label for="costo">Costo: </label>
            <input type="number" name="costo" required><br>

            <button type="submit" name="submit_vuelo">Registrar Vuelo de Ida</button>
        </form>
    </div>
<?php else: ?>
    <!-- Formulario de vuelo de vuelta -->
    <div class="form-container">
        <h2>Registrar Vuelo de Vuelta</h2>
        <form method="POST" action="add_vuelo.php">
            <label for="avion">Avión: </label>
            <input type="number" name="avion" value="<?= $flight_data['avion']; ?>" required><br>

            <label for="id_origen">Origen: </label>
            <input type="text" name="id_origen" value="<?= $flight_data['id_origen']; ?>" required><br>


            <label for="id_destino">Destino: </label>
            <input type="text" name="id_destino" value="<?= $flight_data['id_destino']; ?>" required><br>

            <label for="fecha_salida">Fecha de Salida: </label>
            <input type="date" name="fecha_salida" value="<?= $flight_data['fecha_salida']; ?>" required><br>

            <label for="hora_salida">Hora de Salida: </label>
            <input type="time" name="hora_salida" value="<?= $flight_data['hora_salida']; ?>" required><br>

            <label for="fecha_llegada">Fecha de Llegada: </label>
            <input type="date" name="fecha_llegada" value="<?= $flight_data['fecha_llegada']; ?>" required><br>

            <label for="hora_llegada">Hora de Llegada: </label>
            <input type="time" name="hora_llegada" value="<?= $flight_data['hora_llegada']; ?>" required><br>

            <label for="costo">Costo: </label>
            <input type="number" name="costo" value="<?= $flight_data['costo']; ?>" required><br>

            <button type="submit" name="submit_vuelo_vuelta">Registrar Vuelo de Vuelta</button>
        </form>
    </div>
<?php endif; ?>

<script>
    document.getElementById('origen').addEventListener('click', function() {

    fetch('http://localhost/si/aerolinea/getCities.php')
    .then(response => response.json())
        .then(data => {
            const suggestionsList = document.getElementById('origin-suggestions');
            suggestionsList.innerHTML = ''; // Limpiar sugerencias anteriores

            // Llenar el dropdown con las ciudades obtenidas
            data.forEach(city => {
                const li = document.createElement('li');
                li.textContent = city.Nombre; // Agregar el nombre de la ciudad
                li.classList.add('list-group-item'); // Mantener clase de Bootstrap para estilos
                li.addEventListener('click', () => {
                    document.getElementById('origin').value = city.Nombre; // Establecer el valor en el input
                    suggestionsList.innerHTML = ''; // Limpiar sugerencias
                });
                suggestionsList.appendChild(li); // Agregar el elemento a la lista
            });
        })
        .catch(error => {
            console.error('Error al obtener las ciudades:', error);
        });
    });

</script>

</body>
</html>
