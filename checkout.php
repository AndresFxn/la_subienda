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

if (empty($carrito)) {
    header('Location: carrito.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h1 class="text-center text-primary mb-5">
            <i class="fas fa-credit-card me-2"></i>Finalizar Compra
        </h1>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Información de envío -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Información de Envío</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> <?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></p>
                                <p><strong>Teléfono:</strong> <?php echo $usuario['telefono']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>
                                <p><strong>Dirección:</strong> <?php echo $usuario['direccion']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información de pago -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Información de Pago</h5>
                    </div>
                    <div class="card-body">
                        <form id="pago-form">
                            <div class="mb-3">
                                <label for="numero_tarjeta" class="form-label">Número de Tarjeta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                    <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" 
                                           placeholder="1234 5678 9012 3456" maxlength="19" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nombre_tarjeta" class="form-label">Nombre en la Tarjeta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nombre_tarjeta" name="nombre_tarjeta" 
                                           value="<?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="expiracion" class="form-label">Fecha de Expiración</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="text" class="form-control" id="expiracion" name="expiracion" 
                                                   placeholder="MM/AA" maxlength="5" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="text" class="form-control" id="cvv" name="cvv" 
                                                   placeholder="123" maxlength="4" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Resumen del pedido -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach($carrito as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo $item['nombre']; ?> x<?php echo $item['cantidad']; ?></span>
                            <span>$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span>Gratis</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-lg" id="btn-pagar" onclick="procesarPago()">
                                <i class="fas fa-lock me-2"></i>Pagar Ahora
                            </button>
                            <a href="carrito.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Carrito
                            </a>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Pago seguro y protegido
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Formatear número de tarjeta
        document.getElementById('numero_tarjeta').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
        
        // Formatear fecha de expiración
        document.getElementById('expiracion').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
        
        // Solo números en CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>