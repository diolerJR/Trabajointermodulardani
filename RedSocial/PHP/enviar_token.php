<?php
session_start();
include "bd.php";

if (!isset($_POST["email"])) {
    header("Location: recuperar.php");
    exit;
}

$email = trim($_POST["email"]);

// Buscar usuario
$sql = "SELECT id FROM usuarios WHERE email=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $usuario = $result->fetch_assoc();
    $id = $usuario["id"];

    // Generar token único
    $token = bin2hex(random_bytes(32));
    $ahora = date("Y-m-d H:i:s");

    // Guardar token en BD
    $sql = "UPDATE usuarios SET token_recuperacion=?, fecha_token_recuperacion=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $token, $ahora, $id);
    $stmt->execute();

    // Enlace que recibirá el usuario
    $enlace = "http://localhost/tu_ruta/cambiar_password.php?token=$token";

    
    //  Aquí envías el email
    // mail($email, "Recuperación de contraseña", "Haz clic aquí: $enlace");
 
}
header("Location: recuperar.php?enviado=1");
?>
