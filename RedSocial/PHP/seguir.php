<?php
session_start();
include "bd.php";

$yo = $_SESSION["id"];
$otro = $_GET["id"];

seguirUsuario($yo, $otro);

header("Location: perfil.php?id=$otro");
exit;

function seguirUsuario($yo, $otro) {
    // Add your implementation here
    // Example: Insert follow relationship into database
}
?>
