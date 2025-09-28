<?php
/**
 * Controlador para eliminación de registros
 * Maneja la eliminación lógica de registros con confirmación
 * 
 * @author Emmanuel Arenilla
 * @version 2.0
 */

require_once __DIR__ . '/includes/init.php';

// Verificar autenticación y permisos de administrador
requireAuth();
if (!hasRole('admin')) {
    redirectWithMessage(url('registros'), 'No tienes permisos para realizar esta acción', 'error');
}

try {
    $db = Database::getInstance();
    $id = (int)($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        redirectWithMessage(url('registros'), 'ID de registro inválido', 'error');
    }
    
    // Verificar que el registro exista y no esté ya eliminado
    $sql = "SELECT * FROM registers WHERE id = ? AND deleted = 0";
    $stmt = $db->query($sql, [$id]);
    $register = $stmt->fetch();
    
    if (!$register) {
        redirectWithMessage(url('registros'), 'Registro no encontrado o ya eliminado', 'error');
    }
    
    // Procesar confirmación de eliminación
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $confirmDelete = ($_POST['confirm_delete'] ?? '') === 'yes';
        
        if (!$confirmDelete) {
            redirectWithMessage(url('registros'), 'Eliminación cancelada', 'info');
        }
        
        // Realizar eliminación lógica
        $deleteSql = "UPDATE registers 
                    SET deleted = 1
                    WHERE id = ? AND deleted = 0";
        
        $stmt = $db->query($deleteSql, [$id]);
        
        if ($stmt) {
            // Log de la eliminación
            error_log("Registro eliminado: ID {$id} ({$register['name']}) por usuario " . $_SESSION['user']);
            
            redirectWithMessage(url('registros'), 'Registro eliminado correctamente', 'success');
        } else {
            throw new Exception('Error al eliminar el registro');
        }
    }
    
} catch (Exception $e) {
    error_log("Error en eliminar.php: " . $e->getMessage());
    redirectWithMessage(url('registros'), 'Error al procesar la solicitud', 'error');
}

$pageTitle = 'Eliminar Registro';
$userName = $_SESSION['user'];
$userRole = $_SESSION['role'] ?? 'asesor';
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
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/common.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/delete.css') ?>">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?= url() ?>" class="sidebar-brand">
                    <img src="<?= asset('images/logo.jpg') ?>" alt="Logo">
                    Energía Inteligente
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <a href="<?= url('registros') ?>" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="<?= url('registros') ?>" class="nav-link active">
                    <i class="fas fa-list"></i>
                    Registros
                </a>
                <a href="<?= url('editar.php') ?>" class="nav-link">
                    <i class="fas fa-edit"></i>
                    Editar
                </a>
                
                <div class="nav-divider"></div>
                
                <a href="<?= url() ?>" class="nav-link">
                    <i class="fas fa-home"></i>
                    Ir al sitio web
                </a>
                <a href="<?= url('logout.php') ?>" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Barra superior -->
            <div class="topbar">
                <div class="topbar-left">
                    <i class="fas fa-trash-alt" style="color: #dc3545; font-size: 1.2rem;"></i>
                    <h1 class="topbar-title">Eliminar Registro</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($userName, 0, 2)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($userName) ?></div>
                        <div style="font-size: 12px; color: #6c757d;"><?= ucfirst($userRole) ?></div>
                    </div>
                </div>
            </div>

            <!-- Contenido del formulario -->
            <div class="content-container">
                <div class="delete-card">
                    <div class="delete-header">
                        <div class="delete-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h2 class="delete-title">Confirmar Eliminación</h2>
                    </div>
                    
                    <div class="delete-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-warning" style="margin-right: 8px;"></i>
                            <strong>¡Atención!</strong> Esta acción eliminará permanentemente el registro.
                        </div>
                        
                        <!-- Información del registro -->
                        <div class="info-card">
                            <h4 style="margin-bottom: 15px; color: #2d3748;">Información del registro:</h4>
                            
                            <div class="info-row">
                                <div class="info-label">ID:</div>
                                <div class="info-value">#<?= str_pad($register['id'], 6, '0', STR_PAD_LEFT) ?></div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-label">Nombre:</div>
                                <div class="info-value"><?= htmlspecialchars($register['name'] ?? '') ?></div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-label">Email:</div>
                                <div class="info-value"><?= htmlspecialchars($register['email'] ?? '') ?></div>
                            </div>
                        </div>
                        
                        <form method="POST">
                            <div class="form-check" id="confirmCheck">
                                <input type="checkbox" 
                                    id="confirmDelete" 
                                    name="confirm_delete" 
                                    value="yes"
                                    required>
                                <label for="confirmDelete">
                                    Confirmo que deseo eliminar este registro permanentemente
                                </label>
                            </div>
                            
                            <div class="actions">
                                <a href="<?= url('registros') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Cancelar
                                </a>
                                
                                <button type="submit" 
                                        class="btn btn-danger" 
                                        id="deleteBtn"
                                        disabled>
                                    <i class="fas fa-trash"></i>
                                    Eliminar Registro
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Habilitar botón de eliminar solo si se confirma
        document.addEventListener('DOMContentLoaded', function() {
            const confirmCheck = document.getElementById('confirmDelete');
            const deleteBtn = document.getElementById('deleteBtn');
            const checkContainer = document.getElementById('confirmCheck');
            
            // Verificar que los elementos existen
            if (!confirmCheck || !deleteBtn || !checkContainer) {
                console.error('Elementos no encontrados en el DOM');
                return;
            }
            
            confirmCheck.addEventListener('change', function() {
                // Habilitar/deshabilitar botón
                deleteBtn.disabled = !this.checked;
                
                // Cambiar estilos visuales
                if (this.checked) {
                    checkContainer.classList.add('checked');
                    deleteBtn.style.opacity = '1';
                    deleteBtn.style.cursor = 'pointer';
                } else {
                    checkContainer.classList.remove('checked');
                    deleteBtn.style.opacity = '0.6';
                    deleteBtn.style.cursor = 'not-allowed';
                }
            });
        });
    </script>
</body>
</html>