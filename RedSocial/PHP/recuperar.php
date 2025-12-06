<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
    <link href="../CSS/login.css" rel="stylesheet">
</head>

<body>

<div class="login-container">
    <h2>Recuperar contraseña</h2>

    <?php 
    if (isset($_GET["enviado"])) {
        echo "<p style='color:green;'>Si el correo existe, se ha enviado un enlace de recuperación.</p>";
    }
    ?>

    <form method="POST" action="enviar_token.php">
        <label>Email asociado a tu cuenta:</label>
        <input type="email" name="email" required>

        <button type="submit">Enviar enlace</button>
    </form>

</div>

</body>
</html>
