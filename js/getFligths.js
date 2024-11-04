/* const origen = document.getElementById('origin').value;
const destino = document.getElementById('destination').value;
const Fecha_Salida = document.getElementById('departureDate').value;
const Fecha_Llegada = document.getElementById('returnDate').value;


async function fetchFlights(origen, destino, Fecha_Salida, Fecha_Llegada) {
    try {
        // Realiza la solicitud al archivo PHP usando POST
        const response = await fetch('searchVuelo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                origin: $origen,
                destination: $destino,
                departureDate: $Fecha_Salida,
                returnDate: $Fecha_Llegada
            })
        });

        // Convierte la respuesta en JSON
        const data = await response.json();

        // Llama a la función para mostrar los resultados en la tabla
        displayResults(data);

    } catch (error) {
        console.error("Error al obtener los datos:", error);
    }
}

function displayResults(data) {
    const tableBody = document.getElementById('resultsTable').querySelector('tbody');
    tableBody.innerHTML = ''; // Limpia cualquier contenido previo

    data.forEach(flight => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${flight.ciudad_origen}</td>
            <td>${flight.ciudad_destino}</td>
            <td>${flight.aeropuerto_destino}</td>
            <td>${flight.aeropuerto_origen}</td>
            <td>${flight.modelo_avion}</td>
            <td>${flight.Fecha_Salida}</td>
            <td>${flight.Fecha_Llegada}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Llama a la función para hacer la solicitud y mostrar los resultados
fetchFlights(); */


  // Obtener parámetros de la URL
  const params = new URLSearchParams(window.location.search);
  const origin = params.get("origin");
  const destination = params.get("destination");
  const departureDate = params.get("departureDate");
  const returnDate = params.get("returnDate");

  // Mostrar resultados
  document.getElementById("resultados").innerHTML = `
    <tr>
      <td>Origen: ${origin}</td>
      <td>Destino: ${destination}</td>
      <td>Fecha de salida: ${departureDate}</td>
      <td>Fecha de regreso: ${returnDate}</td>
      <td><button>reserva</button></td>
    <tr>
  `;