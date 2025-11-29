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


