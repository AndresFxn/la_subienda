<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_POST) {
    $datos = [
        'nombre' => limpiarInput($_POST['nombre']),
        'apellido' => limpiarInput($_POST['apellido']),
        'cedula' => limpiarInput($_POST['cedula']),
        'direccion' => limpiarInput($_POST['direccion']),
        'telefono' => limpiarInput($_POST['telefono']),
        'email' => limpiarInput($_POST['email']),
        'password' => $_POST['password']
    ];
    
    $confirmar_password = $_POST['confirmar_password'];
    
    if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['cedula']) || 
        empty($datos['direccion']) || empty($datos['telefono']) || empty($datos['email']) || 
        empty($datos['password'])) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!validarEmail($datos['email'])) {
        $error = 'El email no es válido';
    } elseif (!validarCedula($datos['cedula'])) {
        $error = 'La cédula debe tener entre 8 y 12 dígitos';
    } elseif (!validarTelefono($datos['telefono'])) {
        $error = 'El teléfono no es válido';
    } elseif (strlen($datos['password']) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($datos['password'] !== $confirmar_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR cedula = ?");
        $stmt->execute([$datos['email'], $datos['cedula']]);
        
        if ($stmt->fetch()) {
            $error = 'El email o cédula ya están registrados';
        } else {
            if (registrarUsuario($conn, $datos)) {
                $success = 'Usuario registrado exitosamente. Ahora puedes iniciar sesión.';
            } else {
                $error = 'Error al registrar usuario. Intenta nuevamente.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - La Subienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <h2 class="text-center text-primary mb-4">
                        <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                    </h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <div class="mt-2">
                                <a href="login.php" class="btn btn-success btn-sm">Iniciar Sesión</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="registro-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                               value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="apellido" name="apellido" 
                                               value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cedula" class="form-label">Cédula</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" id="cedula" name="cedula" 
                                               value="<?php echo isset($_POST['cedula']) ? htmlspecialchars($_POST['cedula']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control" id="telefono" name="telefono" 
                                               value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2" required><?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="form-text">Mínimo 6 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p>¿Ya tienes cuenta? <a href="login.php" class="text-primary">Inicia sesión aquí</a></p>
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