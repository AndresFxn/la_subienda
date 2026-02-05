<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$productos = obtenerTodosLosProductos($conn);
$categorias = obtenerCategorias($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h1 class="text-center text-primary mb-5">Nuestros Productos</h1>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="busqueda" placeholder="Buscar productos...">
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="filtro-categoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="row">
            <?php foreach($productos as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card" data-categoria="<?php echo $producto['categoria_id']; ?>">
                    <img src="<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo $producto['nombre']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                        <p class="card-text"><?php echo $producto['descripcion']; ?></p>
                        <div class="mb-2">
                            <span class="badge bg-secondary"><?php echo $producto['categoria_nombre']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">$<?php echo number_format($producto['precio'], 2); ?></span>
                            <?php if(isset($_SESSION['usuario_id'])): ?>
                                <button class="btn btn-outline-primary btn-sm" onclick="agregarAlCarrito(<?php echo $producto['id']; ?>)">
                                    <i class="fas fa-cart-plus"></i> Agregar
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(empty($productos)): ?>
        <div class="text-center py-5">
            <i class="fas fa-birthday-cake fa-3x text-muted mb-3"></i>
            <h3 class="text-muted">No hay productos disponibles</h3>
            <p class="text-muted">Pronto tendremos deliciosos productos para ti.</p>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>