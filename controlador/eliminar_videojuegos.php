<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

// Verificar si se recibió un ID válido
if (!isset($_POST['game_id']) || !is_numeric($_POST['game_id'])) {
    $_SESSION['error'] = "ID de videojuego inválido";
    header("Location: videojuegos.php");
    exit();
}

try {
    $conexion = conectarDB();
    
    // Preparar la consulta para evitar inyección SQL
    $stmt = $conexion->prepare("DELETE FROM games WHERE id = ?");
    $game_id = (int)$_POST['game_id'];
    $stmt->bind_param("i", $game_id);

    // Intentar eliminar el videojuego
    if ($stmt->execute()) {
        // Verificar si realmente se eliminó algún registro
        if ($stmt->affected_rows > 0) {
            $_SESSION['mensaje'] = "Videojuego eliminado exitosamente";
        } else {
            $_SESSION['error'] = "No se encontró el videojuego especificado";
        }
    } else {
        throw new Exception("Error al eliminar el videojuego");
    }

    $stmt->close();
    
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}

// Redireccionar de vuelta a la página de gestión de videojuegos
header("Location: videojuegos.php");
exit();
?>