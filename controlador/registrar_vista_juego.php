<?php
session_start();
require_once 'conexion.php';
$conexion = $conexion;

if (isset($_SESSION['usuario_id']) && isset($_POST['game_id'])) {
    $usuario_id = (int)$_SESSION['usuario_id'];
    $game_id = (int)$_POST['game_id'];

    // Verificar si ya accedió a este juego
    $check = $conexion->prepare("SELECT id FROM vistas_videojuegos WHERE usuario_id = ? AND game_id = ?");
    $check->bind_param("ii", $usuario_id, $game_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        // Registrar vista
        $insert = $conexion->prepare("INSERT INTO vistas_videojuegos (usuario_id, game_id) VALUES (?, ?)");
        $insert->bind_param("ii", $usuario_id, $game_id);
        $insert->execute();

        // Agregar puntos (20 puntos por juego único)
        $update_puntos = $conexion->prepare("UPDATE usuarios SET puntos = puntos + 30 WHERE id = ?");
        $update_puntos->bind_param("i", $usuario_id);
        $update_puntos->execute();
    }
}

// Redirigir al juego o a videojuegos.php si no hay URL
$redirect_url = isset($_POST['url']) && !empty($_POST['url']) ? $_POST['url'] : '../videojuegos.php';
header("Location: " . $redirect_url);
exit();
?>