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

// Obtener estadísticas generales
$stats = [
    // Estadísticas de Lanzamientos
    'lanzamientos' => [
        'total' => $conexion->query("SELECT COUNT(*) as total FROM game_launches")->fetch_assoc()['total'],
        'pre_order' => $conexion->query("SELECT COUNT(*) as total FROM game_launches WHERE pre_order = 1")->fetch_assoc()['total'],
        'proximos' => $conexion->query("SELECT COUNT(*) as total FROM game_launches WHERE release_date > CURDATE()")->fetch_assoc()['total'],
        'hoy' => $conexion->query("SELECT COUNT(*) as total FROM game_launches WHERE DATE(release_date) = CURDATE()")->fetch_assoc()['total']
    ],
    // Estadísticas de Administradores
    'administradores' => [
        'total' => $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario IN ('admin', 'moderador', 'adminvista')")->fetch_assoc()['total'],
        'activos' => $conexion->query("SELECT COUNT(DISTINCT usuario_id) as total FROM sesiones WHERE usuario_id IN (SELECT id FROM usuarios WHERE tipo_usuario IN ('admin', 'moderador', 'adminvista')) AND fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['total'],
        'super_admins' => $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'admin'")->fetch_assoc()['total']
    ],
    // Estadísticas de Noticias
    'noticias' => [
        'total' => $conexion->query("SELECT COUNT(*) as total FROM noticias")->fetch_assoc()['total'],
        'destacadas' => $conexion->query("SELECT COUNT(*) as total FROM noticias WHERE destacada = 1")->fetch_assoc()['total'],
        'hoy' => $conexion->query("SELECT COUNT(*) as total FROM noticias WHERE DATE(fecha_publicacion) = CURDATE()")->fetch_assoc()['total']
    ],
    // Estadísticas de Tutoriales
    'tutoriales' => [
        'total' => $conexion->query("SELECT COUNT(*) as total FROM tutoriales")->fetch_assoc()['total'],
        'hoy' => $conexion->query("SELECT COUNT(*) as total FROM tutoriales WHERE DATE(fecha_creacion) = CURDATE()")->fetch_assoc()['total'],
        'programacion' => $conexion->query("SELECT COUNT(*) as total FROM tutoriales WHERE categoria = 'Programación'")->fetch_assoc()['total']
    ],
    // Estadísticas de Usuarios
    'usuarios' => [
        'total' => $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'usuario'")->fetch_assoc()['total'],
        'activos' => $conexion->query("SELECT COUNT(DISTINCT usuario_id) as total FROM sesiones WHERE usuario_id IN (SELECT id FROM usuarios WHERE tipo_usuario = 'usuario') AND fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['total']
    ],
    // Estadísticas de Videojuegos
    'videojuegos' => [
        'total' => $conexion->query("SELECT COUNT(*) as total FROM games")->fetch_assoc()['total'],
        'hoy' => $conexion->query("SELECT COUNT(*) as total FROM games WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['total']
    ]
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/estadisticas.css">
    <link rel="stylesheet" href="../vista/css/admin.css">
    <link rel="stylesheet" href="../vista/css/administrador.css">
    <title>Estadísticas Generales - joystick_genius Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
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
            color: #bb0000ff;
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

        .stats-section h2 {
            color: #ffffff;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">GamersHub Admin</div>
        <div class="nav-menu">
                <div class="menu-item">
                    <a href="administracion.php" class="menu-link">
                        <span class="menu-icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="administradores.php" class="menu-link">
                        <span class="menu-icon">👑</span>
                        <span>Administradores</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="usuarios.php" class="menu-link">
                        <span class="menu-icon">👥</span>
                        <span>Usuarios</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="noticias.php" class="menu-link">
                        <span class="menu-icon">📰</span>
                        <span>Noticias</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="videojuegos.php" class="menu-link">
                        <span class="menu-icon">🎮</span>
                        <span>Videojuegos</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="lanzamiento.php" class="menu-link">
                        <span class="menu-icon">🚀</span>
                        <span>Lanzamientos</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="tutorial.php" class="menu-link">
                        <span class="menu-icon">📚</span>
                        <span>Tutoriales</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="estadisticas.php" class="menu-link active">
                        <span class="menu-icon">📊</span>
                        <span>Estadísticas</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="logout.php" class="menu-link" style="color: #ef4444;">
                        <span class="menu-icon">🚪</span>
                        <span>Salir</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="principal.php" class="menu-link" style="color: #10b981;">
                        <span class="menu-icon">🏠</span>
                        <span>Ir a Principal</span>
                    </a>
                </div>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title" style="color:white">Estadísticas Generales</h1>
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

        <!-- Sección de Lanzamientos -->
        <div class="stats-section">
            <h2>Estadísticas de Lanzamientos</h2>
            <div class="admin-cards">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Lanzamientos</h3>
                        <span class="menu-icon">🎮</span>
                    </div>
                    <div class="card-number"><?php echo $stats['lanzamientos']['total']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Pre-Orders</h3>
                        <span class="menu-icon">🛒</span>
                    </div>
                    <div class="card-number"><?php echo $stats['lanzamientos']['pre_order']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Próximos Lanzamientos</h3>
                        <span class="menu-icon">📅</span>
                    </div>
                    <div class="card-number"><?php echo $stats['lanzamientos']['proximos']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Lanzados Hoy</h3>
                        <span class="menu-icon">🆕</span>
                    </div>
                    <div class="card-number"><?php echo $stats['lanzamientos']['hoy']; ?></div>
                </div>
            </div>
        </div>

        <!-- Sección de Administradores -->
        <div class="stats-section">
            <h2>Estadísticas de Administradores</h2>
            <div class="admin-cards">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Staff</h3>
                        <span class="menu-icon">👑</span>
                    </div>
                    <div class="card-number"><?php echo $stats['administradores']['total']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Staff Activos</h3>
                        <span class="menu-icon">💚</span>
                    </div>
                    <div class="card-number"><?php echo $stats['administradores']['activos']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Super Admins</h3>
                        <span class="menu-icon">👑</span>
                    </div>
                    <div class="card-number"><?php echo $stats['administradores']['super_admins']; ?></div>
                </div>
            </div>
        </div>

        <!-- Sección de Noticias -->
        <div class="stats-section">
            <h2>Estadísticas de Noticias</h2>
            <div class="admin-cards">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Noticias</h3>
                        <span class="menu-icon">📰</span>
                    </div>
                    <div class="card-number"><?php echo $stats['noticias']['total']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Noticias Destacadas</h3>
                        <span class="menu-icon">⭐</span>
                    </div>
                    <div class="card-number"><?php echo $stats['noticias']['destacadas']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Publicadas Hoy</h3>
                        <span class="menu-icon">📅</span>
                    </div>
                    <div class="card-number"><?php echo $stats['noticias']['hoy']; ?></div>
                </div>
            </div>
        </div>

        <!-- Sección de Tutoriales -->
        <div class="stats-section">
            <h2>Estadísticas de Tutoriales</h2>
            <div class="admin-cards">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Tutoriales</h3>
                        <span class="menu-icon">📚</span>
                    </div>
                    <div class="card-number"><?php echo $stats['tutoriales']['total']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Publicados Hoy</h3>
                        <span class="menu-icon">🆕</span>
                    </div>
                    <div class="card-number"><?php echo $stats['tutoriales']['hoy']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Tutoriales de Programación</h3>
                        <span class="menu-icon">💻</span>
                    </div>
                    <div class="card-number"><?php echo $stats['tutoriales']['programacion']; ?></div>
                </div>
            </div>
        </div>

        <!-- Sección de Usuarios -->
        <div class="stats-section">
            <h2>Estadísticas de Usuarios</h2>
            <div class="admin-cards">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Usuarios</h3>
                        <span class="menu-icon">👥</span>
                    </div>
                    <div class="card-number"><?php echo $stats['usuarios']['total']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Usuarios Activos (Últimos 30 días)</h3>
                        <span class="menu-icon">👤</span>
                    </div>
                    <div class="card-number"><?php echo $stats['usuarios']['activos']; ?></div>
                </div>
            </div>
        </div>

        <!-- Sección de Videojuegos -->
        <div class="stats-section">
            <h2>Estadísticas de Videojuegos</h2>
            <div class="admin-cards">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Videojuegos</h3>
                        <span class="menu-icon">🎮</span>
                    </div>
                    <div class="card-number"><?php echo $stats['videojuegos']['total']; ?></div>
                </div>
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">Publicados Hoy</h3>
                        <span class="menu-icon">📅</span>
                    </div>
                    <div class="card-number"><?php echo $stats['videojuegos']['hoy']; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
if (isset($stmt_usuario)) {
    $stmt_usuario->close();
}
$conexion->close();
?>