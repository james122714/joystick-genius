<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del lanzamiento a editar
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    $_SESSION['error'] = "ID de lanzamiento no válido";
    header("Location: lanzamiento.php");
    exit();
}

// Obtener los datos del lanzamiento
$conexion = $conexion;
$stmt = $conexion->prepare("SELECT * FROM game_launches WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['error'] = "Lanzamiento no encontrado";
    header("Location: lanzamiento.php");
    exit();
}

$lanzamiento = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/editar.css">
    <title>Editar Lanzamiento - joystick genius</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">

</head>
<body class="min-h-screen text-gray-100 py-10">
    <!-- Navbar -->
    <nav class="cyber-container fixed top-0 w-full z-50 px-6 py-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold cyber-title">
                JOYSTICK GENIUS_ADMIN
            </h1>
            <div class="flex space-x-6 items-center">
                <a href="lanzamiento.php" class="cyber-button text-sm">
                    « Volver
                </a>
                <a href="logout.php" class="cyber-button bg-red-600 text-sm">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 mt-20">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="cyber-card bg-red-500/20 p-4 mb-6">
                <p class="text-red-300">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="cyber-container max-w-4xl mx-auto p-8">
            <h2 class="text-4xl font-bold mb-8 text-center cyber-title">
                EDITAR_LANZAMIENTO.exe
            </h2>

            <form action="update_lanzamiento.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Columna Izquierda -->
                    <div class="space-y-6">
                        <!-- Título -->
                        <div>
                            <label class="block text-cyan-400 mb-2">TÍTULO_JUEGO:</label>
                            <input type="text" name="title" required 
                                value="<?php echo htmlspecialchars($lanzamiento['title']); ?>"
                                class="cyber-input w-full px-4 py-2 rounded-lg">
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="block text-cyan-400 mb-2">FECHA_LANZAMIENTO:</label>
                            <input type="date" name="release_date" required 
                                value="<?php echo $lanzamiento['release_date']; ?>"
                                class="cyber-input w-full px-4 py-2 rounded-lg">
                        </div>

                        <!-- Precio -->
                        <div>
                            <label class="block text-cyan-400 mb-2">PRECIO_UNITARIO:</label>
                            <input type="number" name="price" step="0.01" min="0" required 
                                value="<?php echo $lanzamiento['price']; ?>"
                                class="cyber-input w-full px-4 py-2 rounded-lg">
                        </div>

                        <!-- Rating -->
                        <div>
                            <label class="block text-cyan-400 mb-2">RATING_SISTEMA:</label>
                            <input type="number" name="rating" min="0" max="5" step="0.1" required 
                                value="<?php echo $lanzamiento['rating']; ?>"
                                class="cyber-input w-full px-4 py-2 rounded-lg">
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-6">
                        <!-- Categoría -->
                        <div>
                            <label class="block text-cyan-400 mb-2">CATEGORÍA_JUEGO:</label>
                            <select name="category" required class="cyber-input w-full px-4 py-2 rounded-lg">
                                <?php
                                $categorias = ['RPG', 'Estrategia', 'Aventura', 'Acción', 'Survival', 'Terror', 'Carreras', 'Deporte'];
                                foreach ($categorias as $categoria) {
                                    $selected = ($categoria === $lanzamiento['category']) ? 'selected' : '';
                                    echo "<option value=\"$categoria\" $selected>$categoria</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- URL -->
                        <div>
                            <label class="block text-cyan-400 mb-2">URL_JUEGO:</label>
                            <input type="url" name="game_url" required 
                                value="<?php echo htmlspecialchars($lanzamiento['game_url']); ?>"
                                class="cyber-input w-full px-4 py-2 rounded-lg">
                        </div>

                        <!-- Pre-order -->
                        <div class="cyber-card p-4">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="pre_order" 
                                    <?php echo $lanzamiento['pre_order'] ? 'checked' : ''; ?>
                                    class="form-checkbox h-5 w-5 text-cyan-400">
                                <span class="text-cyan-400">ACTIVAR_PRE_ORDER</span>
                            </label>
                        </div>

                        <!-- Imagen -->
                        <div>
                            <label class="block text-cyan-400 mb-2">IMAGEN_JUEGO:</label>
                            <?php if($lanzamiento['image_url']): ?>
                                <img src="../vista/<?php echo htmlspecialchars($lanzamiento['image_url']); ?>" 
                                    alt="Imagen actual" class="preview-image w-32 h-32 object-cover mb-4">
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*"
                                class="cyber-input w-full px-4 py-2 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Descripción (Ancho completo) -->
                <div>
                    <label class="block text-cyan-400 mb-2">DESCRIPCIÓN_SISTEMA:</label>
                    <textarea name="description" required rows="4"
                            class="cyber-input w-full px-4 py-2 rounded-lg resize-none"><?php echo htmlspecialchars($lanzamiento['description']); ?></textarea>
                </div>

                <!-- Botón de Actualización -->
                <div class="text-center pt-6">
                    <button type="submit" class="cyber-button group">
                        <span class="group-hover:animate-pulse">ACTUALIZAR_SISTEMA</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Efecto hover en inputs
        document.querySelectorAll('.cyber-input').forEach(input => {
            input.addEventListener('focus', () => {
                input.style.transform = 'scale(1.01)';
            });
            input.addEventListener('blur', () => {
                input.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>