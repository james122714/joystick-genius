<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';
$conexion = $conexion;

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

// Obtener estadísticas
$stats = [
    'total_noticias' => $conexion->query("SELECT COUNT(*) as total FROM noticias")->fetch_assoc()['total'],
    'noticias_destacadas' => $conexion->query("SELECT COUNT(*) as total FROM noticias WHERE destacada = 1")->fetch_assoc()['total'],
    'noticias_hoy' => $conexion->query("SELECT COUNT(*) as total FROM noticias WHERE DATE(fecha_publicacion) = CURDATE()")->fetch_assoc()['total']
];

// Obtener noticias
$noticias = $conexion->query("SELECT n.*, u.nombre as autor_nombre 
                            FROM noticias n 
                            LEFT JOIN usuarios u ON n.autor_id = u.id 
                            ORDER BY n.fecha_publicacion DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Noticias - joystick genius</title>
    <link rel="stylesheet" href="../vista/css/administracion.css">
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
            <a href="usuarios_vip.php">
                <span class="menu-icon">👑</span>
                <span>Usuarios VIP</span>
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
            <a href="gestion_noticias.php">
                <span class="menu-icon">📰</span>
                <span>Noticias</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="logout.php" style="color: #ff4444;">
                <span class="menu-icon">🚪</span>
                <span>Salir</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Gestión de Noticias</h1>
            <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 2); ?></div>
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

        <!-- Formulario para nueva noticia -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Lista de Noticias</h2>
                <a href="crear_noticias.php" class="add-admin-button">+ Agregar Noticias</a>
        </div>

        <!-- Tabla de noticias -->
        <div class="admin-table">
            <h2>Lista de Noticias</h2>
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
                                    <a href="editar_noticia.php?id=<?php echo $noticia['id']; ?>" class="action-button">Editar</a>
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
                
    <form method="post" action="actualizar_destacados.php">
        <h3>Gestionar noticias destacadas</h3>
        <?php
        foreach ($noticias as $noticia) {
            echo "<input type='checkbox' name='destacadas[]' value='{$noticia['id']}' " . ($noticia['destacada'] ? "checked" : "") . "> {$noticia['titulo']}<br>";
        }
        ?>
        <button type="submit">Actualizar destacadas</button>
    </form>
    </table>
    
            </div>
        </div>
    </div>
</body>
</html>