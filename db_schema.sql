-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 24-07-2025 a las 18:55:50
-- Versión del servidor: 8.0.42
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumo`
--

CREATE TABLE `insumo` (
  `id` int NOT NULL,
  `nombre_Insumo` varchar(250) NOT NULL,
  `cantidadComprada` int NOT NULL,
  `unidadMedida` varchar(20) NOT NULL,
  `total_Compra` int NOT NULL,
  `existencia` int NOT NULL,
  `fecha_compra` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `insumo`
--

INSERT INTO `insumo` (`id`, `nombre_Insumo`, `cantidadComprada`, `unidadMedida`, `total_Compra`, `existencia`, `fecha_compra`) VALUES
(1, 'Harina', 10, 'Kilos', 200, 8, '2020-08-14'),
(2, 'Huevos', 30, 'Piezas', 60, 23, '2020-08-14'),
(4, 'Harina', 40, 'kilos', 200, 40, '2025-07-14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint UNSIGNED NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `precioVenta` decimal(5,2) NOT NULL,
  `existencia` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `descripcion`, `precioVenta`, `existencia`) VALUES
(1, '01', 'Concha de chocolate', 2.00, 19),
(2, '02', 'Dona rellena de fresa', 5.00, 30),
(6, '03', 'Pan danés', 5.00, 50),
(7, '456', 'Torta de Chocolate', 27.00, 7),
(8, '3', 'Pay', 3.00, 20),
(10, '9', 'Cuernito', 1.00, 62),
(11, '04', 'Pan de muerto', 2.00, 44),
(12, '06', 'Rosca de reyes', 3.00, 20),
(13, '08', 'Baguette', 3.00, 35),
(14, '07', 'Galleta de mantequilla', 2.00, 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_vendidos`
--

CREATE TABLE `productos_vendidos` (
  `id` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `cantidad` bigint UNSIGNED NOT NULL,
  `id_venta` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `productos_vendidos`
--

INSERT INTO `productos_vendidos` (`id`, `id_producto`, `cantidad`, `id_venta`) VALUES
(10, 1, 1, 6),
(11, 2, 1, 6),
(12, 1, 1, 7),
(13, 1, 1, 8),
(14, 2, 1, 9),
(15, 6, 1, 9),
(16, 1, 1, 10),
(17, 1, 1, 13),
(18, 2, 1, 13),
(19, 6, 1, 13),
(20, 6, 2, 15),
(21, 2, 1, 15),
(22, 6, 2, 16),
(23, 2, 1, 16),
(24, 1, 2, 16),
(25, 1, 1, 17),
(26, 7, 4, 18),
(27, 1, 1, 19),
(28, 1, 1, 20),
(29, 1, 1, 21),
(30, 6, 1, 21),
(31, 8, 1, 22),
(32, 1, 1, 22),
(33, 6, 1, 22),
(34, 1, 1, 23),
(35, 6, 3, 23),
(36, 1, 1, 24),
(37, 1, 1, 25),
(38, 7, 1, 25),
(39, 1, 1, 26),
(40, 6, 1, 26),
(41, 1, 1, 27),
(42, 6, 4, 27),
(43, 2, 7, 27),
(44, 7, 1, 28),
(45, 6, 20, 28),
(46, 8, 10, 29),
(47, 8, 16, 30),
(48, 8, 1, 31),
(49, 1, 5, 32),
(50, 1, 1, 33),
(51, 6, 1, 34),
(52, 1, 1, 35),
(53, 2, 1, 36),
(55, 6, 1, 38),
(56, 7, 2, 38),
(57, 6, 1, 39),
(58, 7, 1, 40),
(59, 1, 1, 41),
(60, 11, 1, 42),
(61, 1, 1, 43);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(50) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'empleado',
  `email` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `usuario`, `contraseña`, `rol`, `email`, `fecha_creacion`, `activo`) VALUES
(1, 'Miguel', 'administrador', 'admin1234', 'administrador', NULL, '2025-07-13 20:54:08', 1),
(2, 'Mario', 'administrador2', 'admin12345', 'empleado', '', '2025-07-13 20:54:08', 1),
(3, 'renzo', 'renzo123', '12345', 'empleado', '', '2025-07-17 17:06:09', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL,
  `total` decimal(7,2) DEFAULT NULL,
  `hora_venta` tinyint GENERATED ALWAYS AS (hour(`fecha`)) STORED,
  `dia_semana` varchar(10) GENERATED ALWAYS AS (dayname(`fecha`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `total`) VALUES
(5, '2025-07-12 20:44:06', 0.00),
(6, '2025-07-13 16:05:42', 7.00),
(7, '2025-07-13 16:12:35', 2.00),
(8, '2025-07-13 16:18:50', 2.00),
(9, '2025-07-13 17:19:37', 10.00),
(10, '2025-07-13 17:51:52', 2.00),
(11, '2025-07-13 17:55:29', 0.00),
(12, '2025-07-13 17:58:50', 0.00),
(13, '2025-07-13 18:09:41', 12.00),
(14, '2025-07-14 01:39:00', 0.00),
(15, '2025-07-14 01:52:36', 15.00),
(16, '2025-07-14 02:03:40', 19.00),
(17, '2025-07-14 05:17:03', 2.00),
(18, '2025-07-14 06:40:50', 108.00),
(19, '2025-07-14 06:59:06', 2.00),
(20, '2025-07-14 07:02:24', 2.00),
(21, '2025-07-14 07:06:40', 7.00),
(22, '2025-07-14 08:13:41', 10.00),
(23, '2025-07-14 08:37:00', 17.00),
(24, '2025-07-14 17:46:10', 2.00),
(25, '2025-07-14 17:57:01', 29.00),
(26, '2025-07-17 11:15:55', 7.00),
(27, '2025-07-17 12:32:28', 57.00),
(28, '2025-07-17 16:57:42', 127.00),
(29, '2025-07-17 17:00:53', 30.00),
(30, '2025-07-17 17:02:09', 48.00),
(31, '2025-07-17 17:02:35', 3.00),
(32, '2025-07-17 18:16:14', 10.00),
(33, '2025-07-17 18:16:58', 2.00),
(34, '2025-07-19 19:30:06', 5.00),
(35, '2025-07-19 22:32:32', 2.00),
(36, '2025-07-20 00:28:39', 5.00),
(37, '2025-07-20 02:51:22', 6.00),
(38, '2025-07-20 03:07:37', 59.00),
(39, '2025-07-20 03:08:12', 5.00),
(40, '2025-07-20 03:10:21', 27.00),
(41, '2025-07-21 07:26:43', 2.00),
(42, '2025-07-21 17:52:23', 2.00),
(43, '2025-07-24 17:54:39', 2.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `insumo`
--
ALTER TABLE `insumo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos_vendidos`
--
ALTER TABLE `productos_vendidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_hora_venta` (`hora_venta`),
  ADD KEY `idx_dia_semana` (`dia_semana`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `insumo`
--
ALTER TABLE `insumo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `productos_vendidos`
--
ALTER TABLE `productos_vendidos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

ALTER TABLE `productos_vendidos`
  ADD CONSTRAINT `productos_vendidos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_vendidos_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
