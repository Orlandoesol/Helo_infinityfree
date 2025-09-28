<?php
/**
 * Archivo de inicialización de la aplicación
 * Carga configuraciones, funciones y inicia sesión
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

// Definir constante para prevenir acceso directo
define('APP_INIT', true);

// Iniciar sesión
session_start();

// Cargar archivos de configuración
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Verificar timeout de sesión
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        session_start();
    }
}
$_SESSION['last_activity'] = time();