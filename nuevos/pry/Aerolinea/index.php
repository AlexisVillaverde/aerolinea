<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aerolinea Skywings</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/principal.css">
</head>
<body>
  <header class="p-3 text-white header-bg">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="http://localhost/si/pry/Aerolinea/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          <img src="img/skywings_logo.png" class="d-inline-block align-text-top" height="54" alt="logo_aerolinea">
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="FrontReservas.html" class="nav-link px-2 text-white">Reserva</a></li>
          <li><a href="faq_aerolinea.html" class="nav-link px-2 text-white">FAQs</a></li>
          <li><a href="contacto-skywings.html" class="nav-link px-2 text-white">Contacto</a></li>
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <input
            type="search"
            class="form-control form-control-dark text-bg-dark"
            placeholder="Search..."
            aria-label="Search"
          />
        </form>

        <div class="text-end">
          <?php if (isset($_SESSION['ID_Pasajero'])): ?>
            <!-- Si la sesión está activa, muestra el botón de cerrar sesión -->
            <a href="http://localhost/si/aerolinea/logout.php" id="logout-btn">
              <button type="button" class="btn btn-outline-danger ms-2">Cerrar Sesión</button>
            </a>
          <?php else: ?>
            <!-- Si no está autenticado, muestra los botones de login y registro -->
            <a href="http://localhost/si/aerolinea/inicio_sesion.php">
              <button type="button" class="btn btn-outline-light me-2">Login</button>
            </a>
            <a href="http://localhost/si/aerolinea/registro_usuario.php" class="text-dark">
              <button type="button" class="btn btn-warning">Sign-up</button>
            </a>
          <?php endif; ?>
        

        </div>
      </div>
    </div>
  </header>
  <section class="principal">
    <h1 class="titulo">Bienvenido a Skywings Airlines</h1>
    <div class="flex">
        <form action="http://localhost/si/aerolinea/searchVuelo.php" method="POST"  class="city-selector mt-5 p-4 border">
        

          <div class="row mb-3 text-center">
            <div class="col">
                <button type="button" class="btn btn-flight-option active" onclick="setFlightOption('Sencillo')">Sencillo</button>
                <button type="button" class="btn btn-flight-option" onclick="setFlightOption('Redondo')">Redondo</button>
            </div>
            <input type="hidden" id="flightOption" name="flightOption" value="Sencillo">
          </div>
        
          <div class="row mb-3">
            <div class="col-md-6">
            <div class="form-group">
                <label for="origin">Origen</label>
                <input
                type="text"
                id="origin"
                name="origin"
                class="form-control input-city"
                placeholder="Selecciona tu origen"
                required
                />
                <!-- Dropdown de sugerencias para origen -->
                <ul id="origin-suggestions" class="list-group suggestions-dropdown"></ul>
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <label for="destination">Destino</label>
                <input
                type="text"
                id="destination"
                name="destination"
                class="form-control input-city"
                placeholder="Selecciona tu destino"
                required
                />
                <!-- Dropdown de sugerencias para destino -->
                <ul id="destination-suggestions" class="list-group suggestions-dropdown"></ul>
            </div>
            </div>
        </div>
        
        <div class="row mb-3 fecha">
            <div class="col-md-6">
            <div class="form-group">
                <label for="departureDate">Fecha de salida</label>
                <input type="date" id="departureDate"  name="departureDate" class="form-control" required />
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <label for="returnDate">Fecha de regreso</label>
                <input type="date" id="returnDate" name="returnDate" class="form-control"  required  disabled/>
            </div>
            </div>
        </div>
        
        <div class="row mb-3 btn-buscar">
            <div class="col-md-12">
            <button class="btn btn-primary w-100" type="submit">
                Buscar vuelos <span>&#9992;</span> <!-- Icono de avión -->
            </button>
            </div>
        </div>
      </form>

        <!-- Columna para el carrusel -->
        <div class="col-md-6">
        <div id="carouselExample" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
            <div class="carousel-item active">
                <img
                class="d-block w-100"
                src="./img/mx1_es_7nov_desc30_home.jpg"
                alt="First slide"
                />
            </div>
            <div class="carousel-item">
                <img
                class="d-block w-100"
                src='./img/base_bm_mty_tarifas.jpg'
                alt="Second slide"
                />
            </div>
            <div class="carousel-item">
                <img
                class="d-block w-100"
                src="./img/mx_es_4nov_tijlas_home.jpg"
                alt="Third slide"
                />
            </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
            </a>
        </div>
        </div>
    </div>
</section>

<footer>
    <div class="footer-bg">
        <div class="container">
          <footer class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-5 my-5 border-top footer-bg">
            <div class="col mb-3">
              <h4 class="text">¿Tienes un vuelo?</h4>
              <a href="/guia-vuelo.html" class="nav-link p-0">Guía para tu vuelo</a>
            </div>
    
            <!-- Espacio vacío -->
            <div class="col"></div>
    
            <div class="col mb-3">
              <h5>Nuestras redes</h5>
              <ul class="nav flex-column">
                <li class="nav-item mb-2">
                  <img src="./img/icons8-home-26.png"></img><a href="#" class="nav-link p-0">Home</a>
                </li>
                <li class="nav-item mb-2">
                  <img src="./img/Facebook_Logo_Secondary.png"></img><a href="#" class="nav-link p-0">Facebook</a>
                </li>
                <li class="nav-item mb-2">
                  <img src="./img/Instagram_Glyph_White.png"></img><a href="#" class="nav-link p-0">Instagram</a>
                </li>
                <li class="nav-item mb-2">
                  <img src="./img/logo-white.png"></img><a href="#" class="nav-link p-0">X</a>
                </li>
                <li class="nav-item mb-2">
                  <img src="./img/yt_icon_mono_dark.png"></img><a href="#" class="nav-link p-0">YouTube</a>
                </li>
              </ul>
            </div>
    
            <div class="col mb-3">
              <h5>De tu interés</h5>
              <ul class="nav flex-column">
                <li class="nav-item mb-2">
                  <a href="#" class="nav-link p-0">Trabaja con nosotros</a>
                </li>
                <li class="nav-item mb-2">
                  <a href="#" class="nav-link p-0">Inversionistas</a>
                </li>
                <li class="nav-item mb-2">
                  <a href="#" class="nav-link p-0">Sustentabilidad</a>
                </li>
                <li class="nav-item mb-2">
                  <a href="#" class="nav-link p-0">Blog SkyWings</a>
                </li>
                <li class="nav-item mb-2">
                  <a href="#" class="nav-link p-0">Alianzas Comerciales</a>
                </li>
              </ul>
            </div>
    
            <div class="col mb-3">
              <h5>Sobre SkyWings</h5>
              <ul class="nav flex-column">
                <li class="nav-item mb-2">
                  <a href="#" class="nav-link p-0">Somos la aerolínea de ultra-bajo costo 
                    con vuelos en México, Estados Unidos, Centroamérica y Sudamérica</a>
                </li>
              </ul>
            </div>
          </footer>
    
          <div class="flex-sm-row py-4 border-top">
            <p class="text-center">&copy; 2024 SkyWings, Inc</p>
          </div>
        </div>
      </div>
</footer>
  <script src="./js/php.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
