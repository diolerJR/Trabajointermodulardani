<?php
session_start();

// 1. Conexión a la base de datos
$cadena_conexion = 'mysql:dbname=red_social;host=localhost';
$usuario_bd = 'root';
$clave_bd = '';

try {
    $bd = new PDO($cadena_conexion, $usuario_bd, $clave_bd);
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos.");
}

$nombre = trim($_POST['usu']);
$email = trim($_POST['mail']);
$password = $_POST['clave'];
$password2 = $_POST['claveconfirm'];

if (empty($nombre) || empty($email) || empty($password) || empty($password2)) {
    header("Location: registro.php?error=campos_vacios");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: registro.php?error=email_invalido");
    exit;
}

if (strlen($password) < 8) {
    header("Location: registro.php?error=pass_corta");
    exit;
}

if ($password !== $password2) {
    header("Location: registro.php?error=pass_no_coinciden");
    exit;
}

$sql = "SELECT id FROM usuarios WHERE email = :email";
$stmt = $bd->prepare($sql);
$stmt->execute([':email' => $email]);

if ($stmt->rowCount() > 0) {
    header("Location: registro.php?error=email_repetido");
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$sql_insert = "INSERT INTO usuarios (email, password_hash, nombre)
               VALUES (:email, :password_hash, :nombre)";

$stmt_insert = $bd->prepare($sql_insert);

if ($stmt_insert->execute([
    ':email' => $email,
    ':password_hash' => $hash,
    ':nombre' => $nombre
])) {
    header("Location: zona_user.php");
    exit;
} else {
    header("Location: registro.php?error=error_db");
    exit;
}

?>
