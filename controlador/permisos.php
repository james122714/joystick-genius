<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conexion = $conexion;
$usuario_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $permisos = filter_var($_POST['permisos'], FILTER_SANITIZE_STRING);
    
    // Verificar si existe registro en administradores
    $stmt_check = $conexion->prepare("SELECT id FROM administradores WHERE usuario_id = ?");
    $stmt_check->bind_param("i", $usuario_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($result->num_rows > 0) {
        // Actualizar
        $row = $result->fetch_assoc();
        $admin_id = $row['id'];
        $stmt = $conexion->prepare("UPDATE administradores SET permisos = ? WHERE id = ?");
        $stmt->bind_param("si", $permisos, $admin_id);
    } else {
        // Insertar nuevo (asumiendo rol basado en tipo_usuario, ajusta si es necesario)
        $stmt_rol = $conexion->prepare("SELECT tipo_usuario FROM usuarios WHERE id = ?");
        $stmt_rol->bind_param("i", $usuario_id);
        $stmt_rol->execute();
        $rol = $stmt_rol->get_result()->fetch_assoc()['tipo_usuario'];
        
        $stmt = $conexion->prepare("INSERT INTO administradores (usuario_id, rol, permisos) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $rol, $permisos);
    }
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Permisos actualizados exitosamente";
        header("Location: administradores.php");
        exit();
    } else {
        $error = "Error al actualizar permisos";
    }
    
    $stmt->close();
}

// Obtener permisos actuales
$stmt = $conexion->prepare("SELECT a.permisos FROM administradores a WHERE a.usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$permisos_actual = $result->num_rows > 0 ? $result->fetch_assoc()['permisos'] : '';

if ($result->num_rows === 0) {
    // Si no existe, redirigir o mostrar error
    $_SESSION['mensaje'] = "No se encontró registro de administrador para este usuario";
    header("Location: administradores.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <title>Editar Permisos - joystick genius</title>
    <link rel="stylesheet" href="../vista/css/editar_admin.css"> <!-- Reusa el CSS de editar -->
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2>Editar Permisos</h2>
            <form method="POST">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="input-group">
                    <label>Permisos (separados por comas, ej: crear,editar,eliminar)</label>
                    <textarea name="permisos" rows="3" required><?php echo htmlspecialchars($permisos_actual); ?></textarea>
                </div>
                <button type="submit">Actualizar Permisos</button>
                <div class="back-link">
                    <a href="administradores.php">Volver al Panel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>