<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$conexion = $conexion;

if ($_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['mensaje'] = "No tienes permisos para eliminar usuarios.";
    header("Location: administradores.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    
    // No permitir eliminar al usuario actual
    if ($id == $_SESSION['usuario_id']) {
        $_SESSION['mensaje'] = "No puedes eliminar tu propio usuario";
        header("Location: administradores.php");
        exit();
    }
    
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario eliminado exitosamente";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar el usuario";
    }
    
    $stmt->close();
}

$conexion->close();
header("Location: administradores.php");
exit();
?>