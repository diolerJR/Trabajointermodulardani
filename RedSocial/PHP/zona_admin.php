<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 1) {
    
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Red Social</title>
    <link href="css/zona_admin.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="admin.php" class="nav-logo">Panel Admin</a>
            <ul class="nav-menu">
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="timeline.php">Ver Red Social</a></li>
                <li><a href="logout.php">Salir</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Panel de Administración</h1>
            <p>Bienvenido, Administrador</p>
        </div>
        
        <div class="estadisticas-admin">
            <div class="stat-card">
                <div class="stat-icon">Usuarios</div>
                <div class="stat-numero">150</div>
                <div class="stat-label">Total usuarios</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">Posts</div>
                <div class="stat-numero">1,234</div>
                <div class="stat-label">Total publicaciones</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">Comentarios</div>
                <div class="stat-numero">3,456</div>
                <div class="stat-label">Total comentarios</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">Activos</div>
                <div class="stat-numero">89</div>
                <div class="stat-label">Usuarios activos hoy</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Gestión de Usuarios</h2>
                <div class="card-acciones">
                    <input type="text" placeholder="Buscar usuario..." class="input-buscar">
                    <button class="btn-buscar">Buscar</button>
                </div>
            </div>
            
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><img src="uploads/avatars/admin.jpg" class="avatar-mini" alt="Avatar"></td>
                        <td>
                            Administrador
                            <span class="badge-verificado">Verificado</span>
                        </td>
                        <td>admin@redsocial.com</td>
                        <td><span class="badge admin">Admin</span></td>
                        <td><span class="badge activo">Activo</span></td>
                        <td>01/01/2024</td>
                        <td>
                            <button class="btn-accion ver" title="Ver perfil">Ver</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>2</td>
                        <td><img src="uploads/avatars/user1.jpg" class="avatar-mini" alt="Avatar"></td>
                        <td>María López</td>
                        <td>maria@example.com</td>
                        <td><span class="badge usuario">Usuario</span></td>
                        <td><span class="badge activo">Activo</span></td>
                        <td>15/03/2024</td>
                        <td>
                            <button class="btn-accion ver" title="Ver perfil">Ver</button>
                            <button class="btn-accion bloquear" title="Bloquear usuario">Bloquear</button>
                            <button class="btn-accion eliminar" title="Eliminar usuario">Eliminar</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>3</td>
                        <td><img src="uploads/avatars/user2.jpg" class="avatar-mini" alt="Avatar"></td>
                        <td>Carlos García</td>
                        <td>carlos@example.com</td>
                        <td><span class="badge usuario">Usuario</span></td>
                        <td><span class="badge bloqueado">Bloqueado</span></td>
                        <td>20/05/2024</td>
                        <td>
                            <button class="btn-accion ver" title="Ver perfil">Ver</button>
                            <button class="btn-accion desbloquear" title="Desbloquear usuario">Desbloquear</button>
                            <button class="btn-accion eliminar" title="Eliminar usuario">Eliminar</button>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>4</td>
                        <td><img src="uploads/avatars/user3.jpg" class="avatar-mini" alt="Avatar"></td>
                        <td>Ana Martínez</td>
                        <td>ana@example.com</td>
                        <td><span class="badge usuario">Usuario</span></td>
                        <td><span class="badge activo">Activo</span></td>
                        <td>10/08/2024</td>
                        <td>
                            <button class="btn-accion ver" title="Ver perfil">Ver</button>
                            <button class="btn-accion bloquear" title="Bloquear usuario">Bloquear</button>
                            <button class="btn-accion eliminar" title="Eliminar usuario">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="paginacion">
                <button class="btn-pag">Anterior</button>
                <span class="pag-info">Página 1 de 5</span>
                <button class="btn-pag">Siguiente</button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Gestión de Publicaciones</h2>
                <div class="card-filtros">
                    <select class="select-filtro">
                        <option>Todas</option>
                        <option>Visibles</option>
                        <option>Ocultas</option>
                        <option>Reportadas</option>
                    </select>
                </div>
            </div>
            
            <div class="lista-posts-admin">
                <div class="post-admin">
                    <div class="post-admin-header">
                        <img src="uploads/avatars/user1.jpg" class="avatar" alt="Avatar">
                        <div class="post-admin-info">
                            <strong>María López</strong>
                            <span class="fecha">29/11/2025 - 15:30</span>
                            <span class="badge visible">Visible</span>
                        </div>
                    </div>
                    
                    <div class="post-admin-contenido">
                        <p>Esta es una publicación de ejemplo que puede contener texto e imágenes...</p>
                        <img src="uploads/posts/post1.jpg" class="post-imagen-mini" alt="Imagen">
                    </div>
                    
                    <div class="post-admin-stats">
                        <span>15 Me gusta</span>
                        <span>8 Comentarios</span>
                        <span>ID: #1234</span>
                    </div>
                    
                    <div class="post-admin-acciones">
                        <button class="btn-admin ver">Ver completo</button>
                        <button class="btn-admin ocultar">Ocultar</button>
                        <button class="btn-admin eliminar">Eliminar</button>
                    </div>
                </div>
                
                <div class="post-admin">
                    <div class="post-admin-header">
                        <img src="uploads/avatars/user2.jpg" class="avatar" alt="Avatar">
                        <div class="post-admin-info">
                            <strong>Carlos García</strong>
                            <span class="fecha">29/11/2025 - 12:00</span>
                            <span class="badge oculto">Oculto</span>
                        </div>
                    </div>
                    
                    <div class="post-admin-contenido">
                        <p>Otra publicación interesante en la red social...</p>
                    </div>
                    
                    <div class="post-admin-stats">
                        <span>42 Me gusta</span>
                        <span>15 Comentarios</span>
                        <span>ID: #1233</span>
                    </div>
                    
                    <div class="post-admin-acciones">
                        <button class="btn-admin ver">Ver completo</button>
                        <button class="btn-admin mostrar">Mostrar</button>
                        <button class="btn-admin eliminar">Eliminar</button>
                    </div>
                </div>
                
                <div class="post-admin reportado">
                    <div class="post-admin-header">
                        <img src="uploads/avatars/user3.jpg" class="avatar" alt="Avatar">
                        <div class="post-admin-info">
                            <strong>Ana Martínez</strong>
                            <span class="fecha">28/11/2025 - 18:30</span>
                            <span class="badge reportado">Reportado (3)</span>
                        </div>
                    </div>
                    
                    <div class="post-admin-contenido">
                        <p>Publicación que ha sido reportada por usuarios...</p>
                    </div>
                    
                    <div class="post-admin-stats">
                        <span>5 Me gusta</span>
                        <span>2 Comentarios</span>
                        <span>ID: #1232</span>
                    </div>
                    
                    <div class="post-admin-acciones">
                        <button class="btn-admin ver">Ver completo</button>
                        <button class="btn-admin ver-reportes">Ver reportes</button>
                        <button class="btn-admin ocultar">Ocultar</button>
                        <button class="btn-admin eliminar">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Comentarios Reportados</h2>
            </div>
            
            <div class="lista-comentarios-admin">
                <div class="comentario-admin">
                    <div class="comentario-info">
                        <img src="uploads/avatars/user4.jpg" class="avatar-mini" alt="Avatar">
                        <div>
                            <strong>Pedro Sánchez</strong>
                            <span class="fecha">29/11/2025 - 14:00</span>
                            <span class="badge reportado">Reportado (2)</span>
                        </div>
                    </div>
                    <p class="comentario-texto">Este es un comentario que ha sido reportado por usuarios...</p>
                    <div class="comentario-acciones">
                        <button class="btn-admin ver">Ver post</button>
                        <button class="btn-admin eliminar">Eliminar comentario</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Registro de Actividad</h2>
            </div>
            
            <div class="lista-logs">
                <div class="log-item">
                    <div class="log-icono eliminar">Eliminar</div>
                    <div class="log-info">
                        <p><strong>Administrador</strong> eliminó al usuario <strong>Pedro Sánchez</strong></p>
                        <small>29/11/2025 - 16:45</small>
                    </div>
                </div>
                
                <div class="log-item">
                    <div class="log-icono bloquear">Bloquear</div>
                    <div class="log-info">
                        <p><strong>Administrador</strong> bloqueó al usuario <strong>Carlos García</strong></p>
                        <small>29/11/2025 - 15:30</small>
                    </div>
                </div>
                
                <div class="log-item">
                    <div class="log-icono eliminar">Eliminar</div>
                    <div class="log-info">
                        <p><strong>Administrador</strong> eliminó la publicación ID: #1234</p>
                        <small>29/11/2025 - 14:20</small>
                    </div>
                </div>
                
                <div class="log-item">
                    <div class="log-icono ocultar">Ocultar</div>
                    <div class="log-info">
                        <p><strong>Administrador</strong> ocultó el comentario ID: #5678</p>
                        <small>29/11/2025 - 12:15</small>
                    </div>
                </div>
                
                <div class="log-item">
                    <div class="log-icono desbloquear">Desbloquear</div>
                    <div class="log-info">
                        <p><strong>Administrador</strong> desbloqueó al usuario <strong>Ana Martínez</strong></p>
                        <small>28/11/2025 - 18:00</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
