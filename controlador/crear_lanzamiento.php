<?php
session_start();
require_once 'conexion.php';

// Verificar autenticación y permitir acceso a admin, moderador y adminvista
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/crearla.css">
    <title>Pixel Play - Crear Lanzamiento</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="glass p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold title-gradient">
                Pixel Play - Admin
            </h1>
            <div class="flex space-x-6 items-center">
                <a href="lanzamiento.php" class="hover:text-green-400 transition-colors">Volver a Gestión</a>
                <a href="logout.php" class="bg-red-700 px-4 py-2 rounded-full hover:bg-red-800 transition-colors">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Mensajes de error -->
        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-700 text-white p-4 rounded mb-4">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="max-w-2xl mx-auto glass rounded-lg p-8 shadow-lg">
            <h2 class="text-3xl font-bold mb-8 text-center title-gradient">
                Crear Nuevo Lanzamiento
            </h2>

            <form action="procesar_lanzamiento.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Título -->
                <div>
                    <label for="title" class="block mb-2">Título del Juego</label>
                    <input type="text" id="title" name="title" required 
                    class="w-full input-dark rounded px-4 py-2">
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block mb-2">Descripción</label>
                    <textarea id="description" name="description" required rows="4"
                    class="w-full input-dark rounded px-4 py-2"></textarea>
                </div>

                <!-- Fecha de Lanzamiento -->
                <div>
                    <label for="release_date" class="block mb-2">Fecha de Lanzamiento</label>
                    <input type="date" id="release_date" name="release_date" required 
                    class="w-full input-dark rounded px-4 py-2">
                </div>

                <!-- Precio -->
                <div>
                    <label for="price" class="block mb-2">Precio</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required 
                    class="w-full input-dark rounded px-4 py-2">
                </div>

                <!-- Imagen -->
                <div>
                    <label for="image" class="block mb-2">Imagen del Juego</label>
                    <input type="file" id="image" name="image" accept="image/*"
                    class="w-full input-dark rounded px-4 py-2">
                </div>

                <!-- Categoría -->
                <div>
                    <label for="category" class="block mb-2">Categoría</label>
                    <select id="category" name="category" required 
                        class="w-full input-dark rounded px-4 py-2">
                        <option value="">Selecciona una categoría</option>
                        <option value="RPG">RPG</option>
                        <option value="Estrategia">Estrategia</option>
                        <option value="Aventura">Aventura</option>
                        <option value="Acción">Acción</option>
                        <option value="Survival">Survival</option>
                        <option value="Terror">Terror</option>
                        <option value="Carreras">Carreras</option>
                        <option value="Deporte">Deporte</option>
                    </select>
                </div>

                <!-- Calificación -->
                <div>
                    <label for="rating" class="block mb-2">Calificación (0-5)</label>
                    <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" required 
                    class="w-full input-dark rounded px-4 py-2">
                </div>

                <!-- URL del Juego -->
                <div>
                    <label for="game_url" class="block mb-2">URL del Juego</label>
                    <input type="url" id="game_url" name="game_url" required 
                    placeholder="https://ejemplo.com/juego"
                    class="w-full input-dark rounded px-4 py-2">
                </div>

                <!-- Pre-order -->
                <div class="flex items-center">
                    <input type="checkbox" id="pre_order" name="pre_order" 
                    class="mr-2 input-dark rounded">
                    <label for="pre_order">¿Está disponible para pre-orden?</label>
                </div>

                <!-- Botón de Envío -->
                <div class="text-center">
                    <button type="submit" class="btn-primary">
                        Crear Lanzamiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
