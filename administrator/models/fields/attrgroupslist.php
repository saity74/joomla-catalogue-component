<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * JFormFieldAttrGroupsList
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAttrGroupsList extends JFormFieldList
{

	protected static $options = array();

	protected $type = 'AttrGroupsList';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	public function getOptions()
	{
		$name = (string) $this->element['name'];
		$old_group = $this->form->getValue($name, 0);

		$options = JHtml::_('catalogueadministrator.attrgroupsoptions', $this->element, $old_group);

		return array_merge(parent::getOptions(), $options);
	}
}
