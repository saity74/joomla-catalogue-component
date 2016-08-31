<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once __DIR__ . '/attrvalue.base.php';

/**
 * CatalogueTableAttrValueDatetime class
 *
 * @since  11.1
 */
class CatalogueTableAttrValueDatetime extends JTable
{

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   JDatabaseDriver  &$_db  JDatabaseDriver object.
	 *
	 * @since   11.1
	 */
	public function __construct(&$_db)
	{
		$key = ['item_id', 'attr_id'];
		parent::__construct('#__catalogue_attr_value_datetime', $key, $_db);
	}
}
