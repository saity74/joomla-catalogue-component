CREATE TABLE IF NOT EXISTS `#__catalogue_assoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `assoc_id` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_attr` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_attrdir` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_attrdir_category` (
  `attrdir_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `min_value` double NOT NULL,
  `max_value` double NOT NULL,
  PRIMARY KEY (`attrdir_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_attr_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `attr_image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_attr_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `attr_price` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_country` (
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

CREATE TABLE IF NOT EXISTS `#__catalogue_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `image_name` varchar(100) NOT NULL,
  `image_desc` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_item` (
    `id` int(10) unsigned NOT NULL,
    `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the j_assets table.',
    `title` varchar(255) NOT NULL DEFAULT '',
    `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `introtext` mediumtext NOT NULL,
    `fulltext` mediumtext NOT NULL,
    `techtext` text NOT NULL,
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
    `manufacturer_id` int(11) NOT NULL,
    `country_id` int(11) NOT NULL,
    `techs` text NOT NULL,
    `price` double NOT NULL,
    `item_sale` double NOT NULL,
    `rate` double NOT NULL,
    `votes_count` int(11) NOT NULL,
    `comments_count` int(11) NOT NULL,
    `sticker` tinyint(4) NOT NULL DEFAULT '0',
    `params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE #__catalogue_item`
    ADD PRIMARY KEY (`id`),
    ADD KEY `idx_access` (`access`),
    ADD KEY `idx_checkout` (`checked_out`),
    ADD KEY `idx_state` (`state`),
    ADD KEY `idx_catid` (`catid`),
    ADD KEY `idx_createdby` (`created_by`),
    ADD KEY `idx_language` (`language`),
    ADD KEY `idx_xreference` (`xreference`);

CREATE TABLE IF NOT EXISTS `#__catalogue_item_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_review_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `item_review_fio` varchar(255) NOT NULL,
  `item_review_rate` tinyint(5) NOT NULL,
  `item_review_text` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__catalogue_manufacturer` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;