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
 * CatalogueTableAttrGroup class
 *
 * @since  11.1
 */
class CatalogueTablePayment extends JTable
{
	/**
	 * Ensure the params in json encoded in the bind method
	 *
	 * @var    array
	 * @since  3.3
	 */
	protected $_jsonEncode = ['attribs'];

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
		parent::__construct('#__catalogue_payment_method', 'id', $_db);

		$this->setColumnAlias('published', 'state');
	}
}
