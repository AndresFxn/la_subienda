-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-02-2026 a las 19:42:01
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pasteleria_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Pasteles', 'Pasteles para toda ocasión'),
(2, 'Postres', 'Deliciosos postres individuales'),
(3, 'Cupcakes', 'Pequeños pasteles decorados'),
(4, 'Tortas', 'Tortas para celebraciones especiales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 5, 25.99),
(2, 1, 5, 15, 12.99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','procesando','completado','cancelado') DEFAULT 'pendiente',
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `total`, `estado`, `fecha_pedido`) VALUES
(1, 1, 324.80, 'pendiente', '2026-02-04 22:06:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `destacado` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `categoria_id`, `stock`, `destacado`, `activo`, `fecha_creacion`) VALUES
(1, 'Pastel de Chocolate', 'Delicioso pastel de chocolate con crema', 25.99, 'assets/images/pastel-chocolate.jpg', 1, 10, 1, 1, '2026-02-04 19:18:09'),
(2, 'Pastel de Vainilla', 'Suave pastel de vainilla con frosting', 22.99, 'assets/images/pastel-vainilla.jpg', 1, 8, 1, 1, '2026-02-04 19:18:09'),
(3, 'Pastel de Limon', 'Clásico pastel de limón', 28.99, 'assets/images/pastel-limon.jpg', 1, 6, 1, 1, '2026-02-04 19:18:09'),
(4, 'Tiramisu', 'Postre italiano con café y mascarpone', 8.99, 'assets/images/tiramisu.jpg', 2, 15, 0, 1, '2026-02-04 19:18:09'),
(5, 'Cheesecake de Fresa', 'Cremoso cheesecake con fresas frescas', 12.99, 'assets/images/cheescake-fresa.jpg', 2, 12, 1, 1, '2026-02-04 19:18:09'),
(6, 'Cupcake de Chocolate', 'Cupcake individual de chocolate', 4.99, 'assets/images/cupcake-chocolate.jpg', 3, 20, 0, 1, '2026-02-04 19:18:09'),
(7, 'Cupcake de Vainilla', 'Cupcake individual de vainilla', 4.99, 'assets/images/cupcake-vainilla.jpg', 3, 18, 0, 1, '2026-02-04 19:18:09'),
(8, 'Pastel de Cumpleaños', 'Pastel personalizado para cumpleaños', 45.99, 'assets/images/pastel-cumpleanos.jpg', 4, 5, 1, 1, '2026-02-04 19:18:09'),
(9, 'Flan de Caramelo', 'Tradicional flan casero', 6.99, 'assets/images/flan-caramelo.jpg', 2, 10, 0, 1, '2026-02-04 19:18:09'),
(10, 'Tres Leches', 'Pastel tres leches tradicional', 18.99, 'assets/images/pastel-leches.jpg', 1, 7, 0, 1, '2026-02-04 19:18:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `direccion` text NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('usuario','admin') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `cedula`, `direccion`, `telefono`, `email`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'Admin', 'Sistema', '00000000', 'Dirección Admin', '0000000000', 'admin@lasubienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-02-04 19:18:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
