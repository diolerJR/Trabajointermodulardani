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
	if (empty($_SESSION['usuario']['id'])) {
		return false; // No hay usuario en sesión
	}

	$bd = conectarBS();

	$sql = "INSERT INTO posts (usuarioid, contenido, imagen, archivoadjunto)
            VALUES (:usuarioid, :contenido, :imagen, :archivoadjunto)";

	$stmt = $bd->prepare($sql);
	$stmt->bindValue(':usuarioid', $_SESSION['usuario']['id'], PDO::PARAM_INT);
	$stmt->bindValue(':contenido', $contenido, PDO::PARAM_STR);
	$stmt->bindValue(':imagen', $nombreImagen, PDO::PARAM_STR);
	$stmt->bindValue(':archivoadjunto', $nombreArchivo, PDO::PARAM_STR);

	return $stmt->execute();
}
function seguirUsuario($seguidor, $seguido) {
    global $conexion;

    $sql = "INSERT INTO seguimientos (seguidor_id, seguido_id, estado)
            VALUES (?, ?, 'aceptado')";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);

    return $stmt->execute();
}

function dejarDeSeguir($seguidor, $seguido) {
    global $conexion;

    $sql = "DELETE FROM seguimientos WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);

    return $stmt->execute();
}

function yaSigue($seguidor, $seguido) {
    global $conexion;

    $sql = "SELECT id FROM seguimientos WHERE seguidor_id = ? AND seguido_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $seguidor, $seguido);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}
?>