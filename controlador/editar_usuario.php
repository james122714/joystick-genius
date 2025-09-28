<?php
session_start();
require_once 'conexion.php';

// Verificar autenticación y permitir acceso a admin, moderador y adminvista
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
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
        header("Location: usuarios.php");
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
    header("Location: usuarios.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.png" type="image/x-icon">
    <title>Editar Usuario - Pixel Play</title>
    <link rel="stylesheet" href="../vista/css/administracion.css">
    <style>
        body{
        display: flex;
        justify-content: center; /* Centrado horizontal */
    align-items: center; 
    background: linear-gradient(135deg, #6e0000ff 0%, #000000ff , darkgreen 100%);
    font-family: 'Roboto Mono', monospace;
    color: #7700008f;
}

.container{
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: radial-gradient(circle at center, rgba(233, 69, 96, 0.1), transparent 70%);
}

.page.floating {
    background: rgba(54, 3, 3, 0.9);
    border: 2px solid #025f0e9a;
    border-radius: 10px;
    box-shadow: 0 0 40px rgba(233, 69, 96, 0.2), 
                inset 0 0 30px rgba(233, 69, 96, 0.1);
    padding: 40px;
    width: 420px;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.page.floating.editar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: repeating-linear-gradient(
        transparent 0,
        rgba(233, 69, 96, 0.05) 5px,
        transparent 10px
    );
    animation: holographicEffect 5s linear infinite;
}

@keyframes holographicEffect {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.input-group {
    margin-bottom: 25px;
    position: relative;
}

.input-group label {
    display: block;
    margin-bottom: 8px;
    color: #4b0000b7;
    font-weight: 300;
    letter-spacing: 1px;
}

.input-group input, 
.input-group select {
    width: 100%;
    padding: 12px;
    background: rgba(71, 4, 4, 0.7);
    border: 1px solid #000000ff;
    color: #ffffff;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.input-group input:focus,
.input-group select:focus {
    outline: none;
    box-shadow: 0 0 20px rgba(5, 53, 1, 0.57);
}

button {
    width: 100%;
    padding: 14px;
    background: #4b000cff;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    text-transform: uppercase;
    letter-spacing: 3px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    background: #7e000085;
    transform: scale(1.05);
}
    </style>
</head>
<body>
    <div class="container">
        <div class="page floating">
            <h2 style="color : white">Editar Usuario</h2>
            <form method="POST">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="input-group">
                    <label style="color : white">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="input-group">
                    <label style="color : white">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="input-group">
                    <label  style="color : white">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                    <input type="password" name="password">
                </div>
                <div class="input-group">
                    <label  style="color : white">Tipo de Usuario</label>
                    <select name="tipo_usuario" required>
                        <option value="usuario" <?php echo $usuario['tipo_usuario'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                        <option value="admin" <?php echo $usuario['tipo_usuario'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                <button type="submit"  style="color : white">Actualizar Usuario</button>
                <div class="back-link">
                    <a href="usuarios.php"  style="color : darkblue">Volver al Panel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>