<?php
session_start();

// Verificar autenticación y permitir acceso a admin, moderador y adminvista
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';
$conexion = $conexion;

// Obtener información del usuario logueado incluyendo foto de perfil
$stmt_usuario = $conexion->prepare("SELECT nombre, foto_perfil FROM usuarios WHERE id = ?");
$stmt_usuario->bind_param("i", $_SESSION['usuario_id']);
$stmt_usuario->execute();
$usuario_info = $stmt_usuario->get_result()->fetch_assoc();

// Configuración de paginación
$usuarios_por_pagina = 10; // Número de usuarios por página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $usuarios_por_pagina;

// Obtener total de usuarios para calcular páginas
$total_usuarios_query = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'usuario'";
$total_usuarios_result = $conexion->query($total_usuarios_query);
$total_usuarios = $total_usuarios_result->fetch_assoc()['total'];
$total_paginas = ceil($total_usuarios / $usuarios_por_pagina);

// Consultar usuarios regulares con información adicional, con paginación
$consulta = "SELECT u.*, 
            (SELECT COUNT(*) FROM sesiones WHERE usuario_id = u.id) as total_sesiones,
            (SELECT fecha FROM sesiones WHERE usuario_id = u.id ORDER BY fecha DESC LIMIT 1) as ultima_sesion
            FROM usuarios u
            WHERE u.tipo_usuario = 'usuario'
            ORDER BY u.id DESC
            LIMIT ? OFFSET ?";
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("ii", $usuarios_por_pagina, $offset);
$stmt->execute();
$resultado = $stmt->get_result();

// Determinar permisos según el tipo de usuario logueado
$tipo_logueado = $_SESSION['tipo_usuario'];
$permite_crear = ($tipo_logueado === 'admin'); // Solo admin puede crear
$permite_editar = in_array($tipo_logueado, ['admin', 'moderador']); // Admin y moderador pueden editar
$permite_eliminar = ($tipo_logueado === 'admin'); // Solo admin puede eliminar

// Estadísticas de usuarios
$stats = [
    'total_usuarios' => $total_usuarios,
    'usuarios_activos' => $conexion->query("SELECT COUNT(DISTINCT usuario_id) as total FROM sesiones WHERE usuario_id IN (SELECT id FROM usuarios WHERE tipo_usuario = 'usuario') AND fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['total'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <title>Gestión de Usuarios - Pixel Play</title>
    <link rel="stylesheet" href="../vista/css/administracion.css">
    <style>
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .user-name {
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            color: #333;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .pagination a:hover {
            background-color: #f0f0f0;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        .pagination a.disabled {
            color: #ccc;
            pointer-events: none;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <div class="logo">GamersHub Admin</div>
        <div class="menu-item">
            <a href="administracion.php">
                <span class="menu-icon">🏠</span>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="administradores.php">
                <span class="menu-icon">⭐</span>
                <span>Administradores</span>
            </a>
        </div>
        <div class="menu-item active">
            <a href="usuarios.php">
                <span class="menu-icon">👥</span>
                <span>Usuarios</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="noticias.php">
                <span class="menu-icon">📰</span>
                <span>Noticias</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="videojuegos.php">
                <span class="menu-icon">🎮</span>
                <span>Juegos</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="lanzamiento.php">
                <span class="menu-icon">🎮</span>
                <span>Lanzamientos</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="tutorial.php">
                <span class="menu-icon">🆕</span>
                <span>Tutoriales</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="estadisticas.php">
                <span class="menu-icon">📊</span>
                <span>Estadísticas</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="logout.php" style="color: #ff4444;">
                <span class="menu-icon">🚪</span>
                <span>Salir</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="principal.php" class="nav-link" style="color: #10b981;">
                <span class="nav-icon">🏠</span>
                <span>Ir a Principal</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Gestión de Usuarios</h1>
            <div class="user-info">
                <?php if (!empty($usuario_info['foto_perfil']) && file_exists('../vista/' . $usuario_info['foto_perfil'])): ?>
                    <img src="../vista/<?php echo htmlspecialchars($usuario_info['foto_perfil']); ?>" 
                         alt="Foto de perfil" 
                         class="user-avatar-img">
                <?php else: ?>
                    <div class="user-avatar"><?php echo substr($usuario_info['nombre'] ?? $_SESSION['nombre'], 0, 2); ?></div>
                <?php endif; ?>
                <span class="user-name"><?php echo htmlspecialchars($usuario_info['nombre'] ?? $_SESSION['nombre']); ?></span>
            </div>
        </div>

        <!-- Mostrar mensaje de sesión si existe -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_SESSION['mensaje']); ?></div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <!-- Tarjetas de estadísticas -->
        <div class="admin-cards">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Total Usuarios</h3>
                    <span class="menu-icon">👥</span>
                </div>
                <div class="card-number"><?php echo $stats['total_usuarios']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Usuarios Activos</h3>
                    <span class="menu-icon">👤</span>
                </div>
                <div class="card-number"><?php echo $stats['usuarios_activos']; ?></div>
            </div>
        </div>

        <div class="admin-table">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Lista de Usuarios</h2>
                <?php if ($permite_crear): ?>
                    <a href="crear_usuario.php" class="add-admin-button">+ Agregar Usuario</a>
                <?php endif; ?>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Último acceso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $usuario['id']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo $usuario['ultima_sesion'] ? date('d/m/Y H:i', strtotime($usuario['ultima_sesion'])) : 'Nunca'; ?></td>
                            <td>
                                <span class="status-<?php echo $usuario['estado']; ?>">
                                    <?php echo ucfirst($usuario['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($permite_editar): ?>
                                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="action-button">Editar</a>
                                    <?php endif; ?>
                                    <?php if ($permite_eliminar && $usuario['id'] != $_SESSION['usuario_id']): ?>
                                        <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>" class="action-button" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <a href="?pagina=<?php echo $pagina_actual - 1; ?>">Anterior</a>
                <?php else: ?>
                    <a class="disabled">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?pagina=<?php echo $i; ?>" class="<?php echo ($i == $pagina_actual) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?pagina=<?php echo $pagina_actual + 1; ?>">Siguiente</a>
                <?php else: ?>
                    <a class="disabled">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
if (isset($stmt)) {
    $stmt->close();
}
if (isset($stmt_usuario)) {
    $stmt_usuario->close();
}
$conexion->close();
?>