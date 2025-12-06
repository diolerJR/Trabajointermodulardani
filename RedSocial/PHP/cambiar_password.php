<?php
session_start();
include "bd.php";

if (!isset($_GET["token"])) {
    die("Token no válido.");
}

$token = $_GET["token"];

// Verificar token
$sql = "SELECT id, fecha_token_recuperacion FROM usuarios WHERE token_recuperacion=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Token inválido o expirado.");
}

$usuario = $result->fetch_assoc();
$idUsuario = $usuario["id"];
$fechaToken = strtotime($usuario["fecha_token_recuperacion"]);

// esto hara que el token caduque en 1h 
if (time() - $fechaToken > 3600) {
    die("El token ha expirado. Solicite otro.");
}

// procesar el cambio de contraseña 
if (isset($_POST["cambiar"])) {

    $pass1 = $_POST["password"];
    $pass2 = $_POST["password2"];

    if ($pass1 !== $pass2) {
        echo "<p style='color:red;'>Las contraseñas no coinciden.</p>";
    } else {

        $nuevoHash = password_hash($pass1, PASSWORD_DEFAULT);

        // actualiza la bd para cambiar la contraseña 
        $sql = "UPDATE usuarios 
                SET password_hash=?, token_recuperacion=NULL, fecha_token_recuperacion=NULL 
                WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $nuevoHash, $idUsuario);
        $stmt->execute();

        echo "<p style='color:green;'>Contraseña actualizada correctamente.</p>";
        echo "<p><a href='login.php'>Volver a iniciar sesión</a></p>";
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña</title>
    <link href="../CSS/login.css" rel="stylesheet">
</head>

<body>

<div class="login-container">
    <h2>Crear nueva contraseña</h2>

    <form method="POST">
        <label>Nueva contraseña:</label>
        <input type="password" name="password" required>

        <label>Repetir contraseña:</label>
        <input type="password" name="password2" required>

        <button type="submit" name="cambiar">Guardar nueva contraseña</button>
    </form>
</div>

</body>
</html>
