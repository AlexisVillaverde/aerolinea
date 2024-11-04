<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Estilos básicos */
        body {
            background: #e3ecf7;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: whitesmoke;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 550px;
            width: 100%;
            color: #333;
            border: 3px solid;
            border-image: linear-gradient(to right, #a0e7e5, #b4f8c8, #fdfd96) 1;
            text-align: left;
        }

        .padre > div {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .titulo {
            text-align: center;
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
        }

        .descripcion {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 180px;
            animation: fadeInDown 1s ease; /* Animación al cargar */
        }

        
        .formulario {
            /* Asegura que el formulario esté centrado */
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        /* Animación */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .boton {
            background-color: #009688; /* Color principal */
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .boton:hover {
            background-color: #00796b; /* Color más oscuro en hover */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Sombra en hover */
        }
    </style>
</head>
<body>

<div class="container">
    <form id="registroForm" class="formulario">
        <img src="logo.jpg" alt="Logo" class="logo">
        <h2 class="titulo">REGÍSTRATE</h2>
        <p class="descripcion">Regístrate y disfruta de beneficios exclusivos</p>

        <div class="padre">
            <div class="nombre">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            
            <div class="primer_apellido">
                <label for="primer_apellido">Primer Apellido</label>
                <input type="text" name="primer_apellido" id="primer_apellido" required>
            </div>

            <div class="segundo_apellido">
                <label for="segundo_apellido">Segundo Apellido</label>
                <input type="text" name="segundo_apellido" id="segundo_apellido" required>
            </div>

            <div class="edad">
                <label for="edad">Edad</label>
                <input type="number" name="edad" id="edad" min="0" required>
            </div>

            <div class="telefono">
                <label for="telefono">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" pattern="[0-9]{10}" maxlength="10" placeholder="10 dígitos" required>
            </div>

            <div class="correo_Electronico">
                <label for="correo_electronico">Correo Electrónico</label>
                <input type="email" name="correo_electronico" id="correo_electronico" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="contrasenia">
                <label for="clave">Contraseña</label>
                <input type="password" name="contrasenia" id="contrasenia" required placeholder="Ingrese una contraseña segura">
                <small>Ingrese una contraseña segura (mínimo 8 caracteres, con letras y números).</small>
            </div>
        </div>

        <button type="submit" id="btnRegistro">Registrar</button>
    </form>
</div>

<script>
document.getElementById('registroForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('controlador_registrar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.status === 'success') {

            mostrarVentanaFlotante(data.message);
            setTimeout(() => {
                this.reset(); // Limpia los campos del formulario después de un retraso
            }, 3000);
        } else {
            mostrarVentanaFlotante(data.message, 'error'); // Muestra el error si el correo ya existe
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarVentanaFlotante("Error en el registro", 'error');
    });
});

function mostrarVentanaFlotante(mensaje, tipo = 'success') {
    const ventana = document.createElement('div');
    ventana.textContent = mensaje;
    ventana.style.position = 'fixed';
    ventana.style.top = '50%';
    ventana.style.left = '50%';
    ventana.style.backgroundColor = tipo === 'success' ? '#4CAF50' : '#FF6347'; // Verde si es éxito, rojo si es error
    ventana.style.color = 'white';
    ventana.style.padding = '15px';
    ventana.style.borderRadius = '5px';
    ventana.style.boxShadow = '0px 4px 8px rgba(0, 0, 0, 0.2)';
    ventana.style.zIndex = '1000';
    ventana.style.transform = 'translate(-50%, -50%)';

    document.body.appendChild(ventana);

    setTimeout(() => {
        ventana.remove();
    }, 3000);
}

</script>

</body>
</html>
