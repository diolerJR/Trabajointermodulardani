<?php
// Iniciar la sesión
session_start();

// eliminamos variables de sesion 
session_unset();

// destruimos la sesion para que no quedes logueado
session_destroy();

// Redirigir al login
header("Location: login.php");
?>