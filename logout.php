<?php
/**
 * Cierre de sesión con redirección segura
 * Limpia todas las variables de sesión
 * 
 * @author Emmanuel Arenilla
 * @version 2.0
 */

require_once __DIR__ . '/includes/init.php';

// Verificar si hay sesión activa
if (isLoggedIn()) {
    $username = $_SESSION['user'] ?? 'Usuario';
    
    // Registrar logout en log (opcional)
    error_log("Logout: Usuario {$username} cerró sesión desde IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    // Limpiar todas las variables de sesión
    $_SESSION = [];
    
    // Destruir la cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir la sesión
    session_destroy();
    
    // Iniciar nueva sesión limpia
    session_start();
    
    // Mensaje de despedida
    redirectWithMessage(url('login'), "¡Hasta luego {$username}! Has cerrado sesión correctamente.", 'success');
} else {
    // Si no hay sesión, redirigir directamente
    redirectWithMessage(url('login'), 'No hay una sesión activa', 'info');
}