<?php
session_start();
require_once 'bd.php';

// Obtenemos todos los usuarios
$usuarios = obtenerUsuarios();
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
            <h2>Usuarios</h2>
            <input type="text" class="search-box" placeholder="Buscar usuarios...">
        </div>


        <section class="usuarios-grid">
            <?php foreach ($usuarios as $usuario): ?>
                <?php
                $rutaFoto = '../IMAGES/defaultPerfil.png';
                if (!empty($usuario['foto_perfil'])) {
                    // ajusta según cómo guardes la ruta en BD:
                    $rutaFoto = $usuario['foto_perfil'];
                    // o: $rutaFoto = '../IMAGES/' . $usuario['foto_perfil'];
                }
                ?>
                <a class="usuario-card" href="perfil.php?id=<?php echo $usuario['id']; ?>">
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