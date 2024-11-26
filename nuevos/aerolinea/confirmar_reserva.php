<?php
session_start();
include 'conexion_bd.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['ID_Pasajero'])) {
    // Guardar datos del formulario en la sesión
    $_SESSION['reserva_data'] = $_POST;
    $_SESSION['redirect_to'] = 'confirmar_reserva.php'; // Guardar la página actual
    echo "
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: '¡No estás logueado!',
                text: 'Por favor, inicia sesión para continuar con tu reserva. ¿No tienes cuenta? Regístrate ahora.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iniciar sesión',
                cancelButtonText: 'Registrarse',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'inicio_sesion.php?redirect=confirmar_reserva.php'; // Redirigir a la página de login
                } else {
                    window.location.href = 'registro_usuario.php?redirect=confirmar_reserva.php'; // Redirigir a la página de registro
                }
            });
        });
    </script>";
    exit;
}


// Recuperar datos del formulario desde la sesión si existen
$reservaData = $_SESSION['reserva_data'] ?? [];
$pasajeroId = $_SESSION['ID_Pasajero']; // ID del pasajero logueado
$index = $reservaData['index'] ?? $_POST['index'];
$clase = $reservaData['clase'] ?? $_POST['clase'];
$asiento = $reservaData['asiento'] ?? $_POST['asiento'];
$origen = $reservaData['origen'] ?? $_POST['origen'];
$destino = $reservaData['destino'] ?? $_POST['destino'];
$fecha = $reservaData['fecha'] ?? $_POST['fecha'];

// Limpiar los datos de reserva de la sesión después de usarlos
unset($_SESSION['reserva_data']);

// 1. Consultar si el asiento existe y obtener su ID_Asiento
$query = "
    SELECT a.ID_Asiento 
    FROM asiento a 
    INNER JOIN avion v ON a.ID_Avion = v.ID_Avion
    INNER JOIN vuelo vu ON v.ID_Avion = vu.ID_Avion
    WHERE a.Numero_asiento = ? AND vu.ID_Vuelo = ?";
$stmt = $conn->prepare($query);

// Usar el número de asiento y el ID de vuelo para la consulta
$stmt->bindValue(1, $asiento, PDO::PARAM_STR);  // Número de asiento
$stmt->bindValue(2, $index, PDO::PARAM_INT);   // ID del vuelo

$stmt->execute();

$asientoData = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reserva</title>
    <!-- Enlace a Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="principal.css">
    <!-- Enlace sweeralert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f0f4f7;
            font-family: 'Arial', sans-serif;
        }
        .container-data {
            margin: 0 auto;
            max-width: 800px;
            margin-top: 50px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .headerr {
            color: #007bff;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 20px;
        }
        .result {
            background-color: #e9f7fd;
            border: 1px solid #007bff;
            padding: 20px;
            border-radius: 5px;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
        }
        .details p {
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
<?php include 'header.php'?>

<div class="principal">
    <div class="container-data">
        <div class="headerr">
            <?php if ($asientoData): ?>
                <h1>Reserva Confirmada</h1>
                <div class="result">
                    <h4>Detalles de tu reserva</h4>
                    <div class="details">
                        <p><strong>Vuelo:</strong> <?php echo "$origen a $destino"; ?></p>
                        <p><strong>Fecha de vuelo:</strong> <?php echo $fecha; ?></p>
                        <p><strong>Clase:</strong> <?php echo $clase; ?></p>
                        <p><strong>Asiento:</strong> <?php echo $asiento; ?></p>
                    </div>
                </div>
                <!-- Botón para pagar -->
                <div class="text-center mt-4">
                    <button id="btnPagar" class="btn btn-primary btn-lg">Pagar</button>
                </div>
            <?php else: ?>
                <div class="error">
                    <h4>Error</h4>
                    <p>El asiento <?php echo $asiento; ?> no está disponible o no existe.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($asientoData): ?>
            <?php
            // El asiento existe, podemos obtener su ID_Asiento
            $idAsiento = $asientoData['ID_Asiento'];

            // 2. Insertar la reserva con el ID_Asiento obtenido
            $insertQuery = "INSERT INTO reserva (ID_Pasajero, ID_Vuelo, ID_Asiento) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindValue(1, $pasajeroId, PDO::PARAM_INT);
            $insertStmt->bindValue(2, $index, PDO::PARAM_INT);
            $insertStmt->bindValue(3, $idAsiento, PDO::PARAM_INT);

            if ($insertStmt->execute()) {
                // Obtener el ID de la reserva recién creada
                $idReserva = $conn->lastInsertId();
            } else {
                echo "<p class='text-danger text-center'>Error al confirmar la reserva. Por favor, intenta de nuevo.</p>";
            }
            ?>
        <?php endif; ?>
    </div>
</div>


<!-- Script para manejar el botón de pago -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('btnPagar').addEventListener('click', function() {
        Swal.fire({
            title: '¿Estás listo para proceder al pago?',
            text: "Serás redirigido a la página de pago.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Pagar ahora',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'pago.php?id_reserva=<?php echo $idReserva; ?>';
            }
        });
    });
</script>


<!-- Enlace a los scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
