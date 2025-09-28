/**
 * Funciones JavaScript principales de la aplicación
 * Incluye funcionalidades para navegación, formularios, UI y utilidades
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

// Configuración global de la aplicación
const AppConfig = {
    baseUrl: window.location.origin,
    apiEndpoint: '/api',
    version: '1.0.0',
    debug: false
};

// Clase principal de la aplicación
class EnergyApp {
    constructor() {
        this.init();
        this.bindEvents();
        this.initComponents();
    }

    /**
     * Inicialización de la aplicación
     */
    init() {
        // Configurar CSRF token si existe
        this.setupCSRF();
        
        // Inicializar tooltips y popovers
        this.initTooltips();
        
        // Configurar lazy loading de imágenes
        this.initLazyLoading();
        
        // Configurar smooth scrolling
        this.initSmoothScrolling();
        
        console.log('EnergyApp initialized v' + AppConfig.version);
    }

    /**
     * Configurar CSRF token para peticiones AJAX
     */
    setupCSRF() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            // Configurar token para fetch API
            window.csrfToken = csrfToken.getAttribute('content');
        }
    }

    /**
     * Inicializar tooltips
     */
    initTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip.bind(this));
            element.addEventListener('mouseleave', this.hideTooltip.bind(this));
        });
    }

    /**
     * Inicializar lazy loading de imágenes
     */
    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            const lazyImages = document.querySelectorAll('img[data-src]');
            lazyImages.forEach(img => imageObserver.observe(img));
        }
    }

    /**
     * Inicializar smooth scrolling
     */
    initSmoothScrolling() {
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
    }

    /**
     * Vincular eventos globales
     */
    bindEvents() {
        // Evento para cerrar modales con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Evento para mostrar/ocultar sidebar en dispositivos móviles
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', this.toggleSidebar.bind(this));
        }

        // Evento para dropdowns
        document.addEventListener('click', (e) => {
            if (e.target.closest('.dropdown-toggle')) {
                e.preventDefault();
                this.toggleDropdown(e.target.closest('.dropdown'));
            } else {
                this.closeAllDropdowns();
            }
        });

        // Evento para navegación sticky
        window.addEventListener('scroll', this.handleScroll.bind(this));

        // Evento para formularios con confirmación
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', this.confirmSubmit.bind(this));
        });
    }

    /**
     * Inicializar componentes específicos
     */
    initComponents() {
        // Inicializar formularios de validación
        this.initFormValidation();
        
        // Inicializar tablas con funcionalidades
        this.initDataTables();
        
        // Inicializar contadores animados
        this.initCounters();
        
        // Inicializar animaciones de entrada
        this.initAnimations();
    }

    /**
     * Inicializar validación de formularios
     */
    initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Validación en tiempo real
        const inputs = document.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    /**
     * Validar campo individual
     */
    validateField(field) {
        const errorElement = field.parentNode.querySelector('.invalid-feedback');
        
        if (!field.checkValidity()) {
            field.classList.add('is-invalid');
            if (errorElement) {
                errorElement.textContent = field.validationMessage;
            }
            return false;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        }
    }

    /**
     * Limpiar error de campo
     */
    clearFieldError(field) {
        if (field.classList.contains('is-invalid')) {
            field.classList.remove('is-invalid');
        }
    }

    /**
     * Inicializar contadores animados
     */
    initCounters() {
        const counters = document.querySelectorAll('.stat-value[data-count]');
        
        if ('IntersectionObserver' in window) {
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateCounter(entry.target);
                        counterObserver.unobserve(entry.target);
                    }
                });
            });

            counters.forEach(counter => counterObserver.observe(counter));
        } else {
            counters.forEach(counter => this.animateCounter(counter));
        }
    }

    /**
     * Animar contador
     */
    animateCounter(element) {
        const target = parseInt(element.dataset.count);
        const duration = parseInt(element.dataset.duration) || 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        };

        updateCounter();
    }

    /**
     * Inicializar animaciones de entrada
     */
    initAnimations() {
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in-up');
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.card, .feature-card, .stat-card').forEach(el => {
                animationObserver.observe(el);
            });
        }
    }

    /**
     * Manejar scroll de la página
     */
    handleScroll() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    }

    /**
     * Toggle sidebar
     */
    toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('open');
        }
    }

    /**
     * Toggle dropdown
     */
    toggleDropdown(dropdown) {
        // Cerrar otros dropdowns
        document.querySelectorAll('.dropdown.show').forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('show');
            }
        });
        
        dropdown.classList.toggle('show');
    }

    /**
     * Cerrar todos los dropdowns
     */
    closeAllDropdowns() {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    }

    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'success', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification animate-slide-in-down`;
        notification.innerHTML = `
            <span>${message}</span>
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;

        // Agregar estilos para posicionamiento
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;

        document.body.appendChild(notification);

        // Auto remover después del tiempo especificado
        if (duration > 0) {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, duration);
        }
    }

    /**
     * Mostrar modal
     */
    showModal(modalId, data = {}) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Poblar datos si se proporcionan
            Object.keys(data).forEach(key => {
                const element = modal.querySelector(`[data-field="${key}"]`);
                if (element) {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                        element.value = data[key];
                    } else {
                        element.textContent = data[key];
                    }
                }
            });

            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Cerrar modal
     */
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    /**
     * Cerrar todos los modales
     */
    closeAllModals() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            modal.classList.remove('show');
        });
        document.body.style.overflow = '';
    }

    /**
     * Confirmar envío de formulario
     */
    confirmSubmit(e) {
        const form = e.target;
        const message = form.dataset.confirm || '¿Está seguro de realizar esta acción?';
        
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
        
        // Mostrar loading en el botón
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        }
    }

    /**
     * Mostrar tooltip
     */
    showTooltip(e) {
        const element = e.target;
        const text = element.dataset.tooltip;
        
        if (text) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip-custom';
            tooltip.textContent = text;
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(0,0,0,0.8);
                color: white;
                padding: 0.5rem;
                border-radius: 4px;
                font-size: 0.875rem;
                white-space: nowrap;
                z-index: 10000;
                pointer-events: none;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = element.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            
            element._tooltip = tooltip;
        }
    }

    /**
     * Ocultar tooltip
     */
    hideTooltip(e) {
        if (e.target._tooltip) {
            e.target._tooltip.remove();
            delete e.target._tooltip;
        }
    }

    /**
     * Realizar petición AJAX
     */
    async fetch(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        // Agregar CSRF token si está disponible
        if (window.csrfToken) {
            defaultOptions.headers['X-CSRF-TOKEN'] = window.csrfToken;
        }

        const config = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Fetch error:', error);
            this.showNotification('Error en la conexión', 'danger');
            throw error;
        }
    }

    /**
     * Formatear fecha
     */
    formatDate(date, format = 'dd/mm/yyyy') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        
        switch (format) {
            case 'dd/mm/yyyy':
                return `${day}/${month}/${year}`;
            case 'yyyy-mm-dd':
                return `${year}-${month}-${day}`;
            default:
                return d.toLocaleDateString();
        }
    }

    /**
     * Debounce function para optimizar eventos
     */
    debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
}

// Funciones de utilidad globales
const Utils = {
    /**
     * Formatear número como moneda
     */
    formatCurrency(amount, currency = 'COP') {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },

    /**
     * Formatear porcentaje
     */
    formatPercent(value, decimals = 2) {
        return (value * 100).toFixed(decimals) + '%';
    },

    /**
     * Validar email
     */
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    /**
     * Validar teléfono colombiano
     */
    isValidPhone(phone) {
        const re = /^(\+57|57)?[0-9]{10}$/;
        return re.test(phone.replace(/\s/g, ''));
    },

    /**
     * Generar ID único
     */
    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    },

    /**
     * Obtener parámetros de URL
     */
    getUrlParams() {
        return new URLSearchParams(window.location.search);
    },

    /**
     * Copiar texto al portapapeles
     */
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.error('Error copiando al portapapeles:', err);
            return false;
        }
    }
};

// Inicializar aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.EnergyApp = new EnergyApp();
    
    // Hacer Utils disponible globalmente
    window.Utils = Utils;
    
    // Manejar mensajes de sesión
    const sessionMessage = document.querySelector('[data-session-message]');
    if (sessionMessage) {
        const message = sessionMessage.dataset.sessionMessage;
        const type = sessionMessage.dataset.messageType || 'info';
        window.EnergyApp.showNotification(message, type);
    }
});

// Exportar para uso en módulos
export { EnergyApp, Utils };