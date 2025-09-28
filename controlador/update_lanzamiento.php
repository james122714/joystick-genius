<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: http://localhost/joystick%20genius/controlador/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = $conexion;
    
    // Obtener y limpiar datos
    $id = (int)$_POST['id'];
    $title = $conexion->real_escape_string($_POST['title']);
    $description = $conexion->real_escape_string($_POST['description']);
    $release_date = $conexion->real_escape_string($_POST['release_date']);
    $price = (float)$_POST['price'];
    $category = $conexion->real_escape_string($_POST['category']);
    $rating = (float)$_POST['rating'];
    $game_url = $conexion->real_escape_string($_POST['game_url']);
    $pre_order = isset($_POST['pre_order']) ? 1 : 0;
    
    // Obtener información de la imagen actual
    $stmt = $conexion->prepare("SELECT image_url FROM game_launches WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $lanzamiento = $resultado->fetch_assoc();
    
    // Procesar nueva imagen si se proporcionó
    $image_url = $lanzamiento['image_url']; // Mantener la imagen actual por defecto
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../vista/multimedia';
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            // Eliminar imagen anterior si existe
            if ($lanzamiento['image_url'] && file_exists('../vista/' . $lanzamiento['image_url'])) {
                unlink('../vista/' . $lanzamiento['image_url']);
            }
            $image_url = 'multimedia' . $filename;
        }
    }
    
    // Actualizar registro
    $stmt = $conexion->prepare("UPDATE game_launches SET 
        title = ?, 
        description = ?, 
        release_date = ?, 
        price = ?, 
        image_url = ?, 
        category = ?, 
        rating = ?, 
        pre_order = ?,
        game_url = ?
        WHERE id = ?");
        
    $stmt->bind_param("sssdssidsi", 
        $title, 
        $description, 
        $release_date, 
        $price, 
        $image_url, 
        $category, 
        $rating, 
        $pre_order,
        $game_url,
        $id
    );
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Lanzamiento actualizado exitosamente";
        header("Location: lanzamiento.php");
    } else {
        $_SESSION['error'] = "Error al actualizar el lanzamiento: " . $conexion->error;
        header("Location: editar_lanzamiento.php?id=" . $id);
    }
    
    exit();
}