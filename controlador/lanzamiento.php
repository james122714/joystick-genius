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

// Procesar eliminación de lanzamiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = (int)$_POST['lanzamiento_id'];
    
    // Obtener la imagen para eliminarla del sistema de archivos
    $stmt = $conexion->prepare("SELECT image_url FROM game_launches WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $lanzamiento = $resultado->fetch_assoc();
    
    // Eliminar registro de la base de datos
    $stmt_eliminar = $conexion->prepare("DELETE FROM game_launches WHERE id = ?");
    $stmt_eliminar->bind_param("i", $id);
    
    if ($stmt_eliminar->execute()) {
        // Eliminar imagen del sistema de archivos si existe
        if ($lanzamiento['image_url'] && file_exists('../vista/' . $lanzamiento['image_url'])) {
            unlink('../vista/' . $lanzamiento['image_url']);
        }
    }
}

// Obtener estadísticas
$stats = [
    'total_lanzamientos' => $conexion->query("SELECT COUNT(*) as total FROM game_launches")->fetch_assoc()['total'],
    'lanzamientos_pre_order' => $conexion->query("SELECT COUNT(*) as total FROM game_launches WHERE pre_order = 1")->fetch_assoc()['total'],
    'lanzamientos_proximos' => $conexion->query("SELECT COUNT(*) as total FROM game_launches WHERE release_date > CURDATE()")->fetch_assoc()['total']
];

// Obtener lanzamientos
$lanzamientos = $conexion->query("SELECT * FROM game_launches ORDER BY release_date DESC");

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
    <link rel="stylesheet" href="../vista/css/lanzamiento1.css">
    <link rel="stylesheet" href="../vista/css/admin.css">
    <title>Gestión de Lanzamientos - joystick_genius</title>
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
            color: #753e3eff;
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
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">joystick_genius Admin</div>
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
        <div class="menu-item">
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
        <div class="menu-item active">
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
            <a href="logout.php" style="color: #C0392B;">
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
           <h1 class="dashboard-title" style="color: white;">Gestión de Lanzamientos</h1>
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

        <!-- Tarjetas de estadísticas -->
        <div class="admin-cards">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Total Lanzamientos</h3>
                    <span class="menu-icon">🎮</span>
                </div>
                <div class="card-number"><?php echo $stats['total_lanzamientos']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Pre-Orders</h3>
                    <span class="menu-icon">🛒</span>
                </div>
                <div class="card-number"><?php echo $stats['lanzamientos_pre_order']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Próximos Lanzamientos</h3>
                    <span class="menu-icon">📅</span>
                </div>
                <div class="card-number"><?php echo $stats['lanzamientos_proximos']; ?></div>
            </div>
        </div>

        <!-- Formulario para nuevo lanzamiento -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Lista de Lanzamientos</h2>
            <a href="crear_lanzamiento.php" class="add-admin-button">+ Agregar Lanzamiento</a>
        </div>

        <div class="category-filter">
            <button class="category-button active" value="todas">Todas</button>
            <button class="category-button" value="RPG">RPG</button>
            <button class="category-button" value="Estrategia">Estrategia</button>
            <button class="category-button" value="Aventura">Aventura</button>
            <button class="category-button" value="Acción">Acción</button>
            <button class="category-button" value="Survival">Survival</button>
            <button class="category-button" value="Terror">Terror</button>
            <button class="category-button" value="Carreras">Carreras</button>
            <button class="category-button" value="Deporte">Deporte</button>
        </div>

        <!-- Tabla de lanzamientos -->
        <div class="admin-table">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Fecha Lanzamiento</th>
                            <th>Precio</th>
                            <th>Pre-Orden</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($lanzamiento = $lanzamientos->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $lanzamiento['id']; ?></td>   
                            <td>
                                <?php if($lanzamiento['image_url']): ?>
                                    <img src="../vista/<?php echo htmlspecialchars($lanzamiento['image_url']); ?>" 
                                        alt="<?php echo htmlspecialchars($lanzamiento['title']); ?>" 
                                        style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; display: inline-block; vertical-align: middle;">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($lanzamiento['title']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($lanzamiento['category']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($lanzamiento['release_date'])); ?></td>
                            <td>$<?php echo number_format($lanzamiento['price'], 2); ?></td>
                            <td><?php echo $lanzamiento['pre_order'] ? 'Sí' : 'No'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($permite_editar): ?>
                                        <a href="editar_lanzamiento.php?id=<?php echo $lanzamiento['id']; ?>" class="action-button">Editar</a>
                                    <?php endif; ?>
                                    <?php if ($permite_eliminar): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="lanzamiento_id" value="<?php echo $lanzamiento['id']; ?>">
                                            <button type="submit" class="action-button" onclick="return confirm('¿Estás seguro de eliminar este lanzamiento?')">Eliminar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryButtons = document.querySelectorAll('.category-button');
        const tutorialRows = document.querySelectorAll('tbody tr');

        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const category = this.value;

                tutorialRows.forEach(row => {
                    const rowCategory = row.querySelector('td:nth-child(3)').textContent;
                    if (category === 'todas' || rowCategory === category) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>
</body>
</html>