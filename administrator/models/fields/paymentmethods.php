<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('radio');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');

/**
 * JFormFieldPaymentMethods
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldPaymentMethods extends JFormFieldRadio
{

	protected static $options = array();

	protected $type = 'PaymentMethods';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	public function getOptions()
	{
		$options = JHtml::_('CatalogueAdministrator.PaymentMethodsRadios');

		return array_merge(parent::getOptions(), $options);
	}
}
