<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pedidos = obtenerPedidosUsuario($conn, $_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h1 class="text-center text-primary mb-5">
            <i class="fas fa-list me-2"></i>Mis Pedidos
        </h1>
        
        <?php if(empty($pedidos)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
            <h3 class="text-muted">No tienes pedidos aún</h3>
            <p class="text-muted">Realiza tu primera compra y aparecerá aquí.</p>
            <a href="productos.php" class="btn btn-primary">
                <i class="fas fa-shopping-cart me-2"></i>Empezar a Comprar
            </a>
        </div>
        <?php else: ?>
        
        <div class="row">
            <?php foreach($pedidos as $pedido): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></h6>
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
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1"><strong>Fecha:</strong></p>
                                <p class="text-muted"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><strong>Total:</strong></p>
                                <p class="text-primary h5">$<?php echo number_format($pedido['total'], 2); ?></p>
                            </div>
                        </div>
                        
                        <?php
                        // Obtener detalles del pedido
                        $stmt = $conn->prepare("
                            SELECT dp.*, p.nombre 
                            FROM detalle_pedidos dp 
                            JOIN productos p ON dp.producto_id = p.id 
                            WHERE dp.pedido_id = ?
                        ");
                        $stmt->execute([$pedido['id']]);
                        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        
                        <div class="mt-3">
                            <h6>Productos:</h6>
                            <?php foreach($detalles as $detalle): ?>
                            <div class="d-flex justify-content-between">
                                <span><?php echo $detalle['nombre']; ?> x<?php echo $detalle['cantidad']; ?></span>
                                <span>$<?php echo number_format($detalle['precio_unitario'] * $detalle['cantidad'], 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>