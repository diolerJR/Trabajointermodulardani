<?php
$perfilUrl = 'login.php'; // por si no hay sesión

if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] == 0) {
        $perfilUrl = 'perfil.php';
    } else {
        $perfilUrl = 'perfilAdmin.php';
    }
}
?>

<nav class="navbar">
    <div class="nav-container">
        <a href="Home.php" class="nav-logo">Red Social</a>
        <ul class="nav-menu">
            <li><a href="home.php">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="<?php echo $perfilUrl; ?>">Mi Perfil</a></li>
            <li><a href="notificaciones.php">Notificaciones</a></li>
            <li><a href="logout.php">Salir</a></li>
        </ul>
    </div>
</nav>
