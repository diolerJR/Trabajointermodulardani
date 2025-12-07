<div class="post">
    <div class="post-header">
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar">
        <div>
            <h3><?php echo htmlspecialchars($nombre); ?></h3>
            <span class="fecha"><?php echo htmlspecialchars($fecha); ?></span>
        </div>
    </div>

    <div class="post-contenido">
        <?php echo nl2br(htmlspecialchars($contenido)); ?>
    </div>

    <?php if (!empty($imagenPost)): ?>
        <div class="post-imagen">
            <img src="<?php echo htmlspecialchars($imagenPost); ?>" alt="Imagen del post">
        </div>
    <?php endif; ?>
    <?php if (!empty($archivoAdjunto)): ?>
        <div class="archivo-adjunto">
            <a href="<?php echo htmlspecialchars($archivoAdjunto); ?>" target="_blank">
                Ver archivo adjunto (PDF)
            </a>
        </div>
    <?php endif; ?>

    <div class="post-footer">
        <div class="reacciones">
            <button class="btn-reaccion like">Me gusta <span>23</span></button>
            <button class="btn-reaccion dislike">No me gusta <span>1</span></button>
            <button class="btn-reaccion love">Me encanta <span>12</span></button>
            <button class="btn-reaccion wow">Wow <span>5</span></button>
        </div>

        <div class="post-stats">
            <a href="post.php?id=2" class="link-comentarios">
                Ver comentarios (12)
            </a>
        </div>

        <div class="post-acciones">
            <a href="editar_post.php?id=2" class="btn-editar">Editar</a>
            <a href="eliminar_post.php?id=2" class="btn-eliminar"
                onclick="return confirm('¿Eliminar esta publicación?')">Eliminar</a>
        </div>

    </div>
</div>