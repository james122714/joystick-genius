-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-09-2025 a las 22:08:54
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
-- Base de datos: `pixel_play_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `permisos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `usuario_id`, `rol`, `permisos`) VALUES
(22, 1, 'Admin', 'crear,editar,eliminar'),
(23, 2, 'Moderador', 'moderar'),
(29, 10, 'AdminVista', 'ver_todo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `nombre`, `slug`, `created_at`) VALUES
(1, 'Terror', 'terror', '2024-12-01 23:56:26'),
(2, 'Aventura', 'aventura', '2024-12-01 23:56:26'),
(3, 'Acción', 'accion', '2024-12-01 23:56:26'),
(4, 'Deporte', 'deporte', '2024-12-01 23:56:26'),
(5, 'RPG', 'rpg', '2024-12-01 23:56:26'),
(6, 'Estrategia', 'estrategia', '2024-12-01 23:56:26'),
(7, 'Carreras', 'carreras', '2024-12-01 23:56:26'),
(8, 'Survival', 'survival', '2024-12-01 23:56:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `game_url` varchar(255) DEFAULT NULL,
  `platform_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `games`
--

INSERT INTO `games` (`id`, `title`, `description`, `release_date`, `game_url`, `platform_id`, `image_url`, `created_at`, `updated_at`) VALUES
(4, 'Warframe', 'Shooter cooperativo en tercera persona donde controlas guerreros futuristas llamados Tenno, realizando misiones contra diversas facciones.', '2013-03-25', 'https://www.warframe.com/es/landing', NULL, 'uploads/games/674f46eab0d79_foto 2.jpg', '2024-12-03 17:59:06', '2024-12-03 17:59:06'),
(5, 'Valorant', ' Es un shooter táctico en equipos de 5 contra 5, desarrollado por Riot Games. Ofrece una experiencia intensa de habilidades estratégicas y disparos precisos.', '2020-06-02', 'https://store.epicgames.com/en-US/p/valorant', NULL, 'uploads/games/674f48247411c_prueba 2.jpeg', '2024-12-03 18:04:20', '2024-12-03 18:04:20'),
(6, 'Apex Legends', ' Es un battle royale gratuito de Respawn Entertainment, que combina elementos de acción y estrategia en equipo con personajes únicos', '2019-02-04', 'https://store.steampowered.com/app/1172470/Apex_Legends/', NULL, 'uploads/games/674f48ccb88b2_foto 2.jpg', '2024-12-03 18:07:08', '2024-12-03 18:07:08'),
(7, 'Paladins', 'Shooter en primera persona basado en equipos y personalización de personajes.', '2018-03-08', 'https://store.steampowered.com/app/444090/Paladins/', NULL, 'uploads/games/674f497053ec4_prueba 2.jpeg', '2024-12-03 18:09:52', '2024-12-03 18:09:52'),
(8, 'Call of Duty: Warzone', 'Versión gratuita del famoso juego de disparos con modos Battle Royale.', '2020-03-10', 'https://us.shop.battle.net/en-us/product/call-of-duty-warzone-2', NULL, 'uploads/games/674f4a5ef107c_imagen 1.jpeg', '2024-12-03 18:13:50', '2024-12-03 18:13:50'),
(9, ' Brawlhalla', 'Un juego de lucha estilo arcade donde puedes enfrentarte a otros jugadores con una amplia variedad de personajes, cada uno con habilidades únicas. Similar a Super Smash Bros., incluye modos multijugador y combate dinámico.', '2017-10-30', 'https://store.steampowered.com/app/291550/Brawlhalla/', NULL, 'uploads/games/674f4b3a3d315_imagen 1.jpeg', '2024-12-03 18:17:30', '2024-12-03 18:17:30'),
(10, 'Team Fortress 2', 'Shooter multijugador por equipos con gráficos caricaturescos y modos variados.', '2017-10-10', 'https://store.steampowered.com/app/440/Team_Fortress_2/', NULL, 'uploads/games/674f4bcd263af_foto 2.jpg', '2024-12-03 18:19:57', '2024-12-03 18:19:57'),
(11, 'Counter-Strike 2', ' un juego en equipo donde el objetivo es derrotar al equipo contrario, desactivar bombas y optimizar estrategias en mapas icónicos. Con estas mejoras, el juego busca atraer tanto a los veteranos como a nuevos jugadores', '2012-08-21', 'https://store.steampowered.com/app/730/CounterStrike_2/', NULL, 'uploads/games/674f4d9b2500b_foto 2.jpg', '2024-12-03 18:27:39', '2024-12-03 18:27:39'),
(12, 'Overwatch 2', 'Shooter basado en héroes con modos por equipos y cooperativos.', '2022-10-04', 'https://store.steampowered.com/app/2357570/Overwatch_2/', NULL, 'uploads/games/674f4df09c39a_foto 2.jpg', '2024-12-03 18:29:04', '2024-12-03 18:29:04'),
(13, 'Genshin Impact', ' RPG de mundo abierto con exploración, combate y gráficos estilo anime.', '2020-09-28', 'https://genshin.hoyoverse.com/en/home', NULL, 'uploads/games/674f4efa80bbb_prueba 2.jpeg', '2024-12-03 18:33:30', '2024-12-03 18:39:36'),
(14, 'Star Wars: The Old Republic', ' MMORPG ambientado en el universo de Star Wars con historias y misiones épicas.', '2011-12-20', 'https://www.swtor.com', NULL, 'uploads/games/674f50edae8e4_prueba 2.jpeg', '2024-12-03 18:41:49', '2024-12-03 18:41:49'),
(15, 'Runescape', 'Juego clásico de rol y aventura multijugador con un extenso mundo por explorar.', '2001-01-04', 'https://play.runescape.com', NULL, 'uploads/games/674f512688888_prueba 2.jpeg', '2024-12-03 18:42:46', '2024-12-03 18:42:46'),
(16, 'Neverwinter', 'RPG multijugador basado en el universo de Dungeons & Dragons.', '2013-06-20', 'https://www.playneverwinter.com/en/', NULL, 'uploads/games/674f51652e9ab_prueba 2.jpeg', '2024-12-03 18:43:49', '2024-12-03 18:43:49'),
(17, 'Secret World Legends', 'Aventura y misterio en un mundo contemporáneo lleno de criaturas mitológicas.', '2017-07-31', 'https://www.secretworldlegends.com/', NULL, 'uploads/games/674f51ab7eeaf_prueba 2.jpeg', '2024-12-03 18:44:59', '2024-12-03 18:44:59'),
(18, 'Guild Wars 2', ' MMORPG con un enfoque en exploración y eventos dinámicos en un vasto mundo.', '2012-08-28', 'https://www.guildwars2.com/es/', NULL, 'uploads/games/674f51eb10f97_prueba 2.jpeg', '2024-12-03 18:46:03', '2024-12-03 18:46:03'),
(19, 'Albion Online', 'Aventura y estrategia multijugador donde recolectas, combates y construyes.', '2017-07-17', 'https://albiononline.com/home', NULL, 'uploads/games/674f5243e4499_prueba 2.jpeg', '2024-12-03 18:47:31', '2024-12-03 18:47:31'),
(20, 'Asphalt 9: Legends', 'Juego de carreras arcade con autos de alta gama y gráficos impresionantes.', '2018-07-25', 'https://asphaltlegendsunite.com', NULL, 'uploads/games/674f52e6bce1e_foto 2.jpg', '2024-12-03 18:50:14', '2024-12-03 18:50:14'),
(21, 'TrackMania Nations Forever', ' Juego de carreras con un enfoque en velocidad y precisión en pistas desafiantes.', '2008-04-16', 'https://www.trackmaniaforever.com', NULL, 'uploads/games/674f53653a40a_foto 2.jpg', '2024-12-03 18:52:21', '2024-12-03 18:52:21'),
(22, 'Rocket League', ' Mezcla de fútbol y autos en partidas rápidas y competitivas.', '2015-07-07', 'https://www.rocketleague.com/es-es', NULL, 'uploads/games/6750be2902e6e_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:40:09', '2024-12-04 20:40:09'),
(23, 'Fishing Planet', 'Simulador de pesca con múltiples ubicaciones y especies de peces.', '2015-08-15', 'https://www.fishingplanet.com', NULL, 'uploads/games/6750be8965cd1_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:41:45', '2024-12-04 20:41:45'),
(24, 'Skater XL  / COL$ 54.500', ' Simulador de skateboarding con un control innovador y mapas abiertos.', '2020-07-28', 'https://store.steampowered.com/app/962730/Skater_XL__The_Ultimate_Skateboarding_Game/', NULL, 'uploads/games/6750bf2aa7071_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:44:26', '2024-12-04 20:44:26'),
(25, 'Soccer Manager 2024', 'Juego de gestión de equipos de fútbol con opciones estratégicas.', '2023-10-19', 'https://www.soccermanager.com/', NULL, 'uploads/games/6750bfc238538_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:46:58', '2024-12-04 20:46:58'),
(26, 'Pool Live Pro', ' Juego de billar multijugador en línea con varios modos de juego.', '2017-08-01', 'https://www.gamedesire.com/es', NULL, 'uploads/games/6750c0205b481_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:48:32', '2024-12-04 20:48:32'),
(27, 'Starcraft II (Starter Edition)', 'Juego de estrategia en tiempo real con campañas épicas y multijugador competitivo.', '2010-07-27', 'https://starcraft2.blizzard.com/es-es/', NULL, 'uploads/games/6750c080bddbe_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:50:09', '2024-12-04 20:50:09'),
(28, 'Clash of Clans', 'Construye tu aldea, entrena tropas y compite en batallas estratégicas multijugador.', '2012-08-02', 'https://supercell.com/en/games/clashofclans/', NULL, 'uploads/games/6750c13fb1e8e_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:53:19', '2024-12-04 20:53:19'),
(29, 'Forge of Empires', 'Juego de estrategia donde expandes tu imperio a través de la historia.', '2012-04-17', 'https://ar-play.forgeofempires.com/?ref=dotcom&lps_flow=after_glps_shim', NULL, 'uploads/games/6750c18db8b44_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:54:37', '2024-12-04 20:54:37'),
(30, 'Warframe (Railjack Mode)', 'Juego cooperativo con un enfoque estratégico en batallas espaciales.', '2013-03-25', 'https://www.warframe.com/es/landing', NULL, 'uploads/games/6750c1c697d04_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:55:34', '2024-12-04 20:55:34'),
(31, 'Grepolis', ' Estrategia en línea donde construyes una ciudad griega antigua y compites con otros jugadores.', '2009-12-08', 'https://ar-play.grepolis.com/?lps_flow=after_glps_shim', NULL, 'uploads/games/6750c205da03f_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:56:37', '2024-12-04 20:56:37'),
(32, 'Stronghold Kingdoms', ' Juego de estrategia medieval centrado en la construcción de castillos y guerras de clanes.', '2010-10-24', 'https://www.strongholdkingdoms.com/', NULL, 'uploads/games/6750c28525eb7_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:58:45', '2024-12-04 20:58:45'),
(33, 'Command & Conquer: Tiberium Alliances', 'Estrategia MMO donde recolectas recursos y lideras ejércitos.', '2012-05-24', 'https://www.ea.com/es-es/games/command-and-conquer/command-and-conquer-tiberium-alliances', NULL, 'uploads/games/6750c2cb11013_b07061daec469c4ae0cfb24fe6d3a5c5.jpg', '2024-12-04 20:59:55', '2024-12-04 20:59:55'),
(34, 'Path of Exile', 'RPG de acción en un mundo oscuro con habilidades personalizables.', '2013-10-23', 'https://www.pathofexile.com/', NULL, 'uploads/games/6751b4bdcd97f_prueba 1.png', '2024-12-05 14:12:13', '2024-12-05 14:12:13'),
(35, 'Elder Scrolls Online (versión básica gratuita)', ' RPG multijugador ambientado en el vasto universo de Elder Scrolls.', '2014-04-04', 'https://www.elderscrollsonline.com/en-us/home', NULL, 'uploads/games/6751b54eae4eb_prueba 1.png', '2024-12-05 14:14:38', '2024-12-05 14:14:38'),
(36, 'World of Warcraft (Starter Edition)', ' Explora Azeroth y realiza misiones épicas en este icónico MMORPG.', '2004-11-23', 'https://worldofwarcraft.blizzard.com/es-mx/', NULL, 'uploads/games/6751b5a345a83_prueba 1.png', '2024-12-05 14:16:03', '2024-12-05 14:16:03'),
(37, 'Neverwinter', ' RPG multijugador basado en Dungeons & Dragons.', '2013-06-20', 'https://www.playneverwinter.com/en/', NULL, 'uploads/games/6751b61e10722_prueba 1.png', '2024-12-05 14:18:06', '2024-12-05 14:18:06'),
(38, 'Albion Online', 'MMORPG con un sistema de economía impulsado por los jugadores.', '2017-07-17', 'https://albiononline.com/home', NULL, 'uploads/games/6751b657061ab_prueba 1.png', '2024-12-05 14:19:03', '2024-12-05 14:19:03'),
(39, 'Star Wars: The Old Republic', ' RPG multijugador ambientado en el universo de Star Wars.', '2011-12-20', 'https://www.swtor.com/', NULL, 'uploads/games/6751b699b3404_prueba 1.png', '2024-12-05 14:20:09', '2024-12-05 14:20:09'),
(40, 'AdventureQuest 3D', ' RPG multijugador con combates y exploración en tiempo real.', '2016-10-19', 'https://aq3d.com/', NULL, 'uploads/games/6751b6e5a63a9_prueba 1.png', '2024-12-05 14:21:25', '2024-12-05 14:21:25'),
(41, 'ARK: Survival Evolved (Gratuito en Mobile)', 'Sobrevive en un mundo lleno de dinosaurios, construye refugios y doma criaturas.', '2018-06-14', 'https://playark.com/', NULL, 'uploads/games/6751b76fddf12_pruba pixel 1.png', '2024-12-05 14:23:43', '2024-12-05 14:23:43'),
(42, ' TLauncher: Simplifica tu experiencia en Minecraft', 'TLauncher es un lanzador gratuito diseñado para mejorar tu experiencia en Minecraft. Ofrece la posibilidad de instalar versiones modificadas del juego, agregar mods personalizados, y acceder a contenido exclusivo como skins, texturas y mapas. Su interfaz intuitiva y sus amplias capacidades de personalización lo convierten en una herramienta esencial tanto para jugadores casuales como para modders avanzados. Ideal para quienes desean explorar más allá de la experiencia estándar de Minecraft, TLauncher combina flexibilidad y facilidad de uso.', '2013-01-01', 'https://tlauncher.org/en/', NULL, 'uploads/games/6751b835b16b2_pruba pixel 1.png', '2024-12-05 14:27:01', '2024-12-05 14:27:01'),
(43, 'The Forest', 'Sobrevive en un bosque lleno de misterios y caníbales después de un accidente aéreo.', '2018-04-30', 'https://endnightgames.com/', NULL, 'uploads/games/6751b877e5829_pruba pixel 1.png', '2024-12-05 14:28:07', '2024-12-05 14:28:07'),
(44, 'SCUM', 'Simulador de supervivencia multijugador con un enfoque en el realismo.', '2018-08-29', 'https://www.scum.game/', NULL, 'uploads/games/6751b8a88df36_pruba pixel 1.png', '2024-12-05 14:28:56', '2024-12-05 14:28:56'),
(45, 'DayZ (Experimental Servers)', 'Juego de supervivencia en un mundo lleno de zombis y peligros naturales.', '2013-12-16', 'https://dayz.com/', NULL, 'uploads/games/6751b8fd30d18_pruba pixel 1.png', '2024-12-05 14:30:21', '2024-12-05 14:30:21'),
(46, 'Surviv.io', ' Juego de supervivencia estilo battle royale en 2D.', '2017-10-10', 'https://bitheroesarena.io', NULL, 'uploads/games/6751b93e65b7f_pruba pixel 1.png', '2024-12-05 14:31:26', '2024-12-05 14:31:26'),
(47, 'SCP', 'es un juego gratuito disponible en Steam que ofrece una experiencia inmersiva basada en el universo de SCP (Secure, Contain, Protect). El jugador asume el rol de un sujeto de pruebas dentro de una instalación subterránea de investigación SCP. Durante un fallo catastrófico en el sistema de contención, tu misión será escapar, evitando a las criaturas SCP mientras recolectas objetos anómalos y recursos valiosos.', '2025-01-01', 'https://store.steampowered.com/app/2768300/SCP/', NULL, 'uploads/games/6751ba3779bae_prueba pixel.png', '2024-12-05 14:35:35', '2024-12-05 14:35:35'),
(48, 'Doki Doki Literature Club!', 'Visual novel con un giro oscuro y elementos de terror psicológico.', '2017-09-22', 'https://ddlc.moe/', NULL, 'uploads/games/6751ba832806a_prueba pixel.png', '2024-12-05 14:36:51', '2024-12-05 14:36:51'),
(49, 'Deceit', ' Juego multijugador de terror donde debes descubrir quién está infectado antes de que sea demasiado tarde.', '2017-03-03', 'https://deceit.gg/', NULL, 'uploads/games/6751bb1a3531c_prueba pixel.png', '2024-12-05 14:39:22', '2024-12-05 14:39:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game_categories`
--

CREATE TABLE `game_categories` (
  `game_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `game_categories`
--

INSERT INTO `game_categories` (`game_id`, `category_id`) VALUES
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 7),
(21, 7),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 6),
(28, 6),
(29, 6),
(30, 6),
(31, 6),
(32, 6),
(33, 6),
(34, 5),
(35, 5),
(36, 5),
(37, 5),
(38, 5),
(39, 5),
(40, 5),
(41, 8),
(42, 8),
(43, 8),
(44, 8),
(45, 8),
(46, 8),
(47, 1),
(48, 1),
(49, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game_launches`
--

CREATE TABLE `game_launches` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `release_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `game_url` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `pre_order` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `game_launches`
--

INSERT INTO `game_launches` (`id`, `title`, `description`, `release_date`, `price`, `image_url`, `game_url`, `category`, `rating`, `pre_order`, `created_at`, `updated_at`) VALUES
(3, 'Dragon\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\'s Legacy', 'Explora un mundo mágico lleno de dragones y misterios ancestrales.', '2024-09-20', 29.99, 'uploads/launches/67478fcf1d2ae_foto 2.jpg', 'https://store.steampowered.com/app/1222690/Dragon_Age_Inquisition/', 'Estrategia', 5.0, 0, '2024-11-20 23:00:36', '2024-12-03 16:40:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game_likes`
--

CREATE TABLE `game_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `game_likes`
--

INSERT INTO `game_likes` (`id`, `user_id`, `game_id`, `created_at`) VALUES
(1, 1, 49, '2025-09-07 18:22:40'),
(2, 1, 48, '2025-09-07 18:22:46'),
(3, 8, 49, '2025-09-07 19:43:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria` enum('general','tecnologia','deportes','entretenimiento') NOT NULL,
  `destacada` tinyint(1) DEFAULT 0,
  `autor_id` int(11) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` datetime DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `vistas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `contenido`, `imagen`, `categoria`, `destacada`, `autor_id`, `fecha_publicacion`, `fecha_expiracion`, `estado`, `vistas`) VALUES
(31, 'ghwdhihf wdefie', 'i4ihfjsdfjk wfw', NULL, 'general', 1, 1, '2025-09-07 17:37:52', '2025-09-10 19:37:52', 'activo', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `platforms`
--

CREATE TABLE `platforms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `website_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `platforms`
--

INSERT INTO `platforms` (`id`, `name`, `website_url`) VALUES
(1, 'Steam', 'https://store.steampowered.com'),
(2, 'Epic Games Store', 'https://www.epicgames.com'),
(3, 'Origin', 'https://www.origin.com'),
(4, 'Nintendo eShop', 'https://www.nintendo.com/store'),
(5, 'Xbox Store', 'https://www.xbox.com/store');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rangos`
--

CREATE TABLE `rangos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `requisitos` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rangos`
--

INSERT INTO `rangos` (`id`, `nombre`, `descripcion`, `requisitos`, `created_at`) VALUES
(1, 'Novato Pixel', 'Un aventurero principiante en el vasto mundo de los videojuegos. Primeros pasos en la gran aventura digital.', '0 puntos', '2024-11-27 02:31:42'),
(2, 'Aprendiz de Pixel', 'Comenzando a comprender los secretos y mecánicas de los juegos. Tu viaje apenas comienza.', '100 puntos', '2024-11-27 02:31:42'),
(3, 'Explorador Digital', 'Navegando con confianza por diferentes géneros y desafiando tus primeros retos.', '250 puntos', '2024-11-27 02:31:42'),
(4, 'Técnico de Juego', 'Dominando mecánicas básicas y comenzando a desarrollar estrategias más complejas.', '500 puntos', '2024-11-27 02:31:42'),
(5, 'Estratega Novato', 'Empiezas a pensar más allá de lo obvio. Tus decisiones en juego son cada vez más calculadas.', '750 puntos', '2024-11-27 02:31:42'),
(6, 'Maestro de Pixels', 'Tu habilidad y conocimiento de juegos comienza a destacar. Los desafíos te motivan.', '1000 puntos', '2024-11-27 02:31:42'),
(7, 'Comandante Virtual', 'Tus habilidades te distinguen. Lideras estrategias y inspiras a otros jugadores.', '1500 puntos', '2024-11-27 02:31:42'),
(8, 'Héroe de Código', 'Dominas mecánicas complejas y tienes una comprensión profunda de múltiples juegos.', '2000 puntos', '2024-11-27 02:31:42'),
(9, 'Leyenda Retro', 'Un veterano respetado con conocimientos que abarcan diferentes épocas de juego.', '2500 puntos', '2024-11-27 02:31:42'),
(10, 'Arquitecto de Mundos', 'No solo juegas, entiendes la mecánica y diseño detrás de los videojuegos.', '3000 puntos', '2024-11-27 02:31:42'),
(11, 'Maestro de Estrategia', 'Tus habilidades de planificación y ejecución son casi profesionales.', '3500 puntos', '2024-11-27 02:31:42'),
(12, 'Campeón Multiversal', 'Dominas múltiples géneros con una maestría impresionante.', '5000 puntos', '2024-11-27 02:31:42'),
(13, 'Eminencia Pixel', 'Tu reputación en la comunidad gamer es legendaria.', '10000 puntos', '2024-11-27 02:31:42'),
(14, 'Guardián del Reino Digital', 'Un jugador cuya experiencia y conocimiento son casi mitológicos.', '15000 puntos', '2024-11-27 02:31:42'),
(15, 'Emperador de Pixels', 'El más alto honor. Tu dominio de los juegos es incomparable.', '20000 puntos', '2024-11-27 02:31:42'),
(16, 'Alma de Videojuego', 'Más que un jugador, eres una parte integral de la cultura gamer.', '30000 puntos', '2024-11-27 02:31:42'),
(17, 'Maestro del Universo Virtual', 'Tu sabiduría y habilidad trascienden los límites de cualquier juego.', '60500 puntos', '2024-11-27 02:31:42'),
(18, 'Leyenda Viviente', 'Tu nombre es sinónimo de excelencia en la comunidad gamer.', '70000 puntos', '2024-11-27 02:31:42'),
(19, 'Avatar Supremo', 'Representas la cumbre de la maestría en videojuegos.', '75000 puntos', '2024-11-27 02:31:42'),
(20, 'Dios de los Pixels', 'El rango más alto. Has convertido el juego en un arte suprema.', '80000 puntos', '2024-11-27 02:31:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restablecimiento_contrasena`
--

CREATE TABLE `restablecimiento_contrasena` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `expira` datetime NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `restablecimiento_contrasena`
--

INSERT INTO `restablecimiento_contrasena` (`id`, `email`, `token`, `codigo`, `expira`, `creado_en`) VALUES
(3, 'carmona167803@gmail.com', 'fc8b61174ed2ec1c6fcd2ce5cce7d7ac0431fa864dc4d0f2b67dc2498f41b8a9', '', '2024-12-04 03:57:29', '2024-12-04 01:57:29'),
(4, 'carmona167803@gmail.com', '0b9a3a09a567efb812f0bc44938f76fb66d04fd96086a5825f5353dc950937af', '', '2024-12-04 03:57:40', '2024-12-04 01:57:40'),
(5, 'carmona167803@gmail.com', 'aa9e87f781e51fb0a0fea2b58557c1973d870af35983fe7ec0b94bc476fdbd6a', '', '2024-12-04 20:15:50', '2024-12-04 18:15:50'),
(6, 'carmona167803@gmail.com', 'cbc555a0d753e34a48f785f3f981b7f27dd232a502e549daf76afb94994fafe9', '', '2024-12-04 21:34:10', '2024-12-04 19:34:10'),
(7, 'carmona167803@gmail.com', 'acf600bd03e2efeba25ee5cfe362fabd0307ab22cf0117cc3a633a4f036ed7be', '', '2025-08-30 02:27:52', '2025-08-29 23:27:52'),
(8, 'carmona167803@gmail.com', '69e92c51554dd384a35c961b0651675e387ae97342ff839bc9ee0969c391fbbb', '$2y$10', '2025-09-03 00:26:20', '2025-09-02 22:16:20'),
(9, 'carmona167803@gmail.com', '57fe3e103839f8ed6821d451d174aaaa709c436361f9e43e417eb4bbeabe8fa5', '$2y$10', '2025-09-03 00:27:10', '2025-09-02 22:17:10'),
(10, 'carmona167803@gmail.com', '61dd2fdf923724ec08dc40103a01db41bd7397a6cad029b272d306a288f6469a', '$2y$10', '2025-09-03 00:30:14', '2025-09-02 22:20:14'),
(11, 'carmona167803@gmail.com', 'c4afb63c5266773eed94b71e1873cf07378cd260d37a95251aa8f7ddc04b8f45', '$2y$10', '2025-09-03 00:31:24', '2025-09-02 22:21:24'),
(12, 'carmona167803@gmail.com', '80443774308c8b704dabd42dd6000682dd90e09233a120e184b993e014e5180f', '$2y$10', '2025-09-03 00:32:51', '2025-09-02 22:22:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sesiones`
--

INSERT INTO `sesiones` (`id`, `usuario_id`, `fecha`, `ip`) VALUES
(1, 1, '2025-08-24 19:59:05', '::1'),
(2, 1, '2025-08-29 21:10:21', '::1'),
(3, 1, '2025-08-29 21:10:48', '::1'),
(4, 1, '2025-08-29 23:34:52', '::1'),
(5, 1, '2025-08-31 16:34:31', '::1'),
(6, 1, '2025-08-31 16:53:54', '::1'),
(7, 2, '2025-09-02 21:08:51', '::1'),
(8, 1, '2025-09-02 21:27:51', '::1'),
(9, 9, '2025-09-02 21:43:17', '::1'),
(10, 1, '2025-09-02 21:50:42', '::1'),
(11, 1, '2025-09-03 13:47:27', '::1'),
(12, 8, '2025-09-03 13:48:10', '::1'),
(13, 1, '2025-09-07 16:17:12', '::1'),
(14, 8, '2025-09-07 19:43:05', '::1'),
(15, 1, '2025-09-08 19:23:04', '::1'),
(16, 1, '2025-09-13 15:39:15', '::1'),
(17, 2, '2025-09-13 16:26:20', '::1'),
(18, 1, '2025-09-13 18:03:39', '::1'),
(19, 8, '2025-09-13 18:09:06', '::1'),
(20, 8, '2025-09-13 18:21:15', '::1'),
(21, 9, '2025-09-13 22:18:51', '::1'),
(22, 1, '2025-09-13 22:24:30', '::1'),
(23, 1, '2025-09-19 20:25:55', '::1'),
(24, 1, '2025-09-19 21:10:51', '::1'),
(25, 1, '2025-09-19 22:58:12', '::1'),
(26, 9, '2025-09-19 22:58:45', '::1'),
(27, 8, '2025-09-19 22:59:24', '::1'),
(28, 1, '2025-09-21 19:22:12', '::1'),
(29, 8, '2025-09-21 19:24:52', '::1'),
(30, 1, '2025-09-21 19:29:23', '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `streams`
--

CREATE TABLE `streams` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `plataforma` enum('YouTube','Twitch','Facebook Gaming') NOT NULL,
  `url_stream` varchar(500) NOT NULL,
  `streamer` varchar(100) NOT NULL,
  `estado` enum('En Vivo','Offline','Próximamente') DEFAULT 'Offline',
  `categoria` varchar(100) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `streams`
--

INSERT INTO `streams` (`id`, `titulo`, `descripcion`, `plataforma`, `url_stream`, `streamer`, `estado`, `categoria`, `fecha_creacion`) VALUES
(1, 'vegeta777', 'variedad de juegos', 'YouTube', 'https://www.youtube.com/user/vegetta777', 'vegeta777', 'En Vivo', 'vieojuegos', '2024-11-25 16:32:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutoriales`
--

CREATE TABLE `tutoriales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `url_video` varchar(500) NOT NULL,
  `categoria` enum('Basico','Intermedio','Avanzado') NOT NULL,
  `duracion` varchar(10) DEFAULT NULL,
  `nivel_dificultad` enum('Principiante','Intermedio','Avanzado') NOT NULL,
  `puntuacion` decimal(3,2) DEFAULT 0.00,
  `image_url` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutoriales`
--

INSERT INTO `tutoriales` (`id`, `nombre`, `descripcion`, `url_video`, `categoria`, `duracion`, `nivel_dificultad`, `puntuacion`, `image_url`, `fecha_creacion`) VALUES
(4, 'Guía Completa para Principiantes en Minecraft: Construcción y Supervivencia', 'Aprende desde cero cómo empezar en Minecraft con este tutorial paso a paso. Descubre cómo recolectar recursos, construir tu primer refugio y sobrevivir tu primera noche. Ideal para jugadores nuevos o aquellos que buscan perfeccionar sus habilidades básicas.', 'https://youtu.be/4z6Uqe-uEUY?si=rxaOmDwISb1CwrlN', 'Intermedio', '20:00', 'Principiante', 0.00, 'vista/multimedia/imagenes/tutoriales/674f30b646e86_foto 2.jpg', '2024-11-25 21:47:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `user_id`, `game_id`, `created_at`) VALUES
(2, 1, 49, '2025-09-07 18:30:14'),
(3, 1, 48, '2025-09-07 18:30:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('usuario','admin','adminvista','moderador') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `rango_id` int(11) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `puntos` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `tipo_usuario`, `fecha_registro`, `ultimo_acceso`, `estado`, `rango_id`, `foto_perfil`, `puntos`) VALUES
(1, 'Admin Principal', 'admin@gmail.com', 'admin_123', 'admin', '2024-11-17 13:18:26', '2025-09-21 19:29:23', 'activo', 20, 'multimedia/fotos_perfil/1_1733418403.jpg', 110),
(2, 'shadow', 'usuario@gmail.com', 'moderador_123', 'moderador', '2024-11-17 14:15:38', '2025-09-13 16:26:20', 'activo', NULL, 'multimedia/fotos_perfil/2_1733065591.jpeg', 0),
(8, 'berlis pc', 'natalia@gmail.com', 'moderador_123', 'moderador', '2024-11-27 00:32:35', '2025-09-21 19:24:52', 'activo', NULL, 'multimedia/fotos_perfil/8_1732751155.png', 0),
(9, 'shadow', 'carmona167803@gmail.com', 'adminvista_123', 'adminvista', '2024-12-02 00:41:00', '2025-09-19 22:58:45', 'activo', NULL, 'multimedia/fotos_perfil/9_1733100075.jpeg', 0),
(10, 'te', 'te@gmal.com', '$2y$10$fnE3H.M2c8XiuB3sO702.u3cQmiYoznXSMaYzgP6EpvIF3VeA8Ysa', 'usuario', '2025-08-24 19:18:49', NULL, 'activo', NULL, NULL, 0),
(11, 'shadow', 'asd@gmail.com', '$2y$10$I4kLkJRydFFqAeF3hRbE3O1d1ALvCiMkEawawcv9QSKprD7p70CWK', 'usuario', '2025-09-13 16:58:04', NULL, 'activo', NULL, 'multimedia/fotos_perfil/11_1757783120.jpg', 0),
(12, 'shadow', 'shadow@gmail.com', '$2y$10$vqMNrLRguV3R2m9LUgqhp.dsQFlujqjHvcM9OiPcXf.TSDetJVdt.', 'usuario', '2025-09-19 22:40:03', NULL, 'activo', NULL, 'multimedia/fotos_perfil/12_1758321638.jpeg', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vistas_tutoriales`
--

CREATE TABLE `vistas_tutoriales` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tutorial_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vistas_tutoriales`
--

INSERT INTO `vistas_tutoriales` (`id`, `usuario_id`, `tutorial_id`, `fecha`) VALUES
(1, 1, 4, '2025-09-19 21:13:01'),
(2, 12, 4, '2025-09-19 22:40:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vistas_videojuegos`
--

CREATE TABLE `vistas_videojuegos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vistas_videojuegos`
--

INSERT INTO `vistas_videojuegos` (`id`, `usuario_id`, `game_id`, `fecha`) VALUES
(1, 1, 49, '2025-09-19 21:14:18'),
(2, 1, 48, '2025-09-19 21:15:31'),
(3, 1, 47, '2025-09-19 21:19:20'),
(4, 1, 44, '2025-09-19 21:20:30'),
(5, 1, 45, '2025-09-19 21:20:52'),
(6, 1, 41, '2025-09-19 21:21:17');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `game_categories`
--
ALTER TABLE `game_categories`
  ADD PRIMARY KEY (`game_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `game_launches`
--
ALTER TABLE `game_launches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `game_likes`
--
ALTER TABLE `game_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor_id` (`autor_id`);

--
-- Indices de la tabla `platforms`
--
ALTER TABLE `platforms`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rangos`
--
ALTER TABLE `rangos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `restablecimiento_contrasena`
--
ALTER TABLE `restablecimiento_contrasena`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_email` (`email`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `streams`
--
ALTER TABLE `streams`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tutoriales`
--
ALTER TABLE `tutoriales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rango_id` (`rango_id`);

--
-- Indices de la tabla `vistas_tutoriales`
--
ALTER TABLE `vistas_tutoriales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`tutorial_id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

--
-- Indices de la tabla `vistas_videojuegos`
--
ALTER TABLE `vistas_videojuegos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `game_launches`
--
ALTER TABLE `game_launches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `game_likes`
--
ALTER TABLE `game_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `platforms`
--
ALTER TABLE `platforms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rangos`
--
ALTER TABLE `rangos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `restablecimiento_contrasena`
--
ALTER TABLE `restablecimiento_contrasena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `streams`
--
ALTER TABLE `streams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tutoriales`
--
ALTER TABLE `tutoriales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `vistas_tutoriales`
--
ALTER TABLE `vistas_tutoriales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vistas_videojuegos`
--
ALTER TABLE `vistas_videojuegos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD CONSTRAINT `administradores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_usuario_admin` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `game_categories`
--
ALTER TABLE `game_categories`
  ADD CONSTRAINT `game_categories_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `game_likes`
--
ALTER TABLE `game_likes`
  ADD CONSTRAINT `game_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `game_likes_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `user_favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `user_favorites_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rango_id`) REFERENCES `rangos` (`id`);

--
-- Filtros para la tabla `vistas_tutoriales`
--
ALTER TABLE `vistas_tutoriales`
  ADD CONSTRAINT `vistas_tutoriales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vistas_tutoriales_ibfk_2` FOREIGN KEY (`tutorial_id`) REFERENCES `tutoriales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vistas_videojuegos`
--
ALTER TABLE `vistas_videojuegos`
  ADD CONSTRAINT `vistas_videojuegos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vistas_videojuegos_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
