<?php
session_start();
require_once 'bd.php';
$bd = conectarBS();

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] != 1) {
    header("Location: login.php?redirigido=1"); // Si no esta logueado o tiene rol 1 sed redirige al login
    exit;
}

// Procesar publicación de post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crearpost'])) {
    $contenido = trim($_POST['contenido'] ?? '');

    $nombreImagen = null;
    $nombreArchivo = null;

    if (!empty($_FILES['imagen']['name'])) {
        $nombreImagen = basename($_FILES['imagen']['name']);
        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            'uploads/posts/' . $nombreImagen
        );
    }

    if (!empty($_FILES['archivo']['name'])) {
        $nombreArchivo = basename($_FILES['archivo']['name']);
        move_uploaded_file(
            $_FILES['archivo']['tmp_name'],
            'uploads/archivos/' . $nombreArchivo
        );
    }

    if ($contenido !== '') {
        crearPost($contenido, $nombreImagen, $nombreArchivo);
    }

    // Evitar reenvío del formulario al recargar
    header('Location: perfilAdmin.php');
    exit;
}

$usuario = $_SESSION["usuario"];
$idUsuario = $_SESSION["usuario"]['id'];
$posts = obtenerPostsUsuario($idUsuario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Red Social</title>
    <link href="../CSS/perfiles.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="timeline.php" class="nav-logo">Red Social</a>
            <ul class="nav-menu">
                <li><a href="timeline.php">Inicio</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="perfilAdmin.php">Mi Perfil</a></li>
                <li><a href="notificaciones.php">Notificaciones <span class="badge">3</span></a></li>
                <li><a href="logout.php">Salir</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="perfil-header">
            <div class="foto-perfil">
                <img src="../IMAGES/<?php echo $_SESSION["nick"] ?>.jpg" alt="Foto de perfil">
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="foto_perfil" id="foto_perfil">
                    <button type="submit" name="cambiar_foto" class="btn-cambiar-foto">Cambiar foto</button>
                    <button type="submit" name="eliminar_foto" class="btn-eliminar-foto">Eliminar foto</button>
                </form>
            </div>

            <div class="info-perfil">
                <h1><?php echo htmlspecialchars($usuario["nombre"]); ?> <img src="../IMAGES/verificado.png" alt=""></h1>
                <p class="biografia">
                    <?php
                    echo !empty($usuario["biografia"])
                        ? nl2br(htmlspecialchars($usuario['biografia']))
                        : 'Sin biografía aún.';
                    ?>
                </p>
                <p class="info-adicional">
                    Fecha de nacimiento:
                    <?php echo htmlspecialchars($usuario['fecha_nacimiento'] ?? 'No indicada'); ?><br>
                    Ciudad:
                    <?php echo htmlspecialchars($usuario['ciudad'] ?? 'No indicada'); ?><br>
                </p>
                <button class="btn-editar-perfil">Editar perfil</button>
                <button class="btn-cerrar-cuenta">Cerrar cuenta</button>
            </div>

            <div class="estadisticas">
                <div class="stat">
                    <strong>45</strong>
                    <span>Posts</span>
                </div>
                <div class="stat">
                    <strong>120</strong>
                    <span>Seguidores</span>
                </div>
                <div class="stat">
                    <strong>98</strong>
                    <span>Siguiendo</span>
                </div>
            </div>
        </div>

        <div class="card crear-post">
            <h2>Crear un nuevo Post </h2>
            <form action="perfilAdmin.php" method="POST" enctype="multipart/form-data">
                <textarea name="contenido" placeholder="¿Qué estás pensando?" rows="4" required></textarea>

                <div class="opciones-post">
                    <label class="upload-btn">
                        Añadir foto
                        <input type="file" name="imagen" accept="image/*">
                    </label>

                    <label class="upload-btn">
                        Adjuntar archivo
                        <input type="file" name="archivo">
                    </label>
                </div>

                <button type="submit" name="crear_post" class="btn-publicar">Publicar</button>
            </form>
        </div>

        <div class="feed">
            <h2>Mis Posts</h2>

            <?php foreach ($posts as $post): ?>
                <?php
                $avatar = "../IMAGES/" . $_SESSION["nick"] . ".jpg";
                $nombre = $_SESSION['usuario']['nombre'];
                $fecha = $post['fecha_publicacion'];
                $contenido = $post['contenido'];
                ?>

                <?php if (!empty($post['imagen'])): ?>
                    <?php
                    $imagenPost = $post['imagen'];          // ruta guardada en BD
                    $archivoAdjunto = $post['archivo_adjunto']; // si lo usas
                    include 'postImagen.php';
                    ?>
                <?php else: ?>
                    <?php include 'post.php'; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>