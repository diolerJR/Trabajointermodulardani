<?php
session_start();

if (!isset($_SESSION['usuario']['foto_perfil'])) {
    // Opcional: redirige a una imagen por defecto
    header('Location: ../IMAGES/defaultPerfil.png');
    exit;
}

$binario = $_SESSION['usuario']['foto_perfil'];

// Cambia el content-type según el tipo real si lo guardas
header('Content-Type: image/jpeg');
echo $binario;
