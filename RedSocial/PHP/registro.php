<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Red Social</title>
    <link rel="stylesheet" href="../CSS/registro.css"> 
</head>
<body>

    <form action="procesar_registro.php" method="POST" class="form">
        
        <p id="heading">Crear Cuenta</p>
        
        <div class="field">
            <input autocomplete="off" placeholder="Nombre de usuario" class="input-field" type="text" name="usu" required>
        </div>

        <div class="field">
            <input autocomplete="off" placeholder="Email" class="input-field" type="email" name="mail" required>
        </div>

        <div class="field">
            <input placeholder="Contraseña" class="input-field" type="password" name="cla" required>
        </div>

        <div class="field">
            <input placeholder="Repetir Contraseña" class="input-field" type="password" name="claveconfirm" required>
        </div>
        
        <div class="btn">
            <button type="submit" class="button3">Crear Cuenta</button>
        </div>
        
        <p style="text-align:center; margin-top:10px;">
            <a href="login.php" style="color: #d3d3d3; text-decoration: none;">¿Ya tienes cuenta? Inicia sesión</a>
        </p>
    </form>

</body>
</html>