<?php
/**
 * Template de navegación para el dashboard administrativo
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

// Verificar inicialización
if (!defined('APP_INIT')) {
    exit('Acceso denegado');
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$userName = $_SESSION['user'] ?? 'Usuario';
$userRole = $_SESSION['role'] ?? 'asesor';
$userInitials = strtoupper(substr($userName, 0, 2));
?>

<!-- Overlay para móvil -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Header del sidebar -->
    <div class="sidebar-header">
        <a href="<?= url() ?>" class="sidebar-brand">
            <img src="<?= asset('images/logo.jpg') ?>" alt="Logo">
            Energía Inteligente
        </a>
    </div>
    
    <!-- Navegación del sidebar -->
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="<?= url('listar.php') ?>" class="nav-link <?= $currentPage === 'listar' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Tablero
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= url('listar.php') ?>" class="nav-link <?= $currentPage === 'listar' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    Registros
                </a>
            </li>
            
            <?php if (hasRole('admin')): ?>
                
            <!--
            <li class="nav-item">
                <a href="<?= url('usuarios.php') ?>" class="nav-link <?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                    <i class="fas fa-user-cog"></i>
                    Usuarios
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= url('reportes.php') ?>" class="nav-link <?= $currentPage === 'reportes' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    Reportes
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= url('configuracion.php') ?>" class="nav-link <?= $currentPage === 'configuracion' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    Configuración
                </a>
            </li>
            -->
            <?php endif; ?>
            
            <li class="nav-item mt-4">
                <hr class="my-3" style="border-color: var(--gray-300);">
                <a href="<?= url() ?>" class="nav-link">
                    <i class="fas fa-home"></i>
                    Ir al sitio web
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= url('logout.php') ?>" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Contenido principal -->
<main class="main-content">
    <!-- Barra superior -->
    <div class="topbar">
        <div class="d-flex align-items-center">
            <button type="button" class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <nav aria-label="breadcrumb" class="ml-3" style="display: none;">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item active">
                        Tablero
                    </li>
                    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $breadcrumb): ?>
                            <?php if (isset($breadcrumb['url'])): ?>
                                <li class="breadcrumb-item">
                                    <a href="<?= $breadcrumb['url'] ?>"><?= htmlspecialchars($breadcrumb['title']) ?></a>
                                </li>
                            <?php else: ?>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <?= htmlspecialchars($breadcrumb['title']) ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        
        <div class="topbar-user">
            <div class="user-info d-none d-md-block">
                <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                <div class="user-role"><?= ucfirst($userRole) ?></div>
            </div>
            
            <div class="dropdown">
                <button type="button" class="user-avatar dropdown-toggle" data-toggle="dropdown">
                    <?= $userInitials ?>
                </button>
                
                <div class="dropdown-menu">
                    <div class="dropdown-item">
                        <strong><?= htmlspecialchars($userName) ?></strong>
                        <br>
                        <small class="text-muted"><?= ucfirst($userRole) ?></small>
                    </div>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="<?= url('perfil.php') ?>" class="dropdown-item">
                        <i class="fas fa-user me-2"></i>
                        Mi perfil
                    </a>
                    
                    <a href="<?= url('configuracion.php') ?>" class="dropdown-item">
                        <i class="fas fa-cog me-2"></i>
                        Configuración
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="<?= url('logout.php') ?>" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Área de contenido -->
    <div class="content-area">
        <?php if (isset($pageTitle)): ?>
        <div class="page-header">
            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
            <?php if (isset($pageSubtitle)): ?>
                <p class="page-subtitle"><?= htmlspecialchars($pageSubtitle) ?></p>
            <?php endif; ?>
            
            <?php if (isset($pageActions) && is_array($pageActions)): ?>
                <div class="page-actions">
                    <?php foreach ($pageActions as $action): ?>
                        <a href="<?= $action['url'] ?>" 
                            class="btn <?= $action['class'] ?? 'btn-primary' ?>"
                            <?= isset($action['attributes']) ? $action['attributes'] : '' ?>>
                            <?php if (isset($action['icon'])): ?>
                                <i class="<?= $action['icon'] ?>"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($action['title']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

<script>
// JavaScript específico para navegación del dashboard
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Toggle del sidebar
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('show');
            
            // Prevenir scroll del body cuando el sidebar está abierto en móvil
            if (window.innerWidth <= 1024) {
                document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
            }
        });
    }
    
    // Cerrar sidebar al hacer clic en el overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    }
    
    // Cerrar sidebar en móviles al redimensionar ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    });
    
    // Auto-cerrar sidebar al navegar en móviles
    const navLinks = sidebar.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                setTimeout(() => {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }, 100);
            }
        });
    });
    
    // Manejar dropdowns del topbar
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.dropdown');
            
            // Cerrar otros dropdowns
            document.querySelectorAll('.dropdown.show').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });
            
            dropdown.classList.toggle('show');
        });
    });
    
    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
    
    // Mejorar experiencia táctil en dispositivos móviles
    if ('ontouchstart' in window) {
        document.querySelectorAll('.nav-link, .btn').forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    }
});
</script>