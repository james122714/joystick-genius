<?php
session_start();

// Verificar autenticación y permitir acceso a admin, moderador y adminvista
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista',])) {
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

// Procesar formulario de nueva noticia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear':
                $titulo = $conexion->real_escape_string($_POST['titulo']);
                $contenido = $conexion->real_escape_string($_POST['contenido']);
                $destacada = isset($_POST['destacada']) ? 1 : 0;
                
                // Procesamiento de imagen
                $imagen = '';
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                    $imagen = subir_imagen($_FILES['imagen']);
                }
                
                $sql = "INSERT INTO noticias (titulo, contenido, imagen, destacada, autor_id) 
                        VALUES ('$titulo', '$contenido', '$imagen', $destacada, {$_SESSION['usuario_id']})";
                $conexion->query($sql);
                break;
                
            case 'eliminar':
                $id = (int)$_POST['noticia_id'];
                $conexion->query("DELETE FROM noticias WHERE id = $id");
                break;
                
            case 'actualizar':
                $id = (int)$_POST['noticia_id'];
                $titulo = $conexion->real_escape_string($_POST['titulo']);
                $contenido = $conexion->real_escape_string($_POST['contenido']);
                $destacada = isset($_POST['destacada']) ? 1 : 0;
                
                $sql = "UPDATE noticias SET 
                        titulo = '$titulo',
                        contenido = '$contenido',
                        destacada = $destacada
                        WHERE id = $id";
                $conexion->query($sql);
                break;
        }
    }
}

// Determinar filtro de categoría
$categoria_filtro = isset($_GET['categoria']) ? $conexion->real_escape_string($_GET['categoria']) : '';

// Obtener estadísticas
$stats = [
    'total_noticias' => $conexion->query("SELECT COUNT(*) as total FROM noticias")->fetch_assoc()['total'],
    'noticias_destacadas' => $conexion->query("SELECT COUNT(*) as total FROM noticias WHERE destacada = 1")->fetch_assoc()['total'],
    'noticias_hoy' => $conexion->query("SELECT COUNT(*) as total FROM noticias WHERE DATE(fecha_publicacion) = CURDATE()")->fetch_assoc()['total']
];

// Obtener noticias con filtro de categoría
$query = "SELECT n.*, u.nombre as autor_nombre 
        FROM noticias n 
        LEFT JOIN usuarios u ON n.autor_id = u.id ";
if (!empty($categoria_filtro)) {
    $query .= "WHERE n.categoria = '$categoria_filtro' ";
}
$query .= "ORDER BY n.fecha_publicacion DESC";
$noticias = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <title>Gestión de Noticias - joystick_genius</title>
    <link rel="stylesheet" href="../vista/css/administracion.css">
    <link rel="stylesheet" href="../vista/css/noticia.css">
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
        <div class="menu-item active">
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
            <a href="principal.php" style="color: #10b981;">
                <span class="menu-icon">🏠</span>
                <span>Ir a Principal</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Gestión de Noticias</h1>
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

        <div class="admin-cards">
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Total Noticias</h3>
                    <span class="menu-icon">📰</span>
                </div>
                <div class="card-number"><?php echo $stats['total_noticias']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Destacadas</h3>
                    <span class="menu-icon">⭐</span>
                </div>
                <div class="card-number"><?php echo $stats['noticias_destacadas']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Publicadas Hoy</h3>
                    <span class="menu-icon">📅</span>
                </div>
                <div class="card-number"><?php echo $stats['noticias_hoy']; ?></div>
            </div>
        </div>

        <div class="category-filters">
            <a href="noticias.php" class="category-button <?php echo empty($categoria_filtro) ? 'active' : ''; ?>">Todas</a>
            <a href="noticias.php?categoria=general" class="category-button <?php echo $categoria_filtro == 'general' ? 'active' : ''; ?>">General</a>
            <a href="noticias.php?categoria=tecnologia" class="category-button <?php echo $categoria_filtro == 'tecnologia' ? 'active' : ''; ?>">Tecnología</a>
            <a href="noticias.php?categoria=deportes" class="category-button <?php echo $categoria_filtro == 'deportes' ? 'active' : ''; ?>">Deportes</a>
            <a href="noticias.php?categoria=entretenimiento" class="category-button <?php echo $categoria_filtro == 'entretenimiento' ? 'active' : ''; ?>">Entretenimiento</a>
        </div>

        <!-- Formulario para nueva noticia -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Lista de Noticias</h2>
                <a href="crear_noticias.php" class="add-admin-button">+ Agregar Noticias</a>
        </div>

        <!-- Tabla de noticias -->
        <div class="admin-table">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Fecha</th>
                            <th>Destacada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($noticia = $noticias->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $noticia['id']; ?></td>
                            <td><?php echo htmlspecialchars($noticia['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($noticia['autor_nombre']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($noticia['fecha_publicacion'])); ?></td>
                            <td><?php echo $noticia['destacada'] ? 'Sí' : 'No'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="editar_noticias.php?id=<?php echo $noticia['id']; ?>" class="action-button">Editar</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="noticia_id" value="<?php echo $noticia['id']; ?>">
                                        <button type="submit" class="action-button" onclick="return confirm('¿Estás seguro de eliminar esta noticia?')">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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