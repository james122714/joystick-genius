<?php
session_start();
require_once 'conexion.php';
$conexion = $conexion;

if (isset($_SESSION['usuario_id']) && isset($_POST['tutorial_id'])) {
    $usuario_id = (int)$_SESSION['usuario_id'];
    $tutorial_id = (int)$_POST['tutorial_id'];

    $check = $conexion->prepare("SELECT id FROM vistas_tutoriales WHERE usuario_id = ? AND tutorial_id = ?");
    $check->bind_param("ii", $usuario_id, $tutorial_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $insert = $conexion->prepare("INSERT INTO vistas_tutoriales (usuario_id, tutorial_id) VALUES (?, ?)");
        $insert->bind_param("ii", $usuario_id, $tutorial_id);
        $insert->execute();

        $update_puntos = $conexion->prepare("UPDATE usuarios SET puntos = puntos + 10 WHERE id = ?");
        $update_puntos->bind_param("i", $usuario_id);
        $update_puntos->execute();
    }
}

$redirect_url = isset($_POST['url']) && !empty($_POST['url']) ? $_POST['url'] : '../tutoriales.php';
header("Location: " . $redirect_url);
exit();
?>