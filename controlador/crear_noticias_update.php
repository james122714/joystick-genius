<?php
session_start();

// Verificación de seguridad
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== ['admin'] ['adminvista'] ['moderador']) {
    header("Location: login.php");
    exit();
}

// Incluir archivo de conexión a la base de datos
require_once 'conexion.php';
$conexion = $conexion;

// Función para manejar la subida de imágenes
function subirImagen($archivo) {
    // Directorio para guardar imágenes
    $directorioSubida = '../vista/multimedia/';
    
    // Crear directorio si no existe
    if (!file_exists($directorioSubida)) {
        mkdir($directorioSubida, 0777, true);
    }

    // Validaciones de archivo
    if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Validar tamaño (5MB máximo)
    if ($archivo['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = "El archivo es demasiado grande. Máximo 5MB.";
        return null;
    }

    // Validar tipo de archivo
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($archivo['type'], $tiposPermitidos)) {
        $_SESSION['error'] = "Formato de imagen no permitido. Use JPG, PNG o GIF.";
        return null;
    }

    // Generar nombre único para el archivo
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid('noticia_') . '.' . $extension;
    $rutaCompleta = $directorioSubida . $nombreArchivo;

    // Intentar mover el archivo
    if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        return $nombreArchivo;
    } else {
        $_SESSION['error'] = "Error al subir la imagen.";
        return null;
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos requeridos
    $camposRequeridos = ['titulo', 'contenido', 'categoria'];
    $error = false;

    foreach ($camposRequeridos as $campo) {
        if (empty(trim($_POST[$campo]))) {
            $_SESSION['error'] = "El campo $campo es obligatorio.";
            $error = true;
            break;
        }
    }

    // Si hay error, redirigir
    if ($error) {
        header("Location: crear_noticias.php");
        exit();
    }

    // Procesar datos del formulario
    $titulo = htmlspecialchars(trim($_POST['titulo']));
    $contenido = htmlspecialchars(trim($_POST['contenido']));
    $categoria = htmlspecialchars(trim($_POST['categoria']));
    
    // Manejar imagen
    $nombreImagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombreImagen = subirImagen($_FILES['imagen']);
    }

    // Procesar otras opciones
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $duracion = isset($_POST['duracion']) ? intval($_POST['duracion']) : 1;

    // Calcular fecha de expiración
    $fechaExpiracion = date('Y-m-d H:i:s', strtotime("+$duracion days"));

    try {
        // Preparar consulta SQL
        $stmt = $conexion->prepare("
            INSERT INTO noticias 
            (titulo, contenido, imagen, categoria, destacada, autor_id, fecha_expiracion, estado) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, 'activo')
        ");

        $stmt->bind_param(
            "ssssiis", 
            $titulo, 
            $contenido, 
            $nombreImagen, 
            $categoria, 
            $destacada, 
            $_SESSION['usuario_id'], 
            $fechaExpiracion
        );

        // Ejecutar consulta
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Noticia creada exitosamente.";
            header("Location: noticias.php");
            exit();
        } else {
            throw new Exception("Error al crear la noticia: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Manejo de errores
        error_log($e->getMessage());
        $_SESSION['error'] = "Ocurrió un error al guardar la noticia. Intente nuevamente.";
        header("Location: crear_noticias.php");
        exit();
    }
} else {
    // Acceso directo no permitido
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: crear_noticias.php");
    exit();
}