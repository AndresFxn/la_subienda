<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pedido_id = $_GET['pedido'] ?? null;

if (!$pedido_id) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    
                    <h1 class="text-primary mb-4">¡Pago Exitoso!</h1>
                    
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Detalles del Pedido</h5>
                            
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p><strong>Número de Pedido:</strong> #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 2); ?></p>
                                    <p><strong>Estado:</strong> <span class="badge bg-success">Confirmado</span></p>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-info-circle me-2"></i>Información Importante:</h6>
                                <ul class="mb-0">
                                    <li>Tu pedido ha sido confirmado y está siendo procesado</li>
                                    <li>Recibirás un email de confirmación en breve</li>
                                    <li>El tiempo estimado de entrega es de 2-3 días hábiles</li>
                                    <li>Puedes seguir el estado de tu pedido en "Mis Pedidos"</li>
                                </ul>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                <a href="mis-pedidos.php" class="btn btn-primary">
                                    <i class="fas fa-list me-2"></i>Ver Mis Pedidos
                                </a>
                                <a href="productos.php" class="btn btn-outline-primary">
                                    <i class="fas fa-shopping-bag me-2"></i>Seguir Comprando
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h4 class="text-primary">¡Gracias por tu compra!</h4>
                        <p class="text-muted">Esperamos que disfrutes nuestros deliciosos productos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>