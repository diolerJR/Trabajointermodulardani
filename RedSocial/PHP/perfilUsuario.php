<?php
session_start();
require_once 'bd.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$usuarioLogueadoId = $_SESSION['usuario']['id'];
$perfilId = (int) $_GET['id'];

// Evitar que vea su propio perfil aquí (opcional)
if ($perfilId === $usuarioLogueadoId) {
    header('Location: perfilAdmin.php');
    exit;
}

$bd = conectarBS();

// Datos del usuario cuyo perfil vemos
$sql = "SELECT id, nombre, fecha_nacimiento, ciudad, biografia, foto_perfil
        FROM usuarios
        WHERE id = :id";
$stmt = $bd->prepare($sql);
$stmt->bindValue(':id', $perfilId, PDO::PARAM_INT);
$stmt->execute();
$perfil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$perfil) {
    header('Location: usuarios.php');
    exit;
}

// Posts de ese usuario (reutiliza tu función)
$posts = obtenerPostsUsuario($perfilId);

// Primero saber si ya lo sigues
$yaSigue = yaSigue($usuarioLogueadoId, $perfilId);

// Procesar seguir / dejar de seguir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['seguir']) && !$yaSigue) {
        seguirUsuario($usuarioLogueadoId, $perfilId);
    } elseif (isset($_POST['dejar_de_seguir']) && $yaSigue) {
        dejarDeSeguir($usuarioLogueadoId, $perfilId);
    }

    header("Location: perfilUsuario.php?id=" . $perfilId);
    exit;
}

// Recalcular por si ha cambiado
$yaSigue = yaSigue($usuarioLogueadoId, $perfilId);

// Solo cargar posts si lo sigues
$posts = $yaSigue ? obtenerPostsUsuario($perfilId) : [];

// Contadores para las tarjetas
$totalSeguidores = contarSeguidores($perfilId);
$totalSiguiendo = contarSiguiendo($perfilId);

// Ruta de foto segura (si no tiene, usar la de por defecto)
$rutaFoto = '../IMAGES/defaultPerfil.png';
if (!empty($perfil['foto_perfil'])) {
    $rutaFoto = $perfil['foto_perfil'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($perfil['nombre']); ?></title>
    <link rel="stylesheet" href="../CSS/perfiles.css">
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="container">
        <div class="perfil-header">
            <div class="foto-perfil">
                <img src="<?php echo htmlspecialchars($rutaFoto); ?>" alt="Foto de perfil">
            </div>

            <div class="info-perfil">
                <h1><?php echo htmlspecialchars($perfil['nombre']); ?></h1>

                <p class="biografia">
                    <?php echo !empty($perfil['biografia'])
                        ? nl2br(htmlspecialchars($perfil['biografia']))
                        : 'Sin biografía aún.'; ?>
                </p>

                <p class="info-adicional">
                    Fecha de nacimiento:
                    <?php echo !empty($perfil['fecha_nacimiento'])
                        ? htmlspecialchars($perfil['fecha_nacimiento'])
                        : 'No indicada'; ?>
                    <br>
                    Ciudad:
                    <?php echo !empty($perfil['ciudad'])
                        ? htmlspecialchars($perfil['ciudad'])
                        : 'No indicada'; ?>
                </p>

                <form method="POST">
                    <?php if ($yaSigue): ?>
                        <button type="submit" name="dejar_de_seguir" class="btn-cerrar-cuenta">
                            Dejar de seguir
                        </button>
                    <?php else: ?>
                        <button type="submit" name="seguir" class="btn-editar-perfil">
                            Seguir
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <div class="estadisticas">
                <div class="stat">
                    <strong><?php echo count($posts); ?></strong>
                    <span>Posts</span>
                </div>
                <div class="stat">
                    <strong><?php echo $totalSeguidores; ?></strong>
                    <span>Seguidores</span>
                </div>
                <div class="stat">
                    <strong><?php echo $totalSiguiendo; ?></strong>
                    <span>Siguiendo</span>
                </div>
            </div>
        </div>

        <div class="feed">
            <h2>Posts de <?php echo htmlspecialchars($perfil['nombre']); ?></h2>

            <?php if (!$yaSigue): ?>
                <p>Debes seguir a <?php echo htmlspecialchars($perfil['nombre']); ?> para ver sus posts.</p>
            <?php elseif (empty($posts)): ?>
                <p>No hay posts todavía.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <?php
                    $avatar = $rutaFoto;
                    $nombre = $perfil['nombre'];
                    $fecha = $post['fecha_publicacion'];
                    $contenido = $post['contenido'];
                    $imagenPost = $post['imagen'];
                    $archivoAdjunto = $post['archivo_adjunto'];
                    ?>
                    <?php include 'post.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>