<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = $conexion;
    
    $id = (int)$_POST['id']; // Obtener el ID de la noticia a modificar
    
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $contenido = $conexion->real_escape_string($_POST['contenido']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    
    $ruta_imagen = null;
    
    // Procesar imagen si se subió una
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $directorio_destino = '../vista/multimedia';
        
        // Crear el directorio si no existe
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }
        
        $nombre_archivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $ruta_archivo = $directorio_destino . $nombre_archivo;
        
        // Verificar tamaño (5MB máximo)
        if ($_FILES['imagen']['size'] <= 5000000) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_archivo)) {
                $ruta_imagen = 'multimedia' . $nombre_archivo;
            } else {
                $_SESSION['error'] = "Error al subir la imagen.";
                header("Location: editar_noticias.php?id=" . $id);
                exit();
            }
        } else {
            $_SESSION['error'] = "La imagen es demasiado grande. Máximo 5MB.";
            header("Location: editar_noticias.php?id=" . $id);
            exit();
        }
    }
    
    // Preparar la consulta de actualización
    if ($ruta_imagen) {
        // Si se sube nueva imagen, actualizar también la imagen
        $stmt = $conexion->prepare("UPDATE noticias SET titulo = ?, contenido = ?, imagen = ?, categoria = ?, destacada = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $titulo, $contenido, $ruta_imagen, $categoria, $destacada, $id);
    } else {
        // Si no se sube nueva imagen, mantener la imagen actual
        $stmt = $conexion->prepare("UPDATE noticias SET titulo = ?, contenido = ?, categoria = ?, destacada = ? WHERE id = ?");
        $stmt->bind_param("sssii", $titulo, $contenido, $categoria, $destacada, $id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Noticia actualizada exitosamente";
        header("Location: noticias.php");
    } else {
        $_SESSION['error'] = "Error al actualizar la noticia";
        header("Location: editar_noticias.php?id=" . $id);
    }
    exit();
}
?>