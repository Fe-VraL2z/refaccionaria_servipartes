-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-05-2025 a las 03:16:09
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
-- Base de datos: `refaccionaria_servipartes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `ID del cliente` varchar(4) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Telefono` varchar(14) NOT NULL,
  `Direccion` varchar(100) NOT NULL,
  `RFC` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`ID del cliente`, `Nombre`, `Telefono`, `Direccion`, `RFC`) VALUES
('0001', 'Juan perez', '4125538975', 'joajidf@grande.gro.mx', '<s.d.vs{dñv.s'),
('0003', 'Michelle Orduño Jiménez ', '7331259358', 'michelleorduñojim03@gmail.com', 'michezvd121242'),
('0008', 'Joaquín León ', '7331542437', 'joajidf@grande.gro.mx', 'jo51qu35l3leds'),
('0009', 'Julieta Venega', '733 145 85', 'julietavksdvgvgmdfbs,@gima.com', 'vss12rgeber34g'),
('0010', 'Melanie Martinez Garcia ', '7331468243', 'menanimartinez@gmail.com', 'me15gar45magc5'),
('0011', 'Melanie Martinez Garcia ', '7331468243', 'menanimartinez@gmail.com', 'me15gar45magc5'),
('0012', 'Julia Mireles Flores', '7331645275', 'juliamigiomer@outlook.es', 'ju13kd04sbgc56'),
('0018', 'Marco Antonio González Galicia ', '7331045243', 'marcxotaridochiquis04@gmail.com', 'MAC15SF62AFES6');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `Id Producto` int(11) NOT NULL,
  `Nombre del producto` varchar(50) NOT NULL,
  `Descripcion` varchar(50) NOT NULL,
  `Modelo` varchar(30) NOT NULL,
  `Precio` decimal(8,2) NOT NULL,
  `Cantidad de existencia` int(11) NOT NULL,
  `Codigo de pieza` varchar(50) NOT NULL,
  `Marca` varchar(30) NOT NULL,
  `Categoria` varchar(25) NOT NULL,
  `Unidad de medida` varchar(30) NOT NULL,
  `Dimensiones` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`Id Producto`, `Nombre del producto`, `Descripcion`, `Modelo`, `Precio`, `Cantidad de existencia`, `Codigo de pieza`, `Marca`, `Categoria`, `Unidad de medida`, `Dimensiones`) VALUES
(1, 'Radiador Automotirz', 'Radiador Chevrolet Luv ', 'CHEVROLET LUV', 7051.49, 10, '1997.2006', 'Chevrolet', 'Radiador de tubo', 'Cilindros (4)', '97-06 26mm'),
(2, 'Aceite de carro', 'Aceite para motores de vehículos de 5L', 'L - 65-800', 756.00, 3, 'nevaw124dxdf1254456', 'LTH', 'Aceites', 'Litros', '50 cm alto x 50 cm largo x 10 cm ancho'),
(3, 'Radiador Automotirz', 'Radiador Chevrolet Luv ', 'CHEVROLET LUV', 7051.49, 0, '1997.2006', 'Chevrolet', 'Radiador de tubo', 'Cilindros (4)', '97-06 26mm'),
(4, 'Bateria', 'Bateria para auto', 'L - 65-800', 8450.99, 53, 'erverberbart', 'LTH', 'Baterias ', '120 Amperio Hora  (Ah)', '19 cm/a, 30.5 cm/L 18.9 cm/h'),
(8, 'Faros con lupa', 'Estos faros utilizan lentes de lupa para enfocar l', 'italica w150', 539.99, 78, 'iw150frsdxv', 'ITALIKA', 'Iluminacion', 'H4 o B-35 ', '20x14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `ID Proveedor` int(4) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Telefono` varchar(10) NOT NULL,
  `Direccion` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `RFC` varchar(14) NOT NULL,
  `Contacto principal` varchar(10) NOT NULL,
  `Metodo Pago` varchar(30) NOT NULL,
  `Plazo Entrega` varchar(30) NOT NULL,
  `Forma Envio` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`ID Proveedor`, `Nombre`, `Telefono`, `Direccion`, `Email`, `RFC`, `Contacto principal`, `Metodo Pago`, `Plazo Entrega`, `Forma Envio`) VALUES
(1, 'Julieta Venega Sanchez ', '3774517561', 'Ciudad de mexico, Av. Rogel No: 042', 'jlipepsq@pips.com.mx', 'JU13747V39A84H', 'PepsiCo ', '', '6 dias', ''),
(2, 'Javier', '17263', 'Dire', 'BaeVAj@naver.com', 'aksjfdbq39i', 'PepsiCO', 'Tarjeta', '7 dias', 'Envío estándar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contraseña`) VALUES
(1, 'Minerva Martínez Neri ', '95fbfc39c9fdf005967b2aa0d52a3a30'),
(2, 'Francisco Eduardo', 'fb38dc30fe231e364bb482d578871798'),
(3, 'Javuer', '202cb962ac59075b964b07152d234b70'),
(4, 'Luis', '88eae2b2b2d7b72b8999391e78c260d7'),
(5, 'Minerva Martínez Neri', 'bf58b10e8208930b4b3ddefc5139a668');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_ventas` int(5) NOT NULL,
  `folio` varchar(12) NOT NULL,
  `cliente_id` varchar(4) NOT NULL,
  `direccion_negocio` varchar(100) NOT NULL,
  `numero_atencion_cliente` varchar(12) NOT NULL,
  `garantia` tinyint(4) NOT NULL,
  `tiempo_garantia_valor` int(11) NOT NULL,
  `tiempo_garantia_unidad` varchar(10) NOT NULL,
  `metodo_pago` varchar(25) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `fecha` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_ventas`, `folio`, `cliente_id`, `direccion_negocio`, `numero_atencion_cliente`, `garantia`, `tiempo_garantia_valor`, `tiempo_garantia_unidad`, `metodo_pago`, `nombre_usuario`, `fecha`, `total`) VALUES
(3, 'MINE20250530', '0001', '123', '123', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 20:01:00', 14102.98),
(5, '683a1744ed1e', '0001', 'as', '123', 1, 1, 'Meses', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 22:38:28', 28205.96),
(6, '683a1ba90d84', '0001', 'asd', '123', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 22:57:13', 8563.49),
(7, '683a1f193137', '0003', 'qwe', '123', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 23:11:53', 7051.49),
(8, '683a1f408f41', '0003', 'we', '123', 0, 0, '', 'Tarjeta', 'Minerva Martínez Neri', '2025-05-30 23:12:32', 7051.49),
(9, '683a20e61fc6', '0003', 'qwe', '123', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 23:19:34', 7051.49),
(10, '683a20fd9e7d', '0003', 'qew', '132', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 23:19:57', 7051.49),
(11, '683a22014ac3', '0003', 'qwe', '132', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 23:24:17', 7051.49),
(12, '683a231bca29', '0003', 'qwe', '123', 0, 0, '', 'Tarjeta', 'Minerva Martínez Neri', '2025-05-30 23:28:59', 7051.49),
(13, '683a34623d2f', '0001', 'eqw', 'qwe', 0, 0, '', 'Tarjeta', 'Minerva Martínez Neri', '2025-05-30 16:42:42', 7051.49),
(14, '683a37075213', '0001', 'qwe', '123', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 16:53:59', 756.00),
(15, '683a3739ef6a', '0003', 'qwe', '123', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 16:54:49', 1512.00),
(16, '683a38bada59', '0003', '1', '213', 0, 0, '', 'Efectivo', 'Minerva Martínez Neri', '2025-05-30 17:01:14', 84617.88);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_productos`
--

CREATE TABLE `ventas_productos` (
  `id_ventas` int(5) NOT NULL,
  `Id Producto` int(11) NOT NULL,
  `producto` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,0) NOT NULL,
  `subtotal` decimal(10,0) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas_productos`
--

INSERT INTO `ventas_productos` (`id_ventas`, `Id Producto`, `producto`, `cantidad`, `precio_unitario`, `subtotal`, `id`) VALUES
(6, 1, 'Radiador Automotirz', 1, 7051, 7051, 6),
(6, 2, 'Aceite de carro', 1, 756, 756, 7),
(6, 2, 'Aceite de carro', 1, 756, 756, 8),
(7, 1, 'Radiador Automotirz', 1, 7051, 7051, 9),
(8, 1, 'Radiador Automotirz', 1, 7051, 7051, 10),
(9, 1, 'Radiador Automotirz', 1, 7051, 7051, 11),
(10, 1, 'Radiador Automotirz', 1, 7051, 7051, 12),
(11, 1, 'Radiador Automotirz', 1, 7051, 7051, 13),
(12, 1, 'Radiador Automotirz', 1, 7051, 7051, 14),
(13, 1, 'Radiador Automotirz', 1, 7051, 7051, 15),
(14, 2, 'Aceite de carro', 1, 756, 756, 16),
(15, 2, 'Aceite de carro', 1, 756, 756, 17),
(15, 2, 'Aceite de carro', 1, 756, 756, 18),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 19),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 20),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 21),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 22),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 23),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 24),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 25),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 26),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 27),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 28),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 29),
(16, 3, 'Radiador Automotirz', 1, 7051, 7051, 30),
(5, 1, 'Radiador Automotirz', 2, 7051, 14103, 31),
(5, 1, 'Radiador Automotirz', 1, 7051, 7051, 32),
(5, 1, 'Radiador Automotirz', 1, 7051, 7051, 33),
(3, 1, 'Radiador Automotirz', 2, 7051, 14103, 36);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`ID del cliente`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`Id Producto`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`ID Proveedor`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_ventas`),
  ADD UNIQUE KEY `UNIQUE` (`folio`),
  ADD KEY `INDEX` (`cliente_id`);

--
-- Indices de la tabla `ventas_productos`
--
ALTER TABLE `ventas_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `INDEX_VENTAS` (`id_ventas`),
  ADD KEY `INDEX_Id Producto` (`Id Producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `Id Producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_ventas` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `ventas_productos`
--
ALTER TABLE `ventas_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ventas_productos`
--
ALTER TABLE `ventas_productos`
  ADD CONSTRAINT `ventas_productos_ibfk_1` FOREIGN KEY (`id_ventas`) REFERENCES `ventas` (`id_ventas`),
  ADD CONSTRAINT `ventas_productos_ibfk_2` FOREIGN KEY (`Id Producto`) REFERENCES `producto` (`Id Producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
