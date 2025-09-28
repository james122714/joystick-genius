<?php
// perfil.php - Vista de perfil de usuario con diseño profesional en negro y rojo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';
$conexion = $conexion;

// Función para obtener la foto de perfil
function obtenerFotoPerfil($usuario, $size) {
    $base_path = '../vista/';
    $default_image = $base_path . 'multimedia/default-profile.png';
    $foto_perfil = !empty($usuario['foto_perfil']) ? $base_path . $usuario['foto_perfil'] : $default_image;
    return htmlspecialchars($foto_perfil);
}

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conexion->prepare("SELECT u.*, r.nombre as rango_nombre, r.descripcion as rango_descripcion 
                            FROM usuarios u 
                            LEFT JOIN rangos r ON u.rango_id = r.id 
                            WHERE u.id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    $mensaje = "Error: No se encontró el usuario.";
} else {
    $mensaje = '';
    $puntos_actuales = $usuario['puntos'] ?? 0;
    // Obtener tipo_usuario desde administradores
    $tipo_usuario = 'usuario';
    $admin_check = $conexion->prepare("SELECT rol FROM administradores WHERE usuario_id = ?");
    $admin_check->bind_param("i", $usuario_id);
    $admin_check->execute();
    $admin_result = $admin_check->get_result();
    if ($admin_result->num_rows > 0) {
        $admin_data = $admin_result->fetch_assoc();
        $tipo_usuario = strtolower($admin_data['rol']);
    }
}

// Procesar actualización de foto
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['foto_perfil']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $upload_dir = '../vista/multimedia/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $nuevo_nombre = $usuario_id . '_' . time() . '.' . $ext;
        $destino = $upload_dir . $nuevo_nombre;

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destino)) {
            $ruta_foto = 'multimedia/' . $nuevo_nombre;
            $stmt = $conexion->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
            $stmt->bind_param("si", $ruta_foto, $usuario_id);
            
            if ($stmt->execute()) {
                $_SESSION['foto_perfil'] = $ruta_foto;
                $usuario['foto_perfil'] = $ruta_foto;
            } else {
                $mensaje = "Error al actualizar la foto de perfil en la base de datos";
            }
        } else {
            $mensaje = "Error al subir la imagen";
        }
    } else {
        $mensaje = "Tipo de archivo no permitido. Solo se permiten: jpg, jpeg, png, gif";
    }
}

// Procesar actualización de nombre
if (isset($_POST['actualizar_nombre'])) {
    $nuevo_nombre = trim($_POST['nombre']);
    if (!empty($nuevo_nombre)) {
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_nombre, $usuario_id);
        
        if ($stmt->execute()) {
            $_SESSION['nombre'] = $nuevo_nombre;
            $usuario['nombre'] = $nuevo_nombre;
        } else {
            $mensaje = "Error al actualizar el nombre";
        }
    }
}

// Procesar actualización manual de rango (solo para admin o moderador)
if (isset($_POST['actualizar_rango']) && in_array($tipo_usuario, ['admin', 'moderador'])) {
    $nuevo_rango_id = (int)$_POST['rango_id'];
    $stmt = $conexion->prepare("UPDATE usuarios SET rango_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $nuevo_rango_id, $usuario_id);
    
    if ($stmt->execute()) {
        // Actualizar datos locales
        $rango_stmt = $conexion->prepare("SELECT nombre, descripcion FROM rangos WHERE id = ?");
        $rango_stmt->bind_param("i", $nuevo_rango_id);
        $rango_stmt->execute();
        $rango_result = $rango_stmt->get_result()->fetch_assoc();
        $usuario['rango_id'] = $nuevo_rango_id;
        $usuario['rango_nombre'] = $rango_result['nombre'];
        $usuario['rango_descripcion'] = $rango_result['descripcion'];
    } else {
        $mensaje = "Error al actualizar el rango";
    }
}

// Obtener todos los rangos
$rangos_query = $conexion->query("SELECT * FROM rangos ORDER BY id ASC");
$rangos = [];
while ($rango = $rangos_query->fetch_assoc()) {
    $rangos[] = $rango;
}

// Lógica para admins, moderadores o adminvistas: Asignar último rango automáticamente
$ultimo_rango_id = end($rangos)['id'] ?? 20;
$current_rango_id = $usuario['rango_id'] ?? 1;

if (in_array($tipo_usuario, ['admin', 'moderador']) && $current_rango_id != $ultimo_rango_id) {
    $update_rango = $conexion->prepare("UPDATE usuarios SET rango_id = ? WHERE id = ?");
    $update_rango->bind_param("ii", $ultimo_rango_id, $usuario_id);
    $update_rango->execute();
    $usuario['rango_id'] = $ultimo_rango_id;
    $usuario['rango_nombre'] = end($rangos)['nombre'] ?? 'Dios de los Pixels';
    $usuario['rango_descripcion'] = end($rangos)['descripcion'] ?? '';
    $current_rango_id = $ultimo_rango_id;
} elseif (!in_array($tipo_usuario, ['admin', 'moderador'])) {
    // Lógica para usuarios: Actualizar rango basado en puntos
    foreach ($rangos as $rango) {
        $requisitos = (int)str_replace(' puntos', '', $rango['requisitos']);
        if ($puntos_actuales >= $requisitos && $rango['id'] > $current_rango_id) {
            $update_rango = $conexion->prepare("UPDATE usuarios SET rango_id = ? WHERE id = ?");
            $update_rango->bind_param("ii", $rango['id'], $usuario_id);
            $update_rango->execute();
            $usuario['rango_id'] = $rango['id'];
            $usuario['rango_nombre'] = $rango['nombre'];
            $usuario['rango_descripcion'] = $rango['descripcion'];
            $current_rango_id = $rango['id'];
        }
    }
}

// Calcular progreso hacia el siguiente rango (solo para usuarios normales)
$next_rango = null;
$puntos_para_siguiente = 0;
$progreso_porcentaje = 0;

if (!in_array($tipo_usuario, ['nombre','adminvista']) && $current_rango_id < count($rangos)) {
    $next_rango = $rangos[$current_rango_id];
    $puntos_para_siguiente = (int)str_replace(' puntos', '', $next_rango['requisitos']);
    if ($puntos_para_siguiente > 0) {
        $progreso_porcentaje = min(100, ($puntos_actuales / $puntos_para_siguiente) * 100);
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOYSTICK GENIUS - Perfil</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../vista/css/perfil.css">
</head>
<body class="min-h-screen flex flex-col text-white">
    <div class="flex-grow flex items-center justify-center p-6">
        <div class="bg-black/60 border border-red-600/30 w-full max-w-6xl p-8 rounded-2xl red-glow">
            <?php if ($mensaje): ?>
            <div class="bg-red-900/50 text-red-200 px-6 py-3 rounded-lg mb-6 text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            <?php endif; ?>

            <?php if (!$usuario): ?>
            <div class="bg-red-900/50 text-red-200 px-6 py-3 rounded-lg mb-6 text-center">
                No se pudo cargar la información del usuario. Por favor, intenta de nuevo.
            </div>
            <?php else: ?>
            <div class="profile-container">
                <!-- Enlace a tabla de rangos estilizado -->
                <div class="w-full text-center">
                    <a href="tabla_rango.php" class="nav-link">
                        📊 Ver Tabla de Rangos
                    </a>
                </div>

                <!-- Columna Principal: Perfil -->
                <div class="profile-main text-center">
                    <div class="avatar-container mb-6">
                        <img 
                            src="<?php echo obtenerFotoPerfil($usuario, 'large'); ?>"
                            alt="Foto de perfil" 
                            class="avatar"
                        >
                        <div class="avatar-back">Pixel Play</div>
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data" class="mb-6">
                        <label for="foto_perfil" class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded-full inline-block cursor-pointer transition hover-red-glow">
                            Actualizar Avatar
                        </label>
                        <input type="file" id="foto_perfil" name="foto_perfil" class="hidden" onchange="this.form.submit()">
                    </form>

                    <form action="" method="POST" class="flex items-center justify-center gap-4 mb-6">
                        <input 
                            type="text" 
                            name="nombre" 
                            value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" 
                            class="bg-red-900/30 border border-red-500/30 text-white px-4 py-2 rounded-lg w-full max-w-xs"
                        >
                        <button 
                            type="submit" 
                            name="actualizar_nombre" 
                            class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded-full text-white transition hover-red-glow"
                        >
                            Actualizar Nombre
                        </button>
                    </form>

                    <h2 class="text-3xl font-bold mb-2 text-red-500">
                        <?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?>
                    </h2>
                    
                    <p class="text-gray-300 mb-6">
                        Rango: <?php echo htmlspecialchars($usuario['rango_nombre'] ?? 'Novato'); ?>
                    </p>

                    <!-- Sección de Progreso (solo para usuarios normales) -->
                    <?php if (!in_array($tipo_usuario, ['admin', 'moderador', 'adminvista'])): ?>
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-red-400 mb-4">Progreso hacia el siguiente rango</h3>
                        <p class="text-gray-300 mb-2">Puntos actuales: <?php echo $puntos_actuales; ?></p>
                        <?php if ($next_rango): ?>
                            <p class="text-gray-300 mb-2">Siguiente: <?php echo htmlspecialchars($next_rango['nombre']); ?> (<?php echo $puntos_para_siguiente; ?> puntos)</p>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progreso_porcentaje; ?>%;"></div>
                            </div>
                            <p class="text-red-300 mt-2"><?php echo round($progreso_porcentaje); ?>% completado</p>
                        <?php else: ?>
                            <p class="text-red-300">¡Has alcanzado el rango máximo!</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="bg-black/70 border border-red-500/30 p-4 rounded-xl text-center red-glow hover-red-glow transition">
                            <h4 class="text-sm uppercase tracking-wider text-red-300 mb-2">
                                Puntuación
                            </h4>
                            <p class="text-2xl font-bold text-red-300">
                                <?php echo $puntos_actuales; ?>
                            </p>
                        </div>
                        <div class="bg-black/70 border border-red-500/30 p-4 rounded-xl text-center red-glow hover-red-glow transition">
                            <h4 class="text-sm uppercase tracking-wider text-red-300 mb-2">
                                Nivel
                            </h4>
                            <p class="text-2xl font-bold text-red-300">
                                <?php echo round($progreso_porcentaje); ?>%
                            </p>
                        </div>
                        <div class="bg-black/70 border border-red-500/30 p-4 rounded-xl text-center red-glow hover-red-glow transition">
                            <h4 class="text-sm uppercase tracking-wider text-red-300 mb-2">
                                Karma
                            </h4>
                            <p class="text-2xl font-bold text-red-300">
                                ∞
                            </p>
                        </div>
                    </div>

                    <!-- Selector de Rango Manual (solo para admin o moderador) -->
                    <?php if (in_array($tipo_usuario, ['admin', 'moderador'])): ?>
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-red-400 mb-4">Modificar Rango</h3>
                        <form action="" method="POST" class="flex items-center justify-center gap-4">
                            <select name="rango_id" class="bg-red-900/30 border border-red-500/30 text-white px-4 py-2 rounded-lg">
                                <?php foreach ($rangos as $rango): ?>
                                    <option value="<?php echo $rango['id']; ?>" <?php echo $rango['id'] == $current_rango_id ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rango['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button 
                                type="submit" 
                                name="actualizar_rango" 
                                class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded-full text-white transition hover-red-glow"
                            >
                                Actualizar Rango
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <div class="space-x-4">
                        <a href="principal.php" class="border border-red-400 text-red-400 hover:bg-red-400 hover:text-black px-6 py-2 rounded-full transition-all duration-300 inline-block">
                            Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer Profesional -->
    <footer class="bg-black/90 text-gray-300 py-12 px-6 mt-auto">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h4 class="text-red-500 font-bold mb-4">Joystick genius</h4>
                <p>Plataforma para gamers profesionales y entusiastas.</p>
            </div>
            <div>
                <h4 class="text-red-500 font-bold mb-4">Enlaces Rápidos</h4>
                <ul class="space-y-2">
                    <li><a href="tutoriales.php" class="hover:text-red-500 transition">Tutoriales</a></li>
                    <li><a href="juegos.php" class="hover:text-red-500 transition">Juegos</a></li>
                    <li><a href="comunidad.php" class="hover:text-red-500 transition">Comunidad</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-red-500 font-bold mb-4">Contacto</h4>
                <p>Email: info@pixelplay.com</p>
                <p>Teléfono: +1 234 567 890</p>
            </div>
            <div>
                <h4 class="text-red-500 font-bold mb-4">Síguenos</h4>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-red-500">Twitter</a>
                    <a href="#" class="hover:text-red-500">Instagram</a>
                    <a href="#" class="hover:text-red-500">Facebook</a>
                </div>
            </div>
        </div>
        <div class="text-center mt-8 border-t border-red-600/30 pt-4">
            <p>&copy; 2025 Joystick Genius. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>