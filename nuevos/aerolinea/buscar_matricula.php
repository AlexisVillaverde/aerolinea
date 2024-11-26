<?php
require_once 'conexion_bd.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Matrícula</title>
    <style>
        /* Estilo básico */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 300px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        li {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            background: #f9f9f9;
            cursor: pointer;
        }

        li:hover {
            background: #ececec;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Buscar Matrícula</h3>
        <input type="text" id="matricula" placeholder="Escribe la matrícula" autocomplete="off">
        <ul id="resultados"></ul>
    </div>

    <script>
        const inputMatricula = document.getElementById('matricula');
        const resultados = document.getElementById('resultados');

        inputMatricula.addEventListener('input', function() {
            const valor = inputMatricula.value;

            if (valor.length > 0) {
                fetch(`buscar_matricula.php?matricula=${encodeURIComponent(valor)}`)
                    .then(response => response.json())
                    .then(data => {
                        resultados.innerHTML = ''; // Limpiar resultados anteriores

                        if (data.length > 0) {
                            data.forEach(item => {
                                const li = document.createElement('li');
                                li.textContent = item.Matricula;

                                // Agregar evento para seleccionar matrícula
                                li.addEventListener('click', function() {
                                    inputMatricula.value = item.Matricula; // Rellenar el campo
                                    resultados.innerHTML = ''; // Limpiar resultados
                                });

                                resultados.appendChild(li);
                            });
                        } else {
                            resultados.innerHTML = '<li>No se encontraron resultados</li>';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                resultados.innerHTML = ''; // Limpiar resultados si no hay texto
            }
        });
    </script>
</body>
</html>
