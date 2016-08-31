<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class Com_CatalogueInstallerScript
 *
 * @since  1.5
 */
class Com_CatalogueInstallerScript
{
	/**
	 * Method to install an extension.
	 *
	 * @return  void
	 */
	public function install()
	{
		// TODO: add Aggregion user group (ID MUST BE 1000!)

		// TODO: create /images/aggregion folder

		// TODO: INSERT INTO `#__content_types` (`type_id`, `type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`,`content_history_options`) VALUES ('0', 'Catalogue Item', 'com_catalogue.item', '{"special":{"dbtable":"#__catalogue_item","key":"id","type":"Items","prefix":"CatalogueTable","config":"array()"}, "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Items","prefix":"CatalogueTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"introtext", "core_hits":"hits","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"attribs", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"urls", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"xreference", "asset_id":"asset_id"}, "special":{"fulltext":"fulltext", "parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","params":"params","path":"path"}}', 'CatalogueHelperRoute::getItemRoute', '{"formFile":"administrator\\/components\\/com_catalogue\\/models\\/forms\\/item.xml", "hideFields":["asset_id", "parent_id","checked_out","checked_out_time","version","lft","rgt","level","path"],"ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time", "version", "hits", "lft", "rgt", "level", "parent_id"],"convertToInt":["publish_up", "publish_down", "ordering"],"displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}');

		// TODO: INSERT INTO `#__catalogue_item` (`id`, `sku`, `asset_id`, `title`, `alias`, `introtext`, `fulltext`, `state`, `catid`, `created`,`created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`, `featured`, `language`, `xreference`, `price`, `item_sale`, `rate`, `votes_count`, `comments_count`, `sticker`, `params`, `similar_items`, `assoc_items`, `parent_id`, `lft`, `rgt`, `level`, `path`, `version`) VALUES (1, '0', 0, 'ROOT', 'root', '', '', 0, 0, '2016-01-20 20:05:17', 42, '', '2016-01-21 12:49:18', 0, 0, '0000-00-00 00:00:00', '2016-01-20 20:05:17', '0000-00-00 00:00:00', '{}', '{}', '', 1, '', '', 1, 0, '{}', 0, '*', '', 0, 0, 0, 0, 0, 0, '', '', '', 0, 0, 71, 0, '', 1);

		// Create categories for our component
		$basePath = JPATH_ADMINISTRATOR . '/components/com_categories';

		/** @noinspection PhpIncludeInspection */
		require_once $basePath . '/models/category.php';
		$config = array('table_path' => $basePath . '/tables');
		$model = new CategoriesModelCategory($config);
		$data = array(
			'id' => 0,
			'parent_id' => 1,
			'level' => 1,
			'path' => 'uncategorised',
			'extension' => 'com_catalogue',
			'title' => 'Uncategorised',
			'alias' => 'uncategorised',
			'published' => 1,
			'language' => '*'
		);
		$status = $model->save($data);

		if (!$status)
		{
			JError::raiseWarning(500, JText::_('Unable to create default content category!'));
		}
	}

	/**
	 * method to uninstall the component
	 *
	 * @return  void
	 */
	public function uninstall()
	{
		echo '<p>' . JText::_('COM_CATALOGUE_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @param   object  $parent  Object
	 *
	 * @return  void
	 */
	public function update($parent)
	{
		echo '<p>' . JText::sprintf('COM_CATALOGUE_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
	}
}
