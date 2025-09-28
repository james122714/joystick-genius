<?php
session_start();

// Incluir archivo de conexión
include_once 'conexion.php';

// Función para limpiar datos de entrada
function limpiarDatos($dato) {
    global $conexion;
    return mysqli_real_escape_string($conexion, trim(htmlspecialchars($dato)));
}

// Función para registrar intento de sesión
function registrarSesion($usuario_id, $ip) {
    global $conexion;

    if (!isset($conexion) || !$conexion) {
        error_log('registrarSesion: $conexion no disponible');
        return;
    }

    $stmt = $conexion->prepare("INSERT INTO sesiones (usuario_id, ip) VALUES (?, ?)");
    if (!$stmt) {
        error_log('Error en prepare registrarSesion: ' . $conexion->error);
        return;
    }

    $stmt->bind_param("is", $usuario_id, $ip);
    $stmt->execute();
    $stmt->close();
}

// Función para actualizar último acceso
function actualizarUltimoAcceso($usuario_id) {
    global $conexion;

    if (!isset($conexion) || !$conexion) {
        error_log('actualizarUltimoAcceso: $conexion no disponible');
        return;
    }

    $stmt = $conexion->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
    if (!$stmt) {
        error_log('Error en prepare actualizarUltimoAcceso: ' . $conexion->error);
        return;
    }

    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->close();
}

// Verificar si el usuario ya está logueado
if (isset($_SESSION['usuario_id'])) {
    if (in_array($_SESSION['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
        header("Location: administracion.php");
    } else {
        header("Location: principal.php");
    }
    exit();
}

$error = '';
$success = '';

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } elseif (!isset($_POST['terms_conditions'])) {
        $error = "Debes aceptar los términos y condiciones para continuar.";
    } else {
        $email = limpiarDatos($_POST['email']);
        $password = $_POST['password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Por favor, ingresa un correo electrónico válido.";
        } else {
            $stmt = $conexion->prepare("SELECT id, nombre, email, password, tipo_usuario, estado FROM usuarios WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows == 1) {
                    $usuario = $resultado->fetch_assoc();

                    if ($usuario['estado'] != 'activo') {
                        $error = "Tu cuenta se encuentra inactiva. Contacta al administrador.";
                    } else {
                        // ⚠️ Mejor usar password_verify() si las contraseñas están hasheadas
                        if ($password === $usuario['password']) {
                            $_SESSION['usuario_id'] = $usuario['id'];
                            $_SESSION['nombre'] = $usuario['nombre'];
                            $_SESSION['email'] = $usuario['email'];
                            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                            $_SESSION['login_time'] = time();

                            $ip_usuario = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                            registrarSesion($usuario['id'], $ip_usuario);

                            actualizarUltimoAcceso($usuario['id']);

                            session_regenerate_id(true);

                            if (in_array($usuario['tipo_usuario'], ['admin', 'moderador', 'adminvista'])) {
                                header("Location: administracion.php");
                            } else {
                                header("Location: principal.php");
                            }
                            exit();
                        } else {
                            $error = "Credenciales incorrectas. Por favor, verifica tu email y contraseña.";
                        }
                    }
                } else {
                    $error = "Credenciales incorrectas. Por favor, verifica tu email y contraseña.";
                }
                $stmt->close();
            } else {
                $error = "Error en la base de datos: " . $conexion->error;
            }
        }
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/login.css">
    <script src="../vista/js/login.js" defer></script>
    <title>joystrick_genius</title>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../vista/multimedia/logo.jpeg" alt="joystrick_genius Logo">
        </div>
        <h2 style="text-align: center; margin-bottom: 20px; color: var(--accent-color);">bienvenido</h2>
        
        <form method="POST" action="login.php" autocomplete="off" id="loginForm">
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="error-icon">⚠️</i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <i class="success-icon">✅</i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <div class="input-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" 
                    id="email" 
                    name="email" 
                    required 
                    placeholder="usuario@ejemplo.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    maxlength="100">
            </div>
            
            <div class="input-group">
                <label for="password">Contraseña</label>
                <div class="password-container">
                    <input type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Ingresa tu contraseña"
                        minlength="6">
                    <button type="button" 
                            class="toggle-password" 
                            onclick="togglePassword()">👁️</button>
                </div>
            </div>
            
            <div class="terms-conditions">
                <input type="checkbox" 
                    id="terms_conditions" 
                    name="terms_conditions" 
                    required>
                <label for="terms_conditions">
                    Acepto los 
                    <a href="Terminos_y_Condiciones.php" target="_blank">
                        Términos y Condiciones
                    </a>
                </label>
            </div>
            
            <button type="submit" class="btn-login" id="submitBtn">Ingresar</button>
            
            <div class="links">
                <a href="olvidar_contraseña.php">¿Olvidaste tu contraseña?</a>
                <a href="registro.php">¿No tienes cuenta? Regístrate</a>
            </div>
            
            <div class="back-button">
                <a href="pagina_inicio.php" class="btn-back">← Regresar</a>
            </div>
        </form>
    </div>
</body>
</html>
