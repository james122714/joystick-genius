<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
require_once 'conexion.php';

// Obtener categorías
$categorias_query = $conexion->query("SELECT id, nombre FROM categories");
if (!$categorias_query) {
    die("Error al obtener categorías: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Videojuego - Interfaz Tecnológica</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/crearvi.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="digital-container w-full max-w-2xl p-10 rounded-2xl">
        <h2 class="text-4xl mb-8 text-center" style="background: linear-gradient(90deg,#0b3d02,#7b0a0a); -webkit-background-clip: text; color: transparent;">
            Nuevo Videojuego
        </h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-red-400 mb-4">
                <?php 
                echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form action="procesar_videojuegos.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="text" name="title" placeholder="Título del Videojuego" required
                class="digital-input w-full p-4 rounded-xl">

            <textarea name="description" placeholder="Descripción del Videojuego" required
                class="digital-input w-full p-4 rounded-xl h-40"></textarea>

            <div class="grid grid-cols-2 gap-4">
                <input type="date" name="release_date" class="digital-input w-full p-4 rounded-xl">
                <input type="url" name="game_url" placeholder="URL del Videojuego"
                    class="digital-input w-full p-4 rounded-xl">
            </div>

            <div class="digital-input w-full p-4 rounded-xl">
                <select name="category_id" required class="w-full bg-transparent text-white">
                    <option value="">Selecciona una categoría</option>
                    <?php while($categoria = $categorias_query->fetch_assoc()): ?>
                        <option value="<?php echo (int)$categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="flex items-center space-x-4">
                <label class="text-white">Imagen del Juego:</label>
                <input type="file" name="image" accept="image/jpeg,image/png" 
                    class="digital-input p-3 rounded-xl">
                <small>Imagen del juego (opcional) - Formatos: JPG, PNG (Máximo 5MB)</small>
            </div>

            <div class="form-buttons">
                <button type="submit" class="submit-button create-button">Guardar cambios</button>
                <a href="videojuegos.php" class="cancel-button">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
