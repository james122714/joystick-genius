<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';
$conexion = $conexion;

// Obtener ID de la noticia
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener datos de la noticia
$sql = "SELECT * FROM noticias WHERE id = $id";
$resultado = $conexion->query($sql);

if (!$resultado || $resultado->num_rows === 0) {
    header("Location: noticias.php");
    exit();
}

$noticia = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/noticiaedi.css">
    <title>Editar Noticia - Panel Futurista</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>
<body>
    <div class="admin-form">
        <h2><i class="fas fa-edit"></i> Editar Noticia</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php 
                echo $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php endif; ?>

        <form action="procesar_noticias.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-group">
                <input type="text" name="titulo" placeholder="Título de la noticia" 
                    value="<?php echo htmlspecialchars($noticia['titulo']); ?>" required>
            </div>

            <div class="form-group">
                <textarea name="contenido" placeholder="Contenido de la noticia" 
                    required><?php echo htmlspecialchars($noticia['contenido']); ?></textarea>
            </div>

            <?php if (!empty($noticia['imagen'])): ?>
                <div class="current-image">
                    <img src="../vista/<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                        alt="Imagen actual">
                    <p>Imagen actual</p>
                </div>
            <?php endif; ?>

            <div class="form-group checkbox-wrapper">
                <input type="checkbox" name="destacada" id="destacada" 
                    <?php echo $noticia['destacada'] ? 'checked' : ''; ?>>
                <label for="destacada">Marcar como destacada</label>
            </div>

            <div class="form-group">
                <select name="categoria" required>
                    <option value="general" <?php echo $noticia['categoria'] == 'general' ? 'selected' : ''; ?>>General</option>
                    <option value="tecnologia" <?php echo $noticia['categoria'] == 'tecnologia' ? 'selected' : ''; ?>>Tecnología</option>
                    <option value="deportes" <?php echo $noticia['categoria'] == 'deportes' ? 'selected' : ''; ?>>Deportes</option>
                    <option value="entretenimiento" <?php echo $noticia['categoria'] == 'entretenimiento' ? 'selected' : ''; ?>>Entretenimiento</option>
                </select>
            </div>

            <div class="button-group">
                <a href="noticias.php" class="button button-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="button button-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</body>
</html>