<?php

// Habilitar CORS p$allowedOrigins = ['http://localhost:3000', 'http://127.0.0.1:5500'];
//
//if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
//    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
//    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//    header("Access-Control-Allow-Headers: Content-Type, Authorization");
//    header("Access-Control-Allow-Credentials: true"); // Permitir envío de cookies
//}ara permitir solicitudes desde localhost:3000 y 127.0.0.1:5500


session_start();

// Verifica si la sesión está activa
if (isset($_SESSION['email']) && isset($_SESSION['perfil'])) {
    echo json_encode([
        'isLoggedIn' => true,
        'nombre' => $_SESSION['email'],
        'perfil' => $_SESSION['perfil']
    ]);
} else {
    echo json_encode([
        'isLoggedIn' => false,
        'message' => 'No session data available.'
    ]);
}
?>
