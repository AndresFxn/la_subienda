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

$producto = obtenerProductoPorId($conn, $producto_id);
if (!$producto) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit;
}

$limites = validarLimitesUsuario($conn, $_SESSION['usuario_id']);

$stmt = $conn->prepare("SELECT c.nombre FROM categorias c JOIN productos p ON c.id = p.categoria_id WHERE p.id = ?");
$stmt->execute([$producto_id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if ($categoria) {
    if ($categoria['nombre'] == 'Pasteles' && !$limites['puede_agregar_pastel']) {
        echo json_encode(['success' => false, 'message' => 'Has alcanzado el límite de 5 pasteles']);
        exit;
    }
    
    if ($categoria['nombre'] == 'Postres' && !$limites['puede_agregar_postre']) {
        echo json_encode(['success' => false, 'message' => 'Has alcanzado el límite de 15 postres']);
        exit;
    }
}

if (agregarAlCarrito($conn, $_SESSION['usuario_id'], $producto_id, $cantidad)) {
    $cart_count = obtenerCantidadCarrito($_SESSION['usuario_id']);
    echo json_encode([
        'success' => true, 
        'message' => 'Producto agregado al carrito',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar producto']);
}
?>