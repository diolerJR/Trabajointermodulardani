<?php
session_start();
require_once 'bd.php';
$bd = conectarBS();

// Comprobar que el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php?redirigido=1');
    exit;
}

$usuario = $_SESSION['usuario']; // array con id, nombre, biografia, etc.

// Cambiar imagen de Perfil
if (isset($_POST["cambiarfoto"]) && isset($_FILES["fotoperfil"])) {
    if ($_FILES["fotoperfil"]["error"] === 0) {

        $nombreUsuario = $_SESSION['nick'];
        $ruta = "../IMAGES/";
        $nombreArchivo = $nombreUsuario . ".jpg";
        $rutaCompleta = $ruta . $nombreArchivo;

        if (move_uploaded_file($_FILES["fotoperfil"]["tmp_name"], $rutaCompleta)) {
            cambiarFotoPerfil($rutaCompleta);
            $_SESSION["usuario"]["foto_perfil"] = $rutaCompleta;
        }
    }
}

// Procesar eliminación de foto
if (isset($_POST['eliminarfoto'])) {
    cambiarFotoPerfil(null);
    $_SESSION['usuario']['foto_perfil'] = "../IMAGES/defaultPerfil.png";
}

// Procesar actualización de datos del perfil
if (isset($_POST['guardarperfil'])) {
    $nuevoNick = trim($_POST['nick']);
    $biografia = trim($_POST['biografia']);
    $fechaNac = $_POST['fechanacimiento'];
    $ciudad = trim($_POST['ciudad']);

    actualizarPerfilUsuario($usuario['id'], $nuevoNick, $biografia, $fechaNac, $ciudad); // crea esta función en bd.php
    $_SESSION['usuario']['nombre'] = $nuevoNick;
    $_SESSION['usuario']['biografia'] = $biografia;
    $_SESSION['usuario']['fecha_nacimiento'] = $fechaNac;
    $_SESSION['usuario']['ciudad'] = $ciudad;

    header('Location: perfil.php?ok=1');
    exit;
}

$rutaFoto = !empty($usuario['fotoperfil']) ? $usuario['fotoperfil'] : 'IMAGES/defaultPerfil.png';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar perfil</title>
    <link rel="stylesheet" href="../CSS/editarPerfil.css">
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="container">
        <div class="perfil-header">
            <div class="foto-perfil">
                <?php
                $avatar = "../IMAGES/defaultPerfil.png";
                if (!empty($_SESSION['usuario']['foto_perfil'])) {
                    $avatar = $_SESSION['usuario']['foto_perfil'];
                }
                ?>
                <img src="<?php echo $avatar; ?>" alt="Foto de perfil">
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="fotoperfil" id="fotoperfil">
                    <label for="fotoperfil" class="btn-cambiar-foto">Seleccionar imagen</label>
                    <button type="submit" name="cambiarfoto" class="btn-cambiar-foto">Guardar nueva foto</button>
                    <button type="submit" name="eliminarfoto" class="btn-eliminar-foto">Eliminar foto</button>
                </form>
            </div>

            <div class="info-perfil">
                <h1>Editar perfil</h1>

                <form method="POST" class="form-editar-perfil">
                    <div class="form-group">
                        <label for="nick">Nombre de usuario</label>
                        <input type="text" id="nick" name="nick"
                            value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="biografia">Biografía</label>
                        <textarea id="biografia" name="biografia" rows="4" placeholder="Cuéntanos algo sobre ti"><?php
                        echo htmlspecialchars($usuario['biografia'] ?? '');
                        ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fechanacimiento">Fecha de nacimiento</label>
                            <input type="date" id="fechanacimiento" name="fechanacimiento"
                                value="<?php echo htmlspecialchars($usuario['fecha_nacimiento'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad"
                                value="<?php echo htmlspecialchars($usuario['ciudad'] ?? ''); ?>"
                                placeholder="Ej: Madrid">
                        </div>
                    </div>

                    <button type="submit" name="guardarperfil" class="btn-guardar-perfil">
                        Guardar cambios
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>

</html>