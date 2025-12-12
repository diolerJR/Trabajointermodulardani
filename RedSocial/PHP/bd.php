<?php
// Función para conectar con la base de datos
function conectarBS()
{
    static $bd = null;
    if ($bd === null) {
        // Incluyo los parámetros de conexión y creo el objeto PDO
        include "configuracion_bd.php";
        $bd = new PDO(
            "mysql:dbname=" . $bd_config["nombrebd"] . ";host=" . $bd_config["ip"],
            $bd_config["usuario"],
            $bd_config["clave"]
        );
        $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bd->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
    return $bd;
}

// Loguearse
function hacer_login($email, $pass)
{
    $bd = conectarBS();

    $sql = "SELECT id, nombre, email, password_hash, rol, fecha_nacimiento, ciudad, biografia, foto_perfil
			FROM usuarios 
			WHERE email= :email";

    $stmt = $bd->prepare($sql);
    $stmt->execute([
        ":email" => $email,
    ]);

    if ($stmt->rowCount() !== 1) {
        return false; // Si no nos devuelve una línea el usuario no existe
    }
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Para convertir las columnas en arrays asociativos

    if (!password_verify($pass, $usuario['password_hash'])) {
        return false;
    }

    $_SESSION["usuario"] = $usuario; //Almacenara toda la información recolectada de la sentencia SQL
    $_SESSION["nick"] = $usuario["nombre"];
    $_SESSION["rol"] = $usuario["rol"];

    return true;
}

// Crear usuario

// Obtener todos los Usuarios de la base de datos
function obtenerUsuarios()
{
    $bd = conectarBS();

    $sql = "SELECT id, nombre, email, rol, fecha_nacimiento, ciudad, biografia, foto_perfil
            FROM usuarios
            ORDER BY nombre ASC";

    $stmt = $bd->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener Post Usuario
function obtenerPostsUsuario($usuarioId)
{
    $bd = conectarBS();

    $sql = "SELECT id, contenido, fecha_publicacion, imagen, archivo_adjunto, visible, editado
            FROM posts
            WHERE usuario_id = :id AND visible = 1
            ORDER BY fecha_publicacion DESC";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Publicar POST
function crearPost($contenido, $nombreImagen = null, $nombreArchivo = null)
{
    $bd = conectarBS();

    $sql = "INSERT INTO posts (usuario_id, contenido, imagen, archivo_adjunto)
            VALUES (:usuarioid, :contenido, :imagen, :archivoadjunto)";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':usuarioid', $_SESSION['usuario']['id']);
    $stmt->bindValue(':contenido', $contenido);
    $stmt->bindValue(':imagen', $nombreImagen);
    $stmt->bindValue(':archivoadjunto', $nombreArchivo);

    return $stmt->execute();
}

// Buscar usuario / usuarios
function buscarUsuariosPorNombre($termino)
{
    $bd = conectarBS();
    $sql = "SELECT id, nombre, email, rol, fecha_nacimiento, ciudad, biografia, foto_perfil
            FROM usuarios
            WHERE nombre LIKE :nombre
            ORDER BY nombre ASC";
    $stmt = $bd->prepare($sql);
    $stmt->execute([':nombre' => $termino . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Actualizar información del Usuario

// Actualizar foto perfil | Borrar foto de perfil 
function cambiarFotoPerfil($rutaImagen)
{
    $bd = conectarBS();

    $sql = "UPDATE usuarios
            SET foto_perfil = :foto_perfil
            WHERE id = :id";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':foto_perfil', $rutaImagen);
    $stmt->bindValue(':id', $_SESSION['usuario']['id'], PDO::PARAM_INT);

    return $stmt->execute();
}
// Actualizar información del usuario
function actualizarPerfilUsuario($idUsuario, $nuevoNombre, $biografia, $fechaNacimiento, $ciudad)
{
    $bd = conectarBS();
    $sql = "UPDATE usuarios
            SET nombre = :nombre,
                biografia = :biografia,
                fecha_nacimiento = :fecha_nacimiento,
                ciudad = :ciudad
            WHERE id = :id";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':nombre', $nuevoNombre);
    $stmt->bindValue(':biografia', $biografia);
    $stmt->bindValue(':fecha_nacimiento', $fechaNacimiento);
    $stmt->bindValue(':ciudad', $ciudad);
    $stmt->bindValue(':id', $idUsuario, PDO::PARAM_INT);

    return $stmt->execute();
}

// Ver comentarios del Post
function obtenerComentariosPorPOst($postId)
{
    $bd = conectarBS();
    $sql = "SELECT c.*, u.nombre, u.foto_perfil
            FROM comentarios c
            JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.post_id = :id
            ORDER BY c.fecha_comentario ASC";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ver Post Seleccionado
function verPostPorID($postId)
{
    $bd = conectarBS();
    $sql = "SELECT p.*, u.nombre, u.foto_perfil, u.id as usuario_autor_id
            FROM posts p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = :id AND p.visible = 1";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Crear comentario
function crearComentario($postId, $usuarioId, $contenido)
{
    $bd = conectarBS();

    $sql = "INSERT INTO comentarios (post_id, usuario_id, contenido, fecha_comentario)
            VALUES (:post_id, :usuario_id, :contenido, NOW())"; // NOW() para fecha/hora actual

    $stmt = $bd->prepare($sql);
    return $stmt->execute([
        ':post_id' => $postId,
        ':usuario_id' => $usuarioId,
        ':contenido' => $contenido
    ]);
}


// ------------------------------------------------------------------------------ Cambiar PDO ------------------------------------------------------------------------------

// Seguir usuario (relación aceptada directamente)
function seguirUsuario($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "INSERT INTO seguimientos (seguidor_id, seguido_id, estado)
            VALUES (:seguidor, :seguido, 'aceptado')";
    $stmt = $bd->prepare($sql);

    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido,
    ]);
}

// Comprobar si ya sigue
function yaSigue($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "SELECT id
            FROM seguimientos
            WHERE seguidor_id = :seguidor
              AND seguido_id  = :seguido
              AND estado = 'aceptado'";
    $stmt = $bd->prepare($sql);
    $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido,
    ]);

    return $stmt->rowCount() > 0;
}

// Enviar solicitud de seguir (pendiente)
function enviarSolicitudSeguir($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "INSERT INTO seguimientos (seguidor_id, seguido_id, estado)
            VALUES (:seguidor, :seguido, 'pendiente')";
    $stmt = $bd->prepare($sql);

    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido,
    ]);
}

// Obtener estado de seguimiento
function obtenerEstadoSeguimiento($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "SELECT estado
            FROM seguimientos
            WHERE seguidor_id = :seguidor
              AND seguido_id  = :seguido";
    $stmt = $bd->prepare($sql);
    $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido,
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC); // ['estado' => '...'] o false
}

// Aceptar solicitud
function aceptarSolicitud($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "UPDATE seguimientos
            SET estado = 'aceptado', fecha_respuesta = NOW()
            WHERE seguidor_id = :seguidor
              AND seguido_id  = :seguido";
    $stmt = $bd->prepare($sql);

    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido,
    ]);
}

// Rechazar solicitud
function rechazarSolicitud($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "UPDATE seguimientos
            SET estado = 'rechazado', fecha_respuesta = NOW()
            WHERE seguidor_id = :seguidor
              AND seguido_id  = :seguido";
    $stmt = $bd->prepare($sql);

    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido,
    ]);
}

// Dejar de seguir (eliminar relación aceptada)
function dejarDeSeguir($seguidor, $seguido)
{
    $bd = conectarBS();

    $sql = "DELETE FROM seguimientos
            WHERE seguidor_id = :seguidor
              AND seguido_id  = :seguido
              AND estado = 'aceptado'";
    $stmt = $bd->prepare($sql);

    return $stmt->execute([
        ':seguidor' => $seguidor,
        ':seguido'  => $seguido,
    ]);
}

// Contar seguidores de un usuario
function contarSeguidores($usuarioId)
{
    $bd = conectarBS();

    $sql = "SELECT COUNT(*) FROM seguimientos
            WHERE seguido_id = :id AND estado = 'aceptado'";
    $stmt = $bd->prepare($sql);
    $stmt->execute([':id' => $usuarioId]);

    return (int) $stmt->fetchColumn();
}

// Contar a cuántos sigue un usuario
function contarSiguiendo($usuarioId)
{
    $bd = conectarBS();

    $sql = "SELECT COUNT(*) FROM seguimientos
            WHERE seguidor_id = :id AND estado = 'aceptado'";
    $stmt = $bd->prepare($sql);
    $stmt->execute([':id' => $usuarioId]);

    return (int) $stmt->fetchColumn();
}
?>