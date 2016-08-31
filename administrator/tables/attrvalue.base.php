<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * CatalogueTableAttr class
 *
 * @since  11.1
 */
class CatalogueTableAttrValue
{
	protected $int;

	protected $text;

	protected $float;

	protected $bool;

	protected $datetime;

	/**
	 * Method for create table object
	 *
	 * @param   String  $name  Type name
	 *
	 * @return mixed
	 */
	public function __get($name)
	{
		$prefix = 'CatalogueTable';
		$class = 'AttrValue' . ucfirst(strtolower($name));

		if (class_exists($class) && property_exists($this, $name))
		{
			if ( ! isset($this->$name) )
			{
				$this->$name = JTable::getInstance($name, $prefix);
			}

			return $this->$name;
		}

		throw new \InvalidArgumentException(sprintf('Argument %s produced an invalid class name: %s', $name, $class));
	}
}
