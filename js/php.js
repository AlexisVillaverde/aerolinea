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

// Manejar el evento de entrada para el campo de destino
document.getElementById('destination').addEventListener('input', function() {
    const query = this.value;

    // Si la longitud del input es menor a 2, limpiar sugerencias
    if (query.length < 2) {
        document.getElementById('destination-suggestions').innerHTML = '';
        return;
    }

    // Hacer la solicitud al servidor
    fetch(`http://localhost/si/aerolinea/getCities.php?query=${query}`)
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
