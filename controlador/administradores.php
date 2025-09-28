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

// Consultar todos los usuarios con tipo_usuario admin, moderador o adminvista
$consulta = "SELECT u.* 
            FROM usuarios u
            WHERE u.tipo_usuario IN ('admin', 'moderador', 'adminvista')
            ORDER BY u.id DESC";
$resultado = $conexion->query($consulta);

// Estadísticas de staff
$stats = [
    'total_staff' => $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario IN ('admin', 'moderador', 'adminvista')")->fetch_assoc()['total'],
    'staff_activos' => $conexion->query("SELECT COUNT(DISTINCT usuario_id) as total FROM sesiones WHERE usuario_id IN (SELECT id FROM usuarios WHERE tipo_usuario IN ('admin', 'moderador', 'adminvista')) AND fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['total'],
    'super_admins' => $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'admin'")->fetch_assoc()['total']
];

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
    <link rel="icon" href="../vista/multimedia/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/administradores.css">
    <link rel="stylesheet" href="../vista/css/administracion.css">
    <title>Gestión de Administradores - GamersHub Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
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

        .alert {
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
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo">GamersHub Admin</div>
            <div class="nav-menu">
                <div class="nav-item">
                    <a href="administracion.php" class="nav-link">
                        <span class="nav-icon">🏠</span>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="administradores.php" class="nav-link active">
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
                    <a href="estadisticas.php" class="nav-link">
                        <span class="nav-icon">📊</span>
                        <span>Estadísticas</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="tutorial.php" class="nav-link">
                        <span class="nav-icon">📚</span>
                        <span>Tutoriales</span>
                    </a>
                </div>
                <div class="nav-item" style="margin-top: auto;">
                    <a href="logout.php" class="nav-link" style="color: #ff0000;">
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
                <h1>Gestión de Administradores</h1>
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
            </header>

            <!-- Success Message -->
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert"><?php echo htmlspecialchars($_SESSION['mensaje']); ?></div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👑</div>
                    <div class="stat-title">Total Admin</div>
                    <div class="stat-number"><?php echo $stats['total_staff']; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💚</div>
                    <div class="stat-title">Admins Activos</div>
                    <div class="stat-number"><?php echo $stats['staff_activos']; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👑</div>
                    <div class="stat-title">Admins Principales</div>
                    <div class="stat-number"><?php echo $stats['super_admins']; ?></div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="section-header">
                <h2 class="section-title">Lista de Staff</h2>
                <?php if ($permite_crear): ?>
                    <a href="crear_admin.php" class="add-btn">+ Agregar Staff</a>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while ($admin = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="ID">#<?php echo $admin['id']; ?></td>
                                    <td data-label="Nombre"><?php echo htmlspecialchars($admin['nombre']); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td data-label="Rol"><?php echo htmlspecialchars(ucfirst($admin['tipo_usuario'])); ?></td>
                                    <td data-label="Acciones">
                                        <div class="action-buttons">
                                            <?php if ($permite_editar): ?>
                                                <a href="editar_admin.php?id=<?php echo $admin['id']; ?>" class="action-button">Editar</a>
                                            <?php endif; ?>
                                            <?php if ($tipo_logueado === 'admin'): ?>
                                                <a href="permisos.php?id=<?php echo $admin['id']; ?>" class="action-button">Permisos</a>
                                            <?php endif; ?>
                                            <?php if ($permite_eliminar && $admin['id'] != $_SESSION['usuario_id']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                                    <button type="submit" class="action-button" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">No hay administradores registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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