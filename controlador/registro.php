<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validaciones de contraseña
    $errors = [];
    
    // Validar longitud de la contraseña (mínimo 8 caracteres)
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }
    
    // Validar que contenga al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "La contraseña debe contener al menos una letra mayúscula";
    }
    
    // Validar que contenga al menos un número
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "La contraseña debe contener al menos un número";
    }
    
    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden";
    }
    
    // Si hay errores, no continuar con el registro
    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    } else {
        // Hashear la contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        //$conexion = conectarDB();
        
        // Verificar si el email ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $error = "Este email ya está registrado";
        } else {
            // Insertar nuevo usuario
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, tipo_usuario) VALUES (?, ?, ?, 'usuario')");
            $stmt->bind_param("sss", $nombre, $email, $password_hash);
            
            if ($stmt->execute()) {
                session_start();
                $_SESSION['usuario_id'] = $stmt->insert_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['tipo_usuario'] = 'usuario';
                
                header("principal.php");
                exit();
            } else {
                $error = "Error al registrar usuario";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/registro.css">
    <script src="../vista/js/registro.js"></script>
    <title>joystrick_genius</title>
    <style>
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body>
    <div class="page floating">
        <h2>Registro</h2>
        <form method="POST" action="registro.php">
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="input-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre">
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>
            <div class="input-group password-container">
                <label>Contraseña</label>
                <input type="password" name="password" id="password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
            </div>
            <div class="input-group password-container">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">👁️</span>
            </div>
            <div class="terms-conditions">
                <input type="checkbox" 
                    id="terms_conditions" 
                    name="terms_conditions" 
                    required>
                <label for="terms_conditions">
                    Acepto los 
                    <a href="Terminos_y_Condiciones.php">
                        Términos y Condiciones
                    </a>
                </label>
            </div>
            <button type="submit" class="btn-register">Crear Cuenta</button>
            <div class="login-link">
                <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </form>
        <div class="back-button">
            <a href="javascript:history.back()" class="btn-back">
                <span class="back-icon">←</span> Regresar
            </a>
        </div>
    </div>
</body>
</html>