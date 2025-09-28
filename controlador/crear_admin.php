<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== ['admin'] ['adminvista'] ['moderador']) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $tipo_usuario = $_POST['tipo_usuario'];
    
    $conexion = $conexion;
    
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, tipo_usuario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $password, $tipo_usuario);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario creado exitosamente";
        header("Location: administracion.php");
        exit();
    } else {
        $error = "Error al crear el usuario";
    }
    
    $stmt->close();
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.png" type="image/x-icon">
    <title>Crear Usuario - joystrck_genius</title>
    <link rel="stylesheet" href="../vista/css/crear_admin.css">
    <link rel="stylesheet" href="../vista/css/administracion.css">
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2>Crear Nuevo Usuario</h2>
            <form method="POST" action="crear_admin.php">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="input-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                <div class="input-group">
                    <label>Tipo de Usuario</label>
                    <select name="tipo_usuario" required>
                        <option value="usuario">Usuario</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit">Crear Usuario</button>
                <div class="back-link">
                    <a href="administradores.php">Volver al Panel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>




