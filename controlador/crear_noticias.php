<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
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
    <link rel="stylesheet" href="../vista/css/crear_noticias.css">
    <title>Crear Noticia - Panel de Administración</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-form">
        <h2><i class="fas fa-newspaper"></i> Crear Nueva Noticia</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php 
                echo htmlspecialchars($_SESSION['mensaje']);
                unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="crear_noticias_update.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título de la Noticia</label>
                <input type="text" id="titulo" name="titulo" placeholder="Ingrese el título" required aria-required="true">
            </div>
            
            <div class="form-group">
                <label for="contenido">Contenido de la Noticia</label>
                <textarea id="contenido" name="contenido" placeholder="Escriba el contenido de la noticia" required aria-required="true"></textarea>
            </div>
            
            <div class="form-group">
                <label for="imagen">Imagen de la Noticia</label>
                <div class="file-upload">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <p>Arrastra una imagen o haz clic para seleccionar</p>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png" aria-describedby="file-help">
                    <small id="file-help">Formatos: JPG, PNG (Máximo 5MB)</small>
                </div>
            </div>
            
            <div class="form-group checkbox-wrapper">
                <input type="checkbox" id="destacada" name="destacada">
                <label for="destacada">Marcar como noticia destacada</label>
            </div>
            
            <div class="form-group">
                <label for="categoria">Categoría</label>
                <select id="categoria" name="categoria" required aria-required="true">
                    <option value="">Seleccione una categoría</option>
                    <option value="general">General</option>
                    <option value="tecnologia">Tecnología</option>
                    <option value="deportes">Deportes</option>
                    <option value="entretenimiento">Entretenimiento</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="duracion">Duración en Portada</label>
                <select id="duracion" name="duracion">
                    <option value="1">1 día</option>
                    <option value="3">3 días</option>
                    <option value="7">1 semana</option>
                    <option value="30">1 mes</option>
                </select>
            </div>
            
            <button type="submit">
                <i class="fas fa-paper-plane"></i> Publicar Noticia
            </button>
            
            <div class="back-link">
                <a href="noticias.php">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </form>
    </div>
</body>
</html>