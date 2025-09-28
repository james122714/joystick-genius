<?php
// lanzamiento.php - Vista actualizada con diseño profesional en negro y rojo, función de estrellas y estadísticas
require_once 'conexion.php';
$conexion = $conexion;

// Configuración de paginación
$lanzamientos_por_pagina = 9;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $lanzamientos_por_pagina;

// Filtro de categoría
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$where_categoria = '';
if ($categoria_filtro) {
    $categoria_filtro = $conexion->real_escape_string($categoria_filtro);
    $where_categoria = "AND category = '$categoria_filtro'";
}

// Consulta para obtener el total de lanzamientos
$total_query = "SELECT COUNT(*) as total FROM game_launches WHERE 1=1 $where_categoria";
$total_result = $conexion->query($total_query);
$total_lanzamientos = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_lanzamientos / $lanzamientos_por_pagina);

// Consulta para obtener los lanzamientos
$query = "SELECT * FROM game_launches 
        WHERE 1=1 $where_categoria
        ORDER BY release_date DESC 
        LIMIT $offset, $lanzamientos_por_pagina";
$lanzamientos = $conexion->query($query);

// Función para mostrar estrellas
function displayStars($rating) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;
    $output = '';
    
    // Estrellas completas
    for ($i = 0; $i < $full_stars; $i++) {
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>';
    }
    
    // Media estrella
    if ($half_star) {
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                        <path d="M12 5.173l2.335 4.817 5.305.732-3.861 3.757.912 5.318-4.686-2.465V5.173z"/>
                    </svg>';
    }
    
    // Estrellas vacías
    for ($i = 0; $i < $empty_stars; $i++) {
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-yellow-400">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>';
    }
    
    return $output;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide-icons/0.263.1/lucide.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../vista/css/lanzamiento.css">
</head>
<body class="min-h-screen text-white flex flex-col">
    <!-- Encabezado Profesional -->
    <header class="sticky top-0 z-50 bg-black/80 backdrop-blur-md border-b border-red-600/30 p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="principal.php" class="flex items-center space-x-2 bg-red-600/30 hover:bg-red-600/50 py-2 px-4 rounded-full transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-red-300 group-hover:translate-x-[-5px] transition-transform">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                <span class="text-red-200">Regresar</span>
            </a>
            <h1 class="text-2xl font-bold text-red-500">Próximos Lanzamientos</h1>
        </div>
    </header>

    <!-- Banner Informativo Profesional -->
    <div class="relative overflow-hidden bg-gradient-to-r from-red-900/30 to-black/30 border border-red-600/20 rounded-2xl my-8 mx-4">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-grid-white/[0.02] bg-[length:30px_30px]"></div>
            <div class="absolute h-full w-full bg-gradient-to-br from-red-500/10 via-transparent to-black/10 animate-pulse"></div>
        </div>
        
        <div class="relative p-8 flex flex-col md:flex-row items-center gap-6">
            <div class="flex-shrink-0 w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center border border-red-400/20">
                <svg class="w-8 h-8 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="flex-grow text-center md:text-left">
                <h2 class="text-2xl font-bold text-red-200 mb-2 tracking-wide">Próximos Lanzamientos</h2>
                <p class="text-gray-300 leading-relaxed">
                    Descubre los juegos que están por llegar y prepárate para experiencias épicas. 
                    Reserva ahora y sé el primero en explorar nuevos mundos de entretenimiento.
                </p>
            </div>
        </div>
    </div>


    <!-- Filtro de Categorías -->
    <div class="container mx-auto px-4 py-6 flex justify-center flex-wrap gap-4">
        <?php
        $categorias = ['', 'accion', 'aventura', 'rpg', 'estrategia'];
        foreach ($categorias as $cat):
            $categoria_nombre = $cat ? ucfirst($cat) : 'Todas';
        ?>
            <a href="lanzamiento2.php<?php echo $cat ? '?categoria='.$cat : ''; ?>" 
                class="px-4 py-2 <?php echo $categoria_filtro === $cat ? 'bg-red-600/50' : 'bg-red-900/30'; ?> hover:bg-red-600/40 border border-red-500/30 rounded-full flex items-center space-x-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter text-red-300">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span><?php echo $categoria_nombre; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Cuadrícula de Lanzamientos -->
    <main class="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 py-8 flex-grow">
        <?php while ($game = $lanzamientos->fetch_assoc()): ?>
            <div class="bg-black/60 border border-red-500/20 rounded-xl overflow-hidden transform hover:scale-105 transition-all duration-300 red-glow hover-red-glow">
                <?php if($game['image_url']): ?>
                    <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                        <img src="../vista/<?php echo htmlspecialchars($game['image_url']); ?>" 
                            alt="<?php echo htmlspecialchars($game['title']); ?>"
                            class="w-full h-48 object-cover opacity-90 hover:opacity-100 transition-opacity">
                    </div>
                <?php else: ?>
                    <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-black to-red-900 opacity-90"></div>
                <?php endif; ?>

                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="bg-red-600/30 text-red-300 px-3 py-1 rounded-full text-xs uppercase tracking-wider">
                            <?php echo htmlspecialchars($game['category']); ?>
                        </span>
                        <span class="flex items-center text-yellow-400">
                            <?php echo displayStars($game['rating']); ?>
                        </span>
                    </div>
                    <h2 class="text-xl font-semibold text-red-100 mb-3">
                        <?php echo htmlspecialchars($game['title']); ?>
                    </h2>
                    <p class="text-gray-300 mb-4">
                        <?php echo substr(strip_tags($game['description']), 0, 150) . '...'; ?>
                    </p>
                    <div class="flex justify-between text-sm text-gray-400">
                        <span>Lanzamiento: <?php echo date('d.m.Y', strtotime($game['release_date'])); ?></span>
                        <span class="font-bold text-red-400">$<?php echo number_format($game['price'], 2); ?></span>
                    </div>
                    <a href="#" 
                        onclick="handleGameAction('<?php 
                            $gameUrl = $game['game_url'];
                            if (!preg_match("~^(?:f|ht)tps?://~i", $gameUrl)) {
                                $gameUrl = 'https://' . $gameUrl;
                            }
                            echo htmlspecialchars($gameUrl); 
                        ?>', '<?php echo $game['release_date']; ?>')"
                        class="block mt-4 text-center bg-red-600/30 hover:bg-red-600/50 text-red-200 py-2 rounded transition-all">
                        <?php echo $game['pre_order'] ? 'Pre-ordenar' : 'Comprar'; ?>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </main>

    <!-- Paginación Profesional -->
    <?php if ($total_paginas > 1): ?>
        <div class="container mx-auto flex justify-center space-x-2 pb-8">
            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?><?php echo $categoria_filtro ? '&categoria=' . $categoria_filtro : ''; ?>" 
                    class="bg-red-900/30 hover:bg-red-600/40 border border-red-500/30 px-4 py-2 rounded-full transition-all">
                    Anterior
                </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $categoria_filtro ? '&categoria=' . $categoria_filtro : ''; ?>" 
                    class="<?php echo $pagina === $i ? 'bg-red-600/50' : 'bg-red-900/30'; ?> hover:bg-red-600/40 border border-red-500/30 px-4 py-2 rounded-full transition-all">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($pagina < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina + 1; ?><?php echo $categoria_filtro ? '&categoria=' . $categoria_filtro : ''; ?>" 
                    class="bg-red-900/30 hover:bg-red-600/40 border border-red-500/30 px-4 py-2 rounded-full transition-all">
                    Siguiente
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

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
                    <li><a href="principal.php" class="hover:text-red-500 transition">Inicio</a></li>
                    <li><a href="juegos2.php" class="hover:text-red-500 transition">Juegos</a></li>
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
            <p>&copy; 2025 joystick genius. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
<?php
// Cerrar la conexión
$conexion->close();
?>