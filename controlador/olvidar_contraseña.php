<?php
require 'autoload.php'; // Ajusta la ruta si es necesario

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// ...existing code...

include_once 'conexion.php';  // Asegúrate de que esto conecte a tu DB
include_once 'OAuthTokenProvider.php';  // Si usas Composer para PHPMailer; si no, incluye los archivos manualmente

$conexion = $conexion;  // Usa tu función de conexión

$error = '';
$mensaje = '';

function enviarCorreoCodigo($email, $codigo) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP (usa Gmail como en tu ejemplo)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tu_correo@gmail.com';  // Reemplaza con tu email
        $mail->Password   = 'tu_contrasena_app';    // Contraseña de app de Google (no la normal, genera una en accounts.google.com)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Configurar remitente y destinatario
        $mail->setFrom('tu_correo@gmail.com', 'Pixel Play');  // Tu nombre de app
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Código de Recuperación de Cuenta - Pixel Play';
        $mail->Body    = '
            <html>
            <body>
                <h2>Recuperación de Cuenta</h2>
                <p>Hemos recibido una solicitud para recuperar tu cuenta.</p>
                <p>Tu código de verificación es: <strong>' . $codigo . '</strong></p>
                <p>Ingresa este código en la página de recuperación. Expira en 10 minutos.</p>
                <p>Si no solicitaste esto, ignora este email.</p>
            </body>
            </html>
        ';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        $error = 'Ingresa un email válido.';
    } else {
        // Verificar si el email existe en usuarios
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Generar código de 6 dígitos
            $codigo = sprintf("%06d", mt_rand(0, 999999));  // Ej: 001234
            $expira = date('Y-m-d H:i:s', strtotime('+10 minutes'));  // Expira en 10 min
            $token = bin2hex(random_bytes(32));  // Opcional, si quieres mantener token

            // Guardar en DB (usa hash para seguridad, pero para código simple no es necesario)
            $codigo_hash = password_hash($codigo, PASSWORD_BCRYPT);  // Hash para no almacenar plano
            $stmt = $conexion->prepare("INSERT INTO restablecimiento_contrasena (email, token, codigo, expira) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $token, $codigo_hash, $expira);
            $stmt->execute();

            // Enviar email con el código
            if (enviarCorreoCodigo($email, $codigo)) {
                $mensaje = 'Se envió un código a tu email. Verifícalo en la siguiente página.';
                // Redirige a una página de verificación
                header("Location: verificar_codigo.php?email=" . urlencode($email));
                exit();
            } else {
                $error = 'Error al enviar el email.';
            }
        } else {
            $error = 'Email no registrado.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Recuperar Cuenta</title>
</head>
<body>
    <h2>Recuperar Cuenta</h2>
    <?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
    <?php if ($mensaje): ?><p style="color:green;"><?php echo $mensaje; ?></p><?php endif; ?>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Enviar Código</button>
    </form>
</body>
</html>