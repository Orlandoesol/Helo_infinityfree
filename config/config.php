<?php
/**
 * Configuración principal de la aplicación
 * Contiene configuraciones de base de datos, paths y configuraciones generales
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

// Prevenir acceso directo
if (!defined('APP_INIT')) {
    exit('Acceso denegado');
}

// Configuración de la aplicación
define('APP_NAME', 'Energía Inteligente');
define('APP_VERSION', '1.0.0');

// Configuración SIMPLE y FIJA de URL base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// URL base fija para evitar problemas con XAMPP
if ($host === 'localhost' || $host === '127.0.0.1') {
    // Entorno local - RUTA FIJA
    define('APP_URL', $protocol . $host . '/Helo_infinityfree');
} else {
    // Entorno de producción - usar dominio root
    define('APP_URL', $protocol . $host);
}

// Configuración de la base de datos
define('DB_HOST', 'sql204.infinityfree.com');
define('DB_USER', 'if0_40003724');
define('DB_PASS', 'q5dhg3QuTpKBAPQ');
define('DB_NAME', 'if0_40003724_helo');
define('DB_CHARSET', 'utf8mb4');

// Configuración de paths
define('BASE_PATH', dirname(__DIR__));
define('ASSETS_PATH', BASE_PATH . '/assets');
define('VIEWS_PATH', BASE_PATH . '/views');
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora
define('PASSWORD_SALT', 'energia_inteligente_2024');

// Configuración de timezone
date_default_timezone_set('America/Bogota');

// Configuración de errores (desarrollo)
define('APP_DEBUG', true); // Cambiar a false en producción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
if (APP_DEBUG) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}
