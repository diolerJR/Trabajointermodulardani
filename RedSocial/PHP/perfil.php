<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] != 0) {
    header("Location: login.php?redirigido=1");
    exit;
}
$usuario = $_SESSION["usuario"];
include "bd.php";
$yo = $_SESSION["id"];

session_start();

$yo = $_SESSION["id"];
$perfilId = $_GET["id"];

$estado = obtenerEstadoSeguimiento($yo, $perfilId);

// Si no es mi propio perfil
if ($yo != $perfilId) {
    if (!$estado || $estado["estado"] != "aceptado") {
        echo "<h3>Este perfil es privado</h3>";
        echo "<p>Debes seguir a este usuario para ver sus fotos.</p>";
        exit; // no permite ver el contenido si no es seguidor aceptado
    }
}


function actualizarPerfil($conexion, $usuarioId)
{
// permite actualizar el perfil del usuario logueado
    if (isset($_POST["guardar_perfil"])) {

        $nombre = trim($_POST["nombre"]);
        $biografia = trim($_POST["biografia"]);
        $fecha_nacimiento = $_POST["fecha_nacimiento"];
        $ciudad = trim($_POST["ciudad"]);

        $sql = "UPDATE usuarios 
                SET nombre=?, biografia=?, fecha_nacimiento=?, ciudad=? 
                WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $biografia, $fecha_nacimiento, $ciudad, $usuarioId);
        $stmt->execute();

        echo "<p style='color:green;'>Perfil actualizado correctamente.</p>";

        // esto hara que se actualice la sesion con los cambios realizados por el usuario
        $_SESSION["usuario"]["nombre"] = $nombre;
        $_SESSION["usuario"]["biografia"] = $biografia;
        $_SESSION["usuario"]["fecha_nacimiento"] = $fecha_nacimiento;
        $_SESSION["usuario"]["ciudad"] = $ciudad;
    }

    // esto permite a los usuarios cambiar su foto de perfil o eliminarla
    if (isset($_POST["cambiar_foto"]) && isset($_FILES["foto_perfil"])) {

        if ($_FILES["foto_perfil"]["error"] === 0) {

            $ruta = "uploads/avatars/";
            $nombreArchivo = "perfil_" . $usuarioId . "_" . time() . ".jpg";
            $rutaCompleta = $ruta . $nombreArchivo;

            move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $rutaCompleta);

            $sql = "UPDATE usuarios SET foto_perfil=? WHERE id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("si", $rutaCompleta, $usuarioId);
            $stmt->execute();

            echo "<p style='color:green;'>Foto de perfil actualizada.</p>";
        }
    }

    // funcion para eleiminar la foto de perfil 
    if (isset($_POST["eliminar_foto"])) {

        // aqui en caso de que el usuario no ponga foto se pondra una por defecto
        $foto_por_defecto = "uploads/avatars/default.jpg";

        $sql = "UPDATE usuarios SET foto_perfil=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $foto_por_defecto, $usuarioId);
        $stmt->execute();

        echo "<p style='color:red;'>Foto eliminada.</p>";
    }

    // funcion de crear una hisotria 
    if (isset($_POST["crear_historia"]) && isset($_FILES["historia_foto"])) {

        if ($_FILES["historia_foto"]["error"] === 0) {

            $ruta = "uploads/historias/";
            $archivo = "historia_" . $usuarioId . "_" . time() . ".jpg";
            $rutaCompleta = $ruta . $archivo;

            move_uploaded_file($_FILES["historia_foto"]["tmp_name"], $rutaCompleta);

            $sql = "INSERT INTO historias (usuario_id, foto, fecha) VALUES (?, ?, NOW())";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("is", $usuarioId, $rutaCompleta);
            $stmt->execute();

            echo "<p style='color:blue;'>Historia añadida a tu perfil.</p>";
        }
    }

    // metodo para crear una subida de una imagen con post 
    if (isset($_POST["crear_post"])) {

        $contenido = trim($_POST["contenido"]);
        $imagen_subida = null;
        $archivo_subido = null;

        // Imagen que subira el usuario 
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {
            $ruta = "uploads/posts/";
            $imagen_subida = "post_" . $usuarioId . "_" . time() . ".jpg";
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta . $imagen_subida);
        }

        //el archivo que subira el usuario 
        if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === 0) {
            $ruta = "uploads/archivos/";
            $archivo_subido = "archivo_" . $usuarioId . "_" . time() . "_" . $_FILES["archivo"]["name"];
            move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta . $archivo_subido);
        }

        $sql = "INSERT INTO posts (usuario_id, contenido, imagen, archivo, fecha, editado)
                VALUES (?, ?, ?, ?, NOW(), 0)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("isss", $usuarioId, $contenido, $imagen_subida, $archivo_subido);
        $stmt->execute();

        echo "<p style='color:green;'>Publicación creada con éxito.</p>";
    }
}

?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Red Social</title>
    <link href="../CSS/zona_user.css" rel="stylesheet">
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
                <img src="mostrarFoto.php" alt="Foto de perfil">
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="foto_perfil" id="foto_perfil">
                    <button type="submit" name="cambiar_foto" class="btn-cambiar-foto">Cambiar foto</button>
                    <button type="submit" name="eliminar_foto" class="btn-eliminar-foto">Eliminar foto</button>
                </form>
            </div>

            <div class="info-perfil">
                <h1><?php echo htmlspecialchars($usuario["nombre"]); ?></h1>
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

        <div id="modal-editar" class="modal">
            <div class="modal-contenido">
                <h2>Editar perfil</h2>
                <form method="POST">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="Nombre Usuario">

                    <label>Biografía:</label>
                    <textarea name="biografia" rows="3">Mi biografía...</textarea>

                    <label>Fecha de nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" value="1995-05-15">

                    <label>Ciudad:</label>
                    <input type="text" name="ciudad" value="Madrid">

                    <button type="submit" name="guardar_perfil">Guardar cambios</button>
                    <button type="button" class="btn-cancelar">Cancelar</button>
                </form>
            </div>
        </div>

        <div class="card crear-post">
            <h2>Crear nueva publicación</h2>
            <form method="POST" enctype="multipart/form-data">
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
            <h2>Mis publicaciones</h2>

            <div class="post">
                <div class="post-header">
                    <img src="uploads/avatars/default.jpg" class="avatar" alt="Avatar">
                    <div class="post-info">
                        <strong>Mi Nombre</strong>
                        <span class="fecha">29/11/2025 - 15:30</span>
                        <span class="editado">(editado)</span>
                    </div>
                </div>

                <div class="post-contenido">
                    <p>Este es el contenido de mi publicación...</p>

                    <img src="uploads/posts/imagen.jpg" class="post-imagen" alt="Imagen del post">

                    <div class="archivo-adjunto">
                        <a href="uploads/archivos/documento.pdf" target="_blank">
                            Descargar: documento.pdf
                        </a>
                    </div>
                </div>

                <div class="post-footer">
                    <div class="reacciones">
                        <button class="btn-reaccion like active">Me gusta <span>15</span></button>
                        <button class="btn-reaccion dislike">No me gusta <span>2</span></button>
                        <button class="btn-reaccion love">Me encanta <span>8</span></button>
                        <button class="btn-reaccion wow">Wow <span>3</span></button>
                    </div>

                    <div class="post-stats">
                        <a href="post.php?id=1" class="link-comentarios">
                            Ver comentarios (8)
                        </a>
                    </div>

                    <div class="post-acciones">
                        <a href="editar_post.php?id=1" class="btn-editar">Editar</a>
                        <a href="eliminar_post.php?id=1" class="btn-eliminar"
                            onclick="return confirm('¿Eliminar esta publicación?')">Eliminar</a>
                    </div>
                </div>
            </div>

            <div class="post">
                <div class="post-header">
                    <img src="uploads/avatars/default.jpg" class="avatar" alt="Avatar">
                    <div class="post-info">
                        <strong>Mi Nombre</strong>
                        <span class="fecha">28/11/2025 - 10:15</span>
                    </div>
                </div>

                <div class="post-contenido">
                    <p>Otra publicación de ejemplo sin imagen...</p>
                </div>

                <div class="post-footer">
                    <div class="reacciones">
                        <button class="btn-reaccion like">Me gusta <span>23</span></button>
                        <button class="btn-reaccion dislike">No me gusta <span>1</span></button>
                        <button class="btn-reaccion love">Me encanta <span>12</span></button>
                        <button class="btn-reaccion wow">Wow <span>5</span></button>
                    </div>

                    <div class="post-stats">
                        <a href="post.php?id=2" class="link-comentarios">
                            Ver comentarios (12)
                        </a>
                    </div>

                    <div class="post-acciones">
                        <a href="editar_post.php?id=2" class="btn-editar">Editar</a>
                        <a href="eliminar_post.php?id=2" class="btn-eliminar"
                            onclick="return confirm('¿Eliminar esta publicación?')">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>