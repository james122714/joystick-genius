<?php
require_once('conexion.php');
$conexion = $conexion;

// Configuración de paginación
$tutoriales_por_pagina = 9;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $tutoriales_por_pagina;

// Filtro de dificultad
$dificultad_filtro = isset($_GET['dificultad']) ? $_GET['dificultad'] : '';
$where_dificultad = '';
if ($dificultad_filtro) {
    $dificultad_filtro = $conexion->real_escape_string($dificultad_filtro);
    $where_dificultad = "WHERE nivel_dificultad = '$dificultad_filtro'";
}

// Consulta para obtener el total de tutoriales
$total_query = "SELECT COUNT(*) as total FROM tutoriales $where_dificultad";
$total_result = $conexion->query($total_query);
$total_tutoriales = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_tutoriales / $tutoriales_por_pagina);

// Consulta para obtener los tutoriales con paginación
$tutoriales_query = "SELECT * FROM tutoriales $where_dificultad LIMIT $tutoriales_por_pagina OFFSET $offset";
$tutoriales = $conexion->query($tutoriales_query);
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
    <link rel="stylesheet" href="../vista/css/tutoriales.css">
</head>

<body class="min-h-screen text-white flex flex-col">
    <!-- Encabezado Profesional -->
    <header class="sticky top-0 z-50 bg-black/80 backdrop-blur-md border-b border-red-600/30 p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="principal.php" class="flex items-center space-x-2 bg-red-600/30 hover:bg-red-600/50 py-2 px-4 rounded-full transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-red-300 group-hover:translate-x-[-5px] transition-transform">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                <span class="text-red-200">Regresar</span>
            </a>
            <h1 class="text-2xl font-bold text-red-500">joystrick genius - Tutoriales</h1>
        </div>
    </header>

    <!-- Descripción de la página - Más profesional -->
    <main class="container mx-auto px-4 py-8 flex-grow">
        <section class="text-center">
            <h2 class="text-3xl font-semibold mb-4 text-red-500">Tutoriales de Juegos</h2>
            <p class="text-lg text-gray-300">
                Accede a tutoriales curados por expertos, adaptados a todos los niveles de experiencia.
                Desde fundamentos esenciales hasta estrategias avanzadas y optimizaciones para speedruns.
            </p>
            <p class="mt-4 text-lg text-gray-300">
                Filtra por dificultad para personalizar tu aprendizaje y maximizar tu rendimiento en el juego.
            </p>
        </section>
    </main>

    <!-- Filtro de Dificultad - Más profesional -->
    <div class="container mx-auto px-4 py-6 flex justify-center flex-wrap gap-4">
        <?php
        $dificultades = ['', 'Principiante', 'Intermedio', 'Avanzado', 'Trucos y Secretos', 'Speedrun'];
        foreach ($dificultades as $dif):
            $dificultad_nombre = $dif ? $dif : 'Todas';
        ?>
            <a href="tutoriales.php<?php echo $dif ? '?dificultad=' . urlencode($dif) : ''; ?>"
                class="px-4 py-2 <?php echo $dificultad_filtro === $dif ? 'bg-red-600/50' : 'bg-red-900/30'; ?> hover:bg-red-600/40 border border-red-500/30 rounded-full flex items-center space-x-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-gamepad-2 text-red-300">
                    <line x1="6" y1="11" x2="10" y2="11"></line>
                    <line x1="8" y1="9" x2="8" y2="13"></line>
                    <line x1="14" y1="11" x2="18" y2="11"></line>
                    <path d="M15.5 19a3 3 0 0 0-2.5-1.5 3 3 0 0 0-2.5 1.5c0 1.5 2.5 3 2.5 3s2.5-1.5 2.5-3Z"></path>
                    <path d="M20 16.58A5 5 0 0 0 16.5 7h-1.79A6 6 0 0 0 4 12a6 6 0 0 0 2 4.47h0A5 5 0 0 0 9.5 19c.95 0 1.83-.38 2.5-1a3.35 3.35 0 0 1 2.5-1c.95 0 1.83.38 2.5 1"></path>
                </svg>
                <span><?php echo $dificultad_nombre; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Cuadrícula de Tutoriales - Más profesional en temas (mejor layout, descripciones y estilos) -->
    <main class="container mx-auto px-4 py-8 flex-grow grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($tutorial = $tutoriales->fetch_assoc()): ?>
            <div class="bg-black/60 border border-red-500/20 rounded-xl overflow-hidden transform hover:scale-105 transition-all duration-300 red-glow hover-red-glow 
                        <?php
                        switch ($tutorial['nivel_dificultad']) {
                            case 'Principiante':
                                echo 'difficulty-beginner';
                                break;
                            case 'Intermedio':
                                echo 'difficulty-intermediate';
                                break;
                            case 'Avanzado':
                                echo 'difficulty-advanced';
                                break;
                        }
                        ?>">
                <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                    <?php if (!empty($tutorial['image_url'])): ?>
                        <img src="../vista/<?php echo htmlspecialchars($tutorial['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($tutorial['nombre']); ?>"
                            class="w-full h-48 object-cover opacity-90 hover:opacity-100 transition-opacity">
                    <?php else: ?>
                        <img src="/api/placeholder/400/200"
                            alt="<?php echo htmlspecialchars($tutorial['nombre']); ?>"
                            class="w-full h-48 object-cover opacity-90 hover:opacity-100 transition-opacity">
                    <?php endif; ?>
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="bg-red-600/30 text-red-300 px-3 py-1 rounded-full text-xs uppercase tracking-wider">
                            <?php echo htmlspecialchars($tutorial['nivel_dificultad']); ?>
                        </span>
                        <div class="flex items-center text-yellow-400">
                            <?php
                            $estrellas = 5; // Asumiendo 5 estrellas
                            for ($i = 0; $i < $estrellas; $i++):
                            ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <h2 class="text-xl font-semibold text-red-100 mb-3">
                        <?php echo htmlspecialchars($tutorial['nombre']); ?>
                    </h2>
                    <p class="text-gray-300 mb-4">
                        <?php echo substr(htmlspecialchars($tutorial['descripcion']), 0, 150) . '...'; ?>
                    </p>
                    <div class="flex justify-between text-sm text-gray-400">
                        <span>Duración: <?php echo htmlspecialchars($tutorial['duracion']); ?> min</span>
                        <span class="text-red-300">Pixel Points: 250</span>
                    </div>
                    <form action="registrar_vista_tutorial.php" method="POST" target="_blank">
                        <input type="hidden" name="tutorial_id" value="<?php echo $tutorial['id']; ?>">
                        <input type="hidden" name="url" value="<?php echo htmlspecialchars($tutorial['url_video']); ?>">
                        <button type="submit" class="block mt-4 text-center bg-red-600/30 hover:bg-red-600/50 text-red-200 py-2 rounded transition-all">
                            Iniciar Tutorial Profesional
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </main>

    <!-- Paginación Profesional -->
    <?php if ($total_paginas > 1): ?>
        <div class="container mx-auto flex justify-center space-x-2 pb-8">
            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?><?php echo $dificultad_filtro ? '&dificultad=' . urlencode($dificultad_filtro) : ''; ?>"
                    class="bg-red-900/30 hover:bg-red-600/40 border border-red-500/30 px-4 py-2 rounded-full transition-all">
                    Anterior
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $dificultad_filtro ? '&dificultad=' . urlencode($dificultad_filtro) : ''; ?>"
                    class="<?php echo $pagina === $i ? 'bg-red-600/50' : 'bg-red-900/30'; ?> hover:bg-red-600/40 border border-red-500/30 px-4 py-2 rounded-full transition-all">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina + 1; ?><?php echo $dificultad_filtro ? '&dificultad=' . urlencode($dificultad_filtro) : ''; ?>"
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
                <h4 class="text-red-500 font-bold mb-4">Pixel Play</h4>
                <p>Plataforma para gamers profesionales y entusiastas.</p>
            </div>
            <div>
                <h4 class="text-red-500 font-bold mb-4">Enlaces Rápidos</h4>
                <ul class="space-y-2">
                    <li><a href="principal.php" class="hover:text-red-500 transition">Inicio</a></li>
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
<?php
// Cerrar la conexión
$conexion->close();
?>