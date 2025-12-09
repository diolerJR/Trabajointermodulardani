<?php
session_start();


   //CONEXIÓN A LA BASE DE DATOS (PDO)

function conectarBS()
{
    static $bd = null;
    if ($bd === null) {
        include "configuracion_bd.php";
        $bd = new PDO(
            "mysql:dbname=" . $bd_config["nombrebd"] . ";host=" . $bd_config["ip"],
            $bd_config["usuario"],
            $bd_config["clave"],
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );
        $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $bd;
}

//login 
function hacer_login($email, $pass)
{
    $bd = conectarBS();
    $hashPass = hash('sha256', $pass);

    $sql = "SELECT id, nombre, email, password_hash, rol, fecha_nacimiento, ciudad, biografia, foto_perfil
            FROM usuarios 
            WHERE email = :email AND password_hash = :pass";

    $stmt = $bd->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':pass'  => $hashPass
    ]);

    if ($stmt->rowCount() !== 1) {
        return false;
    }

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION["usuario"] = $usuario;
    $_SESSION["nick"]    = $usuario["nombre"];
    $_SESSION["rol"]     = $usuario["rol"];

    return true;
}

//obtener post del usuario 
function obtenerPostsUsuario($usuarioId)
{
    $bd = conectarBS();

    $sql = "SELECT *
            FROM posts
            WHERE usuario_id = :id AND visible = 1
            ORDER BY fecha_publicacion DESC";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// crear los post del usuario 
function crearPost($contenido, $nombreImagen = null, $nombreArchivo = null)
{
    if (!isset($_SESSION['usuario']['id'])) {
        return false;
    }

    $bd = conectarBS();

    $sql = "INSERT INTO posts (usuario_id, contenido, imagen, archivo_adjunto)
            VALUES (:usuario_id, :contenido, :imagen, :archivo_adjunto)";

    $stmt = $bd->prepare($sql);
    return $stmt->execute([
        ':usuario_id'     => $_SESSION['usuario']['id'],
        ':contenido'      => $contenido,
        ':imagen'         => $nombreImagen,
        ':archivo_adjunto'=> $nombreArchivo
    ]);
}

// obtener información del usuario por ID

//Ver si ya sigue 
function yaSigue($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "SELECT id FROM seguimientos 
            WHERE seguidor_id = :seguidor AND seguido_id = :seguido";

    $stmt = $bd->prepare($sql);
    $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido
    ]);

    return $stmt->rowCount() > 0;
}

//(cuentas públicas)
function seguirUsuario($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "INSERT INTO seguimientos (seguidor_id, seguido_id, estado)
            VALUES (:seguidor, :seguido, 'aceptado')";

    $stmt = $bd->prepare($sql);
    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido
    ]);
}

// enviar solicitud de seguimiento 
function enviarSolicitudSeguir($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "INSERT INTO seguimientos (seguidor_id, seguido_id, estado)
            VALUES (:seguidor, :seguido, 'pendiente')";

    $stmt = $bd->prepare($sql);
    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido
    ]);
}

// obtener el estado de la solicitud de seguimiento
function obtenerEstadoSeguimiento($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "SELECT estado 
            FROM seguimientos
            WHERE seguidor_id = :seguidor AND seguido_id = :seguido";

    $stmt = $bd->prepare($sql);
    $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//aceptar solicitud del usuario
function aceptarSolicitud($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "UPDATE seguimientos
            SET estado = 'aceptado', fecha_respuesta = NOW()
            WHERE seguidor_id = :seguidor AND seguido_id = :seguido";

    $stmt = $bd->prepare($sql);
    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido
    ]);
}

//funcion para rechazar la solicitud de seguimiento
function rechazarSolicitud($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "UPDATE seguimientos
            SET estado = 'rechazado', fecha_respuesta = NOW()
            WHERE seguidor_id = :seguidor AND seguido_id = :seguido";

    $stmt = $bd->prepare($sql);
    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido
    ]);
}

// funcion de deja de seguir a un usuario 
function dejarDeSeguir($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "DELETE FROM seguimientos
            WHERE seguidor_id = :seguidor 
              AND seguido_id = :seguido
              AND estado = 'aceptado'";

    $stmt = $bd->prepare($sql);

    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido
    ]);
}

?>
