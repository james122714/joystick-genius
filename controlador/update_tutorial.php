<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = $conexion; // Usar la conexión ya establecida
    
    // Limpiar y validar datos
    $id = (int)$_POST['id'];
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $url_video = $conexion->real_escape_string($_POST['url_video']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $duracion = $conexion->real_escape_string($_POST['duracion']);
    $nivel_dificultad = $conexion->real_escape_string($_POST['nivel_dificultad']);
    
    // Obtener la información del tutorial actual
    $stmt = $conexion->prepare("SELECT image_url FROM tutoriales WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $tutorial_actual = $resultado->fetch_assoc();
    
    // Procesar imagen
    $image_url = $tutorial_actual['image_url']; // Usar imagen actual por defecto
    $upload_dir = '../vista/multimedia';
    
    // Verificar si se subió una nueva imagen
    if (!empty($_FILES['imagen']['name'])) {
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
            // Eliminar imagen anterior si existe
            if ($tutorial_actual['image_url'] && file_exists('../vista/' . $tutorial_actual['image_url'])) {
                unlink('../vista/' . $tutorial_actual['image_url']);
            }
            
            $image_url = 'vista/multimedia' . $filename;
        }
    }
    
    // Preparar consulta de actualización
    $stmt = $conexion->prepare("UPDATE tutoriales SET 
        nombre = ?, 
        descripcion = ?, 
        url_video = ?, 
        categoria = ?, 
        duracion = ?, 
        nivel_dificultad = ?,
        image_url = ?
        WHERE id = ?");
    
    $stmt->bind_param("sssssssi", 
        $nombre, 
        $descripcion, 
        $url_video, 
        $categoria, 
        $duracion, 
        $nivel_dificultad, 
        $image_url,
        $id
    );
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Tutorial actualizado exitosamente";
        header("Location: tutorial.php");
    } else {
        $_SESSION['error'] = "Error al actualizar el tutorial: " . $conexion->error;
        header("Location: editar_tutorial.php?id=" . $id);
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