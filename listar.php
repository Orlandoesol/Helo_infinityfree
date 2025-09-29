<?php
/**
 * Dashboard principal - Listado de registros
 */

require_once __DIR__ . '/includes/init.php';

// Verificar autenticación
requireAuth();

// Obtener datos del usuario
$userName = $_SESSION['user'];
$userRole = $_SESSION['role'] ?? 'asesor';

try {
    $db = Database::getInstance();
    
    // Configuración de paginación
    $perPage = isset($_GET['per_page']) && in_array($_GET['per_page'], [10, 25, 40]) ? (int)$_GET['per_page'] : 10;
    $page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $perPage;
    
    // Consulta para contar total de registros
    $countSql = "SELECT COUNT(*) as total FROM registers WHERE deleted = 0";
    $countStmt = $db->query($countSql);
    $totalRecords = $countStmt->fetch()['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Consulta principal para obtener registros
    if ($totalRecords > 0) {
        $sql = "SELECT * FROM registers WHERE deleted = 0 ORDER BY date DESC";
        $allStmt = $db->query($sql);
        $allRegisters = $allStmt->fetchAll();
        
        // Aplicar paginación manualmente
        $registers = array_slice($allRegisters, $offset, $perPage);
    } else {
        $registers = [];
    }
    
    // Usar el total ya calculado para evitar consultas duplicadas
    $totalCount = $totalRecords;
    
    $todaySql = "SELECT COUNT(*) as today FROM registers WHERE deleted = 0 AND DATE(date) = CURDATE()";
    $todayStmt = $db->query($todaySql);
    $todayCount = $todayStmt->fetch()['today'];
    
    $monthSql = "SELECT COUNT(*) as month FROM registers WHERE deleted = 0 AND YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())";
    $monthStmt = $db->query($monthSql);
    $monthCount = $monthStmt->fetch()['month'];
    
} catch (Exception $e) {
    $registers = [];
    $totalCount = 0;
    $todayCount = 0;
    $monthCount = 0;
    $totalRecords = 0;
    $totalPages = 1;
    $page = 1;
    $perPage = 10;
    
    $debug_error = "Error en dashboard: " . $e->getMessage();
    error_log($debug_error);
}

$pageTitle = 'Tablero Administrativo';
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
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
</head>
<body>
    <div class="dashboard-container">
        <?php 
        // Configurar datos para el layout
        $pageTitle = 'Tablero';
        $pageSubtitle = 'Gestión de registros';
        
        // Incluir navegación del dashboard
        include __DIR__ . '/views/layouts/dashboard-nav.php';
        ?>

            <!-- Contenido del dashboard -->
            <div class="stats-container">
                <!-- Estadísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <h4 class="stat-title">Total Registros</h4>
                            <div class="stat-icon primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($totalCount) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Todos los registros</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <h4 class="stat-title">Registros Hoy</h4>
                            <div class="stat-icon success">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= $todayCount ?></div>
                        <div class="stat-change">
                            <i class="fas fa-clock"></i>
                            <span>Últimas 24 horas</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <h4 class="stat-title">Este Mes</h4>
                            <div class="stat-icon warning">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($monthCount) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-percentage"></i>
                            <span><?= $totalCount > 0 ? round(($monthCount / $totalCount) * 100) : 0 ?>% del total</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <h4 class="stat-title">Promedio Diario</h4>
                            <div class="stat-icon info">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= $totalCount > 0 ? round($monthCount / date('j')) : 0 ?></div>
                        <div class="stat-change">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Este mes</span>
                        </div>
                    </div>
                </div>

                <!-- Tabla de registros recientes -->
                <div class="content-section">
                    <div class="section-header">
                        <h2 class="section-title">Registros</h2>
                        <div>
                            <!--
                            <a href="<?= url() ?>#registro" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Nuevo Registro
                            </a>
                            <?php if (hasRole('admin')): ?>
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i>
                                Nuevo Usuario
                            </a>
                            <?php endif; ?>
                            -->
                            <a href="export.php" class="btn btn-secondary">
                                <i class="fas fa-file-csv"></i>
                                Exportar CSV
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <?php if (isset($debug_error)): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <strong>Debug Info:</strong> <?= htmlspecialchars($debug_error) ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($registers && count($registers) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Objetivos de la empresa</th>
                                    <th>Estado</th>
                                    <?php if (hasRole('admin')): ?>
                                        <th>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registers as $register): ?>
                                <tr>
                                    <td><strong>#<?= $register['id'] ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($register['date'])) ?></td>
                                    <td><?= htmlspecialchars($register['name']) ?></td>
                                    <td><?= htmlspecialchars($register['phone']) ?></td>
                                    <td><?= htmlspecialchars($register['email']) ?></td>
                                    <td>
                                        <span class="text-cell" title="<?= htmlspecialchars($register['text'] ?? 'Sin información') ?>">
                                            <?= htmlspecialchars(substr($register['text'] ?? 'Sin información', 0, 50)) ?><?= strlen($register['text'] ?? '') > 50 ? '...' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $register['deleted'] ? 'inactive' : 'active' ?>">
                                            <?= $register['deleted'] ? 'Inactivo' : 'Activo' ?>
                                        </span>
                                    </td>
                                    <?php if (hasRole('admin')): ?>
                                    <td>
                                        <a href="editar.php?id=<?= $register['id'] ?>" style="color: #007bff; margin-right: 10px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="confirmarEliminar(<?= $register['id'] ?>)" style="color: #dc3545;">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- Controles de paginación -->
                        <div class="pagination-controls">
                            <div class="pagination-info">
                                Mostrando <?= (($page - 1) * $perPage) + 1 ?> a <?= min($page * $perPage, $totalRecords) ?> de <?= $totalRecords ?> registros
                            </div>
                            
                            <div class="pagination-actions">
                                <div class="per-page-selector">
                                    <label>Mostrar:</label>
                                    <select onchange="changePerPage(this.value)">
                                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                                        <option value="40" <?= $perPage == 40 ? 'selected' : '' ?>>40</option>
                                    </select>
                                </div>
                                
                                <?php if ($totalPages > 1): ?>
                                <div class="pagination-nav">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>" class="pagination-btn">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="pagination-btn disabled">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    
                                    if ($startPage > 1): ?>
                                        <a href="?page=1&per_page=<?= $perPage ?>" class="pagination-btn">1</a>
                                        <?php if ($startPage > 2): ?>
                                            <span class="pagination-btn disabled">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <?php if ($i == $page): ?>
                                            <span class="pagination-btn active"><?= $i ?></span>
                                        <?php else: ?>
                                            <a href="?page=<?= $i ?>&per_page=<?= $perPage ?>" class="pagination-btn"><?= $i ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    
                                    <?php if ($endPage < $totalPages): ?>
                                        <?php if ($endPage < $totalPages - 1): ?>
                                            <span class="pagination-btn disabled">...</span>
                                        <?php endif; ?>
                                        <a href="?page=<?= $totalPages ?>&per_page=<?= $perPage ?>" class="pagination-btn"><?= $totalPages ?></a>
                                    <?php endif; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <a href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>" class="pagination-btn">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="pagination-btn disabled">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>No hay registros</h4>
                            <p>Aún no se han registrado datos en el sistema.</p>
                            <a href="<?= url() ?>#registro" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Crear primer registro
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de confirmación -->
    <div id="modalConfirmacion" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="modal-title">¿Eliminar registro?</h2>
                <p class="modal-subtitle">Esta acción no se puede deshacer</p>
            </div>
            
            <div class="modal-body">
                <p class="modal-message">
                    ¿Estás seguro de que deseas eliminar este registro de forma permanente?
                </p>
                <div class="modal-detail">
                    <strong>ID del registro:</strong> <span id="registroId"></span>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="cerrarModal()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="button" class="modal-btn modal-btn-delete" onclick="eliminarRegistro()" id="btnEliminar">
                        <i class="fas fa-trash"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let registroParaEliminar = null;

        // Función para cambiar registros por página
        function changePerPage(perPage) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('per_page', perPage);
            urlParams.set('page', '1'); // Reset to first page
            window.location.search = urlParams.toString();
        }

        // Función para mostrar modal de confirmación
        function confirmarEliminar(id) {
            registroParaEliminar = id;
            document.getElementById('registroId').textContent = id;
            
            const modal = document.getElementById('modalConfirmacion');
            modal.classList.add('show');
            
            // Enfocar el botón cancelar para accesibilidad
            setTimeout(() => {
                document.querySelector('.modal-btn-cancel').focus();
            }, 300);
        }

        // Función para cerrar modal
        function cerrarModal() {
            const modal = document.getElementById('modalConfirmacion');
            modal.classList.remove('show');
            registroParaEliminar = null;
        }

        // Función para eliminar registro
        function eliminarRegistro() {
            if (!registroParaEliminar) return;
            
            const btnEliminar = document.getElementById('btnEliminar');
            const originalContent = btnEliminar.innerHTML;
            
            // Mostrar estado de carga
            btnEliminar.innerHTML = '<span class="loading-spinner"></span>Eliminando...';
            btnEliminar.classList.add('loading');
            btnEliminar.disabled = true;
            
            // Simular delay para UX (opcional)
            setTimeout(() => {
                window.location.href = 'eliminar.php?id=' + registroParaEliminar;
            }, 800);
        }

        // Cerrar modal con Escape o clic fuera
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });

        document.getElementById('modalConfirmacion').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });

        // Animación de contadores
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stat-value');
            
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/,/g, ''));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current).toLocaleString();
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target.toLocaleString();
                    }
                };
                
                updateCounter();
            });
        });
    </script>
</body>
</html>