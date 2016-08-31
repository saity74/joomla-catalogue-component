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

DELETE FROM `#__categories` WHERE `extension` = 'com_catalogue';
DELETE FROM `#__content_types` WHERE `type_alias` = 'com_catalogue.item';
