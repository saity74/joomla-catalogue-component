
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `#__catalogue_assoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_assoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `assoc_id` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_attr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attrdir_id` int(11) NOT NULL,
  `alias` varchar(64) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `state` tinyint(3) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `attr_name` varchar(255) NOT NULL,
  `attr_type` enum('date','string','integer','real','list','bool') NOT NULL,
  `attr_description` varchar(255) NOT NULL,
  `attr_default` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_attr_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_attr_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `attr_image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_attr_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_attr_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `attr_price` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_attrdir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_attrdir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(32) NOT NULL,
  `reset_attr_name` varchar(255) NOT NULL,
  `dir_name` varchar(255) NOT NULL,
  `filter_type` enum('checkbox','radio','slider','range','input','listbox','none') NOT NULL,
  `filter_field` varchar(32) NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_attrdir_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_attrdir_category` (
  `attrdir_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `min_value` double NOT NULL,
  `max_value` double NOT NULL,
  PRIMARY KEY (`attrdir_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `country_image` varchar(255) NOT NULL,
  `country_description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `params` text NOT NULL,
  `metadata` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `image_name` varchar(100) NOT NULL,
  `image_desc` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `alias` varchar(45) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_art` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `item_sale` double NOT NULL,
  `sticker` tinyint(4) NOT NULL DEFAULT '0',
  `item_count` int(11) NOT NULL,
  `item_image` varchar(255) NOT NULL,
  `item_image_2` varchar(255) NOT NULL,
  `item_image_3` varchar(255) NOT NULL,
  `item_image_4` varchar(255) NOT NULL,
  `item_image_5` varchar(255) NOT NULL,
  `item_description` text NOT NULL,
  `introtext` text NOT NULL,
  `fulltext` text NOT NULL,
  `params` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `metadata` text NOT NULL,
  `item_shortdesc` text NOT NULL,
  `item_techdesc` text NOT NULL,
  `item_image_desc` varchar(255) NOT NULL,
  `item_image_desc_2` varchar(255) NOT NULL,
  `item_image_desc_3` varchar(255) NOT NULL,
  `item_image_desc_4` varchar(255) NOT NULL,
  `item_image_desc_5` varchar(255) NOT NULL,
  `techs` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_item_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_item_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_review_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `item_review_fio` varchar(255) NOT NULL,
  `item_review_rate` tinyint(5) NOT NULL,
  `item_review_text` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `#__catalogue_manufacturer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__catalogue_manufacturer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `manufacturer_name` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `manufacturer_image` varchar(255) NOT NULL,
  `manufacturer_description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `params` text NOT NULL,
  `metadata` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

