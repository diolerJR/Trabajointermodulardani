<?php
session_start();
require_once 'bd.php';
$bd = conectarBS();

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] != 1) {
    header("Location: login.php?redirigido=1"); // Si no esta logueado o tiene rol 1 sed redirige al login
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_post'])) {
    $contenido = $_POST['contenido'];
    $nombreImagen = null;
    $nombreArchivo = null;

    if ($contenido === '') {
        $errorPublicacion = 'El contenido no puede estar vacío.';
    } else {

        $nombreUsuario = $_SESSION['nick'];
        // Carpeta base donde se guardan los posts de este usuario
        $carpetaPost = '../POST/' . $nombreUsuario . '/';

        // Crear directorio si no existe
        if (!is_dir($carpetaPost)) {
            mkdir($carpetaPost, 0777, true);
        }

        // POST CON IMAGEN
        if (!empty($_FILES['imagen']['name'])) {
            $extImg = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $extensionesPermitidasImg = ['jpg', 'jpeg', 'png'];

            if (in_array($extImg, $extensionesPermitidasImg, true)) {
                $nombreImagenFisico = 'img_' . time() . '.' . $extImg;

                // Ruta FÍSICA donde se guarda el archivo
                $rutaFisicaImg = $carpetaPost . $nombreImagenFisico;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisicaImg)) {
                    // Ruta que se guarda en la BD (RELATIVA, con ../POST/...)
                    $nombreImagen = $carpetaPost . $nombreImagenFisico;
                }
            }
        }

        // POST CON ARCHIVO SOLO PDFS 
        if (!empty($_FILES['archivo']['name'])) {
            $extFile = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
            $extensionesPermitidasFile = ['pdf'];

            if (in_array($extFile, $extensionesPermitidasFile, true)) {
                $nombreArchivoFisico = 'file_' . time() . '.' . $extFile;

                $rutaFisicaFile = $carpetaPost . $nombreArchivoFisico;

                if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaFisicaFile)) {
                    $nombreArchivo = $carpetaPost . $nombreArchivoFisico;
                }
            }
        }

        // Guardar en BD
        if (crearPost($contenido, $nombreImagen, $nombreArchivo)) {
            // Importante: NADA de echo/HTML antes de este header
            header('Location: perfilAdmin.php');
            exit;
        } else {
            $errorPublicacion = 'No se ha podido publicar el post.';
        }
    }
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
    <?php include 'menu.php'; ?>
    <div class="container">
        <div class="perfil-header">
            <div class="foto-perfil">
                <?php
                $rutaFoto = '../IMAGES/defaultPerfil.png';
                if (!empty($_SESSION['usuario']['foto_perfil'])) {
                    $rutaFoto = $_SESSION['usuario']['foto_perfil'];
                }
                ?>
                <img src="<?php echo $rutaFoto; ?>" alt="Foto de perfil">
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
                    if (!empty($usuario['biografia'])) {
                        echo nl2br(htmlspecialchars($usuario['biografia']));
                    } else {
                        echo 'Sin biografía aún.';
                    }
                    ?>
                </p>
                <p class="info-adicional">
                    Fecha de nacimiento:
                    <?php
                    if (!empty($usuario['fecha_nacimiento'])) {
                        echo htmlspecialchars($usuario['fecha_nacimiento']);
                    } else {
                        echo 'No indicada';
                    }
                    ?>
                    <br>
                    Ciudad:
                    <?php
                    if (!empty($usuario['ciudad'])) {
                        echo htmlspecialchars($usuario['ciudad']);
                    } else {
                        echo 'No indicada';
                    }
                    ?>
                    <br>
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
                $avatar = "../IMAGES/defaultPerfil.png";
                if (!empty($_SESSION['usuario']['foto_perfil'])) {
                    $avatar = $_SESSION['usuario']['foto_perfil'];
                }

                $nombre = $_SESSION['usuario']['nombre'];
                $fecha = $post['fecha_publicacion'];
                $contenido = $post['contenido'];

                $imagenPost = $post['imagen'];
                $archivoAdjunto = $post['archivo_adjunto'];
                ?>

                <?php include 'post.php'; ?>
            <?php endforeach; ?>

        </div>
    </div>
</body>

</html>