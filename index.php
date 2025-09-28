<?php
/**
 * Landing Page - Energía Inteligente
 */

require_once __DIR__ . '/includes/init.php';

// Obtener mensaje de sesión si existe
$message = getMessage();

$pageTitle = 'Eficiencia Energética Empresarial - Energía Inteligente';
$pageDescription = 'Reduce hasta un 35% tus costos de energía con nuestras soluciones inteligentes para empresas';
$bodyClass = 'landing-page';

include VIEWS_PATH . '/layouts/header.php';
?>

<!-- CSS Externo para Landing Page -->
<link rel="stylesheet" href="<?= asset('css/common.css') ?>">
<link rel="stylesheet" href="<?= asset('css/landing.css') ?>">

<!-- Navegación -->
<nav class="navbar">
    <div class="container">
        <div class="navbar-container">
            <a href="<?= url() ?>" class="navbar-brand">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Logo">
                Energía Inteligente
            </a>
            
            <ul class="navbar-nav">
                <li><a href="#inicio" class="nav-link">Inicio</a></li>
                <li><a href="#servicios" class="nav-link">Servicios</a></li>
                <li><a href="#estadisticas" class="nav-link">Resultados</a></li>
                <li><a href="#contacto" class="nav-link">Contacto</a></li>
                <li><a href="<?= url('login') ?>" class="nav-link btn-outline-primary">🔐 Acceso</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section" id="inicio" style="background-image: linear-gradient(135deg, rgba(40, 167, 69, 0.8) 0%, rgba(32, 201, 151, 0.8) 100%), url('<?= asset('images/eficiencia_energetica_0.png') ?>'); background-size: cover; background-position: center center;">
    <div class="container">
        <div class="hero-content">
            <h1>Reduce hasta un 35% tus costos de energía</h1>
            <p class="lead">Soluciones inteligentes para empresas que buscan ahorrar y ser más sostenibles sin comprometer la productividad.</p>
            <div class="hero-buttons">
                <a href="#contacto" class="btn btn-white">
                    📊 Solicita tu diagnóstico gratuito
                </a>
                <a href="<?= url('login') ?>" class="btn btn-outline-light">
                    🔐 Acceso Administrador
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section" id="servicios">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Soluciones que transforman tu empresa</h2>
            <p class="section-subtitle">Más de 500 empresas ya confían en nosotros</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <img src="<?= asset('images/monitoreo-inteligente.png') ?>" alt="Monitoreo Inteligente" class="feature-image">
                <div class="feature-content">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h3>Monitoreo Inteligente</h3>
                    <p>Identificamos ineficiencias en tiempo real para que tomes decisiones informadas y reduzcas tu factura energética.</p>
                </div>
            </div>
            
            <div class="feature-card">
                <img src="<?= asset('images/gestion-automatizada.jpg') ?>" alt="Gestión Automatizada" class="feature-image">
                <div class="feature-content">
                    <i class="fas fa-cogs feature-icon"></i>
                    <h3>Gestión Automatizada</h3>
                    <p>Implementamos sistemas que ajustan automáticamente el uso de energía, maximizando eficiencia sin afectar operaciones.</p>
                </div>
            </div>
            
            <div class="feature-card">
                <img src="<?= asset('images/ahorro-sostenible.jpeg') ?>" alt="Ahorro Sostenible" class="feature-image">
                <div class="feature-content">
                    <i class="fas fa-leaf feature-icon"></i>
                    <h3>Ahorro Sostenible</h3>
                    <p>Obtén resultados inmediatos en reducción de costos y fortalece la imagen de tu empresa como líder en sostenibilidad.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section" id="estadisticas">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title text-white" style="color: white !important;">Resultados que hablan por sí solos</h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat-item">
                <h3>35%</h3>
                <p>Ahorro promedio en energía</p>
            </div>
            <div class="stat-item">
                <h3>500+</h3>
                <p>Empresas atendidas</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>Soporte especializado</p>
            </div>
            <div class="stat-item">
                <h3>10+</h3>
                <p>Años de experiencia</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section" id="contacto">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">¿Listo para transformar tu empresa?</h2>
            <p class="section-subtitle">Obtén tu diagnóstico energético gratuito</p>
        </div>
        
        <?php if ($message): ?>
        <div class="alert <?= $message['type'] ?>">
            <?= htmlspecialchars($message['text']) ?>
        </div>
        <?php endif; ?>
        
        <div class="form-container" id="registro">
            <form action="<?= url('procesar.php') ?>" method="POST" id="contactForm">
                <div class="form-group">
                    <label for="name" class="form-label">Nombre completo *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email corporativo *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Teléfono *</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required pattern="[0-9]+" minlength="7" maxlength="15">
                </div>
                
                <div class="form-group">
                    <label for="text" class="form-label">Cuéntanos sobre tu empresa y objetivos *</label>
                    <textarea id="text" name="text" class="form-control" required maxlength="255" 
                            placeholder="Describe brevemente tu empresa, sector y principales desafíos energéticos..."
                            oninput="updateCharCounter(this)"></textarea>
                    <small class="form-text" style="color: #6c757d; font-size: 12px; text-align: right; display: block; margin-top: 5px;">
                        <span id="charCounter">0</span>/255 caracteres
                    </small>
                </div>
                
                <button type="submit" class="btn btn-white" style="width: 100%; background: #28a745; color: white; font-size: 1.1rem;">
                    🚀 Solicitar diagnóstico gratuito
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Energía Inteligente. Transformando empresas hacia la sostenibilidad energética.</p>
        <p style="margin-top: 0.5rem; opacity: 0.8;">
            <a href="<?= url('login') ?>" style="color: #28a745;">Panel Administrativo</a>
        </p>
    </div>
</footer>

<script>
// Función para actualizar el contador de caracteres
function updateCharCounter(textarea) {
    const counter = document.getElementById('charCounter');
    const currentLength = textarea.value.length;
    const maxLength = 255;
    
    counter.textContent = currentLength;
    
    // Cambiar color según el porcentaje usado
    if (currentLength > maxLength * 0.9) {
        counter.style.color = '#dc3545'; // Rojo cuando está cerca del límite
    } else if (currentLength > maxLength * 0.7) {
        counter.style.color = '#ffc107'; // Amarillo cuando está en el 70%
    } else {
        counter.style.color = '#28a745'; // Verde cuando está bien
    }
}

// Smooth scrolling para navegación
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Inicializar contador al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('text');
    if (textarea) {
        updateCharCounter(textarea);
    }
});

// Validación básica del formulario
document.getElementById('contactForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const text = document.getElementById('text').value.trim();
    
    if (!name || !email || !phone || !text) {
        alert('Por favor completa todos los campos obligatorios.');
        e.preventDefault();
        return;
    }
    
    if (text.length < 10) {
        alert('El mensaje debe tener al menos 10 caracteres.');
        e.preventDefault();
        return;
    }
    
    // Mostrar loading
    const button = e.target.querySelector('button[type="submit"]');
    const originalText = button.textContent;
    button.textContent = '⏳ Enviando...';
    button.disabled = true;
    
    // Re-habilitar en caso de error
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
    }, 10000);
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.background = 'linear-gradient(135deg, #1e7e34 0%, #17a2b8 100%)';
    } else {
        navbar.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
    }
});
</script>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>