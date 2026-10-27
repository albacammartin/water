-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:8889
-- Tiempo de generación: 27-10-2026 a las 16:14:19
-- Versión del servidor: 8.0.44
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `water_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `idCita` int NOT NULL,
  `idUser` int NOT NULL,
  `fechaCita` date NOT NULL,
  `motivoCita` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `idNoticia` int NOT NULL,
  `idUser` int NOT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `texto` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`idNoticia`, `idUser`, `titulo`, `imagen`, `texto`, `fecha`) VALUES
(1, 7, 'Abrimos las citas de diagnóstico de piel', 'img/noticias/citas-diagnostico.jpg', 'Desde este mes puedes reservar una cita gratuita con nuestras especialistas. En unos 30 minutos analizamos tu tipo de piel, su nivel de hidratación y tus hábitos para recomendarte una rutina a tu medida.\r\n\r\nSolo tienes que registrarte en la web y entrar en la sección Citaciones para elegir el día que mejor te venga.', '2026-09-28'),
(2, 7, 'Así es nuestra rutina facial en 4 pasos', 'img/noticias/rutina-facial.jpg', 'Limpiar, tratar, hidratar y refrescar. Con el Aqua Cleanser eliminas impurezas sin resecar, el Hydra Serum aporta ácido hialurónico, la Aqua Gel Cream sella la hidratación y el Hydra Mist te acompaña durante el día.\n\nUna rutina ligera pensada para pieles que no quieren sentir peso ni brillos.', '2026-09-14'),
(3, 7, 'Llega la nueva línea de cuidado capilar', 'img/noticias/linea-capilar.jpg', 'Hydra Shampoo, Silk Conditioner, Leave-in Milk, Wave Mist, Scalp Serum y Repair Hair Mask: seis fórmulas inspiradas en el agua para un cabello hidratado, suave y con movimiento.\n\nTodas comparten ingredientes como el aloe vera, la biotina y el agua de coco.', '2026-08-30'),
(4, 7, 'Maquillaje ligero: base, máscara y polvos', 'img/noticias/maquillaje-ligero.jpg', 'Nuestra Weightless Foundation con SPF 20 unifica el tono sin cubrir la piel, la Lash Flow Mascara define sin apelmazar y los Translucent Setting Powder fijan el maquillaje con acabado natural.\n\nTres básicos para un look fresco de la mañana a la noche.', '2026-08-12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_data`
--

CREATE TABLE `users_data` (
  `idUser` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `direccion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sexo` enum('MUJER','HOMBRE','OTRO') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users_data`
--

INSERT INTO `users_data` (`idUser`, `nombre`, `apellidos`, `email`, `telefono`, `fechaNacimiento`, `direccion`, `sexo`) VALUES
(7, 'administrador', '1', 'admin@gmail.com', '655767677', '2000-11-01', NULL, 'OTRO'),
(10, 'alba', 'camacho', 'alba@gmail.com', '67676767', '2001-11-07', 'Gandía', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_login`
--

CREATE TABLE `users_login` (
  `idLogin` int NOT NULL,
  `idUser` int NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users_login`
--

INSERT INTO `users_login` (`idLogin`, `idUser`, `usuario`, `password`, `rol`) VALUES
(3, 7, 'administrador', '$2y$10$xXyQSqAzN9KxmBeEA8Gi0eEoAO7pgvU4LawXu7evqiiKaakUTgrme', 'admin'),
(7, 10, 'alba', '$2y$10$tupVKLR9EC28j2sGvG6ES.5hifpqYtXNCTLZJjqsXBdGEeoMMYDvm', 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `idUser` (`idUser`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`idNoticia`),
  ADD UNIQUE KEY `titulo` (`titulo`),
  ADD KEY `idUser` (`idUser`);

--
-- Indices de la tabla `users_data`
--
ALTER TABLE `users_data`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `users_login`
--
ALTER TABLE `users_login`
  ADD PRIMARY KEY (`idLogin`),
  ADD UNIQUE KEY `idUser` (`idUser`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `idCita` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `idNoticia` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users_data`
--
ALTER TABLE `users_data`
  MODIFY `idUser` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `users_login`
--
ALTER TABLE `users_login`
  MODIFY `idLogin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `users_login`
--
ALTER TABLE `users_login`
  ADD CONSTRAINT `users_login_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
