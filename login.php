<?php
/**
 * Página de login 
 */

require_once __DIR__ . '/includes/init.php';

// Si ya está logueado, redirigir
if (isset($_SESSION['user'])) {
    header('Location: ' . url('registros'));
    exit;
}

$error = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } else {
        try {
            $db = Database::getInstance();
            
            // Verificar que la tabla existe
            $tableCheck = $db->query("SHOW TABLES LIKE 'users'");
            if (!$tableCheck->fetch()) {
                throw new Exception("Tabla de usuarios no encontrada");
            }
            
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $db->query($sql, [$username]);
            $user = $stmt->fetch();
            
            error_log("Login attempt - User: $username, Found: " . ($user ? 'Yes' : 'No'));
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $username;
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];
                
                // Redirigir al dashboard
                header('Location: ' . url('registros'));
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos";
            }
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // En desarrollo, mostrar el error real
            if (defined('APP_DEBUG') && APP_DEBUG) {
                $error = "Error de desarrollo: " . $e->getMessage();
            } else {
                $error = "Error del sistema. Intente nuevamente. (" . date('H:i:s') . ")";
            }
            
            // Log adicional para debugging
            error_log("Login attempt - User: $username, Error: " . $e->getMessage());
        }
    }
}

$pageTitle = 'Acceso Administrativo';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Energía Inteligente</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?= asset('css/common.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-bolt"></i>
            </div>
            <h1>Panel Administrativo</h1>
            <p>Energía Inteligente</p>
        </div>
        
        <div class="login-form">
            <?php if ($error): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        required 
                        autocomplete="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <i class="fas fa-user form-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required 
                        autocomplete="current-password">
                    <i class="fas fa-lock form-icon"></i>
                </div>
                
                <button type="submit" class="btn" id="loginBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner loading"></i>
                        Verificando...
                    </span>
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <a href="<?= url() ?>">
                <i class="fas fa-arrow-left"></i>
                Volver al sitio web
            </a>
        </div>
    </div>

    <script>
        // Manejo del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = btn.querySelector('.btn-text');
            const btnLoading = btn.querySelector('.btn-loading');
            
            // Mostrar loading
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            btn.disabled = true;
            
            // Simular delay mínimo para UX
            setTimeout(() => {
                // El formulario se enviará normalmente
            }, 500);
        });

        // Focus automático en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Enter en username va a password
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>