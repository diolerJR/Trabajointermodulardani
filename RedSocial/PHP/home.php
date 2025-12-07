<?php
session_start();
require_once 'bd.php';

if (!isset($_SESSION["usuario"]) && !isset($_SESSION["rol"])) {
    header("Location: login.php?redirigido=1"); // Si no esta logueado se redirige al login
    exit;
}

$usuarios = obtenerUsuarios(); //Cargara todos los usuarios de la base de datos

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Red Social</title>
    <link rel="stylesheet" href="../CSS/home.css" />
</head>

<body>

    <header>
        <?php include 'menu.php'; ?>
    </header>
    <div class="stories-bar">
        <h3>Usuarios</h3>
        <div class="stories-wrapper">
            <?php foreach ($usuarios as $usuario): ?>
                <?php
                $rutaFoto = '../IMAGES/defaultPerfil.png';

                if (!empty($usuario['foto_perfil'])) {
                    $rutaFoto = $usuario['foto_perfil'];
                }
                ?>
                <div class="story-item">
                    <div class="story-avatar">
                        <img src="<?php echo $rutaFoto; ?>" alt="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                    </div>
                    <span class="story-username">
                        <?php echo htmlspecialchars($usuario['nombre']); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <section id="inicio" class="container">
        <h2>Ultimas Publicaciones</h2>
    </section>
</body>

</html>