<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar si es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Obtener estadísticas
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'usuario'");
$stmt->execute();
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM productos WHERE activo = 1");
$stmt->execute();
$total_productos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos");
$stmt->execute();
$total_pedidos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT SUM(total) as total FROM pedidos WHERE estado != 'cancelado'");
$stmt->execute();
$ventas_totales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Pedidos recientes
$stmt = $conn->prepare("
    SELECT p.*, u.nombre, u.apellido 
    FROM pedidos p 
    JOIN usuarios u ON p.usuario_id = u.id 
    ORDER BY p.fecha_pedido DESC 
    LIMIT 5
");
$stmt->execute();
$pedidos_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - La Subienda</title>
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="productos.php">
                            <i class="fas fa-birthday-cake me-2"></i>Productos
                        </a>
                        <a class="nav-link" href="pedidos.php">
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
                    <h1 class="text-primary mb-4">Dashboard</h1>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo $total_usuarios; ?></h3>
                                    <p class="text-muted">Usuarios</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-birthday-cake fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo $total_productos; ?></h3>
                                    <p class="text-muted">Productos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo $total_pedidos; ?></h3>
                                    <p class="text-muted">Pedidos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-dollar-sign fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary">$<?php echo number_format($ventas_totales, 2); ?></h3>
                                    <p class="text-muted">Ventas Totales</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pedidos Recientes -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pedidos Recientes</h5>
                        </div>
                        <div class="card-body">
                            <?php if(empty($pedidos_recientes)): ?>
                            <p class="text-muted">No hay pedidos recientes.</p>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($pedidos_recientes as $pedido): ?>
                                        <tr>
                                            <td>#<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo $pedido['nombre'] . ' ' . $pedido['apellido']; ?></td>
                                            <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($pedido['estado']) {
                                                    case 'pendiente': $badge_class = 'bg-warning'; break;
                                                    case 'procesando': $badge_class = 'bg-info'; break;
                                                    case 'completado': $badge_class = 'bg-success'; break;
                                                    case 'cancelado': $badge_class = 'bg-danger'; break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($pedido['estado']); ?></span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>