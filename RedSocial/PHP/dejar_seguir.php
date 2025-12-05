<?php
session_start();
include "bd.php";

$yo = $_SESSION["id"];
$otro = $_GET["id"];

dejarDeSeguir($yo, $otro);

header("Location: perfil.php?id=$otro");
exit;
?>
