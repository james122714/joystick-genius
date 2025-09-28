<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';
$conexion = $conexion;

// Obtener el ID del videojuego a editar
$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($game_id === 0) {
    $_SESSION['error'] = "ID de videojuego no válido";
    header("Location: videojuegos.php");
    exit();
}

// Obtener la información del videojuego
$query = "SELECT g.*, gc.category_id 
        FROM games g 
        LEFT JOIN game_categories gc ON g.id = gc.game_id 
        WHERE g.id = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();

if (!$game) {
    $_SESSION['error'] = "Videojuego no encontrado";
    header("Location: videojuegos.php");
    exit();
}

// Obtener todas las categorías para el select
$categories = $conexion->query("SELECT * FROM categories ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/editarvideo.css">
    <title>Editar Videojuego - joystick genius</title>
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2>Editar Videojuego</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="update_videojuegos.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
                
                <div class="input-group">
                    <label for="title">Título</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($game['title']); ?>" required>
                </div>

                <div class="input-group">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($game['description']); ?></textarea>
                </div>

                <div class="input-group">
                    <label for="release_date">Fecha de lanzamiento</label>
                    <input type="date" id="release_date" name="release_date" value="<?php echo $game['release_date']; ?>">
                </div>

                <div class="input-group">
                    <label for="game_url">URL del juego</label>
                    <input type="url" id="game_url" name="game_url" value="<?php echo htmlspecialchars($game['game_url']); ?>" required>
                </div>

                <div class="input-group">
                    <label for="category_id">Categoría</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Seleccionar categoría</option>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($category['id'] == $game['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-cyan-400 mb-2">IMAGEN_JUEGO:</label>
                    <?php if($game['image_url']): ?>
                        <img src="../vista/<?php echo htmlspecialchars($game['image_url']); ?>" 
                            alt="Imagen actual" 
                            class="w-64 h-48 object-cover rounded-lg mb-4"
                            style="max-width: 300px; max-height: 200px;"
                        >
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*"
                        class="cyber-input w-full px-4 py-2 rounded-lg">
                </div>

                <div class="form-buttons">
                    <button type="submit" class="submit-button">Guardar cambios</button>
                    <a href="videojuegos.php" class="cancel-button">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>