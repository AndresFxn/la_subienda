-- Crear base de datos
CREATE DATABASE IF NOT EXISTS pasteleria_db;
USE pasteleria_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    direccion TEXT NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),
    categoria_id INT,
    stock INT DEFAULT 0,
    destacado BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de carrito
CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    producto_id INT,
    cantidad INT DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'procesando', 'completado', 'cancelado') DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de detalles de pedidos
CREATE TABLE detalle_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre, apellido, cedula, direccion, telefono, email, password, rol) 
VALUES ('Admin', 'Sistema', '00000000', 'Dirección Admin', '0000000000', 'admin@lasubienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion) VALUES 
('Pasteles', 'Pasteles para toda ocasión'),
('Postres', 'Deliciosos postres individuales'),
('Cupcakes', 'Pequeños pasteles decorados'),
('Tortas', 'Tortas para celebraciones especiales');

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, imagen, categoria_id, stock, destacado) VALUES 
('Pastel de Chocolate', 'Delicioso pastel de chocolate con crema', 25.99, 'assets/images/pastel-chocolate.jpg', 1, 10, TRUE),
('Pastel de Vainilla', 'Suave pastel de vainilla con frosting', 22.99, 'assets/images/pastel-vainilla.jpg', 1, 8, TRUE),
('Pastel Red Velvet', 'Clásico pastel red velvet con cream cheese', 28.99, 'assets/images/pastel-red-velvet.jpg', 1, 6, TRUE),
('Tiramisu', 'Postre italiano con café y mascarpone', 8.99, 'assets/images/tiramisu.jpg', 2, 15, FALSE),
('Cheesecake de Fresa', 'Cremoso cheesecake con fresas frescas', 12.99, 'assets/images/cheesecake-fresa.jpg', 2, 12, TRUE),
('Cupcake de Chocolate', 'Cupcake individual de chocolate', 4.99, 'assets/images/cupcake-chocolate.jpg', 3, 20, FALSE),
('Cupcake de Vainilla', 'Cupcake individual de vainilla', 4.99, 'assets/images/cupcake-vainilla.jpg', 3, 18, FALSE),
('Torta de Cumpleaños', 'Torta personalizada para cumpleaños', 45.99, 'assets/images/torta-cumpleanos.jpg', 4, 5, TRUE),
('Flan de Caramelo', 'Tradicional flan casero', 6.99, 'assets/images/flan.jpg', 2, 10, FALSE),
('Tres Leches', 'Pastel tres leches tradicional', 18.99, 'assets/images/tres-leches.jpg', 1, 7, FALSE);