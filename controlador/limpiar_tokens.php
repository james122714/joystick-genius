<?php
require_once 'conexion.php';

// Script para eliminar tokens expirados
function limpiarTokensExpirados() {
    $conexion = $conexion;
    
    try {
        // Eliminar tokens que han expirado
        $stmt = $conexion->prepare("
            DELETE FROM restablecimiento_contrasena 
            WHERE expira < NOW()
        ");
        $stmt->execute();
        
        // Log opcional para seguimiento
        error_log('Tokens expirados eliminados: ' . $stmt->affected_rows);
        
        cerrarConexion($conexion);
    } catch (Exception $e) {
        error_log('Error al limpiar tokens: ' . $e->getMessage());
    }
}

// Puedes ejecutar esta función periódicamente mediante un cron job o 
// incluirla en un script de mantenimiento del sistema
limpiarTokensExpirados();
?>