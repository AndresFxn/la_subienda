<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar si es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$mensaje = '';
$error = '';

// Procesar acciones
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'cambiar_rol':
            $id = (int)$_POST['id'];
            $nuevo_rol = $_POST['rol'];
            
            if (in_array($nuevo_rol, ['usuario', 'admin'])) {
                $stmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
                if ($stmt->execute([$nuevo_rol, $id])) {
                    $mensaje = 'Rol actualizado exitosamente';
                } else {
                    $error = 'Error al actualizar el rol';
                }
            }
            break;
            
        case 'eliminar':
            $id = (int)$_POST['id'];
            
            // No permitir eliminar el propio usuario
            if ($id != $_SESSION['usuario_id']) {
                $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $mensaje = 'Usuario eliminado exitosamente';
                } else {
                    $error = 'Error al eliminar el usuario';
                }
            } else {
                $error = 'No puedes eliminar tu propia cuenta';
            }
            break;
    }
}

// Obtener usuarios con estadísticas
$stmt = $conn->prepare("
    SELECT u.*, 
           COUNT(p.id) as total_pedidos,
           COALESCE(SUM(p.total), 0) as total_gastado
    FROM usuarios u 
    LEFT JOIN pedidos p ON u.id = p.usuario_id 
    GROUP BY u.id 
    ORDER BY u.fecha_registro DESC
");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-birthday-cake me-2"></i>La Subienda
                    </h4>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="productos.php">
                            <i class="fas fa-birthday-cake me-2"></i>Productos
                        </a>
                        <a class="nav-link" href="pedidos.php">
                            <i class="fas fa-shopping-cart me-2"></i>Pedidos
                        </a>
                        <a class="nav-link active" href="usuarios.php">
                            <i class="fas fa-users me-2"></i>Usuarios
                        </a>
                        <a class="nav-link" href="categorias.php">
                            <i class="fas fa-tags me-2"></i>Categorías
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-2"></i>Ir al Sitio
                        </a>
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h1 class="text-primary mb-4">Gestión de Usuarios</h1>
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo count($usuarios); ?></h3>
                                    <p class="text-muted">Total Usuarios</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-user-shield fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo count(array_filter($usuarios, function($u) { return $u['rol'] == 'admin'; })); ?></h3>
                                    <p class="text-muted">Administradores</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-user fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo count(array_filter($usuarios, function($u) { return $u['rol'] == 'usuario'; })); ?></h3>
                                    <p class="text-muted">Clientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-calendar fa-2x text-primary mb-2"></i>
                                    <h3 class="text-primary"><?php echo count(array_filter($usuarios, function($u) { return strtotime($u['fecha_registro']) > strtotime('-30 days'); })); ?></h3>
                                    <p class="text-muted">Nuevos (30 días)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Usuarios -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Rol</th>
                                            <th>Pedidos</th>
                                            <th>Total Gastado</th>
                                            <th>Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo $usuario['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></strong>
                                                <br><small class="text-muted">Cédula: <?php echo htmlspecialchars($usuario['cedula']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                            <td>
                                                <?php if ($usuario['rol'] == 'admin'): ?>
                                                    <span class="badge bg-danger">Administrador</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Cliente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $usuario['total_pedidos']; ?></span>
                                            </td>
                                            <td>$<?php echo number_format($usuario['total_gastado'], 2); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                            data-bs-toggle="dropdown">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <button class="dropdown-item" 
                                                                    onclick="cambiarRol(<?php echo $usuario['id']; ?>, '<?php echo $usuario['rol']; ?>', '<?php echo htmlspecialchars($usuario['nombre']); ?>')">
                                                                <i class="fas fa-user-cog me-2"></i>Cambiar Rol
                                                            </button>
                                                        </li>
                                                        <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                                                        <li>
                                                            <button class="dropdown-item text-danger" 
                                                                    onclick="eliminarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>')">
                                                                <i class="fas fa-trash me-2"></i>Eliminar
                                                            </button>
                                                        </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cambiar Rol -->
    <div class="modal fade" id="modalCambiarRol" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Rol de Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="cambiar_rol">
                        <input type="hidden" name="id" id="cambiar_rol_id">
                        
                        <p>Cambiar rol del usuario: <strong id="cambiar_rol_nombre"></strong></p>
                        
                        <div class="mb-3">
                            <label for="rol" class="form-label">Nuevo Rol</label>
                            <select class="form-select" name="rol" id="cambiar_rol_select" required>
                                <option value="usuario">Cliente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cambiar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" id="eliminar_id">
                        
                        <p>¿Estás seguro de que deseas eliminar al usuario <strong id="eliminar_nombre"></strong>?</p>
                        <p class="text-danger"><small>Esta acción eliminará también todos sus pedidos y no se puede deshacer.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cambiarRol(id, rolActual, nombre) {
            document.getElementById('cambiar_rol_id').value = id;
            document.getElementById('cambiar_rol_nombre').textContent = nombre;
            document.getElementById('cambiar_rol_select').value = rolActual;
            
            new bootstrap.Modal(document.getElementById('modalCambiarRol')).show();
        }
        
        function eliminarUsuario(id, nombre) {
            document.getElementById('eliminar_id').value = id;
            document.getElementById('eliminar_nombre').textContent = nombre;
            
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        }
    </script>
</body>
</html>