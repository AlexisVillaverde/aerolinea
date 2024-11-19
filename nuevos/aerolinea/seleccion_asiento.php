<?php
session_start();
include 'conexion_bd.php'; // Conexión a la base de datos

// Obtener los parámetros de la URL
$id_vuelo = $_GET['index'] ?? null;
$clase = $_GET['clase'];
$fecha = $_GET['fecha'];
$origen = $_GET['origen'];
$destino = $_GET['destino'];

// Consulta para obtener los asientos ocupados para el vuelo seleccionado
try {
    $stmt = $conn->prepare("
        SELECT a.Numero_asiento
        FROM asiento a
        JOIN avion av ON a.ID_Avion = av.ID_Avion
        JOIN vuelo v ON av.ID_Avion = v.ID_Avion
        WHERE v.ID_Vuelo = :id_vuelo AND a.Estado = 0 AND a.Clase = :clase
    ");
    $stmt->bindParam(':id_vuelo', $id_vuelo, PDO::PARAM_INT);
    $stmt->bindParam(':clase', $clase, PDO::PARAM_STR);
    $stmt->execute();

    // Verificar resultados
    $occupiedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($occupiedSeats)) {
        echo "No hay asientos ocupados para el vuelo $id_vuelo.";
    } else {
    }

} catch (PDOException $e) {
    die("Error al consultar asientos ocupados: " . $e->getMessage());
}
?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Selección de Asientos </title>
        <style>
            /* Fondo de pantalla */
            body {
                background: linear-gradient(to bottom, #2c3e50, #4ca1af);
                font-family: Arial, sans-serif;
                display: flex;
                color: white !important;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
            }

            .content{
                display: flex;
                gap: 10px;
            }

            .data{
                padding: 10px;
            }

            /* Título centrado */
            h1 {
                font-size: 2rem;
                text-align: center;
                margin: 20px 0;
                color: white !important;
                text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
            }

            .seatss{
                height: 600px;
                overflow-y: scroll;
                scroll-behavior: smooth;
            }

            /* Contenedor principal */
            #seat-container {
                display: grid;
                grid-template-columns: repeat(6, 50px);
                gap: 5px;
                padding: 150px;
                background-image: url("airbus.svg");
                background-position: center;
                background-repeat: no-repeat;
            }

            /* Estilo para cada asiento */
            .seat {
                width: 50px;
                height: 50px;
                background-color: #90EE90;
                border: 1px solid #333;
                cursor: pointer;
                text-align: center;
                line-height: 50px;
                font-weight: bold;
                color: black;
                font-size: 12px;
                border-radius: 5px;
                transition: transform 0.2s;
            }

            /* Efecto hover para el asiento */
            .seat:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            /* Asiento ocupado */
            .occupied {
                background-color: #D3D3D3;
                cursor: not-allowed;
            }

            /* Asiento seleccionado */
            .selected {
                background-color: #FFA500;
                box-shadow: 0 4px 8px rgba(255, 165, 0, 0.4);
            }

            /* Leyenda de colores */
            #legend {
                gap: 20px;
                width: 70%;
                margin-top: 20px;
                padding: 10px;
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            }
            .legend-item {
                display: flex;
                align-items: center;
            }
            .legend-color {
                width: 20px;
                height: 20px;
                margin-right: 10px;
                border: 1px solid #333;
                border-radius: 3px;
            }
            .available { background-color: #90EE90; }
            .occupied { background-color: #D3D3D3; }
            .selected { background-color: #FFA500; }

            /* Estilo del botón Seleccionar */
            #select-button {
                margin-top: 20px;
                padding: 10px 20px;
                font-size: 16px;
                background-color: #FFA500;
                border: none;
                color: #fff;
                cursor: pointer;
                border-radius: 5px;
                display: none; /* Oculto inicialmente */
            }
            #select-button:disabled {
                background-color: #D3D3D3;
                cursor: not-allowed;
            }

            .disabled {
                background-color: #555; /* Un gris más oscuro */
                cursor: not-allowed; /* Indicar que no es seleccionable */
                opacity: 0.5; /* Reducir opacidad para diferenciarlos */
            }
        </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="container py-5">

    <?php include 'header.php' ?>

<div class="principal">
    <div class="content">

        <div class="data">

            <h1>Seleccionar Asiento <br> Vuelo <?= htmlspecialchars($id_vuelo) ?></h1>
            <p><strong>Clase Elegida:</strong> <?= htmlspecialchars($clase) ?></p>
            <p><strong>Origen:</strong> <?= htmlspecialchars($origen) ?></p>
            <p><strong>Destino:</strong> <?= htmlspecialchars($destino) ?></p>
            <p><strong>Fecha de Salida:</strong> <?= htmlspecialchars($fecha) ?></p>
            <!-- Botón para confirmar selección -->
            <button id="select-button" disabled>Seleccionar Asiento</button>
            <!-- Leyenda de colores -->
            <div id="legend">
                <div class="legend-item">
                    <div class="legend-color available"></div> <span>Asiento Disponible</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color occupied"></div> <span>Asiento Ocupado</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color selected"></div> <span>Asiento Seleccionado</span>
                </div>
            </div>
            <br>
            <form action="confirmar_reserva.php" method="POST">
                <input type="hidden" name="index" value="<?= $id_vuelo ?>">
                <input type="hidden" name="clase" value="<?= $clase ?>">
                <input type="hidden" name="fecha" value="<?= $fecha ?>">
                <input type="hidden" name="origen" value="<?= $origen ?>">
                <input type="hidden" name="destino" value="<?= $destino ?>">
                <input type="hidden" name="asiento" required>
                <button type="submit" class="btn btn-primary">Confirmar Reserva</button>
            </form>
        </div>


        <div class="seatss">
            <div id="seat-container">
                <!-- Asientos se generarán aquí -->
            </div>
        </div>
    </div>

</div>





    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const seatContainer = document.getElementById('seat-container');
            const selectButton = document.getElementById('select-button');
            let selectedSeat = null;

            // Asientos ocupados desde PHP
            const occupiedSeats = <?= json_encode($occupiedSeats); ?>;
            console.log(occupiedSeats);

            // Configuración de las filas y columnas según la clase
            const seatConfig = {
                "Primera Clase": { rows: [1, 2, 3, 4], columns: ["A", "B", "C", "D","E","F"] },
                "Clase Ejecutiva": { rows: [ 5, 6,7,8], columns: ["A", "B", "C", "D", "E","F"] },
                "Clase Turísta": { rows: Array.from({ length: 32 - 8 }, (_, i) => i + 9), columns: ["A", "B", "C", "D", "E", "F"] }
            };

            // Clase seleccionada (proporcionada por PHP)
            const selectedClass = <?= json_encode(urldecode($clase)); ?>;
            console.log(selectedClass);

            // Obtener configuración de asientos para la clase seleccionada
            const classSeats = seatConfig[selectedClass];
            if (!classSeats) {
                console.error("Clase de asiento no válida.");
                return;
            }

            const selectedRows = classSeats.rows;
            const selectedColumns = classSeats.columns;

            // Generación de todos los asientos (filas 1 a 32 y columnas A a F)
            const allRows = Array.from({ length: 32 }, (_, i) => i + 1);
            const allColumns = ["A", "B", "C", "D", "E", "F"];

            allRows.forEach(row => {
                allColumns.forEach(col => {
                    const seatNumber = `${row}${col}`;
                    const seat = document.createElement('div');
                    seat.classList.add('seat');
                    seat.innerText = seatNumber;

                    // Marcar como ocupado si está en la lista de asientos ocupados
                    if (occupiedSeats.includes(seatNumber)) {
                        seat.classList.add('occupied');
                    } else if (!selectedRows.includes(row) || !selectedColumns.includes(col)) {
                        // Deshabilitar asientos fuera de la clase seleccionada
                        seat.classList.add('disabled');
                    } else {
                        // Permitir selección solo para asientos de la clase seleccionada
                        seat.addEventListener('click', () => selectSeat(seat));
                    }

                    seatContainer.appendChild(seat);
                });
            });

            // Función para seleccionar un asiento
            function selectSeat(seat) {
                if (seat.classList.contains('occupied') || seat.classList.contains('disabled')) return;

                // Deseleccionar asiento anterior si existe
                if (selectedSeat && selectedSeat !== seat) {
                    selectedSeat.classList.remove('selected');
                }

                // Seleccionar asiento actual
                seat.classList.toggle('selected');
                selectedSeat = seat.classList.contains('selected') ? seat : null;

                // Mostrar u ocultar botón dependiendo de si hay un asiento seleccionado
                selectButton.style.display = selectedSeat ? 'block' : 'none';
                selectButton.disabled = !selectedSeat;
            }

            // Función para confirmar selección del asiento
            selectButton.addEventListener('click', () => {
                if (selectedSeat) {
                    selectedSeat.classList.remove('selected');
                    selectedSeat.classList.add('occupied');
                    occupiedSeats.push(selectedSeat.innerText); // Añadir a la lista de ocupados
                    document.querySelector('input[name="asiento"]').value = selectedSeat.innerText;
                    selectedSeat = null;
                    selectButton.style.display = 'none'; // Ocultar el botón
                    alert("Asiento seleccionado con éxito.");
                }
            });
        });


    </script>

    </body>
    </html>
