<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("plane-login.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        main {
            background-color: rgb(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 9, 0.5);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: white;
        }

        form {
            display: flex;
            flex-direction: column;
            color: white;
        }

        label {
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #00305b;
        }

        p {
            margin-top: 15px;
            color: white;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .toggle-button {
            background-color: transparent;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
        }

        .toggle-button:hover {
            color: white;
            font-weight: bold;
            transform: scale(1.1);
            transition: 0.3s;
        }
    </style>
</head>
<body>
    <main>
        <h2 id="formTitle">Iniciar Sesión</h2>
        <form id="authForm">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" required />
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" required />
            </div>
            <button type="submit" id="submitButton">Login</button>
        </form>
        <p id="message" class="message"></p>
        <p>
            <span id="toggleText">¿No tienes cuenta?</span> 
            <button type="button" class="toggle-button" id="toggleModeButton">
                Regístrate aquí
            </button>
        </p>
    </main>

    <script>
        const form = document.getElementById('authForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const messageElement = document.getElementById('message');
        const toggleModeButton = document.getElementById('toggleModeButton');

        // Obtener la URL de redirección si está disponible
        const params = new URLSearchParams(window.location.search);
        const redirectUrl = params.get('redirect') || 'http://localhost/si/pry/Aerolinea/'; // Por defecto, redirigir a index.html

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = emailInput.value;
            const password = passwordInput.value;

            const endpoint = 'http://localhost/si/aerolinea/login.php';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password,redirectUrl }),
                });

                if (!response.ok) {
                    throw new Error('Error en la conexión con el servidor');
                }

                const result = await response.json();

                if (result.status === 'success') {
                    messageElement.textContent = 'Login exitoso';
                    messageElement.className = 'message';
                    // Redirigir a index.html después de un inicio de sesión exitoso
                    setTimeout(function() {
                        window.location.href = result.redirect || 'http://localhost/si/pry/Aerolinea/';
                    }, 1500); // Espera 1.5 segundos antes de redirigir
                } else {
                    messageElement.textContent = result.message || 'Ocurrió un error, intenta de nuevo';
                    messageElement.className = 'error';
                }
            } catch (error) {
                messageElement.textContent = 'Error al conectar con el servidor. Intenta nuevamente.';
                messageElement.className = 'error';
            }
        });

        toggleModeButton.addEventListener('click', function() {
            window.location.href = 'http://localhost/Si/aerolinea/registro_usuario.php';
        });
    </script>
</body>
</html>
