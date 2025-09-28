<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador'])) {
    header("Location:login.php");
    exit();
}

$conexion = $conexion;
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $tipo_usuario = $_POST['tipo_usuario'];
    
    $query = "UPDATE usuarios SET nombre = ?, email = ?, tipo_usuario = ?";
    $params = [$nombre, $email, $tipo_usuario];
    $types = "sss";
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $query .= ", password = ?";
        $params[] = $password;
        $types .= "s";
    }
    
    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario actualizado exitosamente";
        header("Location: administradores.php");
        exit();
    } else {
        $error = "Error al actualizar el usuario";
    }
    
    $stmt->close();
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    header("Location: administracion.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/editar_admin.css">
    <title>Editar Usuario - joystick genius</title>
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2>Editar Usuario</h2>
            <form method="POST">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="input-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="input-group">
                    <label>Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                    <input type="password" name="password">
                </div>
                <div class="input-group" >
                    <label>Tipo de Usuario</label>
                    <select name="tipo_usuario" required>
                        <option value="usuario" <?php echo $usuario['tipo_usuario'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                        <option value="admin" <?php echo $usuario['tipo_usuario'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="adminvista" <?php echo $usuario['tipo_usuario'] === 'adminvista' ? 'selected' : ''; ?>>Admin vistas</option>
                        <option value="moderador" <?php echo $usuario['tipo_usuario'] === 'moderador' ? 'selected' : ''; ?>>moderador</option>
                    </select>
                </div>
                <button type="submit">Actualizar Usuario</button>
                <div class="back-link">
                    <a href="administradores.php">Volver al Panel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>