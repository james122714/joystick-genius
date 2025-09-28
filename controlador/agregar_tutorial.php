<?php
require_once('conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== ['admin'] ['adminvista'] ['moderador']) {
    header("Location: login.php");
    exit();
}

// Obtener la conexión llamando a la función
$conexion = $conexion;

$tutorial = [
    'nombre' => '',
    'descripcion' => '',
    'url_video' => '',
    'categoria' => '',
    'duracion' => '',
    'nivel_dificultad' => '',
    'imagen' => '',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../vista/css/editar_tutorial.css">
    <link rel="icon" href="../vista/multimedia/logo.png" type="image/x-icon">
    <title>Agregar Tutorial - Pixel Play</title>
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2>Agregar Tutorial</h2>
            
            <form action="procesar_tutorial.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Nombre del Tutorial</label>
                    <input type="text" name="nombre" required value="">
                </div>
                
                <div class="input-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" required></textarea>
                </div>
                
                <div class="input-group">
                    <label>URL del Video</label>
                    <input type="url" name="url_video" required value="">
                </div>
                
                <div class="input-group">
                    <label>Imagen del Tutorial</label>
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/gif">
                </div>
                
                <div class="input-group">
                    <label>Categoría</label>
                    <select name="categoria" required>
                        <option value="Basico">Básico</option>
                        <option value="Intermedio">Intermedio</option>
                        <option value="Avanzado">Avanzado</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Duración (mm:ss)</label>
                    <input type="text" name="duracion" pattern="[0-5][0-9]:[0-5][0-9]" placeholder="15:30" required>
                </div>
                
                <div class="input-group">
                    <label>Nivel de Dificultad</label>
                    <select name="nivel_dificultad" required>
                        <option value="Principiante">Principiante</option>
                        <option value="Intermedio">Intermedio</option>
                        <option value="Avanzado">Avanzado</option>
                    </select>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="submit-button">Agregar Tutorial</button>
                    <a href="tutorial.php" class="cancel-button">Cancelar</a>
                </div>

                <div class="back-link">
                    <a href="tutorial.php">← Volver al Panel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>