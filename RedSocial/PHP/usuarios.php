<?php
session_start();
require_once 'bd.php';

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php?redirigido=1"); // Si no esta logueado o tiene rol 1 sed redirige al login
    exit;
}

$termino = '';

if (isset($_POST['usuarioBuscado'])) {
    $termino = trim($_POST['usuarioBuscado']);
}

if ($termino === '') {
    $usuarios = obtenerUsuarios();
} else {
    $usuarios = buscarUsuariosPorNombre($termino);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Usuarios - Red Social</title>
    <link rel="stylesheet" href="../CSS/usuarios.css">
</head>

<body>

    <?php include 'menu.php'; ?>

    <div class="container">
        <div class="usuarios-header">
            <form method="post" action="usuarios.php">
                <input type="text" name="usuarioBuscado" class="search-box" placeholder="Buscar usuarios..." value="<?php
                if (isset($_POST['usuarioBuscado'])) {
                    echo htmlspecialchars($_POST['usuarioBuscado']);
                } else {
                    echo '';
                }
                ?>">
            </form>
        </div>


        <section class="usuarios-grid">
            <?php foreach ($usuarios as $usuario): ?>
                <?php
                $rutaFoto = '../IMAGES/defaultPerfil.png';
                if (!empty($usuario['foto_perfil'])) {
                    $rutaFoto = $usuario['foto_perfil'];
                }

                $esMismoUsuario = ($usuario['id'] == $_SESSION['usuario']['id']);

                if ($esMismoUsuario) {
                    // Según el rol, va a su propio perfil correspondiente
                    if ($_SESSION['usuario']['rol'] == 0) {
                        $href = 'perfil.php';
                    } else {
                        $href = 'perfilAdmin.php';
                    }
                } else {
                    // Perfil de otro usuario
                    $href = 'perfilUsuario.php?id=' . $usuario['id'];
                }

                ?>
                <a class="usuario-card" href="<?php echo $href; ?>">
                    <div class="usuario-avatar">
                        <img src="<?php echo $rutaFoto; ?>" alt="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                    </div>
                    <div class="usuario-info">
                        <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                        <?php if (!empty($usuario['ciudad'])): ?>
                            <span class="usuario-ciudad">
                                <?php echo htmlspecialchars($usuario['ciudad']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($usuario['biografia'])): ?>
                            <p class="usuario-bio">
                                <?php echo htmlspecialchars($usuario['biografia']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </section>
    </div>

</body>

</html>