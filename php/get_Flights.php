<?php
// Habilitar CORS para permitir solicitudes desde localhost:3000 y 127.0.0.1:5500
$allowedOrigins = ['http://localhost:3000', 'http://127.0.0.1:5500'];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}

session_start();


// Verificar si hay resultados en la sesión
if (isset($_SESSION['resultados'])) {
    $flights = $_SESSION['resultados'];
    unset($_SESSION['resultados']); // Limpiar la sesión después de obtener los datos
} else {
    $flights = []; // No hay resultados
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Vuelos</title>
    <!-- Agregar CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos personalizados */
        .flight-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .flight-info {
            display: flex;
            justify-content: space-between;
            gap: 80px;
            align-items: center;
            font-size: 1.1em;
        }
        .flight-details {
            font-size: 0.9em;
            color: #555;
        }
        .btn-reservar {
            background-color: #004274;
            color: white;
            font-weight: bold;
        }
        .btn-reservar:hover {
            background-color: #00305b;
            color: white;
        }

        .horizontalLine{
            background: rgba(0,0,0);
            width: 100%;
            height: 2px;
            position: relative;
            margin: 4px 0;

        }

        .horizontalLine {
            position: relative;
            height: 1px;
            background: #000;
            margin: 10px 0;
        }

        /* Punto al principio de la línea */
        .horizontalLine:before {
            content: "";
            background: #000;
            display: block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            position: absolute;
            top: -3px; /* Ajustar para centrar */
            left: -5px; /* Alinear al borde izquierdo */
        }

        /* Punto al final de la línea */
        .horizontalLine:after {
            content: "";
            background: #000;
            display: block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            position: absolute;
            top: -3px; /* Ajustar para centrar */
            right: -5px; /* Alinear al borde derecho */
        }

        .flight-class{
            width: 70%;
            justify-content: space-between;
            margin: 0 auto;
        }

        /* Estilos generales para las tarjetas de clase de vuelo */
        .flight-class-item {
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            border: 2px solid;
            background-color: #f9f9f9;
            border-top: 10px solid;
            width: 30%;
        }

        /* Colores específicos según el tipo de clase */
        .flight-class-item.turistica {
            border-color: #4CAF50; /* Verde para Clase Turística */
        }

        .flight-class-item.primera {
            border-color: #2196F3; /* Azul para Primera Clase */
        }

        .flight-class-item.ejecutiva {
            border-color: #FF9800; /* Naranja para Clase Ejecutiva */
        }

    </style>
</head>
<body class="container py-5">
<h1 class="mb-4 text-center">Resultados de la Búsqueda de Vuelos</h1>

<?php if (!empty($flights)) : ?>
    <?php foreach ($flights as $index => $flight) : ?>
        <div class="flight-card">
            <div class="flight-info">
                <div>
                    <h5><strong><?= htmlspecialchars($flight['Hora_Salida']) ?></strong></h5>

                    <p class="flight-details">Fecha de salida: <?= htmlspecialchars($flight['Fecha_Salida']) ?> </p>
                    <p class="flight-details"><?= htmlspecialchars($flight['ciudad_origen']) ?> Aeropuerto: <?= htmlspecialchars($flight['aeropuerto_origen']) ?></p>
                </div>
                <div class="horizontalLine"></div>
                <div>
                    <h5><strong><?= htmlspecialchars($flight['Hora_Llegada']) ?></strong></h5>
                    <p class="flight-details">Fecha de llegada: <?= htmlspecialchars($flight['Fecha_Llegada']) ?> </p>
                    <p class="flight-details"><?= htmlspecialchars($flight['ciudad_destino']) ?> Aeropuerto: <?= htmlspecialchars($flight['aeropuerto_destino']) ?></p>
                </div>
                <div>
                    <h5><strong>Avión:</strong> </h5>
                    <p class="flight-details"><?= htmlspecialchars($flight['modelo_avion']) ?></p>
                    <button class="btn btn-reservar" onclick="reservar('<?= $index ?>','<?= htmlspecialchars($flight['ciudad_origen']) ?>', '<?= htmlspecialchars($flight['ciudad_destino']) ?>', '<?= htmlspecialchars($flight['Fecha_Salida']) ?>')">Reservar</button>
                </div>
                <div>
                    <h5><strong>Costo</strong></h5>
                    <p><?= htmlspecialchars($flight['Costo']) ?></p>
                </div>
            </div>

            <div class="flight-class"  id="flight-class-<?= $index ?>" style="display: none">
                <div class="flight-class-item turistica">
                    <h5>Clase turística</h5>
                    <p>1 equipaje de mano <strong>10 kg</strong></p>
                    <p>Selección de asiento estándar</p>
                    <button class="btn btn-clase" onclick="seleccionarClase('<?= $index ?>', 'turistica', '<?= htmlspecialchars($flight['costo_turistica']) ?>')">
                        Costo: <?= htmlspecialchars($flight['costo_turistica']) ?> MXN
                    </button>
                </div>
                <div class="flight-class-item primera">
                    <h5>Primera Clase</h5>
                    <p>1 equipaje de mano <strong>10 kg</strong></p>
                    <p>Asiento AM Plus incluido</p>
                    <p>Servicios prioritarios</p>
                    <p>Compartimiento superior exclusivo</p>
                    <button class="btn btn-clase" onclick="seleccionarClase('<?= $index ?>', 'primera', '<?= htmlspecialchars($flight['costo_primera']) ?>')">
                        Costo: <?= htmlspecialchars($flight['costo_primera']) ?> MXN
                    </button>
                </div>
                <div class="flight-class-item ejecutiva">
                    <h5>Clase Ejecutiva</h5>
                    <p>1 equipaje de mano <strong>10 kg</strong></p>
                    <p>2 equipajes documentados</p>
                    <p>Asiento Premier One</p>
                    <p>Servicio exclusivo de Wi-Fi</p>
                    <button class="btn btn-clase" onclick="seleccionarClase('<?= $index ?>', 'ejecutiva', '<?= htmlspecialchars($flight['costo_ejecutiva']) ?>')">
                        Costo: <?= htmlspecialchars($flight['costo_ejecutiva']) ?> MXN
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p class="alert alert-warning text-center">No se encontraron vuelos para los criterios de búsqueda especificados.</p>
<?php endif; ?>

<script>
    function reservar(index,origen, destino, fecha) {
        const classFlight=document.querySelector(`#flight-class-${index}`);
        //alert(`Reservando ${index} vuelo de ${origen} a ${destino} el ${fecha}`);
        // Si el contenedor está oculto, se muestra, de lo contrario se oculta
        if (classFlight.style.display === 'none' || classFlight.style.display === '') {
            classFlight.style.display = 'flex';
        } else {
            classFlight.style.display = 'none';
        }
    }


    function seleccionarClase(index, clase, costo) {
        alert(`Has seleccionado la clase ${clase} para el vuelo ${index}. Costo: ${costo} MXN`);
        // Aquí puedes realizar cualquier otra acción, como guardar la selección o actualizar la interfaz
    }
</script>

</body>
</html>

