<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$producto_id = $input['producto_id'] ?? null;
$cantidad = $input['cantidad'] ?? 1;

if (!$producto_id) {
    echo json_encode(['success' => false, 'message' => 'Producto no especificado']);
    exit;
}

if (actualizarCantidadCarrito($conn, $_SESSION['usuario_id'], $producto_id, $cantidad)) {
    $cart_count = obtenerCantidadCarrito($_SESSION['usuario_id']);
    echo json_encode([
        'success' => true, 
        'message' => 'Cantidad actualizada',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar cantidad']);
}
?>