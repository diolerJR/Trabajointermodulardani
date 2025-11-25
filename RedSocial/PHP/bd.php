<?php
// Incluyo los parámetros de conexión y creo el objeto PDO
include "configuracion_bd.php";
$bd = new PDO(
	"mysql:dbname=" . $bd_config["nombrebd"] . ";host=" . $bd_config["ip"],
	$bd_config["usuario"],
	$bd_config["clave"]
);

if (isset($_POST["usuario"]) && isset($_POST["clave"])) {

	$sql = "SELECT * FROM usuarios WHERE email='" . $_POST["usuario"] . "' 
                AND password_hash='" . $_POST["clave"] . "'";
	$filas = $bd->query($sql);

	// Compruebo si me llega alguna fila
	if ($filas->rowCount() == 0) {
		header("Location: login.php?error=1");
	} else {
		// Si estoy aquí, es que todo ha ido bien  
		foreach ($filas as $fila) {
			// Fila es un array donde cada columna es la clave
			$_SESSION["logueado"] = $fila["nombre"];
			$_SESSION["rol"] = $fila["rol"];
		}

		// Dependiendo del rol, le lanzo a un sitio u otro
		if ($_SESSION["rol"] == 0)
			header("Location: home.php");
		else
			header("Location: homeAdmin.php");
	}
}
