<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <!-- Hero Section -->
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <h1 class="display-4 text-primary mb-4">Sobre La Subienda</h1>
                <p class="lead">Somos una pastelería artesanal dedicada a crear momentos dulces e inolvidables para nuestros clientes desde hace más de 10 años.</p>
                <p>En La Subienda, cada pastel, postre y dulce es elaborado con amor, utilizando ingredientes frescos y de la más alta calidad. Nuestro compromiso es brindar productos excepcionales que endulcen los momentos más especiales de tu vida.</p>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/nosotros-hero.jpg" alt="Nuestra pastelería" class="img-fluid rounded shadow" 
                     onerror="this.src='https://via.placeholder.com/600x400/e91e63/ffffff?text=La+Subienda'">
            </div>
        </div>

        <!-- Nuestra Historia -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center text-primary mb-4">Nuestra Historia</h2>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <p class="text-center">Todo comenzó en 2013 cuando María Elena, nuestra fundadora, decidió convertir su pasión por la repostería en un negocio familiar. Lo que inició como un pequeño emprendimiento desde casa, hoy se ha convertido en una de las pastelerías más queridas de la ciudad.</p>
                        
                        <p class="text-center">Nuestra filosofía siempre ha sido simple: crear productos de calidad excepcional que no solo satisfagan el paladar, sino que también generen sonrisas y momentos especiales en cada familia que confía en nosotros.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nuestros Valores -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center text-primary mb-5">Nuestros Valores</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-heart fa-2x"></i>
                            </div>
                            <h4>Calidad</h4>
                            <p>Utilizamos solo los mejores ingredientes y técnicas artesanales para garantizar productos excepcionales.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <h4>Familia</h4>
                            <p>Somos un negocio familiar que trata a cada cliente como parte de nuestra gran familia.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                            <h4>Excelencia</h4>
                            <p>Nos esforzamos por superar las expectativas en cada producto y servicio que ofrecemos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nuestro Equipo -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center text-primary mb-5">Nuestro Equipo</h2>
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-center">
                            <img src="assets/images/team-maria.jpg" class="card-img-top" alt="María Elena" 
                                 onerror="this.src='https://via.placeholder.com/300x300/e91e63/ffffff?text=María+Elena'">
                            <div class="card-body">
                                <h5 class="card-title">María Elena</h5>
                                <p class="card-text text-muted">Fundadora y Chef Pastelera</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-center">
                            <img src="assets/images/team-carlos.jpg" class="card-img-top" alt="Carlos" 
                                 onerror="this.src='https://via.placeholder.com/300x300/e91e63/ffffff?text=Carlos'">
                            <div class="card-body">
                                <h5 class="card-title">Carlos</h5>
                                <p class="card-text text-muted">Chef de Repostería</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-center">
                            <img src="assets/images/team-ana.jpg" class="card-img-top" alt="Ana" 
                                 onerror="this.src='https://via.placeholder.com/300x300/e91e63/ffffff?text=Ana'">
                            <div class="card-body">
                                <h5 class="card-title">Ana</h5>
                                <p class="card-text text-muted">Especialista en Decoración</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-center">
                            <img src="assets/images/team-luis.jpg" class="card-img-top" alt="Luis" 
                                 onerror="this.src='https://via.placeholder.com/300x300/e91e63/ffffff?text=Luis'">
                            <div class="card-body">
                                <h5 class="card-title">Luis</h5>
                                <p class="card-text text-muted">Atención al Cliente</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row">
            <div class="col-12">
                <div class="bg-primary text-white text-center p-5 rounded">
                    <h3>¿Listo para endulzar tu día?</h3>
                    <p class="mb-4">Descubre nuestra deliciosa variedad de pasteles, postres y dulces artesanales.</p>
                    <a href="productos.php" class="btn btn-light btn-lg">
                        <i class="fas fa-birthday-cake me-2"></i>Ver Productos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>