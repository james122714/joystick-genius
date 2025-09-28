<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = $conexion;
    
    // Limpiar y validar datos
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $url_video = $conexion->real_escape_string($_POST['url_video']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $duracion = $conexion->real_escape_string($_POST['duracion']);
    $nivel_dificultad = $conexion->real_escape_string($_POST['nivel_dificultad']);
    
    // Procesar imagen
    $imagen_url = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $upload_dir = '../vista';
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
            $imagen_url = '../vista' . $filename;
        }
    }
    
    // Preparar consulta para insertar tutorial
    $stmt = $conexion->prepare("INSERT INTO tutoriales 
        (nombre, descripcion, url_video, categoria, duracion, nivel_dificultad, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssss", 
        $nombre, 
        $descripcion, 
        $url_video, 
        $categoria, 
        $duracion, 
        $nivel_dificultad,
        $image_url
    );
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Tutorial creado exitosamente";
        header("Location: tutorial.php");
    } else {
        $_SESSION['error'] = "Error al crear el tutorial: " . $conexion->error;
        header("Location: agregar_tutorial.php");
    }
    
    $stmt->close();
    $conexion->close();
    exit();
} else {
    // Si no es método POST, redirigir
    header("Location: tutorial.php");
    exit();
}
?>