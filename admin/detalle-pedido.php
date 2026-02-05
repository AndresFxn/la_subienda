<?php
session_start();
require_once '../config/database.php';

// Verificar si es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar que se envió el ID del pedido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de pedido inválido']);
    exit;
}

$pedido_id = (int)$_GET['id'];

try {
    // Obtener información del pedido
    $stmt = $conn->prepare("
        SELECT p.*, u.nombre, u.apellido, u.email, u.telefono, u.direccion, u.cedula
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
        exit;
    }
    
    // Obtener detalles del pedido (productos)
    $stmt = $conn->prepare("
        SELECT dp.*, pr.nombre as producto_nombre, pr.imagen, c.nombre as categoria_nombre
        FROM detalle_pedidos dp
        JOIN productos pr ON dp.producto_id = pr.id
        LEFT JOIN categorias c ON pr.categoria_id = c.id
        WHERE dp.pedido_id = ?
        ORDER BY pr.nombre
    ");
    $stmt->execute([$pedido_id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generar HTML
    ob_start();
    ?>
    
    <div class="row">
        <!-- Información del Cliente -->
        <div class="col-md-6">
            <h6 class="text-primary mb-3">Información del Cliente</h6>
            <div class="card">
                <div class="card-body">
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellido']); ?></p>
                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($pedido['cedula']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['email']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['telefono']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Información del Pedido -->
        <div class="col-md-6">
            <h6 class="text-primary mb-3">Información del Pedido</h6>
            <div class="card">
                <div class="card-body">
                    <p><strong>ID:</strong> #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></p>
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                    <p><strong>Estado:</strong> 
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
                    </p>
                    <p><strong>Total:</strong> <span class="text-success fs-5">$<?php echo number_format($pedido['total'], 2); ?></span></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Productos del Pedido -->
    <div class="row mt-4">
        <div class="col-12">
            <h6 class="text-primary mb-3">Productos del Pedido</h6>
            
            <?php if (empty($detalles)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No se encontraron productos para este pedido.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_calculado = 0;
                            foreach ($detalles as $detalle): 
                                $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
                                $total_calculado += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../<?php echo $detalle['imagen'] ? $detalle['imagen'] : 'assets/images/placeholder.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($detalle['producto_nombre']); ?>"
                                             class="me-3 rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             onerror="this.src='https://via.placeholder.com/50x50/e91e63/ffffff?text=IMG'">
                                        <strong><?php echo htmlspecialchars($detalle['producto_nombre']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($detalle['categoria_nombre'] ?? 'Sin categoría'); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $detalle['cantidad']; ?></span>
                                </td>
                                <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                <td><strong>$<?php echo number_format($subtotal, 2); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th class="text-success">$<?php echo number_format($total_calculado, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <?php if (abs($total_calculado - $pedido['total']) > 0.01): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Nota:</strong> Hay una diferencia entre el total calculado ($<?php echo number_format($total_calculado, 2); ?>) 
                        y el total registrado ($<?php echo number_format($pedido['total'], 2); ?>).
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
    $html = ob_get_clean();
    
    // Devolver respuesta JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'html' => $html,
        'pedido' => [
            'id' => $pedido['id'],
            'cliente' => $pedido['nombre'] . ' ' . $pedido['apellido'],
            'total' => $pedido['total'],
            'estado' => $pedido['estado'],
            'fecha' => $pedido['fecha_pedido']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}
?>