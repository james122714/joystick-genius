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

// Consultar estadísticas generales
$stats = [
    'total_admin' => $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'admin'")->fetch_assoc()['total'],
    'total_lanzamientos' => $conexion->query("SELECT COUNT(*) as total FROM game_launches")->fetch_assoc()['total'],
    'total_videojuegos' => $conexion->query("SELECT COUNT(*) as total FROM games")->fetch_assoc()['total'],
    'total_noticias' => $conexion->query("SELECT COUNT(*) as total FROM noticias")->fetch_assoc()['total'],
    'total_tutoriales' => $conexion->query("SELECT COUNT(*) as total FROM tutoriales")->fetch_assoc()['total']
];

// Consultar todos los usuarios con tipo_usuario admin, moderador o adminvista
$consulta = "SELECT u.*, 
            (SELECT COUNT(*) FROM sesiones WHERE usuario_id = u.id) as total_sesiones,
            (SELECT fecha FROM sesiones WHERE usuario_id = u.id ORDER BY fecha DESC LIMIT 1) as ultima_sesion
            FROM usuarios u
            WHERE u.tipo_usuario IN ('admin', 'moderador', 'adminvista')
            ORDER BY u.id DESC";
$resultado = $conexion->query($consulta);

// Determinar permisos según el tipo de usuario logueado
$tipo_logueado = $_SESSION['tipo_usuario'];
$permite_crear = ($tipo_logueado === 'admin'); // Solo admin puede crear
$permite_editar = in_array($tipo_logueado, ['admin', 'moderador']); // Admin y moderador pueden editar
$permite_eliminar = ($tipo_logueado === 'admin'); // Solo admin puede eliminar
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/admin.css">
    <title>GamersHub Admin - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .profile-avatar-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .profile-name {
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
        }
        
        .avatar {
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo">
                <h2>GamersHub Admin</h2>
            </div>
            
            <div class="nav-menu">
                <div class="nav-item">
                    <a href="administracion.php" class="nav-link active">
                        <span class="nav-icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="administradores.php" class="nav-link">
                        <span class="nav-icon">👑</span>
                        <span>Administradores</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="usuarios.php" class="nav-link">
                        <span class="nav-icon">👥</span>
                        <span>Usuarios</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="noticias.php" class="nav-link">
                        <span class="nav-icon">📰</span>
                        <span>Noticias</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="videojuegos.php" class="nav-link">
                        <span class="nav-icon">🎮</span>
                        <span>Videojuegos</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="lanzamiento.php" class="nav-link">
                        <span class="nav-icon">🚀</span>
                        <span>Lanzamientos</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="tutorial.php" class="nav-link">
                        <span class="nav-icon">📚</span>
                        <span>Tutoriales</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="estadisticas.php" class="nav-link">
                        <span class="nav-icon">📊</span>
                        <span>Estadísticas</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="logout.php" class="nav-link" style="color: #ef4444;">
                        <span class="nav-icon">🚪</span>
                        <span>Salir</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="principal.php" class="nav-link" style="color: #10b981;">
                        <span class="nav-icon">🏠</span>
                        <span>Ir a Principal</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1>Administradores</h1>
                <div class="user-profile">
                    <?php if (!empty($usuario_info['foto_perfil']) && file_exists('../vista/' . $usuario_info['foto_perfil'])): ?>
                        <img src="../vista/<?php echo htmlspecialchars($usuario_info['foto_perfil']); ?>" 
                             alt="Foto de perfil" 
                             class="profile-avatar-img">
                    <?php else: ?>
                        <div class="avatar"><?php echo substr($usuario_info['nombre'] ?? $_SESSION['nombre'], 0, 2); ?></div>
                    <?php endif; ?>
                    <span class="profile-name"><?php echo htmlspecialchars($usuario_info['nombre'] ?? $_SESSION['nombre']); ?></span>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Total Administradores</h3>
                        <div class="stat-icon admin">👑</div>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_admin']; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Lanzamientos</h3>
                        <div class="stat-icon launches">🚀</div>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_lanzamientos']; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Videojuegos</h3>
                        <div class="stat-icon games">🎮</div>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_videojuegos']; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Noticias</h3>
                        <div class="stat-icon news">📰</div>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_noticias']; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Tutoriales</h3>
                        <div class="stat-icon tutorials">📚</div>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_tutoriales']; ?></div>
                </div>
            </div>

            <!-- Admin Table -->
            <div class="admin-table">
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
                            <?php while ($admin = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $admin['id']; ?></td>
                                    <td><?php echo htmlspecialchars($admin['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td><?php echo $admin['ultima_sesion'] ? date('d/m/Y H:i', strtotime($admin['ultima_sesion'])) : 'Nunca'; ?></td>
                                    <td><span class="status-<?php echo $admin['estado']; ?>"><?php echo ucfirst($admin['estado']); ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($permite_editar): ?>
                                                <a href="editar_admin.php?id=<?php echo $admin['id']; ?>" class="action-button">Editar</a>
                                            <?php endif; ?>
                                            <?php if ($tipo_logueado === 'admin'): ?>
                                                <a href="permisos.php?id=<?php echo $admin['id']; ?>" class="action-button">Permisos</a>
                                            <?php endif; ?>
                                            <?php if ($permite_eliminar && $admin['id'] != $_SESSION['usuario_id']): ?>
                                                <a href="eliminar_admin.php?id=<?php echo $admin['id']; ?>" class="action-button" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php
if (isset($stmt_usuario)) {
    $stmt_usuario->close();
}
$conexion->close();
?>