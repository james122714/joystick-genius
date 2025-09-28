<?php
session_start();
require_once 'conexion.php';
$conexion = $conexion;

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';

// Obtener tipo_usuario desde administradores
$tipo_usuario = 'usuario';
$admin_check = $conexion->prepare("SELECT rol FROM administradores WHERE usuario_id = ?");
$admin_check->bind_param("i", $usuario_id);
$admin_check->execute();
$admin_result = $admin_check->get_result();
if ($admin_result->num_rows > 0) {
    $admin_data = $admin_result->fetch_assoc();
    $tipo_usuario = strtolower($admin_data['rol']);
}

// Procesar actualización manual de rango (solo para admin o moderador)
if (isset($_POST['actualizar_rango']) && in_array($tipo_usuario, ['admin', 'moderador'])) {
    $nuevo_rango_id = (int)$_POST['rango_id'];
    $stmt = $conexion->prepare("UPDATE usuarios SET rango_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $nuevo_rango_id, $usuario_id);
    
    if ($stmt->execute()) {
        // Actualizar datos locales
        $rango_stmt = $conexion->prepare("SELECT nombre, descripcion FROM rangos WHERE id = ?");
        $rango_stmt->bind_param("i", $nuevo_rango_id);
        $rango_stmt->execute();
        $rango_result = $rango_stmt->get_result()->fetch_assoc();
        $usuario['rango_id'] = $nuevo_rango_id;
        $usuario['rango_nombre'] = $rango_result['nombre'];
        $usuario['rango_descripcion'] = $rango_result['descripcion'];
    } else {
        $mensaje = "Error al actualizar el rango";
    }
}

// Obtener todos los rangos
$rangos_query = $conexion->query("SELECT * FROM rangos ORDER BY id ASC");
$rangos = [];
while ($rango = $rangos_query->fetch_assoc()) {
    $rangos[] = $rango;
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixel Play - Tabla de Rangos</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/rangota.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col text-white">
    <div class="flex-grow flex items-center justify-center p-6">
        <div class="w-full max-w-6xl">
            <!-- Botón de regreso estilizado -->
            <div class="mb-6">
                <a href="perfil.php" class="back-button">
                    ← Regresar al Perfil
                </a>
            </div>

            <?php if ($mensaje): ?>
            <div class="bg-red-900/50 text-red-200 px-6 py-3 rounded-lg mb-6 text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            <?php endif; ?>

            <!-- Título de la página -->
            <h1 class="page-title">Tabla de Rangos</h1>
            
            <!-- Tabla de rangos mejorada -->
            <div class="rangos-table">
                <div class="table-container">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th>
                                    <span class="rango-icon"></span>
                                    Nombre del Rango
                                </th>
                                <th>Descripción</th>
                                <th>Requisitos</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rangos as $index => $rango): ?>
                                <tr onclick="mostrarDetallesRango('<?php echo htmlspecialchars($rango['nombre']); ?>', '<?php echo htmlspecialchars($rango['descripcion']); ?>', '<?php echo htmlspecialchars($rango['requisitos']); ?>');" 
                                    class="hover:bg-red-900/20 transition-all duration-300">
                                    <td class="font-semibold text-red-300">
                                        <span class="rango-icon" style="background: linear-gradient(135deg, <?php echo '#' . dechex(255 - $index * 10) . '0000'; ?>, #cc0000);"></span>
                                        <?php echo htmlspecialchars($rango['nombre']); ?>
                                    </td>
                                    <td class="text-gray-300">
                                        <?php echo htmlspecialchars($rango['descripcion']); ?>
                                    </td>
                                    <td class="text-yellow-300 font-medium">
                                        <?php echo htmlspecialchars($rango['requisitos']); ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $requisitos_num = (int)str_replace(' puntos', '', $rango['requisitos']);
                                        if ($requisitos_num <= 0) {
                                            echo '<span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Inicial</span>';
                                        } elseif ($requisitos_num <= 1000) {
                                            echo '<span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">Fácil</span>';
                                        } elseif ($requisitos_num <= 5000) {
                                            echo '<span class="bg-yellow-600 text-white px-2 py-1 rounded-full text-xs">Medio</span>';
                                        } elseif ($requisitos_num <= 15000) {
                                            echo '<span class="bg-orange-600 text-white px-2 py-1 rounded-full text-xs">Difícil</span>';
                                        } else {
                                            echo '<span class="bg-red-600 text-white px-2 py-1 rounded-full text-xs">Épico</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Información adicional -->
                <div class="mt-6 p-4 bg-black/50 rounded-lg border border-red-500/20">
                    <h3 class="text-red-400 font-bold mb-2">💡 Información sobre los Rangos</h3>
                    <ul class="text-gray-300 space-y-1 text-sm">
                        <li>• Los rangos se obtienen automáticamente al alcanzar los puntos requeridos</li>
                        <li>• Los administradores y moderadores tienen acceso al rango más alto</li>
                        <li>• Haz clic en cualquier fila para ver más detalles del rango</li>
                        <li>• Los puntos se ganan participando activamente en la plataforma</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalles del rango -->
    <div id="rangoModal" class="fixed inset-0 bg-black/80 flex items-center justify-center hidden z-50">
        <div class="bg-black/90 border border-red-600/50 rounded-2xl p-6 max-w-md w-full mx-4 red-glow">
            <div class="text-center">
                <h3 id="modalTitulo" class="text-2xl font-bold text-red-400 mb-4"></h3>
                <p id="modalDescripcion" class="text-gray-300 mb-4"></p>
                <p id="modalRequisitos" class="text-yellow-300 font-medium mb-6"></p>
                <button onclick="cerrarModal()" class="bg-red-600 hover:bg-red-700 px-6 py-2 rounded-full text-white transition hover-red-glow">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black/90 text-gray-300 py-8 px-6 mt-auto">
        <div class="container mx-auto text-center">
            <div class="mb-4">
                <h4 class="text-red-500 font-bold mb-2">Pixel Play</h4>
                <p class="text-sm">Sistema de rangos para gamers profesionales</p>
            </div>
            <div class="border-t border-red-600/30 pt-4">
                <p class="text-xs">&copy; 2025 Pixel Play. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        function mostrarDetallesRango(nombre, descripcion, requisitos) {
            document.getElementById('modalTitulo').textContent = nombre;
            document.getElementById('modalDescripcion').textContent = descripcion;
            document.getElementById('modalRequisitos').textContent = 'Requisitos: ' + requisitos;
            document.getElementById('rangoModal').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('rangoModal').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('rangoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });

        // Animación de entrada para las filas de la tabla
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>