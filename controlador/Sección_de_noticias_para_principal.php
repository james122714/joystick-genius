<?php
// Añadir esto al principio del archivo después del require de conexion.php
$noticias_destacadas = $conexion->query("SELECT * FROM noticias WHERE destacada = 1 ORDER BY fecha_publicacion DESC LIMIT 3");
?>

<!-- Reemplazar la sección de contenido destacado con esto -->
<div class="glass rounded-2xl p-8 mb-8">
    <h2 class="text-2xl font-bold mb-6">Noticias Destacadas</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="noticiasCarousel">
        <?php while ($noticia = $noticias_destacadas->fetch_assoc()): ?>
            <div class="rounded-xl overflow-hidden hover-glow">
                <?php if ($noticia['imagen']): ?>
                    <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                    <img src="/api/placeholder/400/200" alt="Noticia" class="w-full h-48 object-cover">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>
