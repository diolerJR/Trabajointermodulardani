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
	}
	return $bd;
}

// Loguearse
function hacer_login($email, $pass)
{
	$bd = conectarBS();
	$hashPass = hash('sha256', $pass);

	$sql = "SELECT id, nombre, email, password_hash, rol, fecha_nacimiento, ciudad, biografia, foto_perfil
			FROM usuarios 
			WHERE email= :email 
			AND password_hash=:pass";

	$stmt = $bd->prepare($sql);
	$stmt->execute([
		":email" => $email,
		":pass" => $hashPass
	]);

	if ($stmt->rowCount() !== 1) {
		return false; // Si no nos devuelve una línea el usuario no existe
	}
	$usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Para convertir las columnas en arrays asociativos

	$_SESSION["usuario"] = $usuario; //Almacenara toda la información recolectada de la sentencia SQL
	$_SESSION["nick"] = $usuario["nombre"];
	$_SESSION["rol"] = $usuario["rol"];

	return true;
}

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

// Seguir Usuario
function seguirUsuario($seguidor, $seguido) {
    global $conexion;

    $sql = "INSERT INTO seguimientos (seguidor_id, seguido_id, estado)
            VALUES (?, ?, 'aceptado')";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);

    return $stmt->execute();
}

// Comprobar si ya Sigue
function yaSigue($seguidor, $seguido) {
    global $conexion;

    $sql = "SELECT id FROM seguimientos WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    $stmt->execute();
    $result = $stmt->get_result();

	return $result->num_rows > 0;
}

// funciones de seguimiento de los usuarios (aceptar,rechazar, dejar de seguir)
function enviarSolicitudSeguir($seguidor, $seguido) {
    global $conexion;

    $sql = "INSERT INTO seguimientos (seguidor_id, seguido_id, estado)
            VALUES (?, ?, 'pendiente')";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);

    return $stmt->execute();
}
function obtenerEstadoSeguimiento($seguidor, $seguido) {
    global $conexion;

    $sql = "SELECT estado FROM seguimientos 
            WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc(); 
}
function aceptarSolicitud($seguidor, $seguido) {
    global $conexion;

    $sql = "UPDATE seguimientos 
            SET estado = 'aceptado', fecha_respuesta = NOW()
            WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    return $stmt->execute();
}
function rechazarSolicitud($seguidor, $seguido) {
    global $conexion;

    $sql = "UPDATE seguimientos 
            SET estado = 'rechazado', fecha_respuesta = NOW()
            WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    return $stmt->execute();
}
function dejarDeSeguir($seguidor, $seguido) {
    global $conexion;

    $sql = "DELETE FROM seguimientos 
            WHERE seguidor_id = ? 
            AND seguido_id = ? 
            AND estado = 'aceptado'";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    return $stmt->execute();
}
?>
