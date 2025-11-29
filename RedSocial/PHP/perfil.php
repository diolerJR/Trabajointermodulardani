<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] != 0) {
    header("Location: login.php?redirigido=1");
    exit;
}
$usuario = $_SESSION["usuario"];
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