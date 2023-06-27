-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-06-2023 a las 01:28:53
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
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(80) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sector` varchar(20) NOT NULL,
  `estado` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `email`, `password`, `nombre`, `fecha_nacimiento`, `sector`, `estado`) VALUES
(15, 'pepe@gmail.com', '$2y$10$S2CteyFD0olKccsQq/EMeuYroYTTN3drlWCsy/Ke67f5aM68ynMzi', 'pepe', '1990-02-20', 'cocina', b'1'),
(16, 'admin@gmail.com', '$2y$10$P8Ok4KmuM1H8iypnREkJyeZ/q2OxEUV0Q8bDaSCEzRKEqZ7oVAolu', 'admin', '1990-02-20', 'admin', b'1'),
(18, 'cervezeria@gmail.com', '$2y$10$Y7v/woYjBtARbCvDTaNepuTVaO2LPc8zbZdRY1oHSJVnATBp/1bVq', 'cervezeria', '2000-02-10', 'cervezeria', b'1'),
(19, 'mesero@gmail.com', '$2y$10$T74nJDX1xyUv6zoKakV1mu2vu5dsUrr9AAj4ZaKMoOzvipeMa1vLq', 'mesero', '1990-02-02', 'mesero', b'1'),
(20, 'barra@gmail.com', '$2y$10$1otv/wplt9qlqclN0L.dC.fgRT/ZdqwmnrGGTaGOEhRaF1C9zUFea', 'barra', '1950-01-01', 'barra', b'1'),
(21, 'postres@gmail.com', '$2y$10$bhaL3nY.M0hX1JudZQsU1eSLTmS3uLXGaaH/W899glW3aLLi4uFOG', 'postres', '2010-01-01', 'postres', b'1'),
(23, 'cocinero@gmail.com', '$2y$10$6WZD9GXwI/L7WdFhddPD5u7pe9z..UQBErT8p.T85tgCCRw16bN1.', 'cocinero', '2000-01-01', 'cocina', b'1');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
