<?php
session_start();
require_once 'conexion.php';
$conexion = $conexion;

// Verificar si el usuario está autenticado
$user_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

// Consulta para obtener los favoritos del usuario
$favorites_query = "SELECT g.id, g.title, g.image_url, g.release_date, c.nombre as categoria
                    FROM user_favorites uf
                    JOIN games g ON uf.game_id = g.id
                    LEFT JOIN game_categories gc ON g.id = gc.game_id
                    LEFT JOIN categories c ON gc.category_id = c.id
                    WHERE uf.user_id = ?";
$stmt_favorites = $conexion->prepare($favorites_query);
$stmt_favorites->bind_param("i", $user_id);
$stmt_favorites->execute();
$favorites_result = $stmt_favorites->get_result();

// Procesar eliminación de favoritos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id']) && isset($_POST['remove_favorite'])) {
    $game_id = (int)$_POST['game_id'];
    $delete_favorite = $conexion->prepare("DELETE FROM user_favorites WHERE user_id = ? AND game_id = ?");
    $delete_favorite->bind_param("ii", $user_id, $game_id);
    $delete_favorite->execute();
    header("Location: favoritos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>joystrick genius</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../vista/css/favoritos.css">
</head>
<body class="min-h-screen text-white flex flex-col">
    <!-- Encabezado Profesional -->
    <header class="sticky top-0 z-50 bg-black/90 backdrop-blur-md border-b border-red-600/40 p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="videojuegos.php" class="nav-button flex items-center space-x-2 bg-red-600/30 hover:bg-red-600/50 py-2 px-4 rounded-lg transition-all group" aria-label="Regresar a videojuegos">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-red-300 group-hover:translate-x-[-5px] transition-transform">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                <span class="text-red-200 font-semibold">Regresar</span>
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-red-100">Mis Favoritos</h1>
            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['tipo_usuario'] === 'admin'): ?>
                <a href="admin_videojuegos.php" class="nav-button flex items-center space-x-2 bg-red-600/30 hover:bg-red-600/50 py-2 px-4 rounded-lg transition-all group" aria-label="Ir al panel de administración">
                    <i class="fas fa-cog text-red-300"></i>
                    <span class="text-red-200 font-semibold">Administrar</span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Sección de Favoritos -->
    <main class="container mx-auto px-4 py-8 flex-grow">
        <h2 class="text-2xl font-bold text-red-200 mb-6">Tus Videojuegos Favoritos</h2>
        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <div class="text-center text-gray-400">
                <p>Inicia sesión para ver tus videojuegos favoritos.</p>
                <a href="login.php" class="mt-4 inline-block bg-red-600/50 hover:bg-red-600/70 text-red-200 py-2 px-4 rounded-lg transition-all font-semibold" aria-label="Iniciar sesión">
                    Iniciar Sesión
                </a>
            </div>
        <?php elseif ($favorites_result->num_rows === 0): ?>
            <p class="text-gray-400 text-center">Aún no tienes videojuegos favoritos. ¡Agrega algunos desde la página de videojuegos!</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($favorite = $favorites_result->fetch_assoc()): ?>
                    <div class="favorite-card bg-black/80 border border-red-600/30 rounded-xl overflow-hidden shadow-lg" role="article" aria-labelledby="favorite-title-<?php echo $favorite['id']; ?>">
                        <?php if ($favorite['image_url']): ?>
                            <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                                <img src="../vista/<?php echo htmlspecialchars($favorite['image_url']); ?>" 
                                    alt="<?php echo htmlspecialchars($favorite['title']); ?>"
                                    class="w-full h-48 object-cover opacity-90 hover:opacity-100 transition-opacity"
                                    loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-black to-red-900/50"></div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 id="favorite-title-<?php echo $favorite['id']; ?>" class="text-xl font-bold text-red-100 mb-3 line-clamp-2">
                                <?php echo htmlspecialchars($favorite['title']); ?>
                            </h3>
                            <p class="text-gray-400 mb-2">Categoría: <?php echo htmlspecialchars($favorite['categoria'] ?? 'Sin categoría'); ?></p>
                            <p class="text-gray-400 mb-4">Lanzamiento: <?php echo !empty($favorite['release_date']) ? date('d.m.Y', strtotime($favorite['release_date'])) : 'N/A'; ?></p>
                            <form method="POST" action="">
                                <input type="hidden" name="game_id" value="<?php echo $favorite['id']; ?>">
                                <button type="submit" name="remove_favorite" class="remove-button text-red-300 hover:text-red-200 transition-colors" aria-label="Quitar de favoritos">
                                    <i class="fas fa-heart-broken"></i> Quitar de Favoritos
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Pie de Página -->
    <footer class="bg-gradient-to-br from-black/90 to-red-900/40 backdrop-blur-xl border-t border-red-600/30 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-300 animate-pulse">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        <h3 class="text-2xl font-bold text-red-200 tracking-tight">Pixel Play</h3>
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        Conectando el universo de los videojuegos con pasión y tecnología. Explora el futuro del gaming.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="text-red-400 hover:text-red-300 transition-colors" aria-label="Twitter" title="Link no disponible">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-50">
                                <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5.3c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-red-400 hover:text-red-300 transition-colors" aria-label="LinkedIn" title="Link no disponible">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-50">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                <rect x="2" y="9" width="4" height="12"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                        </a>
                        <a href="#" class="text-red-400 hover:text-red-300 transition-colors" aria-label="GitHub" title="Link no disponible">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-50">
                                <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.28 1.15-.28 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/>
                                <path d="M9 18c-4.51 2-5-2-7-2"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 bg-black/60 p-6 rounded-xl border border-red-600/20">
                    <div>
                        <h4 class="text-lg font-semibold text-red-200 mb-3 border-b border-red-600/30 pb-2">Secciones</h4>
                        <nav class="space-y-2">
                            <a href="informacion.php" class="block text-gray-400 hover:text-red-300 transition-colors flex items-center space-x-2 group" aria-label="Información">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                                <span>Información</span>
                            </a>
                            <a href="nosotros.php" class="block text-gray-400 hover:text-red-300 transition-colors flex items-center space-x-2 group" aria-label="Nosotros">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                                <span>Nosotros</span>
                            </a>
                            <a href="contactenos.php" class="block text-gray-400 hover:text-red-300 transition-colors flex items-center space-x-2 group" aria-label="Contacto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                                <span>Contacto</span>
                            </a>
                        </nav>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-red-200 mb-3 border-b border-red-600/30 pb-2">Legal</h4>
                        <nav class="space-y-2">
                            <a href="Terminos_y_Condiciones.php" class="block text-gray-400 hover:text-red-300 transition-colors flex items-center space-x-2 group" aria-label="Privacidad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                                <span>Privacidad</span>
                            </a>
                            <a href="Terminos_y_Condiciones.php" class="block text-gray-400 hover:text-red-300 transition-colors flex items-center space-x-2 group" aria-label="Términos">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                                <span>Términos</span>
                            </a>
                            <a href="#" class="block text-gray-400 hover:text-red-300 transition-colors flex items-center space-x-2 group" aria-label="Cookies">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500 group-hover:translate-x-1 transition-transform">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                                <span>Cookies</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="mt-6 text-center text-gray-500 border-t border-red-600/20 pt-4">
                © 2024 Pixel Play. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Placeholder para carga -->
    <script>
        // Simular placeholders de carga
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.favorite-card');
            cards.forEach(card => {
                card.classList.add('loading-card');
                setTimeout(() => card.classList.remove('loading-card'), 1000);
            });
        });
    </script>
</body>
</html>
<?php
// Cerrar conexión y declaraciones
$stmt_favorites->close();
$conexion->close();
?>