<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Catalogue filters list field
 *
 * @since  1.0
 */
class JFormFieldFilters extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type = 'Filters';

	protected $renderLayout = 'filters.selectboxes';

	public $filters;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'filters':
				return $this->getFilters();
		}

		return parent::__get($name);
	}


	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'filters':
				$this->filters = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   1.0
	 */
	public function getLabel()
	{
		return false;
	}


	public function getFilters()
	{
		$filters = null;

		$cid = $this->form->getData()->get('request.cid');

		if (!$cid)
		{
			$cid = $this->form->getData()->get('catid');
		}

		if (!empty($cid))
		{
			$db = JFactory::getDbo();

			$query = $db->getQuery(true);

			$query->select('`agcmap`.`group_id`, `ag`.`title`, `ag`.`params`, `ag`.`alias` ')
				->from($db->qn('#__catalogue_attr_group_category', 'agcmap'))
				->where($db->qn('cat_id') . ' = ' . $db->q( (int) $cid))
				->group('group_id')
				->join('LEFT',  $db->qn('#__catalogue_attr_group', 'ag')
					. ' ON ' . $db->qn('ag.id') . ' = ' . $db->qn('agcmap.group_id'))
				->select('GROUP_CONCAT(`a`.`id`, \'::\' , `a`.`title` SEPARATOR \'\\n\') as `values`')
				->join('LEFT',  $db->qn('#__catalogue_attr', 'a')
					. ' ON ' . $db->qn('a.group_id') . ' = ' . $db->qn('ag.id'))
				->where('`a`.`state` = 1 AND `ag`.`state` = 1')
				->order($db->qn('ag.ordering') . 'ASC');

			$db->setQuery($query);

			$filters = $db->loadObjectList();
		}

		return $filters;
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->filters = $this->getFilters();
		}

		return $return;
	}

	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$extData = array(
			'filters'        => $this->filters
		);

		return array_merge($data, $extData);
	}

	/**
	 * Method to get a control group with label and input.
	 *
	 * @param   array  $options  Options to be passed into the rendering of the field
	 *
	 * @return  string  A string containing the html for the control group
	 *
	 * @since   3.2
	 */
	public function renderField($options = array())
	{

		return $this->getRenderer($this->renderLayout)->render($this->getLayoutData());
	}

	/**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
	protected function getLayoutPaths()
	{
		return array(
			JPATH_ADMINISTRATOR . '/components/com_catalogue/layouts'
		);
	}
}
