-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-06-2023 a las 01:29:02
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tp_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(20) NOT NULL,
  `id_comanda` int(20) NOT NULL,
  `id_producto` int(20) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `tiempo_estimado` int(11) DEFAULT NULL,
  `id_empleado` int(20) DEFAULT NULL,
  `orden_recibida` datetime NOT NULL,
  `orden_entregada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_comanda`, `id_producto`, `estado`, `tiempo_estimado`, `id_empleado`, `orden_recibida`, `orden_entregada`) VALUES
(1, 2, 9, 'entregado', 5, 15, '2023-06-22 00:00:00', '2023-06-24 00:19:56'),
(2, 2, 11, 'entregado', 30, 15, '2023-06-22 00:00:00', '2023-06-24 01:19:42'),
(3, 2, 11, 'entregado', 5, 15, '2023-06-22 00:00:00', '2023-06-24 01:20:40'),
(4, 2, 13, 'entregado', 5, 18, '2023-06-22 00:00:00', '2023-06-24 01:04:18'),
(5, 2, 15, 'entregado', 5, 20, '2023-06-22 00:00:00', '2023-06-24 21:15:23'),
(7, 1, 1, 'entregado', 5, 15, '2023-06-24 04:36:26', '2023-06-24 04:37:10'),
(8, 4, 9, 'listo para servir', 10, 23, '2023-06-24 20:19:40', '2023-06-24 20:20:57');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_comanda` (`id_comanda`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_producto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_comanda`) REFERENCES `comanda` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
