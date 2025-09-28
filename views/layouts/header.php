<?php
/**
 * Template del header base de la aplicación
 * Incluye meta tags, CSS y configuraciones comunes
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

// Verificar inicialización
if (!defined('APP_INIT')) {
    exit('Acceso denegado');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $pageDescription ?? 'Soluciones inteligentes de eficiencia energética para empresas' ?>">
    <meta name="keywords" content="eficiencia energética, ahorro energético, sostenibilidad empresarial, energía inteligente">
    <meta name="author" content="Energía Inteligente S.A.S.">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= $pageTitle ?? APP_NAME ?>">
    <meta property="og:description" content="<?= $pageDescription ?? 'Reduce hasta un 35% tus costos de energía con nuestras soluciones inteligentes' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= url() ?>">
    <meta property="og:image" content="<?= asset('images/logo.jpg') ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?? APP_NAME ?>">
    <meta name="twitter:description" content="<?= $pageDescription ?? 'Soluciones de eficiencia energética' ?>">
    
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.jpg') ?>">
    
    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS específico de la página (inline) -->
    <?php if (isset($customCSS)): ?>
        <style><?= $customCSS ?></style>
    <?php endif; ?>
    
    <!-- Scripts en HEAD (si son necesarios) -->
    <?php if (isset($headScripts) && is_array($headScripts)): ?>
        <?php foreach ($headScripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">
    
    <!-- Mensaje de sesión -->
    <?php if (function_exists('getMessage')): ?>
        <?php $message = getMessage(); ?>
        <?php if ($message): ?>
            <div data-session-message="<?= htmlspecialchars($message['text']) ?>" 
                    data-message-type="<?= htmlspecialchars($message['type']) ?>" 
                    style="display: none;"></div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Contenido principal -->