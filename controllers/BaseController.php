<?php
/**
 * Controlador base del que heredan todos los demás controladores
 * Proporciona funcionalidades comunes
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

class BaseController {
    
    protected $db;
    protected $pageTitle = '';
    protected $pageSubtitle = '';
    protected $cssFiles = [];
    protected $jsFiles = [];
    protected $bodyClass = '';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Renderizar vista con layout
     * 
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function render($view, $data = []) {
        // Extraer variables para la vista
        extract($data);
        
        // Incluir header
        include VIEWS_PATH . '/layouts/header.php';
        
        // Incluir vista específica
        include VIEWS_PATH . '/' . $view . '.php';
        
        // Incluir footer
        include VIEWS_PATH . '/layouts/footer.php';
    }
    
    /**
     * Renderizar vista de dashboard con navegación
     * 
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function renderDashboard($view, $data = []) {
        // Verificar autenticación
        requireAuth();
        
        // Extraer variables para la vista
        extract($data);
        
        // Incluir header
        include VIEWS_PATH . '/layouts/header.php';
        
        // Incluir navegación del dashboard
        include VIEWS_PATH . '/layouts/dashboard-nav.php';
        
        // Incluir vista específica
        include VIEWS_PATH . '/dashboard/' . $view . '.php';
        
        // Cerrar el contenido del dashboard
        echo '</div></main>';
        
        // Incluir footer
        include VIEWS_PATH . '/layouts/footer.php';
    }
    
    /**
     * Redireccionar con mensaje
     * 
     * @param string $url
     * @param string $message
     * @param string $type
     * @return void
     */
    protected function redirectWithMessage($url, $message, $type = 'info') {
        redirectWithMessage($url, $message, $type);
    }
    
    /**
     * Respuesta JSON
     * 
     * @param array $data
     * @param int $statusCode
     * @return void
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Validar datos de entrada
     * 
     * @param array $data
     * @param array $rules
     * @return array
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            // Campo requerido
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = $rule['message'] ?? "El campo {$field} es requerido";
                continue;
            }
            
            // Validación de email
            if (isset($rule['email']) && $rule['email'] && !empty($value)) {
                if (!validateEmail($value)) {
                    $errors[$field] = $rule['message'] ?? "El campo {$field} debe ser un email válido";
                }
            }
            
            // Longitud mínima
            if (isset($rule['min_length']) && !empty($value)) {
                if (strlen($value) < $rule['min_length']) {
                    $errors[$field] = $rule['message'] ?? "El campo {$field} debe tener al menos {$rule['min_length']} caracteres";
                }
            }
            
            // Longitud máxima
            if (isset($rule['max_length']) && !empty($value)) {
                if (strlen($value) > $rule['max_length']) {
                    $errors[$field] = $rule['message'] ?? "El campo {$field} no puede tener más de {$rule['max_length']} caracteres";
                }
            }
        }
        
        return $errors;
    }
}
?>