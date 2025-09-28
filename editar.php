<?php
/**
 * Editar Registro
 */

require_once __DIR__ . '/includes/init.php';

// Verificar autenticación
requireAuth();

$id = (int)($_GET['id'] ?? 0);
$errors = [];
$register = null;

try {
    $db = Database::getInstance();
    
    // Procesar formulario POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $text = sanitizeInput($_POST['text'] ?? '');
        
        // Validaciones básicas
        if (empty($name)) $errors[] = 'El nombre es requerido';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
        if (empty($phone)) $errors[] = 'El teléfono es requerido';
        if (empty($text)) $errors[] = 'El mensaje es requerido';
        
        if (empty($errors)) {
            $sql = "UPDATE registers SET name = ?, email = ?, phone = ?, text = ? WHERE id = ?";
            if ($db->query($sql, [$name, $email, $phone, $text, $id])) {
                header('Location: listar.php?msg=updated');
                exit;
            } else {
                $errors[] = 'Error al actualizar el registro';
            }
        }
    }
    
    // Obtener datos del registro
    if ($id > 0) {
        $sql = "SELECT * FROM registers WHERE id = ? AND deleted = 0";
        $stmt = $db->query($sql, [$id]);
        $register = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$register || empty($register)) {
            error_log("Registro no encontrado con ID: " . $id);
            header('Location: listar.php?msg=not_found');
            exit;
        }
    } else {
        header('Location: listar.php');
        exit;
    }
    
} catch (Exception $e) {
    error_log("Error en editar.php: " . $e->getMessage());
    $errors[] = 'Error del sistema. Intente nuevamente.';
    // Asegurar que $register no sea null para evitar errores
    if (!isset($register) || !$register) {
        $register = [
            'id' => $id,
            'name' => '',
            'email' => '',
            'phone' => '',
            'text' => '',
            'date' => date('Y-m-d H:i:s')
        ];
    }
}

// Validación final para asegurar que tenemos un registro válido
if (!isset($register) || !is_array($register) || empty($register)) {
    error_log("Registro inválido o no encontrado para ID: " . $id);
    header('Location: listar.php?msg=not_found');
    exit;
}

$pageTitle = 'Editar Registro';
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
    
    <!-- Estilos -->
    <link rel="stylesheet" href="<?= asset('css/common.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/edit.css') ?>">
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
                
                <a href="<?= url('registros') ?>" class="nav-link">
                    <i class="fas fa-users"></i>
                    Registros
                </a>
                
                <?php if (hasRole('admin')): ?>
                <a href="<?= url('usuarios.php') ?>" class="nav-link">
                    <i class="fas fa-user-cog"></i>
                    Usuarios
                </a>
                
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    Reportes
                </a>
                
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    Configuración
                </a>
                <?php endif; ?>
                
                <div class="nav-divider"></div>
                
                <a href="<?= url() ?>" class="nav-link">
                    <i class="fas fa-home"></i>
                    Ir al sitio web
                </a>
                
                <a href="<?= url('logout.php') ?>" class="nav-link" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Barra superior -->
            <div class="topbar">
                <div class="breadcrumb">
                    <a href="<?= url('registros') ?>">Dashboard</a>
                    <span class="breadcrumb-separator">›</span>
                    <span>Editar Registro</span>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($userName, 0, 2)) ?>
                    </div>
                    <div>
                        <strong><?= htmlspecialchars($userName) ?></strong>
                        <div style="font-size: 12px; color: #6c757d;"><?= ucfirst($userRole) ?></div>
                    </div>
                </div>
            </div>

            <!-- Contenido del formulario -->
            <div class="content-container">
                <div class="form-card">
                    <div class="form-header">
                        <h1><i class="fas fa-edit"></i> Editar Registro</h1>
                        <p>Modificar información del registro #<?= isset($register['id']) ? str_pad($register['id'], 6, '0', STR_PAD_LEFT) : '000000' ?></p>
                    </div>
                    
                    <div class="form-body">
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul style="margin: 10px 0 0 20px;">
                                <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Información del registro -->
                        <div class="register-info">
                            <div class="register-info-item">
                                <i class="fas fa-id-card"></i>
                                <span><strong>ID:</strong> #<?= isset($register['id']) ? str_pad($register['id'], 6, '0', STR_PAD_LEFT) : '000000' ?></span>
                            </div>
                            <div class="register-info-item">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Fecha de registro:</strong> <?= isset($register['date']) ? date('d/m/Y H:i', strtotime($register['date'])) : 'No disponible' ?></span>
                            </div>
                            <div class="register-info-item">
                                <i class="fas fa-user"></i>
                                <span><strong>Registrado por:</strong> Sistema</span>
                            </div>
                        </div>

                        <!-- Formulario -->
                        <form method="POST" id="editForm">
                            <input type="hidden" name="id" value="<?= $register['id'] ?? 0 ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        Nombre completo <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                        id="name" 
                                        name="name" 
                                        class="form-control"
                                        value="<?= htmlspecialchars($register['name'] ?? '') ?>"
                                        required
                                        placeholder="Ingresa el nombre completo">
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        Correo electrónico <span class="required">*</span>
                                    </label>
                                    <input type="email" 
                                        id="email" 
                                        name="email" 
                                        class="form-control"
                                        value="<?= htmlspecialchars($register['email'] ?? '') ?>"
                                        required
                                        placeholder="correo@ejemplo.com">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        Teléfono <span class="required">*</span>
                                    </label>
                                    <input type="tel" 
                                        id="phone" 
                                        name="phone" 
                                        class="form-control"
                                        value="<?= htmlspecialchars($register['phone'] ?? '') ?>"
                                        required
                                        placeholder="3XX XXX XXXX">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="text" class="form-label">
                                    Mensaje <span class="required">*</span>
                                </label>
                                <textarea id="text" 
                                        name="text" 
                                        class="form-control"
                                        required
                                        placeholder="Mensaje o consulta del cliente"><?= htmlspecialchars($register['text'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Guardar Cambios
                                </button>
                                
                                <a href="<?= url('registros') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Volver al listado
                                </a>
                                
                                <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
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

    <!-- Modal de confirmación -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="modal-title">¿Eliminar registro?</h2>
                <p class="modal-subtitle">Esta acción no se puede deshacer</p>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar el registro <span class="highlight">#<?= str_pad($register['id'], 6, '0', STR_PAD_LEFT) ?></span>?</p>
                <p>Toda la información asociada se perderá permanentemente.</p>
                
                <div class="modal-actions">
                    <button type="button" class="btn-modal btn-cancel" onclick="cerrarModal()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn-modal btn-confirm" onclick="eliminarRegistro()">
                        <i class="fas fa-trash"></i> Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validación del formulario
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const form = this;
            let isValid = true;
            
            // Validar nombre
            const name = form.name.value.trim();
            if (name.length < 2) {
                showFieldError('name', 'El nombre debe tener al menos 2 caracteres');
                isValid = false;
            }
            
            // Validar email
            const email = form.email.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showFieldError('email', 'Ingresa un email válido');
                isValid = false;
            }
            
            // Validar teléfono
            const phone = form.phone.value.trim();
            if (phone.length < 7) {
                showFieldError('phone', 'Ingresa un teléfono válido');
                isValid = false;
            }
            
            // Validar mensaje
            const text = form.text.value.trim();
            if (text.length < 10) {
                showFieldError('text', 'El mensaje debe tener al menos 10 caracteres');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            field.classList.add('is-invalid');
            
            let feedback = field.parentNode.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                field.parentNode.appendChild(feedback);
            }
            feedback.textContent = message;
        }
        
        // Limpiar errores al escribir
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            });
        });
        
        // Confirmar eliminación con modal
        function confirmarEliminacion() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevenir scroll
        }
        
        function cerrarModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('active');
            document.body.style.overflow = ''; // Restaurar scroll
        }
        
        function eliminarRegistro() {
            // Mostrar loading en el botón
            const btnConfirm = document.querySelector('.btn-confirm');
            const originalText = btnConfirm.innerHTML;
            btnConfirm.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
            btnConfirm.disabled = true;
            
            // Redirigir después de un breve delay para mostrar el loading
            setTimeout(() => {
                window.location.href = '<?= url("eliminar.php?id=" . $register['id']) ?>';
            }, 1000);
        }
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
        
        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
        
        // Auto-resize textarea
        const textarea = document.getElementById('text');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>
</body>
</html>