<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * CatalogueHelperItem
 *
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @since       3.0
 */
class CatalogueHelperItem
{
	/**
	 * Decodes params, encoded with Registry
	 *
	 * @param $item Catalogue item
	 *
	 * @return void
	 */
	public static function decodeParams(&$item)
	{
		$encoded_params = ['attribs', 'metadata', 'images', 'similar_items', 'assoc_items'];

		if (property_exists($item, 'images') && strpos($item->images, '#IMAGESPATH#'))
		{
			$corrected_path = str_replace('#IMAGESPATH#', '\/images\/' . $item->id, $item->images);
			$item->images = $corrected_path;
		}

		foreach ($encoded_params as $param)
		{
			if (property_exists($item, $param))
			{
				$registry = new Registry;
				$registry->loadString($item->{$param});
				$item->{$param} = $registry->toArray();
			}
		}

		if (property_exists($item, 'images') )
		{
			foreach ($item->images as &$image)
			{
				$image['url'] = $image['name'];
				$image['dir'] = dirname($image['name']);
				$image['name'] = basename($image['name']);
			}
		}
	}

	public static function getTags(&$item)
	{
		$tags_helper = new JHelperTags;
		$tag_encoded_params = ['params', 'metadata', 'images', 'urls'];

		$item->tags = $tags_helper->getItemTags('com_catalogue.item', $item->id);

		// Decode tags params
		foreach ($item->tags as $tag)
		{
			foreach ($tag_encoded_params as $param)
			{
				if (property_exists($tag, $param))
				{
					$registry = new Registry;
					$registry->loadString($tag->{$param});
					$tag->{$param} = $registry->toArray();
				}
			}
		}
	}

	public static function formatPrice($price)
	{
		$currency = JFactory::getApplication()->getParams('com_catalogue')->get('catalogue_currency', 'руб.');
		$formatted_price = number_format($price, 0, '.', ' ');

		return "$formatted_price $currency";
	}
}