<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$productos_destacados = obtenerProductosDestacados($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Subienda - Pastelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="assets/images/logo.jpg">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-primary">La Subienda</h1>
                    <p class="lead">Los pasteles más deliciosos hechos con amor y los mejores ingredientes</p>
                    <a href="productos.php" class="btn btn-primary btn-lg">Ver Productos</a>
                </div>
                <div class="col-lg-6 hero-img text-center">
                    <img src="assets/images/cake-hero.png" alt="Pastel" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Productos Destacados</h2>
            <div class="row">
                <?php foreach ($productos_destacados as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <img src="<?php echo $producto['imagen']; ?>" class="card-img-top"
                                alt="<?php echo $producto['nombre']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                                <p class="card-text"><?php echo $producto['descripcion']; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span
                                        class="h5 text-primary">$<?php echo number_format($producto['precio'], 2); ?></span>
                                    <button class="btn btn-outline-primary btn-sm"
                                        onclick="agregarAlCarrito(<?php echo $producto['id']; ?>)">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>