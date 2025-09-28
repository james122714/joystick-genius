<?php
session_start();

// Verificar autenticación y permitir acceso a admin, moderador y adminvista
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
    header("Location: login.php");
    exit();
}

// TODO: Implementar sistema de autenticación
require_once('conexion.php');

// Obtener la conexión llamando a la función
$conexion = $conexion;

// Obtener información del usuario logueado incluyendo foto de perfil
$stmt_usuario = $conexion->prepare("SELECT nombre, foto_perfil FROM usuarios WHERE id = ?");
$stmt_usuario->bind_param("i", $_SESSION['usuario_id']);
$stmt_usuario->execute();
$usuario_info = $stmt_usuario->get_result()->fetch_assoc();

// Variable para almacenar mensajes
$mensaje = '';
$error = '';

// Estadísticas de tutoriales
$stats = [
    'total_tutoriales' => $conexion->query("SELECT COUNT(*) as total FROM tutoriales")->fetch_assoc()['total'],
    'tutoriales_hoy' => $conexion->query("SELECT COUNT(*) as total FROM tutoriales WHERE DATE(fecha_creacion) = CURDATE()")->fetch_assoc()['total'],
    'tutoriales_categoria_programacion' => $conexion->query("SELECT COUNT(*) as total FROM tutoriales WHERE categoria = 'Programación'")->fetch_assoc()['total']
];

// Procesar eliminación de tutorial
if (isset($_GET['eliminar'])) {
    try {
        $id = intval($_GET['eliminar']);
        $stmt = $conexion->prepare("DELETE FROM tutoriales WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de eliminación: " . $conexion->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar el tutorial: " . $stmt->error);
        }
        $stmt->close();
        $mensaje = "Tutorial eliminado exitosamente";
        header("Location: tutorial.php?mensaje=" . urlencode($mensaje));
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Obtener tutoriales
try {
    $stmt = $conexion->prepare("SELECT * FROM tutoriales ORDER BY fecha_creacion DESC");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conexion->error);
    }
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    $resultado = $stmt->get_result();
    if (!$resultado) {
        throw new Exception("Error al obtener los resultados: " . $stmt->error);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    $resultado = null;
}

// Obtener mensaje de la URL si existe
if (isset($_GET['mensaje'])) {
    $mensaje = htmlspecialchars($_GET['mensaje']);
}

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
    <link rel="stylesheet" href="../vista/css/tutorial.css">
    <link rel="stylesheet" href="../vista/css/admin.css">
    <title>Gestión de Tutoriales - GamersHub</title>
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
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <div class="menu-item active">
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
            <h1 class="dashboard-title" style="color : white">Gestión de Tutoriales</h1>
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
                    <h3 class="card-title">Total Tutoriales</h3>
                    <span class="menu-icon">📚</span>
                </div>
                <div class="card-number"><?php echo $stats['total_tutoriales']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Tutoriales Publicados Hoy</h3>
                    <span class="menu-icon">🆕</span>
                </div>
                <div class="card-number"><?php echo $stats['tutoriales_hoy']; ?></div>
            </div>
            <div class="admin-card">
                <div class="card-header">
                    <h3 class="card-title">Tutoriales de Programación</h3>
                    <span class="menu-icon">💻</span>
                </div>
                <div class="card-number"><?php echo $stats['tutoriales_categoria_programacion']; ?></div>
            </div>
        </div>

        <?php 
        if (!empty($error)) {
            echo "<div class='alert alert-danger'>" . $error . "</div>";
        }
        if (!empty($mensaje)) {
            echo "<div class='alert alert-success'>" . $mensaje . "</div>";
        }
        ?>

        <div class="category-filter">
            <button class="category-button active" data-category="todas">Todas</button>
            <button class="category-button" data-category="Basico">Básico</button>
            <button class="category-button" data-category="Intermedio">Intermedio</button>
            <button class="category-button" data-category="Avanzado">Avanzado</button>
        </div>

        <div class="admin-table">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Lista de Tutoriales</h2>
                <?php if ($permite_crear): ?>
                    <a href="agregar_tutorial.php" class="add-admin-button">+ Agregar Tutorial</a>
                <?php endif; ?>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Imagen</th>
                            <th>Categoría</th>
                            <th>Nivel</th>
                            <th>Duración</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($resultado && $resultado->num_rows > 0) {
                            while($tutorial = $resultado->fetch_assoc()): 
                        ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($tutorial['id']); ?></td>
                            <td><?php echo htmlspecialchars($tutorial['nombre']); ?></td>
                            <td>
                                <?php if(!empty($tutorial['image_url'])): ?>
                                    <img src="../vista/<?php echo htmlspecialchars($tutorial['image_url']); ?>" 
                                        alt="<?php echo htmlspecialchars($tutorial['nombre']); ?>" 
                                        style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; display: inline-block; vertical-align: middle;">
                                <?php else: ?>
                                    Sin imagen
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($tutorial['categoria']); ?></td>
                            <td><?php echo htmlspecialchars($tutorial['nivel_dificultad']); ?></td>
                            <td><?php echo htmlspecialchars($tutorial['duracion']); ?></td>
                            <td data-label="Acciones">
                                <div class="action-buttons">
                                    <?php if ($permite_editar): ?>
                                        <a href="editar_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="action-button">Editar</a>
                                    <?php endif; ?>
                                    <?php if ($permite_eliminar): ?>
                                        <a href="tutorial.php?eliminar=<?php echo $tutorial['id']; ?>" 
                                           class="action-button" 
                                           onclick="return confirm('¿Estás seguro de eliminar este tutorial?')">Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No se encontraron tutoriales</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Limpiar la URL después de mostrar el mensaje
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href.split('?')[0]);
    }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryButtons = document.querySelectorAll('.category-button');
        const tutorialRows = document.querySelectorAll('tbody tr');

        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const category = this.getAttribute('data-category');

                tutorialRows.forEach(row => {
                    const categoryCell = row.querySelector('td:nth-child(4)');
                    if (categoryCell) {
                        const rowCategory = categoryCell.textContent;
                        if (category === 'todas' || rowCategory === category) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        });
    });
    </script>
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