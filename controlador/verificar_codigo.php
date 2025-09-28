<?php
require_once 'conexion.php';

$conexion = $conexion;

$error = '';
$mensaje = '';
$email = isset($_GET['email']) ? urldecode($_GET['email']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo_ingresado = $_POST['codigo'];
    $email = $_POST['email'];  // Hidden input

    // Buscar el código hashed en DB
    $stmt = $conexion->prepare("SELECT codigo FROM restablecimiento_contrasena WHERE email = ? AND expira > NOW() ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        $codigo_hash = $datos['codigo'];

        // Verificar si el código coincide (usando hash)
        if (password_verify($codigo_ingresado, $codigo_hash)) {
            // Código correcto: Permitir cambiar contraseña
            $mensaje = 'Código verificado. Ahora cambia tu contraseña.';
            // Redirige a restablecer_contrasena.php o maneja aquí
            header("Location: restablecer_contrasena.php?email=" . urlencode($email) . "&verified=1");
            exit();
        } else {
            $error = 'Código incorrecto.';
        }
    } else {
        $error = 'Código expirado o inválido.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Verificar Código</title>
</head>
<body>
    <h2>Ingresa el Código de Verificación</h2>
    <?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
    <?php if ($mensaje): ?><p style="color:green;"><?php echo $mensaje; ?></p><?php endif; ?>
    <form method="post">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <label>Código (6 dígitos):</label>
        <input type="text" name="codigo" required maxlength="6">
        <button type="submit">Verificar</button>
    </form>
</body>
</html>