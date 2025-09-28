<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: http://localhost/joystick%20genius/controlador/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = conectarDB();
    
    // Obtener y limpiar datos
    $game_id = (int)$_POST['game_id'];
    $title = $conexion->real_escape_string($_POST['title']);
    $description = $conexion->real_escape_string($_POST['description']);
    $release_date = $conexion->real_escape_string($_POST['release_date']);
    $game_url = $conexion->real_escape_string($_POST['game_url']);
    $category_id = (int)$_POST['category_id'];
    
    // Configuración de directorio de subida
    $upload_dir = '../vista/multimedia';
    
    // Obtener información de la imagen actual
    $stmt = $conexion->prepare("SELECT image_url FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $videojuego = $resultado->fetch_assoc();
    
    // Procesar nueva imagen si se proporcionó
    $image_url = $videojuego['image_url']; // Imagen actual por defecto
    
    if (!empty($_FILES['image']['name'])) {
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            // Eliminar imagen anterior si existe
            if ($videojuego['image_url'] && file_exists('../vista/' . $videojuego['image_url'])) {
                unlink('../vista/' . $videojuego['image_url']);
            }
            $image_url = 'multimedia' . $filename;
        }
    }
    
    // Actualizar registro
    $stmt = $conexion->prepare("UPDATE games SET 
        title = ?, 
        description = ?, 
        release_date = ?,  
        image_url = ?,  
        game_url = ? 
        WHERE id = ?");
        
    $stmt->bind_param("sssssi", 
        $title, 
        $description, 
        $release_date, 
        $image_url, 
        $game_url,
        $game_id
    );
    
    if ($stmt->execute()) {
        // Actualizar categoría
        $conexion->query("DELETE FROM game_categories WHERE game_id = $game_id");
        $conexion->query("INSERT INTO game_categories (game_id, category_id) VALUES ($game_id, $category_id)");

        $_SESSION['mensaje'] = "Videojuego actualizado exitosamente";
        header("Location: videojuegos.php");
    } else {
        $_SESSION['error'] = "Error al actualizar el videojuego: " . $conexion->error;
        header("Location: editar_videojuegos.php?id=$game_id");
    }
    
    $stmt->close();
    $conexion->close();
    exit();
}
?>