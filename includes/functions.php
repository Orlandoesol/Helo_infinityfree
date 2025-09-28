<?php
/**
 * Funciones de utilidad para la aplicación
 * Contiene funciones helpers comúnmente utilizadas
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

/**
 * Limpiar y sanitizar entrada de usuario
 * 
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validar email
 * 
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar teléfono (solo números)
 * 
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    return preg_match('/^[0-9]{7,15}$/', $phone);
}

/**
 * Validar nombre (solo letras y espacios)
 * 
 * @param string $name
 * @return bool
 */
function validateName($name) {
    return preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/', $name);
}

/**
 * Generar hash seguro de contraseña
 * 
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password . PASSWORD_SALT, PASSWORD_BCRYPT);
}

/**
 * Verificar contraseña
 * 
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password . PASSWORD_SALT, $hash);
}

/**
 * Verificar compatibilidad con hash MD5 existente (legacy)
 * 
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyLegacyPassword($password, $hash) {
    return md5($password) === $hash;
}

/**
 * Formatear fecha para mostrar
 * 
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Redirigir con mensaje
 * 
 * @param string $url
 * @param string $message
 * @param string $type
 */
function redirectWithMessage($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit();
}

/**
 * Obtener y limpiar mensaje de sesión
 * 
 * @return array|null
 */
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = [
            'text' => $_SESSION['message'],
            'type' => $_SESSION['message_type'] ?? 'info'
        ];
        unset($_SESSION['message'], $_SESSION['message_type']);
        return $message;
    }
    return null;
}

/**
 * Verificar si el usuario está logueado
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user']);
}

/**
 * Verificar rol de usuario
 * 
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Requerir autenticación
 */
function requireAuth() {
    if (!isLoggedIn()) {
        redirectWithMessage(url('login'), 'Debe iniciar sesión', 'error');
    }
}

/**
 * Incluir vista con datos
 * 
 * @param string $view
 * @param array $data
 */
function includeView($view, $data = []) {
    extract($data);
    include VIEWS_PATH . '/' . $view . '.php';
}

/**
 * Obtener URL base
 * 
 * @param string $path
 * @return string
 */
function url($path = '') {
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Obtener URL de asset
 * 
 * @param string $path
 * @return string
 */
function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}