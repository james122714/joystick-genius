<?php
require_once 'conexion.php';
$conexion = $conexion;

// Verificar si se proporcionó un ID de noticia
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: noticias.php");
    exit();
}

$noticia_id = (int)$_GET['id'];

// Obtener los detalles de la noticia
$stmt = $conexion->prepare("SELECT n.*, u.nombre as autor_nombre, u.foto_perfil as autor_foto 
    FROM noticias n 
    LEFT JOIN usuarios u ON n.autor_id = u.id 
    WHERE n.id = ? AND n.estado = 'activo' 
    AND (n.fecha_expiracion IS NULL OR n.fecha_expiracion > NOW())");
$stmt->bind_param("i", $noticia_id);
$stmt->execute();
$resultado = $stmt->get_result();

// Verificar si la noticia existe
if ($resultado->num_rows === 0) {
    header("Location: noticias.php");
    exit();
}

$noticia = $resultado->fetch_assoc();

// Obtener noticias relacionadas
$categoria = $conexion->real_escape_string($noticia['categoria']);
$related_stmt = $conexion->prepare("SELECT id, titulo, imagen 
    FROM noticias 
    WHERE categoria = ? AND id != ? AND estado = 'activo' 
    AND (fecha_expiracion IS NULL OR fecha_expiracion > NOW()) 
    LIMIT 3");
$related_stmt->bind_param("si", $categoria, $noticia_id);
$related_stmt->execute();
$noticias_relacionadas = $related_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia['titulo']); ?> - Red de Noticias Cuánticas</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0a0a1a, #1a1a2e, #16213e);
            background-attachment: fixed;
        }
        .article-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .article-content h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1.5rem 0 1rem;
            color: #7dd3fc;
        }
    </style>
</head>
<body class="min-h-screen text-white flex flex-col">
    <!-- Encabezado Futurista -->
    <header class="sticky top-0 z-50 bg-black/40 backdrop-blur-md border-b border-blue-500/20 p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="noticias.php" class="flex items-center space-x-2 bg-blue-600/30 hover:bg-blue-600/50 py-2 px-4 rounded-full transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left text-blue-300 group-hover:translate-x-[-5px] transition-transform">
                    <path d="m15 18-6-6 6-6"/></svg>
                <span class="text-blue-200">Regresar</span>
            </a>
            <h1 class="text-2xl font-extralight tracking-wide text-blue-100 uppercase">
                Noticia Cuántica
            </h1>
        </div>
    </header>

    <!-- Contenido Principal de la Noticia -->
    <main class="container mx-auto px-4 py-8 flex-grow">
        <article class="max-w-4xl mx-auto">
            <!-- Cabecera de la Noticia -->
            <header class="mb-8">
                <div class="flex items-center mb-4 space-x-4">
                    <span class="bg-blue-600/30 text-blue-300 px-3 py-1 rounded-full text-xs uppercase tracking-wider">
                        <?php echo htmlspecialchars($noticia['categoria']); ?>
                    </span>
                    <?php if ($noticia['destacada']): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400 w-5 h-5">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    <?php endif; ?>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-bold text-blue-100 mb-4">
                    <?php echo htmlspecialchars($noticia['titulo']); ?>
                </h1>
                
                <div class="flex items-center space-x-4 text-gray-400">
                    <?php 
                    $autor_foto = $noticia['autor_foto'] ? '../../vista/' . $noticia['autor_foto'] : '/api/placeholder/40/40';
                    ?>
                    <img src="<?php echo htmlspecialchars($autor_foto); ?>" 
                        alt="Foto de <?php echo htmlspecialchars($noticia['autor_nombre']); ?>" 
                        class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <p class="font-medium text-blue-200">
                            <?php echo htmlspecialchars($noticia['autor_nombre']); ?>
                        </p>
                        <p class="text-sm">
                            Publicado el <?php echo date('d.m.Y', strtotime($noticia['fecha_publicacion'])); ?>
                        </p>
                    </div>
                </div>
            </header>

            <!-- Imagen destacada -->
            <?php if ($noticia['imagen']): ?>
                <figure class="mb-8 rounded-xl overflow-hidden">
                    <img src="../vista/<?php echo $noticia['imagen']; ?>" 
                        alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" 
                        class="w-full max-h-[500px] object-cover">
                </figure>
            <?php endif; ?>

            <!-- Contenido de la Noticia -->
            <div class="article-content text-gray-300 leading-relaxed">
                <?php echo $noticia['contenido']; ?>
            </div>
        </article>

        <!-- Noticias Relacionadas -->
        <section class="mt-16 max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-blue-200 mb-6">Noticias Relacionadas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while ($relacionada = $noticias_relacionadas->fetch_assoc()): ?>
                    <div class="bg-gray-800/40 border border-blue-500/20 rounded-xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                        <?php if ($relacionada['imagen']): ?>
                            <img src="../vista/<?php echo $relacionada['imagen']; ?>" 
                                alt="<?php echo htmlspecialchars($relacionada['titulo']); ?>"
                                class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="h-48 bg-gradient-to-br from-gray-700 to-blue-900 opacity-80"></div>
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-blue-100 mb-2">
                                <?php echo htmlspecialchars($relacionada['titulo']); ?>
                            </h3>
                            <a href="ver_noticia.php?id=<?php echo $relacionada['id']; ?>" 
                                class="block mt-2 text-center bg-blue-600/30 hover:bg-blue-600/50 text-blue-200 py-2 rounded transition-all">
                                Leer más
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <!-- Pie de Página Futurista -->
    <footer class="bg-black/50 backdrop-blur-md border-t border-blue-500/20 py-8">
        <div class="container mx-auto text-center text-gray-500 border-t border-blue-500/20 pt-4">
            © 2024 Red de Noticias Cuánticas. Todos los derechos neuronales reservados.
        </div>
    </footer>
</body>
</html>