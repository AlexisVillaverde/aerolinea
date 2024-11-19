<?php
// controlador_registrar.php
include 'conexion_bd.php'; // Incluye el archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera los valores enviados desde el formulario
    $nombre = $_POST['nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $edad = $_POST['edad'];
    $telefono = $_POST['telefono'];
    $correo_electronico = $_POST['correo_electronico'];
    $contrasenia = password_hash($_POST['contrasenia'], PASSWORD_DEFAULT); // Encripta la contraseña

    $redirectUrl = isset($_POST['redirectUrl']) ? trim($_POST['redirectUrl']) : '';

    try {
        // Verifica si el correo ya existe
        $sql = "SELECT COUNT(*) FROM pasajero WHERE correo_electronico = :correo_electronico";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':correo_electronico', $correo_electronico);
        $stmt->execute();
        $correoExistente = $stmt->fetchColumn();

        if ($correoExistente > 0) {
            // Si el correo ya está registrado, muestra un mensaje de error
            echo json_encode(['status' =>  'error', 'message' => 'El correo electrónico ya está registrado.']);
        } else {
            // Si el correo no existe, procede con la inserción
            $sql = "INSERT INTO pasajero (nombre, primer_apellido, segundo_apellido, edad, telefono, correo_electronico, contrasenia) 
                    VALUES (:nombre, :primer_apellido, :segundo_apellido, :edad, :telefono, :correo_electronico, :contrasenia)";
            
            $stmt = $conn->prepare($sql);

            // Asigna los valores a cada parámetro
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':primer_apellido', $primer_apellido);
            $stmt->bindParam(':segundo_apellido', $segundo_apellido);
            $stmt->bindParam(':edad', $edad);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo_electronico', $correo_electronico);
            $stmt->bindParam(':contrasenia', $contrasenia);

            // Ejecuta la inserción
            $stmt->execute();
            $idPasajero = $conn->lastInsertId();

            // *** Nueva línea: Establecer sesión para el nuevo pasajero ***
            session_start(); // Asegúrate de iniciar la sesión si aún no se ha iniciado
            $_SESSION['ID_Pasajero'] = $idPasajero;
            echo json_encode([
                'status' => 'success',
                'message' => 'Registro exitoso',
                'id_pasajero' => $idPasajero,
                'redirect' => $redirectUrl ?: 'confirmar_reserva.php' // Redirigir si hay URL
            ]);
        }


    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar: ' . $e->getMessage()]);
    }
    
    // Cierra la conexión
    $conn = null;
}
?>
