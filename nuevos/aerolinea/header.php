<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="./principal.css">


<header class="p-3 text-white header-bg">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="http://localhost/si/pry/Aerolinea/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <img src="skywings_logo.png" class="d-inline-block align-text-top" height="54" alt="logo_aerolinea">
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="http://localhost/si/pry/Aerolinea/FrontReservas.html" class="nav-link px-2 text-white">Reserva</a></li>
                <li><a href="http://localhost/si/pry/Aerolinea/faq_aerolinea.html" class="nav-link px-2 text-white">FAQs</a></li>
                <li><a href="http://localhost/si/pry/Aerolinea/contacto-skywings.html" class="nav-link px-2 text-white">Contacto</a></li>
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