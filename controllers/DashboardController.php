<?php
/**
 * Controlador del Dashboard y gestión de registros
 * Maneja las funcionalidades principales del panel administrativo
 * 
 * @author Emmanuel Arenilla
 * @version 1.0
 */

require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        $this->pageTitle = 'Dashboard Administrativo';
        $this->cssFiles = ['dashboard.css'];
        $this->bodyClass = 'dashboard-layout';
        
        // Verificar autenticación
        requireAuth();
    }
    
    /**
     * Página principal del dashboard
     */
    public function index() {
        try {
            // Obtener estadísticas generales
            $stats = $this->getStatistics();
            
            // Obtener registros recientes
            $recentRegisters = $this->getRecentRegisters();
            
            // Datos para paginación
            $page = (int)($_GET['page'] ?? 1);
            $itemsPerPage = 10;
            $offset = ($page - 1) * $itemsPerPage;
            
            // Obtener filtros
            $search = sanitizeInput($_GET['search'] ?? '');
            $dateFrom = $_GET['date_from'] ?? '';
            $dateTo = $_GET['date_to'] ?? '';
            
            // Construir query con filtros
            $whereConditions = ['deleted = 0'];
            $params = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(name LIKE ? OR email LIKE ? OR company LIKE ?)";
                $searchTerm = "%{$search}%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            }
            
            if (!empty($dateFrom)) {
                $whereConditions[] = "DATE(date) >= ?";
                $params[] = $dateFrom;
            }
            
            if (!empty($dateTo)) {
                $whereConditions[] = "DATE(date) <= ?";
                $params[] = $dateTo;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Contar total de registros filtrados
            $countSql = "SELECT COUNT(*) as total FROM registers WHERE {$whereClause}";
            $totalStmt = $this->db->query($countSql, $params);
            $totalRecords = $totalStmt->fetch()['total'];
            $totalPages = ceil($totalRecords / $itemsPerPage);
            
            // Obtener registros paginados
            $sql = "SELECT * FROM registers 
                    WHERE {$whereClause} 
                    ORDER BY date DESC 
                    LIMIT {$itemsPerPage} OFFSET {$offset}";
            $registersStmt = $this->db->query($sql, $params);
            $registers = $registersStmt->fetchAll();
            
            // Renderizar vista
            $this->renderDashboard('index', [
                'stats' => $stats,
                'recentRegisters' => $recentRegisters,
                'registers' => $registers,
                'totalRecords' => $totalRecords,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'search' => $search,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ]);
            
        } catch (Exception $e) {
            error_log("Error en dashboard: " . $e->getMessage());
            $this->redirectWithMessage('login.php', 'Error al cargar el dashboard', 'error');
        }
    }
    
    /**
     * Obtener estadísticas del dashboard
     */
    private function getStatistics() {
        $stats = [];
        
        try {
            // Total de registros
            $totalStmt = $this->db->query("SELECT COUNT(*) as total FROM registers WHERE deleted = 0");
            $stats['total'] = $totalStmt->fetch()['total'] ?? 0;
            
            // Registros de hoy
            $todayStmt = $this->db->query("SELECT COUNT(*) as today FROM registers WHERE DATE(date) = CURDATE() AND deleted = 0");
            $stats['today'] = $todayStmt->fetch()['today'] ?? 0;
            
            // Registros del mes actual
            $monthStmt = $this->db->query("SELECT COUNT(*) as month FROM registers WHERE YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE()) AND deleted = 0");
            $stats['month'] = $monthStmt->fetch()['month'] ?? 0;
            
            // Registros de la semana
            $weekStmt = $this->db->query("SELECT COUNT(*) as week FROM registers WHERE YEARWEEK(date) = YEARWEEK(CURDATE()) AND deleted = 0");
            $stats['week'] = $weekStmt->fetch()['week'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Obtener registros recientes
     */
    private function getRecentRegisters($limit = 5) {
        try {
            $sql = "SELECT id, name, email, company, DATE(date) as date_formatted 
                    FROM registers 
                    WHERE deleted = 0 
                    ORDER BY date DESC 
                    LIMIT ?";
            $stmt = $this->db->query($sql, [$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error obteniendo registros recientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Exportar datos a CSV
     */
    public function exportCsv() {
        if (!hasRole('admin')) {
            $this->redirectWithMessage('listar.php', 'No tienes permisos para exportar datos', 'error');
        }
        
        try {
            $sql = "SELECT id, name, email, phone, company, text, date 
                    FROM registers 
                    WHERE deleted = 0 
                    ORDER BY date DESC";
            $stmt = $this->db->query($sql);
            $registers = $stmt->fetchAll();
            
            // Configurar headers para descarga
            $filename = 'registros_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Crear output stream
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers del CSV
            fputcsv($output, [
                'ID',
                'Nombre',
                'Email',
                'Teléfono',
                'Empresa',
                'Mensaje',
                'Fecha de Registro'
            ], ';');
            
            // Datos
            foreach ($registers as $register) {
                fputcsv($output, [
                    $register['id'],
                    $register['name'],
                    $register['email'],
                    $register['phone'],
                    $register['company'] ?? '',
                    $register['text'],
                    $register['date']
                ], ';');
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            error_log("Error exportando CSV: " . $e->getMessage());
            $this->redirectWithMessage('listar.php', 'Error al exportar los datos', 'error');
        }
    }
    
    /**
     * Obtener datos para gráficos (AJAX)
     */
    public function getChartData() {
        if (!isAjaxRequest()) {
            http_response_code(400);
            exit;
        }
        
        try {
            $type = $_GET['type'] ?? 'monthly';
            $data = [];
            
            switch ($type) {
                case 'monthly':
                    $sql = "SELECT 
                                YEAR(date) as year,
                                MONTH(date) as month,
                                COUNT(*) as count
                            FROM registers 
                            WHERE deleted = 0 AND date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                            GROUP BY YEAR(date), MONTH(date)
                            ORDER BY year, month";
                    break;
                    
                case 'daily':
                    $sql = "SELECT 
                                DATE(date) as date,
                                COUNT(*) as count
                            FROM registers 
                            WHERE deleted = 0 AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                            GROUP BY DATE(date)
                            ORDER BY date";
                    break;
                    
                default:
                    throw new Exception('Tipo de gráfico no válido');
            }
            
            $stmt = $this->db->query($sql);
            $data = $stmt->fetchAll();
            
            $this->jsonResponse([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            error_log("Error obteniendo datos de gráfico: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener datos del gráfico'
            ], 500);
        }
    }
}
?>