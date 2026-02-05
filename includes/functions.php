<?php
// Funciones generales del sistema

function obtenerProductosDestacados($conn, $limit = 6) {
    $limit = (int)$limit; // Asegurar que sea un entero
    $stmt = $conn->prepare("SELECT * FROM productos WHERE destacado = 1 AND activo = 1 LIMIT " . $limit);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerTodosLosProductos($conn) {
    $stmt = $conn->prepare("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.activo = 1 ORDER BY p.nombre");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductoPorId($conn, $id) {
    $stmt = $conn->prepare("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ? AND p.activo = 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerCategorias($conn) {
    $stmt = $conn->prepare("SELECT * FROM categorias ORDER BY nombre");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function registrarUsuario($conn, $datos) {
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, cedula, direccion, telefono, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $datos['nombre'],
        $datos['apellido'],
        $datos['cedula'],
        $datos['direccion'],
        $datos['telefono'],
        $datos['email'],
        password_hash($datos['password'], PASSWORD_DEFAULT)
    ]);
}

function autenticarUsuario($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($password, $usuario['password'])) {
        return $usuario;
    }
    return false;
}

function agregarAlCarrito($conn, $usuario_id, $producto_id, $cantidad = 1) {
    // Verificar si el producto ya está en el carrito
    $stmt = $conn->prepare("SELECT * FROM carrito WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$usuario_id, $producto_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        // Actualizar cantidad
        $nueva_cantidad = $item['cantidad'] + $cantidad;
        $stmt = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
        return $stmt->execute([$nueva_cantidad, $item['id']]);
    } else {
        // Agregar nuevo item
        $stmt = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, ?)");
        return $stmt->execute([$usuario_id, $producto_id, $cantidad]);
    }
}

function obtenerCarrito($conn, $usuario_id) {
    $stmt = $conn->prepare("
        SELECT c.*, p.nombre, p.precio, p.imagen 
        FROM carrito c 
        JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario_id = ?
        ORDER BY c.fecha_agregado DESC
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerCantidadCarrito($usuario_id) {
    if (!isset($_SESSION['usuario_id'])) {
        return 0;
    }
    
    global $conn;
    $stmt = $conn->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function calcularTotalCarrito($conn, $usuario_id) {
    $stmt = $conn->prepare("
        SELECT SUM(c.cantidad * p.precio) as total 
        FROM carrito c 
        JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function eliminarDelCarrito($conn, $usuario_id, $producto_id) {
    $stmt = $conn->prepare("DELETE FROM carrito WHERE usuario_id = ? AND producto_id = ?");
    return $stmt->execute([$usuario_id, $producto_id]);
}

function actualizarCantidadCarrito($conn, $usuario_id, $producto_id, $cantidad) {
    if ($cantidad <= 0) {
        return eliminarDelCarrito($conn, $usuario_id, $producto_id);
    }
    
    $stmt = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE usuario_id = ? AND producto_id = ?");
    return $stmt->execute([$cantidad, $usuario_id, $producto_id]);
}

function procesarPedido($conn, $usuario_id) {
    try {
        $conn->beginTransaction();
        
        // Calcular total
        $total = calcularTotalCarrito($conn, $usuario_id);
        
        // Crear pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $total]);
        $pedido_id = $conn->lastInsertId();
        
        // Obtener items del carrito
        $carrito = obtenerCarrito($conn, $usuario_id);
        
        // Crear detalles del pedido
        foreach ($carrito as $item) {
            $stmt = $conn->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$pedido_id, $item['producto_id'], $item['cantidad'], $item['precio']]);
        }
        
        // Limpiar carrito
        $stmt = $conn->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        
        $conn->commit();
        return $pedido_id;
        
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function obtenerPedidosUsuario($conn, $usuario_id) {
    $stmt = $conn->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function validarLimitesUsuario($conn, $usuario_id) {
    $stmt = $conn->prepare("
        SELECT 
            SUM(CASE WHEN c.nombre = 'Pasteles' THEN ca.cantidad ELSE 0 END) as pasteles,
            SUM(CASE WHEN c.nombre = 'Postres' THEN ca.cantidad ELSE 0 END) as postres
        FROM carrito ca
        JOIN productos p ON ca.producto_id = p.id
        JOIN categorias c ON p.categoria_id = c.id
        WHERE ca.usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    $limites = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'pasteles' => $limites['pasteles'] ?? 0,
        'postres' => $limites['postres'] ?? 0,
        'puede_agregar_pastel' => ($limites['pasteles'] ?? 0) < 5,
        'puede_agregar_postre' => ($limites['postres'] ?? 0) < 15
    ];
}

function limpiarInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validarCedula($cedula) {
    return preg_match('/^[0-9]{8,12}$/', $cedula);
}

function validarTelefono($telefono) {
    return preg_match('/^[0-9+\-\s()]{10,20}$/', $telefono);
}
?>