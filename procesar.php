<?php
/**
 * Procesador de formulario de registro
 * Procesa los datos enviados desde el formulario de contacto
 */

require_once __DIR__ . '/includes/init.php';

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $db = Database::getInstance();
    
    // Sanitizar y validar datos
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $text = sanitizeInput($_POST['text'] ?? '');
    // Nota: company se mantiene en el formulario pero no se guarda en DB
    
    // Validaciones básicas
    $errors = [];
    
    if (empty($name)) $errors[] = 'El nombre es requerido';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
    if (empty($phone)) $errors[] = 'El teléfono es requerido';
    if (empty($text)) $errors[] = 'El mensaje es requerido';
    
    if (!empty($errors)) {
        $errorMsg = implode(', ', $errors);
        redirectWithMessage(url() . '#registro', $errorMsg, 'error');
    }
    
    // Verificar si el email ya está registrado
    $checkSql = "SELECT COUNT(*) as count FROM registers WHERE email = ? AND deleted = 0";
    $checkStmt = $db->query($checkSql, [$email]);
    $existing = $checkStmt->fetch();
    
    if ($existing['count'] > 0) {
        redirectWithMessage(url() . '#registro', 'Este email ya está registrado en nuestro sistema', 'warning');
    }
    
    // Insertar registro
    $sql = "INSERT INTO registers (name, email, phone, text, date, deleted) 
            VALUES (?, ?, ?, ?, NOW(), 0)";
    
    $stmt = $db->query($sql, [$name, $email, $phone, $text]);
    
    if ($stmt) {
        $registerId = $db->lastInsertId();
        
        // Log del registro exitoso
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        error_log("Nuevo registro: ID {$registerId}, Email: {$email}, IP: {$ipAddress}");
        
        // Página de confirmación con estilos inline
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso - Energía Inteligente</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos de la página de éxito -->
    <link rel="stylesheet" href="<?= asset('css/success.css') ?>">
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle" style="font-size: 2.5rem;"></i>
            </div>
            <h1 class="success-title">¡Registro Exitoso!</h1>
            <p class="success-subtitle">Tu solicitud ha sido procesada correctamente</p>
        </div>
        
        <div class="success-body">
            <img src="<?= asset('images/logo.jpg') ?>" alt="Energía Inteligente" class="company-logo">
            
            <p class="success-message">
                Gracias <strong><?= htmlspecialchars($name) ?></strong> por confiar en nosotros.<br>
                Hemos recibido tu solicitud correctamente.
            </p>
            
            <div class="registration-info">
                <div class="registration-number">
                    <i class="fas fa-id-card"></i>
                    Número de registro: #<?= str_pad($registerId, 6, '0', STR_PAD_LEFT) ?>
                </div>
                <div class="contact-info">
                    Te contactaremos dentro de las próximas 24 horas
                </div>
            </div>
            
            <div class="actions">
                <a href="<?= url() ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Volver al inicio
                </a>
            </div>
            
            <div class="contact-methods">
                <h4>Recibirás una confirmación en</h4>
                <div>
                    <span class="contact-item">
                        <i class="fas fa-envelope"></i>
                        emmajhjhj004@gmail.com
                    </span>
                </div>
                <small style="color: #6c757d; margin-top: 10px; display: block;">
                    Si no la recibes, revisa tu carpeta de spam
                </small>
            </div>
        </div>
    </div>
    
    <script>
        // Animación de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.success-container');
            container.style.transform = 'scale(0.8) translateY(50px)';
            container.style.opacity = '0';
            
            setTimeout(() => {
                container.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
                container.style.transform = 'scale(1) translateY(0)';
                container.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>
        <?php
        exit;
        
    } else {
        throw new Exception('Error al insertar el registro');
    }
    
} catch (Exception $e) {
    // Log del error
    error_log("Error en procesar.php: " . $e->getMessage());
    
    // Redirigir con mensaje de error
    redirectWithMessage(url() . '#registro', 'Error al procesar el registro. Por favor inténtelo nuevamente.', 'error');
}
?>