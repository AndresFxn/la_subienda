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

// Procesar formulario
if ($_POST) {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear':
                $nombre = limpiarInput($_POST['nombre']);
                $descripcion = limpiarInput($_POST['descripcion']);
                
                if (!empty($nombre)) {
                    $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
                    if ($stmt->execute([$nombre, $descripcion])) {
                        $mensaje = 'Categoría creada exitosamente';
                    } else {
                        $error = 'Error al crear la categoría';
                    }
                } else {
                    $error = 'El nombre es obligatorio';
                }
                break;
                
            case 'editar':
                $id = (int)$_POST['id'];
                $nombre = limpiarInput($_POST['nombre']);
                $descripcion = limpiarInput($_POST['descripcion']);
                
                if (!empty($nombre)) {
                    $stmt = $conn->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?");
                    if ($stmt->execute([$nombre, $descripcion, $id])) {
                        $mensaje = 'Categoría actualizada exitosamente';
                    } else {
                        $error = 'Error al actualizar la categoría';
                    }
                } else {
                    $error = 'El nombre es obligatorio';
                }
                break;
                
            case 'eliminar':
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $mensaje = 'Categoría eliminada exitosamente';
                } else {
                    $error = 'Error al eliminar la categoría';
                }
                break;
        }
    }
}

// Obtener categorías
$stmt = $conn->prepare("SELECT * FROM categorias ORDER BY nombre");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Panel Administrativo</title>
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
                        <a class="nav-link" href="usuarios.php">
                            <i class="fas fa-users me-2"></i>Usuarios
                        </a>
                        <a class="nav-link active" href="categorias.php">
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="text-primary">Gestión de Categorías</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                            <i class="fas fa-plus me-2"></i>Nueva Categoría
                        </button>
                    </div>
                    
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
                    
                    <!-- Tabla de Categorías -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Productos</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categorias as $categoria): ?>
                                        <?php
                                        // Contar productos por categoría
                                        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM productos WHERE categoria_id = ?");
                                        $stmt->execute([$categoria['id']]);
                                        $total_productos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                        ?>
                                        <tr>
                                            <td><?php echo $categoria['id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($categoria['nombre']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($categoria['descripcion']); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $total_productos; ?> productos</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" 
                                                        onclick="editarCategoria(<?php echo $categoria['id']; ?>, '<?php echo htmlspecialchars($categoria['nombre']); ?>', '<?php echo htmlspecialchars($categoria['descripcion']); ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="eliminarCategoria(<?php echo $categoria['id']; ?>, '<?php echo htmlspecialchars($categoria['nombre']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

    <!-- Modal Categoría -->
    <div class="modal fade" id="modalCategoria" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formCategoria">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitulo">Nueva Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="crear">
                        <input type="hidden" name="id" id="categoria_id">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
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
                        <h5 class="modal-title">Eliminar Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" id="eliminar_id">
                        <p>¿Estás seguro de que deseas eliminar la categoría <strong id="eliminar_nombre"></strong>?</p>
                        <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
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
        function editarCategoria(id, nombre, descripcion) {
            document.getElementById('modalTitulo').textContent = 'Editar Categoría';
            document.getElementById('accion').value = 'editar';
            document.getElementById('categoria_id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('descripcion').value = descripcion;
            document.getElementById('btnGuardar').textContent = 'Actualizar';
            
            new bootstrap.Modal(document.getElementById('modalCategoria')).show();
        }
        
        function eliminarCategoria(id, nombre) {
            document.getElementById('eliminar_id').value = id;
            document.getElementById('eliminar_nombre').textContent = nombre;
            
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        }
        
        // Limpiar modal al cerrarlo
        document.getElementById('modalCategoria').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formCategoria').reset();
            document.getElementById('modalTitulo').textContent = 'Nueva Categoría';
            document.getElementById('accion').value = 'crear';
            document.getElementById('btnGuardar').textContent = 'Guardar';
        });
    </script>
</body>
</html>