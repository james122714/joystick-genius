<?php
session_start();
require_once 'conexion.php';

// Verificar autenticación y permitir acceso a admin, moderador y adminvista
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
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
        header("Location: usuarios.php");
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
    <title>Crear Usuario - Pixel Play</title>
    <link rel="stylesheet" href="../vista/css/administracion.css">
    <style>
        /* Crear Usuario - Neon Cyberpunk Style */
body {
    background: linear-gradient(45deg, #0a0c1a, #1c2541);
    font-family: 'Orbitron', sans-serif;
    color: #00ffff;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    perspective: 1000px;
}

.page.floating {
    background: rgba(20, 30, 60, 0.8);
    border: 2px solid #00ffff;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0, 255, 255, 0.3), 
                inset 0 0 20px rgba(0, 255, 255, 0.2);
    padding: 40px;
    width: 400px;
    transform: rotateX(10deg);
    transition: all 0.3s ease;
}

.page.floating:hover {
    transform: rotateX(0) scale(1.02);
    box-shadow: 0 0 50px rgba(0, 255, 255, 0.5);
}

.input-group {
    margin-bottom: 20px;
    position: relative;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-size: 0.8em;
}

.input-group input, 
.input-group select {
    width: 100%;
    padding: 10px;
    background: rgba(10, 20, 40, 0.7);
    border: 1px solid #00ffff;
    color: #00ffff;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.input-group input:focus,
.input-group select:focus {
    outline: none;
    box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
}

button {
    width: 100%;
    padding: 12px;
    background: #00ffff;
    color: #0a0c1a;
    border: none;
    border-radius: 5px;
    text-transform: uppercase;
    letter-spacing: 2px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    background: #7fffd4;
    transform: scale(1.05);
}

.error-message {
    color: #ff4444;
    text-align: center;
    margin-bottom: 20px;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2>Crear Nuevo Usuario</h2>
            <form method="POST" action="crear_usuario.php">
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
                    <a href="usuarios.php">Volver al Panel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>




