<?php
// procesar_lanzamiento.php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = $conexion;
    
    // Limpiar y validar datos
    $title = $conexion->real_escape_string($_POST['title']);
    $description = $conexion->real_escape_string($_POST['description']);
    $release_date = $conexion->real_escape_string($_POST['release_date']);
    $price = (float)$_POST['price'];
    $category = $conexion->real_escape_string($_POST['category']);
    $rating = (float)$_POST['rating'];
    $pre_order = isset($_POST['pre_order']) ? 1 : 0;
    $game_url = $conexion->real_escape_string($_POST['game_url']); // Nueva URL del juego
    
    // Procesar imagen
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../vista/multimedoa';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_url = 'multimedia' . $filename;
        }
    }
    
    // Preparar consulta con game_url
    $stmt = $conexion->prepare("INSERT INTO game_launches (title, description, release_date, price, image_url, category, rating, pre_order, game_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdssids", $title, $description, $release_date, $price, $image_url, $category, $rating, $pre_order, $game_url);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Lanzamiento agregado exitosamente";
        header("Location: lanzamiento.php");
    } else {
        $_SESSION['error'] = "Error al agregar el lanzamiento: " . $conexion->error;
        header("Location: crear_lanzamiento.php");
    }
    
    exit();
}
?>