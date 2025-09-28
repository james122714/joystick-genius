<?php
// confi_user.php - Vista de configuración de usuario con diseño profesional en negro y rojo
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos usando tu función de conexión
require_once 'conexion.php';
$conexion = $conexion;

// Función de fallback para obtenerFotoPerfil
function obtenerFotoPerfil($usuario, $size) {
    $base_path = '../vista/';
    $default_image = $base_path . 'multimedia/default-profile.png';
    $foto_perfil = !empty($usuario['foto_perfil']) ? $base_path . $usuario['foto_perfil'] : $default_image;
    return htmlspecialchars($foto_perfil);
}

// Obtener información actual del usuario
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    $mensaje = "Error: No se encontró el usuario.";
} else {
    $mensaje = '';
}

// Manejar actualización de información personal
if (isset($_POST['actualizar_info'])) {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $email = $conexion->real_escape_string($_POST['email']);
    
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $mensaje = "El email ya está en uso por otro usuario";
    } else {
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $email, $usuario_id);
        
        if ($stmt->execute()) {
            $_SESSION['nombre'] = $nombre;
            $mensaje = "Información actualizada correctamente";
            $usuario['nombre'] = $nombre;
            $usuario['email'] = $email;
        } else {
            $mensaje = "Error al actualizar la información";
        }
    }
}

// Manejar cambio de contraseña
if (isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'];
    $password_nuevo = $_POST['password_nuevo'];
    $password_confirmar = $_POST['password_confirmar'];
    
    if ($password_nuevo === $password_confirmar) {
        if (password_verify($password_actual, $usuario['password'])) {
            $password_hash = password_hash($password_nuevo, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $password_hash, $usuario_id);
            
            if ($stmt->execute()) {
                $mensaje = "Contraseña actualizada correctamente";
            } else {
                $mensaje = "Error al actualizar la contraseña";
            }
        } else {
            $mensaje = "La contraseña actual es incorrecta";
        }
    } else {
        $mensaje = "Las nuevas contraseñas no coinciden";
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>joystrick genius</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #000000, #1a0000, #330000);
            background-attachment: fixed;
        }
        .red-glow {
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.3);
        }
        .hover-red-glow:hover {
            box-shadow: 0 0 25px rgba(255, 0, 0, 0.5);
        }
    </style>
</head>
<body class="min-h-screen text-white flex flex-col">
    <header class="sticky top-0 z-50 bg-black/80 backdrop-blur-md border-b border-red-600/30 p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="principal.php" class="flex items-center space-x-2 bg-red-600/30 hover:bg-red-600/50 py-2 px-4 rounded-full transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-red-300 group-hover:translate-x-[-5px] transition-transform">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                <span class="text-red-200">Regresar</span>
            </a>
            <h1 class="text-2xl font-bold text-red-500">Configuración de Usuario</h1>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 flex-grow">
        <?php if ($mensaje): ?>
            <div class="bg-red-900/50 text-red-200 p-4 rounded-lg mb-6 text-center red-glow">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if (!$usuario): ?>
        <div class="bg-red-900/50 text-red-200 p-4 rounded-lg mb-6 text-center red-glow">
            No se pudo cargar la información del usuario. Por favor, intenta de nuevo.
        </div>
        <?php else: ?>
        <div class="bg-black/60 border border-red-600/30 rounded-xl p-6 mb-8 red-glow">
            <div class="flex items-center mb-6">
                <img src="<?php echo obtenerFotoPerfil($usuario, 'medium'); ?>" 
                    alt="<?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?>"
                    class="w-24 h-24 rounded-full mr-6 object-cover border-2 border-red-500">
                <div>
                    <h1 class="text-2xl font-bold text-red-500">Mi Perfil</h1>
                    <p class="text-gray-300">Gestiona tu configuración y preferencias</p>
                </div>
            </div>
        </div>

        <div class="bg-black/60 border border-red-600/30 rounded-xl p-6 mb-8 red-glow">
            <h2 class="text-xl font-semibold text-red-500 mb-4">Información Personal</h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Nombre de usuario</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required class="w-full bg-red-900/30 border border-red-500/30 p-2 rounded text-white">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Correo electrónico</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required class="w-full bg-red-900/30 border border-red-500/30 p-2 rounded text-white">
                </div>
                <button type="submit" name="actualizar_info" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-full transition hover-red-glow">Guardar cambios</button>
            </form>
        </div>

        <div class="bg-black/60 border border-red-600/30 rounded-xl p-6 mb-8 red-glow">
            <h2 class="text-xl font-semibold text-red-500 mb-4">Estado de la cuenta</h2>
            <div class="mb-2">
                <label class="font-bold text-gray-300">Tipo de usuario:</label>
                <span class="ml-2"><?php echo ucfirst($usuario['tipo_usuario'] ?? 'N/A'); ?></span>
            </div>
            <div class="mb-2">
                <label class="font-bold text-gray-300">Fecha de registro:</label>
                <span class="ml-2"><?php echo isset($usuario['fecha_registro']) ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : 'N/A'; ?></span>
            </div>
            <div class="mb-2">
                <label class="font-bold text-gray-300">Último acceso:</label>
                <span class="ml-2"><?php echo isset($usuario['ultimo_acceso']) && $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'N/A'; ?></span>
            </div>
        </div>

        <div class="bg-black/60 border border-red-600/30 rounded-xl p-6 mb-8 red-glow">
            <h2 class="text-xl font-semibold text-red-500 mb-4">Cambiar Contraseña</h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Contraseña actual</label>
                    <input type="password" name="password_actual" required class="w-full bg-red-900/30 border border-red-500/30 p-2 rounded text-white">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Nueva contraseña</label>
                    <input type="password" name="password_nuevo" required class="w-full bg-red-900/30 border border-red-500/30 p-2 rounded text-white">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmar" required class="w-full bg-red-900/30 border border-red-500/30 p-2 rounded text-white">
                </div>
                <button type="submit" name="cambiar_password" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-full transition hover-red-glow">Cambiar contraseña</button>
            </form>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer Profesional -->
    <footer class="bg-black/90 text-gray-300 py-12 px-6 mt-auto">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h4 class="text-red-500 font-bold mb-4">Pixel Play</h4>
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
                    <a href="#" class="hover:text-red-500"><i class="lucide lucide-twitter"></i></a>
                    <a href="#" class="hover:text-red-500"><i class="lucide lucide-instagram"></i></a>
                    <a href="#" class="hover:text-red-500"><i class="lucide lucide-facebook"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-8 border-t border-red-600/30 pt-4">
            <p>&copy; 2025 Pixel Play. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>