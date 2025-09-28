<?php
/**
 * Controlador para la página principal (Landing Page)
 * Maneja la visualización y procesamiento del formulario de contacto
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        $this->pageTitle = 'Eficiencia Energética Empresarial';
        $this->cssFiles = ['landing.css'];
        $this->bodyClass = 'landing-page';
    }
    
    /**
     * Mostrar la página principal
     */
    public function index() {
        // Datos para la vista
        $heroData = [
            'title' => 'Reduce hasta un 35% tus costos de energía',
            'subtitle' => 'Soluciones inteligentes para empresas que buscan ahorrar y ser más sostenibles sin comprometer la productividad.',
            'cta_primary' => 'Solicita tu diagnóstico gratuito',
            'cta_secondary' => 'Acceso Administrador'
        ];

        $features = [
            [
                'image' => 'https://citsolar.mx/wp-content/uploads/2024/11/image-17.png',
                'title' => 'Monitoreo Inteligente',
                'description' => 'Identificamos ineficiencias en tiempo real para que tomes decisiones informadas y reduzcas tu factura energética.',
                'icon' => 'fas fa-chart-line'
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=800',
                'title' => 'Gestión Automatizada',
                'description' => 'Implementamos sistemas que ajustan automáticamente el uso de energía, maximizando eficiencia sin afectar operaciones.',
                'icon' => 'fas fa-cogs'
            ],
            [
                'image' => 'https://climnatur.com/wp-content/uploads/2023/10/Eficiencia-energetica.-ClimNatur.jpeg',
                'title' => 'Ahorro Sostenible',
                'description' => 'Obtén resultados inmediatos en reducción de costos y fortalece la imagen de tu empresa como líder en sostenibilidad.',
                'icon' => 'fas fa-leaf'
            ]
        ];

        $stats = [
            ['number' => '35', 'suffix' => '%', 'text' => 'Ahorro promedio en energía'],
            ['number' => '500', 'suffix' => '+', 'text' => 'Empresas atendidas'],
            ['number' => '24', 'suffix' '/7', 'text' => 'Soporte especializado'],
            ['number' => '10', 'suffix' '+', 'text' => 'Años de experiencia']
        ];

        $services = [
            [
                'icon' => 'fas fa-solar-panel',
                'title' => 'Energía Solar Empresarial',
                'description' => 'Implementación de sistemas fotovoltaicos para reducir dependencia de la red eléctrica.',
                'features' => ['Paneles de alta eficiencia', 'Monitoreo en tiempo real', 'Financiamiento disponible']
            ],
            [
                'icon' => 'fas fa-lightbulb',
                'title' => 'Iluminación LED Inteligente',
                'description' => 'Sistemas de iluminación eficientes con control automatizado y sensores.',
                'features' => ['Ahorro hasta 70%', 'Control inteligente', 'Mayor vida útil']
            ],
            [
                'icon' => 'fas fa-thermometer-half',
                'title' => 'Climatización Eficiente',
                'description' => 'Optimización de sistemas HVAC para máximo confort y mínimo consumo.',
                'features' => ['Termostatos inteligentes', 'Mantenimiento predictivo', 'Calidad del aire']
            ],
            [
                'icon' => 'fas fa-chart-line',
                'title' => 'Auditoría Energética',
                'description' => 'Análisis detallado de consumo para identificar oportunidades de ahorro.',
                'features' => ['Diagnóstico completo', 'Plan de mejoras', 'ROI garantizado']
            ]
        ];

        // Obtener estadísticas de registros
        try {
            $totalRegistersStmt = $this->db->query("SELECT COUNT(*) as total FROM registers WHERE deleted = 0");
            $totalRegisters = $totalRegistersStmt->fetch()['total'] ?? 0;
            
            // Actualizar estadística de empresas atendidas
            if ($totalRegisters > 0) {
                $stats[1]['number'] = $totalRegisters;
            }
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
        }

        // Renderizar vista
        $this->render('home/index', [
            'heroData' => $heroData,
            'features' => $features,
            'stats' => $stats,
            'services' => $services
        ]);
    }
    
    /**
     * Procesar formulario de contacto
     */
    public function processContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('index.php', 'Método no permitido', 'error');
        }
        
        // Obtener y sanitizar datos
        $data = [
            'name' => sanitizeInput($_POST['name'] ?? ''),
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'phone' => sanitizeInput($_POST['phone'] ?? ''),
            'company' => sanitizeInput($_POST['company'] ?? ''),
            'text' => sanitizeInput($_POST['text'] ?? '')
        ];
        
        // Reglas de validación
        $rules = [
            'name' => [
                'required' => true,
                'min_length' => 2,
                'max_length' => 100,
                'message' => 'El nombre debe tener entre 2 y 100 caracteres'
            ],
            'email' => [
                'required' => true,
                'email' => true,
                'message' => 'Debe proporcionar un email válido'
            ],
            'phone' => [
                'required' => true,
                'min_length' => 7,
                'max_length' => 15,
                'message' => 'El teléfono debe tener entre 7 y 15 dígitos'
            ],
            'text' => [
                'required' => true,
                'min_length' => 10,
                'max_length' => 500,
                'message' => 'El mensaje debe tener entre 10 y 500 caracteres'
            ]
        ];
        
        // Validar datos
        $errors = $this->validate($data, $rules);
        
        // Validaciones adicionales
        if (!validateName($data['name'])) {
            $errors['name'] = 'El nombre solo puede contener letras y espacios';
        }
        
        if (!validatePhone($data['phone'])) {
            $errors['phone'] = 'El teléfono solo puede contener números';
        }
        
        if (!empty($errors)) {
            $this->redirectWithMessage('index.php', 'Por favor corrige los errores: ' . implode(', ', $errors), 'error');
        }
        
        try {
            // Guardar en base de datos
            $sql = "INSERT INTO registers (name, email, phone, company, text, date) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->query($sql, [
                $data['name'],
                $data['email'], 
                $data['phone'],
                $data['company'],
                $data['text']
            ]);
            
            if ($stmt) {
                // Log del registro
                error_log("Nuevo registro de contacto: {$data['name']} ({$data['email']})");
                
                $this->redirectWithMessage('index.php', '¡Gracias por contactarnos! Te responderemos pronto.', 'success');
            } else {
                throw new Exception('Error al guardar el registro');
            }
            
        } catch (Exception $e) {
            error_log("Error procesando formulario de contacto: " . $e->getMessage());
            $this->redirectWithMessage('index.php', 'Error al enviar el mensaje. Por favor intenta nuevamente.', 'error');
        }
    }
}
?>