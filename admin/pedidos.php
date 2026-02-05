<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar si es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$mensaje = '';
$error = '';

// Procesar cambio de estado
if ($_POST && isset($_POST['accion']) && $_POST['accion'] == 'cambiar_estado') {
    $pedido_id = (int)$_POST['pedido_id'];
    $nuevo_estado = $_POST['estado'];
    
    if (in_array($nuevo_estado, ['pendiente', 'procesando', 'completado', 'cancelado'])) {
        $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        if ($stmt->execute([$nuevo_estado, $pedido_id])) {
            $mensaje = 'Estado del pedido actualizado exitosamente';
        } else {
            $error = 'Error al actualizar el estado del pedido';
        }
    }
}

// Obtener pedidos con información del usuario
$stmt = $conn->prepare("
    SELECT p.*, u.nombre, u.apellido, u.email, u.telefono 
    FROM pedidos p 
    JOIN usuarios u ON p.usuario_id = u.id 
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos");
$stmt->execute();
$total_pedidos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos WHERE estado = 'pendiente'");
$stmt->execute();
$pedidos_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT SUM(total) as total FROM pedidos WHERE estado != 'cancelado'");
$stmt->execute();
$ventas_totales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-birthday-cake me-2"></i>La Subienda
                    </h4>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="productos.php">
                            <i class="fas fa-birthday-cake me-2"></i>Productos
                        </a>
                        <a class="nav-link active" href="pedidos.php">
                            <i class="fas fa-shopping-cart me-2"></i>Pedidos
                        </a>
                        <a class="nav-link" href="usuarios.php">
                            <i class="fas fa-users me-2"></i>Usuarios
                        </a>
                        <a class="nav-link" href="categorias.php">
                            <i class="fas fa-tags me-2"></i>Categorías
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-2"></i>Ir al Sitio
                        </a>
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h1 class="text-primary mb-4">Gestión de Pedidos</h1>
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo $total_pedidos; ?></h3>
                                    <p class="text-muted">Total Pedidos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h3 class="text-warning"><?php echo $pedidos_pendientes; ?></h3>
                                    <p class="text-muted">Pendientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                    <h3 class="text-success">$<?php echo number_format($ventas_totales, 2); ?></h3>
                                    <p class="text-muted">Ventas Totales</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                                    <h3 class="text-info"><?php echo count(array_filter($pedidos, function($p) { return strtotime($p['fecha_pedido']) > strtotime('-7 days'); })); ?></h3>
                                    <p class="text-muted">Esta Semana</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Pedidos -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Contacto</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellido']); ?></strong>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($pedido['email']); ?><br>
                                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($pedido['telefono']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong>$<?php echo number_format($pedido['total'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($pedido['estado']) {
                                                    case 'pendiente': $badge_class = 'bg-warning text-dark'; break;
                                                    case 'procesando': $badge_class = 'bg-info'; break;
                                                    case 'completado': $badge_class = 'bg-success'; break;
                                                    case 'cancelado': $badge_class = 'bg-danger'; break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($pedido['estado']); ?></span>
                                            </td>
                                            <td>
                                                <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="verDetalle(<?php echo $pedido['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" 
                                                            onclick="cambiarEstado(<?php echo $pedido['id']; ?>, '<?php echo $pedido['estado']; ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cambiar Estado -->
    <div class="modal fade" id="modalCambiarEstado" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Estado del Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="cambiar_estado">
                        <input type="hidden" name="pedido_id" id="cambiar_estado_id">
                        
                        <p>Pedido: <strong id="cambiar_estado_pedido"></strong></p>
                        
                        <div class="mb-3">
                            <label for="estado" class="form-label">Nuevo Estado</label>
                            <select class="form-select" name="estado" id="cambiar_estado_select" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="procesando">Procesando</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalle -->
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalle-content">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cambiarEstado(pedidoId, estadoActual) {
            document.getElementById('cambiar_estado_id').value = pedidoId;
            document.getElementById('cambiar_estado_pedido').textContent = '#' + String(pedidoId).padStart(6, '0');
            document.getElementById('cambiar_estado_select').value = estadoActual;
            
            new bootstrap.Modal(document.getElementById('modalCambiarEstado')).show();
        }
        
        function verDetalle(pedidoId) {
            // Mostrar loading
            document.getElementById('detalle-content').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                    <p>Cargando detalles del pedido #${String(pedidoId).padStart(6, '0')}...</p>
                </div>
            `;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            modal.show();
            
            // Hacer llamada AJAX
            fetch(`detalle-pedido.php?id=${pedidoId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('detalle-content').innerHTML = data.html;
                    } else {
                        throw new Error(data.error || 'Error desconocido');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detalle-content').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error al cargar los detalles:</strong> ${error.message}
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary" onclick="verDetalle(${pedidoId})">
                                <i class="fas fa-redo me-2"></i>Reintentar
                            </button>
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>