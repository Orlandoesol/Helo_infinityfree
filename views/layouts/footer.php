<?php
/**
 * Template del footer base de la aplicación
 * Incluye scripts JavaScript y configuraciones de cierre
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

// Verificar inicialización
if (!defined('APP_INIT')) {
    exit('Acceso denegado');
}
?>

    <!-- JavaScript Principal -->
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <?php if (isset($jsFiles) && is_array($jsFiles)): ?>
        <?php foreach ($jsFiles as $jsFile): ?>
            <script src="<?= asset('js/' . $jsFile) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Scripts específicos de la página -->
    <?php if (isset($customJS)): ?>
        <script><?= $customJS ?></script>
    <?php endif; ?>
    
    <!-- Scripts externos (Analytics, etc.) -->
    <?php if (defined('GOOGLE_ANALYTICS_ID')): ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= GOOGLE_ANALYTICS_ID ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= GOOGLE_ANALYTICS_ID ?>');
        </script>
    <?php endif; ?>
    
    <!-- Configuración global de JavaScript -->
    <script>
        // Configuración global para JavaScript
        window.AppConfig = {
            baseUrl: '<?= rtrim(APP_URL, '/') ?>',
            currentPage: '<?= basename($_SERVER['PHP_SELF'], '.php') ?>',
            isLoggedIn: <?= isLoggedIn() ? 'true' : 'false' ?>,
            userRole: '<?= $_SESSION['role'] ?? 'guest' ?>',
            csrfToken: '<?= $_SESSION['csrf_token'] ?? '' ?>',
            messages: {
                confirm: '¿Está seguro de realizar esta acción?',
                loading: 'Procesando...',
                error: 'Ha ocurrido un error. Inténtelo nuevamente.',
                success: 'Operación completada exitosamente'
            }
        };
        
        // Configurar timezone
        if (Intl.DateTimeFormat().resolvedOptions().timeZone) {
            document.cookie = 'timezone=' + Intl.DateTimeFormat().resolvedOptions().timeZone + '; path=/';
        }
    </script>
    
    <!-- Service Worker para PWA (opcional) -->
    <?php if (isset($enableServiceWorker) && $enableServiceWorker): ?>
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('<?= asset('js/sw.js') ?>')
                        .then(function(registration) {
                            console.log('SW registered: ', registration);
                        })
                        .catch(function(registrationError) {
                            console.log('SW registration failed: ', registrationError);
                        });
                });
            }
        </script>
    <?php endif; ?>

</body>
</html>