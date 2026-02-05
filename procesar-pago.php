<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Verificar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);

// Validar datos de pago (simulación)
if (empty($input['numero_tarjeta']) || empty($input['nombre_tarjeta']) || 
    empty($input['expiracion']) || empty($input['cvv'])) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

// Verificar que el carrito no esté vacío
$carrito = obtenerCarrito($conn, $_SESSION['usuario_id']);
if (empty($carrito)) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
    exit;
}

// Procesar el pedido
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