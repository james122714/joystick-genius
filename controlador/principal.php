```php
<?php
// principal.php - Vista principal actualizada con diseño profesional en negro y rojo y opción de administración condicional
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener información actualizada del usuario
require_once 'conexion.php';
$conexion = $conexion;

$usuario_id = $_SESSION['usuario_id'];
$stmt = $conexion->prepare("SELECT u.nombre, u.foto_perfil, u.tipo_usuario, r.nombre as rango_nombre 
                        FROM usuarios u 
                        LEFT JOIN rangos r ON u.rango_id = r.id 
                        WHERE u.id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$foto_perfil = $usuario['foto_perfil'] ? '../vista/' . $usuario['foto_perfil'] : '../vista/multimedia/default-profile.png';
$rango_nombre = isset($usuario['rango_nombre']) ? $usuario['rango_nombre'] : 'Novato';
$es_admin = in_array($usuario['tipo_usuario'], ['admin', 'moderador', 'adminvistas']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>joystick genius - Página Principal</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide-icons/0.263.1/lucide.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../vista/css/principal.css">
</head>

<body class="min-h-screen text-white flex flex-col">
    <!-- Encabezado Profesional -->
    <header class="sticky top-0 z-50 bg-black/80 backdrop-blur-md border-b border-red-600/30 p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="principal.php" class="flex items-center space-x-2">
                <span class="text-2xl font-bold text-red-500">joystick genius</span>
            </a>
            <nav class="flex space-x-6">
                <a href="noticias.php" class="text-red-300 hover:text-red-500 transition">Noticias</a>
                <a href="tutoriales.php" class="text-red-300 hover:text-red-500 transition">Tutoriales</a>
                <a href="videojuegos.php" class="text-red-300 hover:text-red-500 transition">Juegos</a>
                <a href="lanzamiento.php" class="text-red-300 hover:text-red-500 transition">Lanzamientos</a>
            </nav>
            <div class="relative">
                <div class="flex items-center cursor-pointer" onclick="toggleUserMenu()">
                    <img src="<?php echo htmlspecialchars($foto_perfil); ?>"
                        alt="Usuario"
                        class="w-10 h-10 rounded-full mr-3 object-cover">
                    <span class="text-red-300"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                </div>
                <div id="userDropdown" class="absolute right-0 mt-2 bg-black/80 border border-red-600/30 rounded-xl p-4 hidden">
                    <div class="text-center mb-4">
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>"
                            alt="Usuario"
                            class="w-20 h-20 rounded-full mx-auto mb-3 object-cover">
                        <h3 class="font-bold text-red-500"><?php echo htmlspecialchars($usuario['nombre']); ?></h3>
                    </div>
                    <p class="text-gray-300 mb-6 font-semibold" style="font-size:1.2rem;">
                        <h1>Rango:</h1><?php echo htmlspecialchars($usuario['rango_nombre'] ?? 'Novato'); ?>
                    </p>
                    <ul class="space-y-2">
                        <li><a href="perfil.php" class="block hover:text-red-500">Mi Perfil</a></li>
                        <li><a href="favoritos.php" class="text-red-200 font-semibold">Mi favoritos</a></li>
                        <li><a href="confi_user.php" class="block hover:text-red-500">Configuración</a></li>
                        <?php if ($es_admin): ?>
                            <li><a href="administracion.php" class="block hover:text-red-500">Administración</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="block hover:text-red-500">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Sección Hero Profesional -->
    <section class="container mx-auto px-4 py-16 text-center">
        <h1 class="text-5xl font-bold text-red-500 mb-4">Bienvenido a joystrick genius</h1>
        <p class="text-xl text-gray-300 mb-8">La plataforma definitiva para gamers apasionados. Descubre tutoriales, trucos y comunidades para elevar tu juego al siguiente nivel.</p>
        <a href="tutoriales.php" class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-full font-semibold transition hover-red-glow">Explorar Tutoriales</a>
    </section>

    <!-- Sección Destacados -->
    <section class="container mx-auto px-4 py-12">
        <h2 class="text-3xl font-semibold text-red-500 mb-8 text-center">Destacados</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-black/60 border border-red-600/30 rounded-xl p-6 red-glow hover-red-glow transition">
                <h3 class="text-2xl font-bold text-red-400 mb-4">Tutoriales Avanzados</h3>
                <p class="text-gray-400">Mejora tus habilidades con guías expertas en juegos populares.</p>
            </div>
            <div class="bg-black/60 border border-red-600/30 rounded-xl p-6 red-glow hover-red-glow transition">
                <h3 class="text-2xl font-bold text-red-400 mb-4">Comunidad Activa</h3>
                <p class="text-gray-400">Únete a discusiones y comparte tus logros con otros gamers.</p>
            </div>
            <div class="bg-black/60 border border-red-600/30 rounded-xl p-6 red-glow hover-red-glow transition">
                <h3 class="text-2xl font-bold text-red-400 mb-4">Eventos Exclusivos</h3>
                <p class="text-gray-400">Participa en torneos y gana premios increíbles.</p>
            </div>
        </div>
    </section>

    <!-- Sección Noticias Destacadas -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-semibold text-red-500 mb-8 text-center">Noticias Destacadas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $noticias = $conexion->query("SELECT n.*, u.nombre as autor_nombre 
                FROM noticias n 
                LEFT JOIN usuarios u ON n.autor_id = u.id 
                WHERE n.estado = 'activo' 
                AND n.destacada = 1 
                AND (n.fecha_expiracion IS NULL OR n.fecha_expiracion > NOW()) 
                ORDER BY n.fecha_publicacion DESC LIMIT 3");
                while ($noticia = $noticias->fetch_assoc()):
                ?>
                    <div class="bg-black/60 border border-red-600/30 rounded-xl overflow-hidden red-glow hover-red-glow transition">
                        <?php if ($noticia['imagen']): ?>
                            <img src="../vista/<?php echo $noticia['imagen']; ?>"
                                alt="<?php echo htmlspecialchars($noticia['titulo']); ?>"
                                class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-red-400 mb-2"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                            <p class="text-sm text-gray-400 mb-3">
                                Por <?php echo htmlspecialchars($noticia['autor_nombre']); ?> |
                                <?php echo htmlspecialchars($noticia['categoria']); ?>
                            </p>
                            <p class="text-gray-300 mb-4">
                                <?php echo substr(strip_tags($noticia['contenido']), 0, 120) . '...'; ?>
                            </p>
                            <a href="ver_noticia.php?id=<?php echo $noticia['id']; ?>"
                                class="text-red-300 hover:text-red-500 font-bold">
                                Leer Más →
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Footer Profesional -->
    <footer class="bg-black/90 text-gray-300 py-12 px-6 mt-auto">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h4 class="text-red-500 font-bold mb-4">joystrick genius</h4>
                <p>Plataforma para gamers profesionales y entusiastas.</p>
            </div>
            <div>
                <h4 class="text-red-500 font-bold mb-4">Enlaces Rápidos</h4>
                <ul class="space-y-2">
                    <li><a href="tutoriales.php" class="hover:text-red-500 transition">Tutoriales</a></li>
                    <li><a href="juego2.php" class="hover:text-red-500 transition">Juegos</a></li>
                    <li><a href="comunidad.php" class="hover:text-red-500 transition">Comunidad</a></li>
                    <li><a href="noticia2.php" class="hover:text-red-500 transition">noticias</a></li>
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
            <p>&copy; 2025 joystick genius. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="../vista/js/principal.js"></script>
</body>

</html>
```