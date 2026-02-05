<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['numero_tarjeta']) || empty($input['nombre_tarjeta']) || 
    empty($input['expiracion']) || empty($input['cvv'])) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

$carrito = obtenerCarrito($conn, $_SESSION['usuario_id']);
if (empty($carrito)) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
    exit;
}

$pedido_id = procesarPedido($conn, $_SESSION['usuario_id']);

if ($pedido_id) {
    echo json_encode([
        'success' => true, 
        'message' => 'Pago procesado exitosamente',
        'pedido_id' => $pedido_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al procesar el pedido']);
}
?>