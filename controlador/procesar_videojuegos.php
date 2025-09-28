<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = conectarDB();
    
    // Limpiar y validar datos
    $title = $conexion->real_escape_string($_POST['title']);
    $description = $conexion->real_escape_string($_POST['description']);
    $release_date = !empty($_POST['release_date']) ? $conexion->real_escape_string($_POST['release_date']) : null;
    $game_url = !empty($_POST['game_url']) ? $conexion->real_escape_string($_POST['game_url']) : null;
    $category_id = (int)$_POST['category_id'];
    
    // Procesar imagen
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../vista/multimedia';
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Validar tipo de archivo
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['image']['type'], $allowed_types) && 
            $_FILES['image']['size'] <= $max_file_size) {
            
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'multimedia' . $filename;
            } else {
                $_SESSION['error'] = "Error al subir la imagen";
                header("Location: crear_videojuegos.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Archivo de imagen inválido. Debe ser JPG o PNG y máximo 5MB";
            header("Location: crear_videojuegos.php");
            exit();
        }
    }
    
    // Iniciar transacción
    $conexion->begin_transaction();
    
    try {
        // Preparar consulta para insertar juego
        $stmt = $conexion->prepare("INSERT INTO games (title, description, release_date, game_url, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $release_date, $game_url, $image_url);
        
        if ($stmt->execute()) {
            // Obtener el ID del juego recién insertado
            $game_id = $conexion->insert_id;
            
            // Insertar la categoría del juego
            $cat_stmt = $conexion->prepare("INSERT INTO game_categories (game_id, category_id) VALUES (?, ?)");
            $cat_stmt->bind_param("ii", $game_id, $category_id);
            
            if ($cat_stmt->execute()) {
                // Confirmar transacción
                $conexion->commit();
                
                $_SESSION['mensaje'] = "Videojuego creado exitosamente";
                header("Location: videojuegos.php");
            } else {
                // Revertir transacción
                $conexion->rollback();
                throw new Exception("Error al insertar categoría");
            }
        } else {
            throw new Exception("Error al insertar juego");
        }
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        
        $_SESSION['error'] = "Error al agregar el videojuego: " . $e->getMessage();
        header("Location: crear_videojuegos.php");
    }
    
    // Cerrar declaraciones y conexión
    $stmt->close();
    $conexion->close();
    exit();
}
?>