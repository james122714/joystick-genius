<?php
require_once('conexion.php');

// Obtener la conexión llamando a la función
$conexion = $conexion;

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conexion->prepare("SELECT * FROM tutoriales WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header('Location: tutorial.php');
    exit();
}

$tutorial = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../vista/css/editar_tutorial.css">
    <link rel="icon" href="../vista/multimedia/logo.png" type="image/x-icon">
    <title>Editar Tutorial - joystick genius</title>
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2>Editar Tutorial</h2>
            
            <form action="update_tutorial.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $tutorial['id']; ?>">
                
                <div class="input-group">
                    <label>Nombre del Tutorial</label>
                    <input type="text" name="nombre" required value="<?php echo htmlspecialchars($tutorial['nombre']); ?>">
                </div>
                
                <div class="input-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" required><?php echo htmlspecialchars($tutorial['descripcion']); ?></textarea>
                </div>
                
                <div class="input-group">
                    <label>URL del Video</label>
                    <input type="url" name="url_video" required value="<?php echo htmlspecialchars($tutorial['url_video']); ?>">
                </div>
                
                <div class="input-group">
                    <label>Imagen del Tutorial</label>
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/gif">
                    <?php if (!empty($tutorial['imagen_url'])): ?>
                        <div class="imagen-actual">
                            <p>Imagen actual:</p>
                            <img src="<?php echo htmlspecialchars($tutorial['imagen_url']); ?>" alt="Imagen del tutorial" style="max-width: 200px; max-height: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="input-group">
                    <label>Categoría</label>
                    <select name="categoria" required>
                        <option value="Basico" <?php echo $tutorial['categoria'] == 'Basico' ? 'selected' : ''; ?>>Básico</option>
                        <option value="Intermedio" <?php echo $tutorial['categoria'] == 'Intermedio' ? 'selected' : ''; ?>>Intermedio</option>
                        <option value="Avanzado" <?php echo $tutorial['categoria'] == 'Avanzado' ? 'selected' : ''; ?>>Avanzado</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Duración (mm:ss)</label>
                    <input type="text" name="duracion" pattern="[0-5][0-9]:[0-5][0-9]" placeholder="15:30" 
                        required value="<?php echo htmlspecialchars($tutorial['duracion']); ?>">
                </div>
                
                <div class="input-group">
                    <label>Nivel de Dificultad</label>
                    <select name="nivel_dificultad" required>
                        <option value="Principiante" <?php echo $tutorial['nivel_dificultad'] == 'Principiante' ? 'selected' : ''; ?>>Principiante</option>
                        <option value="Intermedio" <?php echo $tutorial['nivel_dificultad'] == 'Intermedio' ? 'selected' : ''; ?>>Intermedio</option>
                        <option value="Avanzado" <?php echo $tutorial['nivel_dificultad'] == 'Avanzado' ? 'selected' : ''; ?>>Avanzado</option>
                    </select>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="submit-button">Actualizar Tutorial</button>
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