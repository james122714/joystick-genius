<?php
require_once 'conexion.php';
session_start();

$conexion = $conexion;

$error = '';
$mensaje = '';
$token_valido = false;

if (!isset($_GET['verified']) || $_GET['verified'] != 1) {
    header("Location: olvidar_contrasena.php");
    exit();
}
$email = urldecode($_GET['email']);
// ... resto del código para cambiar contraseña ...

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar validez del token
    $stmt = $conexion->prepare("SELECT email, expira FROM restablecimiento_contrasena WHERE token = ? AND expira > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $token_valido = true;
        $datos = $resultado->fetch_assoc();
    } else {
        $error = 'El enlace de restablecimiento no es válido o ha expirado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_valido) {
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    if ($nueva_contrasena !== $confirmar_contrasena) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($nueva_contrasena) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        // Hash de la nueva contraseña
        $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        // Actualizar contraseña
        $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $contrasena_hash, $datos['email']);
        
        if ($stmt->execute()) {
            // Eliminar tokens usados
            $stmt = $conexion->prepare("DELETE FROM restablecimiento_contrasena WHERE email = ?");
            $stmt->bind_param("s", $datos['email']);
            $stmt->execute();

            $mensaje = 'Contraseña restablecida exitosamente. Inicia sesión con tu nueva contraseña.';
            $token_valido = false;
        } else {
            $error = 'Error al actualizar la contraseña.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Restablecer Contraseña - joystick genius</title>
</head>
<body>
    <h2>Restablecer Contraseña</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($mensaje): ?>
        <p style="color: green;"><?php echo $mensaje; ?></p>
    <?php endif; ?>
    
    <?php if ($token_valido): ?>
    <form method="post">
        <label>Nueva Contraseña:</label>
        <input type="password" name="nueva_contrasena" required>
        
        <label>Confirmar Contraseña:</label>
        <input type="password" name="confirmar_contrasena" required>
        
        <button type="submit">Cambiar Contraseña</button>
    </form>
    <?php endif; ?>
</body>
</html>