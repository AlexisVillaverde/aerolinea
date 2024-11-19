// Manejar el evento de entrada para el campo de origen
document.getElementById('origin').addEventListener('input', function() {
    const query = this.value;

    // Si la longitud del input es menor a 2, limpiar sugerencias
    if (query.length < 2) {
        document.getElementById('origin-suggestions').innerHTML = '';
        return;
    }

    // Hacer la solicitud al servidor
    fetch(`http://localhost/si/aerolinea/getCities.php?query=${query}`)
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

let debounceTimeout; // Variable para almacenar el temporizador

document.getElementById('destination').addEventListener('input', function() {
    const origin = document.getElementById('origin').value; // Obtener el valor del origen

    // Verificar si se ha ingresado un valor en el campo de origen
    if (origin.trim() === "") {
        console.warn("Por favor, selecciona un origen antes de elegir un destino.");
        return;
    }

    // Limpiar el temporizador previo
    clearTimeout(debounceTimeout);
     debounceTimeout = setTimeout(() => {
     // Solicitar las ciudades de destino según el origen
    fetch(`http://localhost/si/aerolinea/getCities.php?origin=${encodeURIComponent(origin)}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            const suggestionsList = document.getElementById('destination-suggestions');
            suggestionsList.innerHTML = ''; // Limpiar sugerencias anteriores

            // Filtrar y mostrar las ciudades que coincidan con el input de destino
            const inputText = document.getElementById('destination').value.toLowerCase();
            const filteredCities = data.filter(city => 
                city.Nombre.toLowerCase().startsWith(inputText)
            );

            // Llenar el dropdown con las ciudades filtradas
            filteredCities.forEach(city => {
                const li = document.createElement('li');
                li.textContent = city.Nombre;
                li.classList.add('list-group-item');
                li.addEventListener('click', () => {
                    document.getElementById('destination').value = city.Nombre; // Establecer el valor en el input
                    suggestionsList.innerHTML = ''; // Limpiar sugerencias
                });
                suggestionsList.appendChild(li); // Agregar el elemento a la lista
            });
        })
        .catch(error => {
            console.error('Error al obtener las ciudades:', error);
        });
    }, 300); // Tiempo de espera (en milisegundos) antes de realizar la solicitud
});


document.getElementById('origin').addEventListener('click', function() {

    fetch(`http://localhost/si/aerolinea/getCities.php`)
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


document.getElementById('destination').addEventListener('click', function() {
    const origin = document.getElementById('origin').value; // Obtener el valor del origen

    // Hacer la solicitud al servidor enviando el origen como parámetro
    fetch(`http://localhost/si/aerolinea/getCities.php?origin=${encodeURIComponent(origin)}`)
        .then(response => response.json())
        .then(data => {
            const suggestionsList = document.getElementById('destination-suggestions');
            suggestionsList.innerHTML = ''; // Limpiar sugerencias anteriores

            // Llenar el dropdown con las ciudades obtenidas
            data.forEach(city => {
                const li = document.createElement('li');
                li.textContent = city.Nombre; // Agregar el nombre de la ciudad
                li.classList.add('list-group-item'); // Mantener clase de Bootstrap para estilos
                li.addEventListener('click', () => {
                    document.getElementById('destination').value = city.Nombre; // Establecer el valor en el input
                    suggestionsList.innerHTML = ''; // Limpiar sugerencias
                });
                suggestionsList.appendChild(li); // Agregar el elemento a la lista
            });
        })
        .catch(error => {
            console.error('Error al obtener las ciudades:', error);
        });
});


// JavaScript para cambiar la opción de vuelo
function setFlightOption(option) {
    // Actualiza el valor del input oculto
    document.getElementById('flightOption').value = option;
    return_date =document.getElementById('returnDate');

    // Cambia la clase "active" entre los botones
    const buttons = document.querySelectorAll('.btn-flight-option');
    buttons.forEach(btn => btn.classList.remove('active'));

    if (option === 'Sencillo') {
        buttons[0].classList.add('active');
        document.getElementById('returnDate').disabled=true;
    } else if (option === 'Redondo') {
        buttons[1].classList.add('active');
        document.getElementById('returnDate').disabled=false;
    }
}



/* document.addEventListener('DOMContentLoaded', async () => {
    fetch('http://localhost/si/aerolinea/estado_sesion.php', {
        method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Aquí recibirás el JSON con los datos de sesión
    
            if (data.isLoggedIn) {
                const logoutButton = document.createElement('a');
                logoutButton.href = 'http://localhost/si/aerolinea/logout.php';
                logoutButton.innerHTML = `
                    <button type="button" class="btn btn-outline-danger ms-2">Cerrar Sesión</button>
                `;
                document.querySelector('.text-end').appendChild(logoutButton);
            }
        })
        .catch(error => {
            console.error('Error al obtener el estado de la sesión:', error);
        });}) */

        
    


