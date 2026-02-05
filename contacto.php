<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$mensaje_enviado = false;
$error = '';

if ($_POST) {
    $nombre = limpiarInput($_POST['nombre']);
    $email = limpiarInput($_POST['email']);
    $telefono = limpiarInput($_POST['telefono']);
    $asunto = limpiarInput($_POST['asunto']);
    $mensaje = limpiarInput($_POST['mensaje']);
    
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        $error = 'Todos los campos marcados con * son obligatorios';
    } elseif (!validarEmail($email)) {
        $error = 'El email no tiene un formato válido';
    } else {
        $mensaje_enviado = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center text-primary mb-5">Contáctanos</h1>
                <p class="text-center lead mb-5">¿Tienes alguna pregunta o quieres hacer un pedido especial? ¡Estamos aquí para ayudarte!</p>
            </div>
        </div>

        <div class="row">
            <!-- Información de Contacto -->
            <div class="col-lg-4 mb-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="text-primary mb-4">Información de Contacto</h4>
                        
                        <div class="mb-4">
                            <h6><i class="fas fa-map-marker-alt text-primary me-2"></i>Dirección</h6>
                            <p class="text-muted">Calle 123 #45-67<br>Bogotá, Colombia</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6><i class="fas fa-phone text-primary me-2"></i>Teléfono</h6>
                            <p class="text-muted">+57 (1) 234-5678<br>+57 300 123 4567</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6><i class="fas fa-envelope text-primary me-2"></i>Email</h6>
                            <p class="text-muted">info@lasubienda.com<br>pedidos@lasubienda.com</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6><i class="fas fa-clock text-primary me-2"></i>Horarios de Atención</h6>
                            <p class="text-muted">
                                <strong>Lunes a Viernes:</strong> 8:00 AM - 7:00 PM<br>
                                <strong>Sábados:</strong> 9:00 AM - 6:00 PM<br>
                                <strong>Domingos:</strong> 10:00 AM - 4:00 PM
                            </p>
                        </div>
                        
                        <div>
                            <h6 class="text-primary mb-3">Síguenos</h6>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-primary fs-4"><i class="fab fa-facebook"></i></a>
                                <a href="#" class="text-primary fs-4"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="text-primary fs-4"><i class="fab fa-whatsapp"></i></a>
                                <a href="#" class="text-primary fs-4"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulario de Contacto -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-primary mb-4">Envíanos un Mensaje</h4>
                        
                        <?php if ($mensaje_enviado): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                ¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos pronto.
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="asunto" class="form-label">Asunto *</label>
                                    <select class="form-select" id="asunto" name="asunto" required>
                                        <option value="">Selecciona un asunto</option>
                                        <option value="pedido_especial" <?php echo (isset($_POST['asunto']) && $_POST['asunto'] == 'pedido_especial') ? 'selected' : ''; ?>>Pedido Especial</option>
                                        <option value="consulta_producto" <?php echo (isset($_POST['asunto']) && $_POST['asunto'] == 'consulta_producto') ? 'selected' : ''; ?>>Consulta sobre Productos</option>
                                        <option value="cotizacion" <?php echo (isset($_POST['asunto']) && $_POST['asunto'] == 'cotizacion') ? 'selected' : ''; ?>>Solicitar Cotización</option>
                                        <option value="reclamo" <?php echo (isset($_POST['asunto']) && $_POST['asunto'] == 'reclamo') ? 'selected' : ''; ?>>Reclamo</option>
                                        <option value="sugerencia" <?php echo (isset($_POST['asunto']) && $_POST['asunto'] == 'sugerencia') ? 'selected' : ''; ?>>Sugerencia</option>
                                        <option value="otro" <?php echo (isset($_POST['asunto']) && $_POST['asunto'] == 'otro') ? 'selected' : ''; ?>>Otro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="mensaje" class="form-label">Mensaje *</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="5" 
                                          placeholder="Cuéntanos en qué podemos ayudarte..." required><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mapa (opcional) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-primary mb-4">Nuestra Ubicación</h4>
                        <div class="ratio ratio-21x9">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.9654458885!2d-74.08209368573!3d4.624335596766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9a3e6b6b6b6b%3A0x6b6b6b6b6b6b6b6b!2sBogot%C3%A1%2C%20Colombia!5e0!3m2!1ses!2sco!4v1234567890123!5m2!1ses!2sco" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        <p class="text-muted mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Estamos ubicados en el corazón de Bogotá, con fácil acceso en transporte público y parqueadero disponible.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>