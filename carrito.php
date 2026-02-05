<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$carrito = obtenerCarrito($conn, $_SESSION['usuario_id']);
$total = calcularTotalCarrito($conn, $_SESSION['usuario_id']);
$limites = validarLimitesUsuario($conn, $_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h1 class="text-center text-primary mb-5">
            <i class="fas fa-shopping-cart me-2"></i>Mi Carrito
        </h1>
        
        <?php if(empty($carrito)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h3 class="text-muted">Tu carrito está vacío</h3>
            <p class="text-muted">Agrega algunos productos deliciosos a tu carrito.</p>
            <a href="productos.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>Ver Productos
            </a>
        </div>
        <?php else: ?>
        
        <!-- Información de límites -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Límites de compra:</h6>
                    <p class="mb-1">Pasteles: <?php echo $limites['pasteles']; ?>/5</p>
                    <p class="mb-0">Postres: <?php echo $limites['postres']; ?>/15</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Items del carrito -->
                <?php foreach($carrito as $item): ?>
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="<?php echo $item['imagen']; ?>" class="img-fluid rounded" alt="<?php echo $item['nombre']; ?>">
                        </div>
                        <div class="col-md-4">
                            <h5><?php echo $item['nombre']; ?></h5>
                            <p class="text-muted mb-0">$<?php echo number_format($item['precio'], 2); ?> c/u</p>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="actualizarCantidad(<?php echo $item['producto_id']; ?>, <?php echo $item['cantidad'] - 1; ?>)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" 
                                       value="<?php echo $item['cantidad']; ?>" min="1" readonly>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="actualizarCantidad(<?php echo $item['producto_id']; ?>, <?php echo $item['cantidad'] + 1; ?>)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <strong class="text-primary">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></strong>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-outline-danger btn-sm" 
                                    onclick="eliminarDelCarrito(<?php echo $item['producto_id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="col-lg-4">
                <!-- Resumen del pedido -->
                <div class="cart-summary">
                    <h4 class="mb-4">Resumen del Pedido</h4>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Envío:</span>
                        <span>Gratis</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total:</strong>
                        <strong>$<?php echo number_format($total, 2); ?></strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="checkout.php" class="btn btn-light btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                        </a>
                        <a href="productos.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>