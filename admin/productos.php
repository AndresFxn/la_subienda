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
                $precio = floatval($_POST['precio']);
                $categoria_id = intval($_POST['categoria_id']);
                $imagen = limpiarInput($_POST['imagen']);
                $destacado = isset($_POST['destacado']) ? 1 : 0;
                
                if ($nombre && $precio > 0 && $categoria_id) {
                    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria_id, imagen, destacado) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$nombre, $descripcion, $precio, $categoria_id, $imagen, $destacado])) {
                        $mensaje = 'Producto creado exitosamente';
                    } else {
                        $error = 'Error al crear producto';
                    }
                } else {
                    $error = 'Todos los campos son obligatorios';
                }
                break;
                
            case 'editar':
                $id = intval($_POST['id']);
                $nombre = limpiarInput($_POST['nombre']);
                $descripcion = limpiarInput($_POST['descripcion']);
                $precio = floatval($_POST['precio']);
                $categoria_id = intval($_POST['categoria_id']);
                $imagen = limpiarInput($_POST['imagen']);
                $destacado = isset($_POST['destacado']) ? 1 : 0;
                
                if ($id && $nombre && $precio > 0 && $categoria_id) {
                    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria_id = ?, imagen = ?, destacado = ? WHERE id = ?");
                    if ($stmt->execute([$nombre, $descripcion, $precio, $categoria_id, $imagen, $destacado, $id])) {
                        $mensaje = 'Producto actualizado exitosamente';
                    } else {
                        $error = 'Error al actualizar producto';
                    }
                } else {
                    $error = 'Todos los campos son obligatorios';
                }
                break;
                
            case 'eliminar':
                $id = intval($_POST['id']);
                if ($id) {
                    $stmt = $conn->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $mensaje = 'Producto eliminado exitosamente';
                    } else {
                        $error = 'Error al eliminar producto';
                    }
                }
                break;
        }
    }
}

// Obtener productos y categorías
$productos = obtenerTodosLosProductos($conn);
$categorias = obtenerCategorias($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Admin</title>
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
                        <a class="nav-link active" href="productos.php">
                            <i class="fas fa-birthday-cake me-2"></i>Productos
                        </a>
                        <a class="nav-link" href="pedidos.php">
                            <i class="fas fa-shopping-cart me-2"></i>Pedidos
                        </a>
                        <a class="nav-link" href="usuarios.php">
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="text-primary">Gestión de Productos</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                            <i class="fas fa-plus me-2"></i>Nuevo Producto
                        </button>
                    </div>
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Tabla de productos -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Precio</th>
                                            <th>Destacado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($productos as $producto): ?>
                                        <tr>
                                            <td><?php echo $producto['id']; ?></td>
                                            <td>
                                                <img src="../<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                            </td>
                                            <td><?php echo $producto['nombre']; ?></td>
                                            <td><?php echo $producto['categoria_nombre']; ?></td>
                                            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                            <td>
                                                <?php if($producto['destacado']): ?>
                                                    <span class="badge bg-success">Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editarProducto(<?php echo htmlspecialchars(json_encode($producto)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(<?php echo $producto['id']; ?>, '<?php echo $producto['nombre']; ?>')">
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

    <!-- Modal Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProductoTitle">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formProducto">
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="crear">
                        <input type="hidden" name="id" id="producto_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoría</label>
                                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach($categorias as $categoria): ?>
                                            <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="imagen" class="form-label">URL de Imagen</label>
                                    <input type="text" class="form-control" id="imagen" name="imagen" placeholder="assets/images/producto.jpg">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="destacado" name="destacado">
                                <label class="form-check-label" for="destacado">
                                    Producto destacado
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarProducto(producto) {
            document.getElementById('modalProductoTitle').textContent = 'Editar Producto';
            document.getElementById('accion').value = 'editar';
            document.getElementById('producto_id').value = producto.id;
            document.getElementById('nombre').value = producto.nombre;
            document.getElementById('descripcion').value = producto.descripcion;
            document.getElementById('precio').value = producto.precio;
            document.getElementById('categoria_id').value = producto.categoria_id;
            document.getElementById('imagen').value = producto.imagen;
            document.getElementById('destacado').checked = producto.destacado == 1;
            
            new bootstrap.Modal(document.getElementById('modalProducto')).show();
        }
        
        function eliminarProducto(id, nombre) {
            if (confirm('¿Estás seguro de que quieres eliminar el producto "' + nombre + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Limpiar modal al cerrarlo
        document.getElementById('modalProducto').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalProductoTitle').textContent = 'Nuevo Producto';
            document.getElementById('formProducto').reset();
            document.getElementById('accion').value = 'crear';
            document.getElementById('producto_id').value = '';
        });
    </script>
</body>
</html>