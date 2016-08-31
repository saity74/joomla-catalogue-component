DROP TABLE IF EXISTS `#__catalogue_attr_value_text`;
DROP TABLE IF EXISTS `#__catalogue_attr_value_int`;
DROP TABLE IF EXISTS `#__catalogue_attr_value_float`;
DROP TABLE IF EXISTS `#__catalogue_attr_value_datetime`;
DROP TABLE IF EXISTS `#__catalogue_attr_value_bool`;
DROP TABLE IF EXISTS `#__catalogue_attr`;
DROP TABLE IF EXISTS `#__catalogue_attr_type`;
DROP TABLE IF EXISTS `#__catalogue_attr_group_category`;
DROP TABLE IF EXISTS `#__catalogue_attr_group`;
DROP TABLE IF EXISTS `#__catalogue_order`;
DROP TABLE IF EXISTS `#__catalogue_item_review`;
DROP TABLE IF EXISTS `#__catalogue_item`;
DROP TABLE IF EXISTS `#__catalogue_cart`;

CREATE TABLE `#__catalogue_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `track_id` varchar(32) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `state` tinyint(3) NOT NULL,
  `items` text NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_by_alias` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL,
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `attribs` text NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `access` int(10) unsigned NOT NULL,
  `xreference` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `track_id` (`track_id`),
  KEY `amount` (`amount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__catalogue_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) NOT NULL,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` varchar(5120) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `language` char(7) NOT NULL COMMENT 'The language code for the article.',
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `price` double NOT NULL,
  `item_sale` double NOT NULL,
  `rate` double NOT NULL,
  `votes_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `sticker` tinyint(4) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `similar_items` text NOT NULL,
  `assoc_items` text NOT NULL,
  `parent_id` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `path` varchar(400) NOT NULL,
  `version` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_catid` (`catid`),
  KEY `idx_access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE `#__catalogue_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `delivery_type` tinyint(3) unsigned NOT NULL,
  `payment_method` tinyint(3) unsigned NOT NULL,
  `state` tinyint(3) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL,
  `checked_out` int(10) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `client_mail` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__catalogue_attr_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID of an user group, that have access to this',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The language code for the attribute group.',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__catalogue_attr_group_category` (
  `group_id` int(10) unsigned NOT NULL,
  `cat_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`cat_id`),
  KEY `idx_group_id` (`group_id`),
  KEY `idx_cat_id` (`cat_id`),
  CONSTRAINT `#__catalogue_attr_group_category_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `#__catalogue_attr_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `#__catalogue_attr_group_category_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `#__categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__catalogue_attr_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `validation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__catalogue_attr_type` (`id`, `alias`, `validation`, `ordering`) VALUES
(1, 'custom', '', 0),
(2, 'text', '{}', 1),
(3, 'int', '{"regexp": "/^\\d+$/"}', 2),
(4, 'float', '{"regexp": "/^-?(?:\\d+|\\d*\\.\\d+)$/"}', 3),
(5, 'bool', '{"regexp": "/^[1,0]$/"}', 4),
(6, 'datetime', '{"regexp": "/^(((\\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\\d{4})(-)(0[469]|1‌​1)(-)(0[1-9]|[12][0-9]|30))|((\\d{4})(-)(02)(-)(0[1-9]|[12][0-9]|2[0-8]))|(([02468‌​][048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(‌​02)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(0‌​2)(-)(29)))(\\s)(([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/"}', 5);

CREATE TABLE `#__catalogue_attr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned DEFAULT NULL,
  `custom_type_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `validation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID of an user group, that have access to this',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The language code for the attribute group.',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_language` (`language`),
  KEY `type_id` (`type_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `#__catalogue_attr_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `#__catalogue_attr_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `#__catalogue_attr_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `#__catalogue_attr_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__catalogue_attr_value_bool` (
  `item_id` int(10) unsigned NOT NULL,
  `attr_id` int(10) unsigned NOT NULL,
  `value` tinyint(1) NOT NULL,
  PRIMARY KEY (`item_id`,`attr_id`),
  KEY `attr_id_to_attr_id_link` (`attr_id`),
  KEY `item_id_to_item_id_link` (`item_id`),
  CONSTRAINT `#__catalogue_attr_value_bool_ibfk_1` FOREIGN KEY (`attr_id`) REFERENCES `#__catalogue_attr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `#__catalogue_attr_value_bool_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `#__catalogue_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__catalogue_attr_value_datetime` (
  `item_id` int(10) unsigned NOT NULL,
  `attr_id` int(10) unsigned NOT NULL,
  `value` datetime NOT NULL,
  PRIMARY KEY (`item_id`,`attr_id`),
  KEY `attr_id_to_attr_id_link` (`attr_id`),
  KEY `item_id_to_item_id_link` (`item_id`),
  CONSTRAINT `#__catalogue_attr_value_datetime_ibfk_1` FOREIGN KEY (`attr_id`) REFERENCES `#__catalogue_attr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `#__catalogue_attr_value_datetime_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `#__catalogue_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__catalogue_attr_value_float` (
  `item_id` int(10) unsigned NOT NULL,
  `attr_id` int(10) unsigned NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`item_id`,`attr_id`),
  KEY `attr_id_to_attr_id_link` (`attr_id`),
  KEY `item_id_to_item_id_link` (`item_id`),
  CONSTRAINT `#__catalogue_attr_value_float_ibfk_1` FOREIGN KEY (`attr_id`) REFERENCES `#__catalogue_attr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `#__catalogue_attr_value_float_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `#__catalogue_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__catalogue_attr_value_int` (
  `item_id` int(10) unsigned NOT NULL,
  `attr_id` int(10) unsigned NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`item_id`,`attr_id`),
  KEY `attr_id_to_attr_id_link` (`attr_id`),
  KEY `item_id_to_item_id_link` (`item_id`),
  CONSTRAINT `#__catalogue_attr_value_int_ibfk_1` FOREIGN KEY (`attr_id`) REFERENCES `#__catalogue_attr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `#__catalogue_attr_value_int_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `#__catalogue_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `#__catalogue_attr_value_text` (
  `item_id` int(10) unsigned NOT NULL,
  `attr_id` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`item_id`,`attr_id`),
  KEY `attr_id_to_attr_id_link` (`attr_id`),
  KEY `item_id_to_item_id_link` (`item_id`),
  CONSTRAINT `#__catalogue_attr_value_text_ibfk_1` FOREIGN KEY (`attr_id`) REFERENCES `#__catalogue_attr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `#__catalogue_attr_value_text_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `#__catalogue_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__content_types` (`type_id`, `type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`,`content_history_options`) VALUES ('0', 'Catalogue Item', 'com_catalogue.item', '{"special":{"dbtable":"#__catalogue_item","key":"id","type":"Items","prefix":"CatalogueTable","config":"array()"}, "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Items","prefix":"CatalogueTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"introtext", "core_hits":"hits","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"attribs", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"urls", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"xreference", "asset_id":"asset_id"}, "special":{"fulltext":"fulltext", "parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","params":"params","path":"path"}}', 'CatalogueHelperRoute::getItemRoute', '{"formFile":"administrator\\/components\\/com_catalogue\\/models\\/forms\\/item.xml", "hideFields":["asset_id", "parent_id","checked_out","checked_out_time","version","lft","rgt","level","path"],"ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time", "version", "hits", "lft", "rgt", "level", "parent_id"],"convertToInt":["publish_up", "publish_down", "ordering"],"displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}');
INSERT INTO `#__catalogue_item` (`id`, `sku`, `asset_id`, `title`, `alias`, `introtext`, `fulltext`, `state`, `catid`, `created`,`created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`, `featured`, `language`, `xreference`, `price`, `item_sale`, `rate`, `votes_count`, `comments_count`, `sticker`, `params`, `similar_items`, `assoc_items`, `parent_id`, `lft`, `rgt`, `level`, `path`, `version`) VALUES (1, '0', 0, 'ROOT', 'root', '', '', 0, 0, '2016-01-20 20:05:17', 42, '', '2016-01-21 12:49:18', 0, 0, '0000-00-00 00:00:00', '2016-01-20 20:05:17', '0000-00-00 00:00:00', '{}', '{}', '', 1, '', '', 1, 0, '{}', 0, '*', '', 0, 0, 0, 0, 0, 0, '', '', '', 0, 0, 71, 0, '', 1);
