<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Verificar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);
$producto_id = $input['producto_id'] ?? null;

if (!$producto_id) {
    echo json_encode(['success' => false, 'message' => 'Producto no especificado']);
    exit;
}

// Eliminar del carrito
if (eliminarDelCarrito($conn, $_SESSION['usuario_id'], $producto_id)) {
    $cart_count = obtenerCantidadCarrito($_SESSION['usuario_id']);
    echo json_encode([
        'success' => true, 
        'message' => 'Producto eliminado del carrito',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar producto']);
}
?>