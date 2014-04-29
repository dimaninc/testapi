-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.25 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table testapi.cinemas
CREATE TABLE IF NOT EXISTS `cinemas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `clean_title` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ct` (`clean_title`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table testapi.cinemas: ~4 rows (approximately)
/*!40000 ALTER TABLE `cinemas` DISABLE KEYS */;
INSERT INTO `cinemas` (`id`, `title`, `clean_title`) VALUES
	(1, 'Центральный', 'centro'),
	(2, 'Космос', 'cosmos'),
	(3, 'Спартак', 'spartack'),
	(4, 'Химари', 'khimki');
/*!40000 ALTER TABLE `cinemas` ENABLE KEYS */;


-- Dumping structure for table testapi.films
CREATE TABLE IF NOT EXISTS `films` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `clean_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ct` (`clean_title`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table testapi.films: ~3 rows (approximately)
/*!40000 ALTER TABLE `films` DISABLE KEYS */;
INSERT INTO `films` (`id`, `title`, `clean_title`) VALUES
	(1, 'Matrix', 'matrix'),
	(2, 'Through the never', 'never'),
	(3, 'Lobsters atack', 'lobsters');
/*!40000 ALTER TABLE `films` ENABLE KEYS */;


-- Dumping structure for table testapi.halls
CREATE TABLE IF NOT EXISTS `halls` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cinema_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx` (`cinema_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Dumping data for table testapi.halls: ~6 rows (approximately)
/*!40000 ALTER TABLE `halls` DISABLE KEYS */;
INSERT INTO `halls` (`id`, `cinema_id`, `title`) VALUES
	(1, 1, 'Красный зал'),
	(2, 1, 'Синий зал'),
	(3, 2, 'Зеленый'),
	(4, 2, 'Желтый'),
	(5, 3, 'Фиолетовый'),
	(6, 4, 'Бирюзовый');
/*!40000 ALTER TABLE `halls` ENABLE KEYS */;


-- Dumping structure for table testapi.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table testapi.orders: ~1 rows (approximately)
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` (`id`, `uid`) VALUES
	(4, 'f456ac7aceb1ec4cc426d9163fac6f18');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;


-- Dumping structure for table testapi.places
CREATE TABLE IF NOT EXISTS `places` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `hall_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx` (`hall_id`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8;

-- Dumping data for table testapi.places: ~174 rows (approximately)
/*!40000 ALTER TABLE `places` DISABLE KEYS */;
INSERT INTO `places` (`id`, `hall_id`, `title`) VALUES
	(1, 1, 'Место №1'),
	(2, 1, 'Место №2'),
	(3, 1, 'Место №3'),
	(4, 1, 'Место №4'),
	(5, 1, 'Место №5'),
	(6, 1, 'Место №6'),
	(7, 1, 'Место №7'),
	(8, 1, 'Место №8'),
	(9, 1, 'Место №9'),
	(10, 1, 'Место №10'),
	(11, 1, 'Место №11'),
	(12, 1, 'Место №12'),
	(13, 1, 'Место №13'),
	(14, 1, 'Место №14'),
	(15, 1, 'Место №15'),
	(16, 1, 'Место №16'),
	(17, 1, 'Место №17'),
	(18, 1, 'Место №18'),
	(19, 1, 'Место №19'),
	(20, 1, 'Место №20'),
	(21, 1, 'Место №21'),
	(22, 1, 'Место №22'),
	(23, 1, 'Место №23'),
	(24, 1, 'Место №24'),
	(25, 1, 'Место №25'),
	(26, 1, 'Место №26'),
	(27, 1, 'Место №27'),
	(28, 1, 'Место №28'),
	(29, 1, 'Место №29'),
	(30, 2, 'Место №1'),
	(31, 2, 'Место №2'),
	(32, 2, 'Место №3'),
	(33, 2, 'Место №4'),
	(34, 2, 'Место №5'),
	(35, 2, 'Место №6'),
	(36, 2, 'Место №7'),
	(37, 2, 'Место №8'),
	(38, 2, 'Место №9'),
	(39, 2, 'Место №10'),
	(40, 2, 'Место №11'),
	(41, 2, 'Место №12'),
	(42, 2, 'Место №13'),
	(43, 2, 'Место №14'),
	(44, 2, 'Место №15'),
	(45, 2, 'Место №16'),
	(46, 2, 'Место №17'),
	(47, 2, 'Место №18'),
	(48, 2, 'Место №19'),
	(49, 2, 'Место №20'),
	(50, 2, 'Место №21'),
	(51, 2, 'Место №22'),
	(52, 2, 'Место №23'),
	(53, 2, 'Место №24'),
	(54, 2, 'Место №25'),
	(55, 2, 'Место №26'),
	(56, 2, 'Место №27'),
	(57, 2, 'Место №28'),
	(58, 2, 'Место №29'),
	(59, 3, 'Место №1'),
	(60, 3, 'Место №2'),
	(61, 3, 'Место №3'),
	(62, 3, 'Место №4'),
	(63, 3, 'Место №5'),
	(64, 3, 'Место №6'),
	(65, 3, 'Место №7'),
	(66, 3, 'Место №8'),
	(67, 3, 'Место №9'),
	(68, 3, 'Место №10'),
	(69, 3, 'Место №11'),
	(70, 3, 'Место №12'),
	(71, 3, 'Место №13'),
	(72, 3, 'Место №14'),
	(73, 3, 'Место №15'),
	(74, 3, 'Место №16'),
	(75, 3, 'Место №17'),
	(76, 3, 'Место №18'),
	(77, 3, 'Место №19'),
	(78, 3, 'Место №20'),
	(79, 3, 'Место №21'),
	(80, 3, 'Место №22'),
	(81, 3, 'Место №23'),
	(82, 3, 'Место №24'),
	(83, 3, 'Место №25'),
	(84, 3, 'Место №26'),
	(85, 3, 'Место №27'),
	(86, 3, 'Место №28'),
	(87, 3, 'Место №29'),
	(88, 4, 'Место №1'),
	(89, 4, 'Место №2'),
	(90, 4, 'Место №3'),
	(91, 4, 'Место №4'),
	(92, 4, 'Место №5'),
	(93, 4, 'Место №6'),
	(94, 4, 'Место №7'),
	(95, 4, 'Место №8'),
	(96, 4, 'Место №9'),
	(97, 4, 'Место №10'),
	(98, 4, 'Место №11'),
	(99, 4, 'Место №12'),
	(100, 4, 'Место №13'),
	(101, 4, 'Место №14'),
	(102, 4, 'Место №15'),
	(103, 4, 'Место №16'),
	(104, 4, 'Место №17'),
	(105, 4, 'Место №18'),
	(106, 4, 'Место №19'),
	(107, 4, 'Место №20'),
	(108, 4, 'Место №21'),
	(109, 4, 'Место №22'),
	(110, 4, 'Место №23'),
	(111, 4, 'Место №24'),
	(112, 4, 'Место №25'),
	(113, 4, 'Место №26'),
	(114, 4, 'Место №27'),
	(115, 4, 'Место №28'),
	(116, 4, 'Место №29'),
	(117, 5, 'Место №1'),
	(118, 5, 'Место №2'),
	(119, 5, 'Место №3'),
	(120, 5, 'Место №4'),
	(121, 5, 'Место №5'),
	(122, 5, 'Место №6'),
	(123, 5, 'Место №7'),
	(124, 5, 'Место №8'),
	(125, 5, 'Место №9'),
	(126, 5, 'Место №10'),
	(127, 5, 'Место №11'),
	(128, 5, 'Место №12'),
	(129, 5, 'Место №13'),
	(130, 5, 'Место №14'),
	(131, 5, 'Место №15'),
	(132, 5, 'Место №16'),
	(133, 5, 'Место №17'),
	(134, 5, 'Место №18'),
	(135, 5, 'Место №19'),
	(136, 5, 'Место №20'),
	(137, 5, 'Место №21'),
	(138, 5, 'Место №22'),
	(139, 5, 'Место №23'),
	(140, 5, 'Место №24'),
	(141, 5, 'Место №25'),
	(142, 5, 'Место №26'),
	(143, 5, 'Место №27'),
	(144, 5, 'Место №28'),
	(145, 5, 'Место №29'),
	(146, 6, 'Место №1'),
	(147, 6, 'Место №2'),
	(148, 6, 'Место №3'),
	(149, 6, 'Место №4'),
	(150, 6, 'Место №5'),
	(151, 6, 'Место №6'),
	(152, 6, 'Место №7'),
	(153, 6, 'Место №8'),
	(154, 6, 'Место №9'),
	(155, 6, 'Место №10'),
	(156, 6, 'Место №11'),
	(157, 6, 'Место №12'),
	(158, 6, 'Место №13'),
	(159, 6, 'Место №14'),
	(160, 6, 'Место №15'),
	(161, 6, 'Место №16'),
	(162, 6, 'Место №17'),
	(163, 6, 'Место №18'),
	(164, 6, 'Место №19'),
	(165, 6, 'Место №20'),
	(166, 6, 'Место №21'),
	(167, 6, 'Место №22'),
	(168, 6, 'Место №23'),
	(169, 6, 'Место №24'),
	(170, 6, 'Место №25'),
	(171, 6, 'Место №26'),
	(172, 6, 'Место №27'),
	(173, 6, 'Место №28'),
	(174, 6, 'Место №29');
/*!40000 ALTER TABLE `places` ENABLE KEYS */;


-- Dumping structure for table testapi.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `hall_id` bigint(20) DEFAULT NULL,
  `film_id` bigint(20) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`hall_id`,`film_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table testapi.sessions: ~13 rows (approximately)
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` (`id`, `hall_id`, `film_id`, `date`) VALUES
	(1, 1, 1, '2014-04-30 13:50:00'),
	(2, 1, 1, '2014-05-01 12:00:00'),
	(3, 1, 1, '2014-05-02 12:00:00'),
	(4, 1, 1, '2014-05-02 19:00:00'),
	(5, 2, 2, '2014-05-01 12:00:00'),
	(6, 2, 2, '2014-05-02 12:00:00'),
	(7, 2, 2, '2014-05-02 19:00:00'),
	(8, 3, 3, '2014-05-01 12:00:00'),
	(9, 3, 3, '2014-05-02 12:00:00'),
	(10, 3, 3, '2014-05-02 19:00:00'),
	(11, 4, 1, '2014-05-01 12:00:00'),
	(12, 4, 1, '2014-05-02 12:00:00'),
	(13, 4, 1, '2014-05-02 19:00:00');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;


-- Dumping structure for table testapi.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `place_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`session_id`,`place_id`),
  KEY `idx2` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table testapi.tickets: ~2 rows (approximately)
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` (`id`, `order_id`, `session_id`, `place_id`) VALUES
	(4, 4, 1, 4),
	(5, 4, 1, 17);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
