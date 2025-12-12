<?php
session_start();
require_once 'bd.php';
$bd = conectarBS();

// Validar sesión: cualquier usuario logueado puede acceder
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php?redirigido=1");
    exit;
}

$idUsuario = $_SESSION["usuario"]["id"];

// Marcar notificación como leída si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_leida'])) {
    $idNotificacion = intval($_POST['id_notificacion']);
    marcarNotificacionLeida($idNotificacion, $idUsuario);
    header('Location: notificaciones.php'); // recargar la página
    exit;
}

// Obtener todas las notificaciones (ordenadas por fecha descendente)
$sql = "SELECT id, tipo, mensaje, creada_en, leida 
        FROM notificaciones 
        WHERE usuario_id = :usuario_id
        ORDER BY creada_en DESC";

$stmt = $bd->prepare($sql);
$stmt->execute([':usuario_id' => $idUsuario]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contador de no leídas
$numNotif = contarNotificacionesNoLeidas($idUsuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - Red Social</title>
    <link href="../CSS/perfiles.css" rel="stylesheet">
    <style>
        .notificacion { padding: 10px; border-bottom: 1px solid #ccc; }
        .leida { background-color: #f0f0f0; }
        .no-leida { background-color: #e0ffe0; font-weight: bold; }
        .btn-leer { margin-left: 10px; }
    </style>
</head>
<body>
<?php include 'menu.php'; ?>

<div class="container">
    <h1>Notificaciones <?php if($numNotif>0) echo "($numNotif sin leer)"; ?></h1>

    <?php if (empty($notificaciones)): ?>
        <p>No tienes notificaciones.</p>
    <?php else: ?>
        <?php foreach ($notificaciones as $notif): ?>
            <div class="notificacion <?= $notif['leida'] ? 'leida' : 'no-leida' ?>">
                <span><?= htmlspecialchars($notif['mensaje']) ?></span>
                <small style="float:right; color:#666;"><?= $notif['creada_en'] ?></small>
                <?php if (!$notif['leida']): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_notificacion" value="<?= $notif['id'] ?>">
                        <button type="submit" name="marcar_leida" class="btn-leer">Marcar como leída</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
