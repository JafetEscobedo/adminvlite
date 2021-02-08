/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `adminvlite` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `adminvlite`;

CREATE TABLE IF NOT EXISTS `config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_business_name_uc` enum('y','n') COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_business_logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_business_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_created_at` timestamp NULL DEFAULT NULL,
  `config_updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` (`config_id`, `config_business_name`, `config_business_name_uc`, `config_business_logo`, `config_business_icon`, `config_created_at`, `config_updated_at`) VALUES
	(1, 'AdminvLite', 'n', '20210125164844_adminvlite_logo.png', '210125165930_adminvlite_icon.png', '2021-01-05 12:05:51', '2021-01-27 11:18:16');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_cost` decimal(10,2) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_brand` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_active` enum('y','n') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_created_at` timestamp NULL DEFAULT NULL,
  `item_updated_at` timestamp NULL DEFAULT NULL,
  `item_inactivated_at` timestamp NULL DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_code` (`item_code`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `fk_item_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `item` DISABLE KEYS */;
/*!40000 ALTER TABLE `item` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `item_history` (
  `item_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_history_stock_on_move` int(11) DEFAULT NULL,
  `item_history_new_stock` int(11) DEFAULT NULL,
  `item_history_cost` decimal(10,2) NOT NULL,
  `item_history_price` decimal(10,2) NOT NULL,
  `item_history_note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_history_created_at` timestamp NULL DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `item_history_event_id` int(11) NOT NULL,
  PRIMARY KEY (`item_history_id`),
  KEY `item_id` (`item_id`),
  KEY `item_history_event_id` (`item_history_event_id`) USING BTREE,
  CONSTRAINT `fk_item_history_item` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_item_history_item_history_event` FOREIGN KEY (`item_history_event_id`) REFERENCES `item_history_event` (`item_history_event_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `item_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_history` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `item_history_event` (
  `item_history_event_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_history_event_system` enum('y','n') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_history_event_type` enum('ingress','egress') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_history_event_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`item_history_event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `item_history_event` DISABLE KEYS */;
INSERT INTO `item_history_event` (`item_history_event_id`, `item_history_event_system`, `item_history_event_type`, `item_history_event_name`) VALUES
	(1, 'y', 'ingress', 'Entrada a inventario'),
	(2, 'y', 'ingress', 'Cancelación de venta'),
	(3, 'y', 'egress', 'Venta realizada'),
	(4, 'n', 'egress', 'Otro motivo'),
	(5, 'n', 'egress', 'Cancelación de compra'),
	(7, 'n', 'egress', 'Producto caducado');
/*!40000 ALTER TABLE `item_history_event` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `sale` (
  `sale_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_serial` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_canceled` enum('n','y') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_cancel_note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_created_at` timestamp NULL DEFAULT NULL,
  `sale_canceled_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`sale_id`) USING BTREE,
  KEY `fk_user` (`user_id`) USING BTREE,
  CONSTRAINT `fk_user_on_sale` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `sale` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `sale_detail` (
  `sale_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_detail_item_cost` decimal(10,2) NOT NULL,
  `sale_detail_item_price` decimal(10,2) NOT NULL,
  `sale_detail_stock_on_move` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`sale_detail_id`) USING BTREE,
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `sales_id` (`sale_id`) USING BTREE,
  CONSTRAINT `fk_item_id` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_sale_id` FOREIGN KEY (`sale_id`) REFERENCES `sale` (`sale_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `sale_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_detail` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `unit` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_singular_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_plural_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_active` enum('y','n') COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_created_at` timestamp NULL DEFAULT NULL,
  `unit_updated_at` timestamp NULL DEFAULT NULL,
  `unit_inactivated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`unit_id`),
  UNIQUE KEY `unit_singular_name` (`unit_singular_name`) USING BTREE,
  UNIQUE KEY `unit_plural_name` (`unit_plural_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `unit` DISABLE KEYS */;
/*!40000 ALTER TABLE `unit` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_surname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_active` enum('y','n') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_created_at` timestamp NULL DEFAULT NULL,
  `user_updated_at` timestamp NULL DEFAULT NULL,
  `user_inactivated_at` timestamp NULL DEFAULT NULL,
  `user_role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_nick` (`user_nickname`) USING BTREE,
  KEY `user_role_id` (`user_role_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`user_role_id`) REFERENCES `user_role` (`user_role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `user_nickname`, `user_password`, `user_name`, `user_surname`, `user_active`, `user_created_at`, `user_updated_at`, `user_inactivated_at`, `user_role_id`) VALUES
	(1, 'admin', '$2y$10$Z//213Tw.6y.uAjNFBQd9uykOdlHClFu.G82ivcbI7EmY7lzKTdNq', 'Administrador', 'Principal', 'y', '2020-12-27 11:52:53', '2020-12-28 10:06:02', NULL, 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `user_access` (
  `user_access_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_access_first` timestamp NULL DEFAULT NULL,
  `user_access_last` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`user_access_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `user_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_access` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `user_role` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role_access` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role_active` enum('y','n') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role_created_at` timestamp NULL DEFAULT NULL,
  `user_role_updated_at` timestamp NULL DEFAULT NULL,
  `user_role_inactivated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `user_role_name` (`user_role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` (`user_role_id`, `user_role_name`, `user_role_access`, `user_role_active`, `user_role_created_at`, `user_role_updated_at`, `user_role_inactivated_at`) VALUES
	(1, 'Administrador', '["sale","sale\\/view\\/create","sale\\/view\\/cancel","sale\\/view\\/sales-list","item-history","item-history\\/view\\/ingress","item-history\\/view\\/egress","item-history\\/view\\/inventory","item","item\\/view\\/create","item\\/view\\/items-list","unit","unit\\/view\\/create","unit\\/view\\/units-list","user","user\\/view\\/create","user\\/view\\/users-list","user-access","user-access\\/view\\/user-access-list","user-role","user-role\\/view\\/create","user-role\\/view\\/user-roles-list","config","config\\/view\\/business-name","config\\/view\\/business-img"]', 'y', '2020-12-27 10:41:12', '2021-01-27 11:20:26', NULL);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
