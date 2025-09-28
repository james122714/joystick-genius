<?php
session_start();
require_once 'conexion.php';
$conexion = $conexion;

// Configuración de paginación
$noticias_por_pagina = 9;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $noticias_por_pagina;

// Filtro de categoría
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$where_categoria = '';
if ($categoria_filtro) {
    $categoria_filtro = $conexion->real_escape_string($categoria_filtro);
    $where_categoria = "AND n.categoria = '$categoria_filtro'";
}

// Consulta para obtener el total de noticias
$total_query = "SELECT COUNT(*) as total FROM noticias n 
                WHERE n.estado = 'activo' 
                AND (n.fecha_expiracion IS NULL OR n.fecha_expiracion > NOW()) 
                $where_categoria";
$total_result = $conexion->query($total_query);
$total_noticias = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_noticias / $noticias_por_pagina);

// Consulta para obtener las noticias
$query = "SELECT n.*, u.nombre as autor_nombre 
        FROM noticias n 
        LEFT JOIN usuarios u ON n.autor_id = u.id 
        WHERE n.estado = 'activo' 
        AND (n.fecha_expiracion IS NULL OR n.fecha_expiracion > NOW()) 
        $where_categoria
        ORDER BY n.fecha_publicacion DESC 
        LIMIT $offset, $noticias_por_pagina";
$noticias = $conexion->query($query);

// Función para verificar si la noticia es reciente (menos de 3 días)
function es_noticia_reciente($fecha_publicacion) {
    $fecha = new DateTime($fecha_publicacion);
    $hoy = new DateTime();
    $intervalo = $hoy->diff($fecha);
    return $intervalo->days <= 3;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide-icons/0.263.1/lucide.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../vista/css/noticias.css">
</head>
<body class="min-h-screen text-white flex flex-col">
    <!-- Encabezado Profesional -->
    <header class="sticky top-0 z-50 bg-black/90 backdrop-blur-md border-b border-red-600/40 p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="principal.php" class="flex items-center space-x-2 bg-red-600/30 hover:bg-red-600/50 py-2 px-4 rounded-lg transition-all group" aria-label="Regresar a la página principal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-red-300 group-hover:translate-x-[-5px] transition-transform">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                <span class="text-red-200 font-semibold">Regresar</span>
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-red-100">Red de Noticias </h1>
            <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin'): ?>
                <a href="noticias.php" class="flex items-center space-x-2 bg-red-600/30 hover:bg-red-600/50 py-2 px-4 rounded-lg transition-all group" aria-label="Ir al panel de administración">
                    <i class="fas fa-cog text-red-300"></i>
                    <span class="text-red-200 font-semibold">Administrar</span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Banner Informativo -->
    <div class="relative overflow-hidden bg-gradient-to-r from-black/90 to-red-900/40 border border-red-600/30 rounded-2xl my-8 mx-4">
        <div class="absolute inset-0 bg-grid-white/[0.02] bg-[length:30px_30px]"></div>
        <div class="relative p-8 flex flex-col md:flex-row items-center gap-6">
            <div class="flex-shrink-0 w-16 h-16 bg-red-600/20 rounded-full flex items-center justify-center border border-red-400/20">
                <svg class="w-8 h-8 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="flex-grow text-center md:text-left">
                <h2 class="text-2xl font-semibold text-red-200 mb-2 tracking-tight">Portal de Noticias Cuánticas</h2>
                <p class="text-gray-300 leading-relaxed max-w-2xl">
                    Explora un universo de información cuidadosamente curada. Conéctate con historias que trascienden el tiempo y el espacio, diseñadas para informar e inspirar.
                </p>
            </div>
        </div>
    </div>

    <!-- Filtro de Categorías -->
    <div class="container mx-auto px-4 py-6 flex justify-center space-x-4 flex-wrap">
        <?php
        $categorias = ['', 'general', 'tecnologia', 'deportes', 'entretenimiento'];
        foreach ($categorias as $cat):
            $categoria_nombre = $cat ? ucfirst($cat) : 'Todas';
        ?>
            <a href="noticia2.php<?php echo $cat ? '?categoria='.$cat : ''; ?>" 
                class="category-button px-4 py-2 <?php echo $categoria_filtro === $cat ? 'bg-red-600/50' : 'bg-black/60'; ?> hover:bg-red-600/40 border border-red-600/30 rounded-lg flex items-center space-x-2 transition-all font-semibold text-red-200" 
                aria-label="Filtrar por categoría <?php echo $categoria_nombre; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-red-300">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span><?php echo $categoria_nombre; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Cuadrícula de Noticias -->
    <main class="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 py-8 flex-grow">
        <?php if ($noticias->num_rows === 0): ?>
            <div class="col-span-full text-center text-gray-400">No hay noticias disponibles en esta categoría.</div>
        <?php else: ?>
            <?php while ($noticia = $noticias->fetch_assoc()): ?>
                <div class="news-card bg-black/80 border border-red-600/30 rounded-xl overflow-hidden shadow-lg" role="article" aria-labelledby="news-title-<?php echo $noticia['id']; ?>">
                    <?php if ($noticia['imagen']): ?>
                        <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                            <img src="../vista/multimedia/<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                alt="<?php echo htmlspecialchars($noticia['titulo']); ?>"
                                class="w-full h-48 object-cover opacity-90 hover:opacity-100 transition-opacity"
                                loading="lazy">
                        </div>
                    <?php else: ?>
                        <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-black to-red-900/50"></div>
                    <?php endif; ?>
                    <div class="p-6 relative">
                        <?php if (es_noticia_reciente($noticia['fecha_publicacion'])): ?>
                            <span class="absolute top-4 right-4 bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Nueva</span>
                        <?php endif; ?>
                        <div class="flex justify-between items-center mb-4">
                            <span class="bg-red-600/30 text-red-300 px-3 py-1 rounded-full text-xs uppercase tracking-wider font-semibold">
                                <?php echo htmlspecialchars($noticia['categoria']); ?>
                            </span>
                            <?php if ($noticia['destacada']): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400 w-5 h-5">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <h2 id="news-title-<?php echo $noticia['id']; ?>" class="text-xl font-bold text-red-100 mb-3 line-clamp-2">
                            <?php echo htmlspecialchars($noticia['titulo']); ?>
                        </h2>
                        <p class="text-gray-400 mb-4 line-clamp-3">
                            <?php echo substr(strip_tags($noticia['contenido']), 0, 150) . '...'; ?>
                        </p>
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <span>Por <?php echo htmlspecialchars($noticia['autor_nombre']); ?></span>
                            <span>Creado: <?php echo date('d.m.Y', strtotime($noticia['fecha_publicacion'])); ?></span>
                        </div>
                        <?php if ($noticia['fecha_expiracion']): ?>
                            <div class="text-sm text-gray-500 mb-4">
                                <span>Expira: <?php echo date('d.m.Y', strtotime($noticia['fecha_expiracion'])); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between items-center">
                            <a href="anuncios.php?id=<?php echo $noticia['id']; ?>" 
                                class="text-center bg-red-600/30 hover:bg-red-600/50 text-red-200 py-2 px-4 rounded-lg transition-all font-semibold"
                                aria-label="Leer más sobre <?php echo htmlspecialchars($noticia['titulo']); ?>">
                                Leer más
                            </a>
                            <button onclick="navigator.share({title: '<?php echo htmlspecialchars($noticia['titulo']); ?>', url: '<?php echo 'anuncios.php?id=' . $noticia['id']; ?>'})"
                                class="share-button text-red-300 hover:text-red-200 transition-colors" 
                                aria-label="Compartir noticia <?php echo htmlspecialchars($noticia['titulo']); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 22a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM6 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM18 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM15.83 6.5l-7.66 4M8.17 13.5l7.66 4"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </main>

    <!-- Paginación -->
    <?php if ($total_paginas > 1): ?>
        <div class="container mx-auto flex justify-center space-x-2 pb-8">
            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?><?php echo $categoria_filtro ? '&categoria=' . $categoria_filtro : ''; ?>" 
                    class="bg-black/60 hover:bg-red-600/40 border border-red-600/30 px-4 py-2 rounded-lg transition-all font-semibold text-red-200"
                    aria-label="Página anterior">
                    Anterior
                </a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $categoria_filtro ? '&categoria=' . $categoria_filtro : ''; ?>" 
                    class="<?php echo $pagina === $i ? 'bg-red-600/50' : 'bg-black/60'; ?> hover:bg-red-600/40 border border-red-600/30 px-4 py-2 rounded-lg transition-all font-semibold text-red-200"
                    aria-label="Página <?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($pagina < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina + 1; ?><?php echo $categoria_filtro ? '&categoria=' . $categoria_filtro : ''; ?>" 
                    class="bg-black/60 hover:bg-red-600/40 border border-red-600/30 px-4 py-2 rounded-lg transition-all font-semibold text-red-200"
                    aria-label="Página siguiente">
                    Siguiente
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Pie de Página -->
    <footer class="bg-gradient-to-br from-black/90 to-red-900/40 backdrop-blur-xl border-t border-red-600/30 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-300 animate-pulse">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        <h3 class="text-2xl font-bold text-red-200 tracking-tight">Noticias Cuánticas</h3>
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        Conectando el universo de la información con precisión cuántica. Descubre el futuro de las noticias.
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
                © 2024 joystick genius. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Placeholder para carga -->
    <script>
        // Simular placeholders de carga
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.news-card');
            cards.forEach(card => {
                card.classList.add('loading-card');
                setTimeout(() => card.classList.remove('loading-card'), 1000);
            });
        });
    </script>
</body>
</html>