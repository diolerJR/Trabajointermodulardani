<?php
session_start();
include "bd.php";

$errores = [];

if (isset($_POST["registrar"])) {

    $email = trim($_POST["email"]);
    $nombre = trim($_POST["nombre"]);
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    // validar datos
    if (empty($email) || empty($nombre) || empty($password) || empty($password2)) {
        $errores[] = "Debe completar todos los campos.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    if ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    //comprobamos si el email existe 
    $sql = "SELECT id FROM usuarios WHERE email=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errores[] = "Este correo ya está registrado.";
    }

    // si no hay errores se guarda el user nuevo en la base de datos
    if (empty($errores)) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (email, password_hash, nombre, rol) 
                VALUES (?, ?, ?, 0)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sss", $email, $password_hash, $nombre);
        $stmt->execute();

        // redirige al login con mensaje de exito
        header("Location: login.php?registrado=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Red Social</title>
    <link rel="stylesheet" href="../CSS/login.css">
</head>
<body>

<div class="login-container">
    <h2>Crear cuenta</h2>

    <?php
    if (!empty($errores)) {
        echo "<div class='error-box'>";
        foreach ($errores as $e) {
            echo "<p>$e</p>";
        }
        echo "</div>";
    }
    ?>

    <form method="POST">

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Nombre de usuario:</label>
        <input type="text" name="nombre" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <label>Repetir contraseña:</label>
        <input type="password" name="password2" required>

        <button type="submit" name="registrar">Crear cuenta</button>
    </form>

    <p style="text-align:center; margin-top:15px;">
        ¿Ya tienes cuenta? 
        <a href="login.php">Iniciar sesión</a>
    </p>
</div>

</body>
</html>
