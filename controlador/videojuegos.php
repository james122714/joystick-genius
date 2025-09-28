
<?php
session_start();

// Verificar autenticación y permitir acceso a admin, moderador y adminvista
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
    header("Location:login.php");
    exit();
}
require_once 'conexion.php';
$conexion = $conexion;

// Obtener información del usuario logueado incluyendo foto de perfil
$stmt_usuario = $conexion->prepare("SELECT nombre, foto_perfil FROM usuarios WHERE id = ?");
$stmt_usuario->bind_param("i", $_SESSION['usuario_id']);
$stmt_usuario->execute();
$usuario_info = $stmt_usuario->get_result()->fetch_assoc();

// Procesar eliminación de videojuego
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = (int)$_POST['game_id'];
    $conexion->query("DELETE FROM games WHERE id = $id");
    $_SESSION['mensaje'] = "Videojuego eliminado exitosamente";
    header("Location: videojuegos.php");
    exit();
}

// Obtener categorías
$categorias = $conexion->query("SELECT * FROM categories ORDER BY nombre");

// Filtrado de categorías
$category_filter = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

// Configuración de paginación
$games_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $games_por_pagina;

// Obtener total de videojuegos con filtro
$total_query = "SELECT COUNT(DISTINCT g.id) as total 
               FROM games g 
               LEFT JOIN game_categories gc ON g.id = gc.game_id
               LEFT JOIN categories c ON gc.category_id = c.id";
if ($category_filter) {
    $total_query .= " WHERE gc.category_id = $category_filter";
}
$total_result = $conexion->query($total_query);
$total_games = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_games / $games_por_pagina);

// Consulta de videojuegos con filtro y paginación
$query = "SELECT g.*, GROUP_CONCAT(DISTINCT c.nombre SEPARATOR ', ') as category_names 
         FROM games g 
         LEFT JOIN game_categories gc ON g.id = gc.game_id
         LEFT JOIN categories c ON gc.category_id = c.id";
if ($category_filter) {
    $query .= " WHERE gc.category_id = $category_filter";
}
$query .= " GROUP BY g.id ORDER BY g.created_at DESC LIMIT $games_por_pagina OFFSET $offset";
$games = $conexion->query($query);

// Estadísticas
$stats = [
    'total_videojuegos' => $conexion->query("SELECT COUNT(*) as total FROM games")->fetch_assoc()['total'],
    'videojuegos_hoy' => $conexion->query("SELECT COUNT(*) as total FROM games WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['total']
];
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <title>Gestión de videojuegos - joystick genius</title>
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

    .category-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 30px;
    }

    .category-btn {
        position: relative;
        padding: 12px 25px;
        text-decoration: none;
        color: #228B22;
        background: transparent;
        border: 2px solid #228B22;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: bold;
    }

    .category-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            120deg, 
            transparent, 
            rgba(34,139,34,0.3), 
            transparent
        );
        transition: all 0.5s ease;
    }

    .category-btn:hover::before {
        left: 100%;
    }

    .category-btn:hover {
        box-shadow: 0 0 20px rgba(34,139,34,0.5);
        transform: scale(1.05);
    }

    .category-btn.active {
        background: rgba(34,139,34,0.2);
        box-shadow: 0 0 15px rgba(34,139,34,0.5);
    }

    .category-btn::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: -100%;
        width: 100%;
        height: 3px;
        background: linear-gradient(
            to right, 
            transparent, 
            #228B22, 
            transparent
        );
        transition: all 0.5s ease;
    }

    .category-btn:hover::after {
        left: 0;
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

    .alert {
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
        font-weight: 500;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
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
        <div class="menu-item active">
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
            <h1 class="dashboard-title">Gestión de videojuegos</h1>
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

        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['mensaje']; 
                unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php endif; ?>

        <div class="admin-cards">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Total videojuegos</h3>
                    <span class="menu-icon">🎮</span>
                </div>
                <div class="card-number"><?php echo $stats['total_videojuegos']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Publicados Hoy</h3>
                    <span class="menu-icon">📅</span>
                </div>
                <div class="card-number"><?php echo $stats['videojuegos_hoy']; ?></div>
            </div>
        </div>

        <!-- Botones de categoría -->
        <div class="category-buttons">
            <a href="videojuegos.php" class="category-btn <?= $category_filter === null ? 'active' : '' ?>">Todos</a>
            <?php while ($categoria = $categorias->fetch_assoc()): ?>
                <a href="videojuegos.php?categoria=<?= $categoria['id'] ?>" 
                    class="category-btn <?= $category_filter == $categoria['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($categoria['nombre']) ?>
                </a>
            <?php endwhile; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
            <h2>Lista de videojuegos</h2>
            <a href="crear_videojuegos.php" class="add-admin-button">+ Agregar videojuego</a>
        </div>

        <div class="admin-table">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Categorías</th>
                            <th>Fecha Lanzamiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($game = $games->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $game['id']; ?></td>
                            <td><?php echo htmlspecialchars($game['title']); ?></td>
                            <td><?php echo htmlspecialchars($game['category_names'] ?? 'Sin categoría'); ?></td>
                            <td><?php echo !empty($game['release_date']) ? date('d/m/Y', strtotime($game['release_date'])) : 'N/A'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="editar_videojuegos.php?id=<?php echo $game['id']; ?>" class="action-button">Editar</a>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                                        <button type="submit" class="action-button" style="background: none; border: none; padding: 0; cursor: pointer;" onclick="return confirm('¿Estás seguro de eliminar este videojuego?')">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="pagination">
                <?php 
                $base_url = "videojuegos.php";
                if ($category_filter) {
                    $base_url .= "?categoria=$category_filter";
                }
                $sep = $category_filter ? '&' : '?';
                ?>
                <?php if ($pagina_actual > 1): ?>
                    <a href="<?php echo $base_url . $sep . 'pagina=' . ($pagina_actual - 1); ?>">Anterior</a>
                <?php else: ?>
                    <a class="disabled">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="<?php echo $base_url . $sep . 'pagina=' . $i; ?>" class="<?php echo ($i == $pagina_actual) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="<?php echo $base_url . $sep . 'pagina=' . ($pagina_actual + 1); ?>">Siguiente</a>
                <?php else: ?>
                    <a class="disabled">Siguiente</a>
                <?php endif; ?>
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