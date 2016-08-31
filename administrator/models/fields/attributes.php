<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use \Joomla\Registry\Registry;

/**
 * Catalogue filters list field
 *
 * @since  1.0
 */
class JFormFieldAttributes extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type = 'Attributes';

	protected $renderLayout = 'filters.attributes';

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
		case 'value':
			return $this->getValue();
		break;
		case 'attributes':
			return $this->getAttributes();
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
			case 'attributes':
				$this->attributes = $value;
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

	public function getValue()
	{
		if (!empty($this->value))
		{
			foreach ($this->value as $group_id => &$group)
			{
				$group->params = new Registry($group->params);

				if (isset($group->values) && !empty($group->values))
				{
					$values = explode("\n", $group->values);
					$group->values = [];
					foreach ($values as $field_value)
					{
						if (strpos($field_value, '::') == false)
						{
							continue;
						}

						$tmp = new stdClass;
						@list($tmp->id, $tmp->label, $tmp->val) = explode('::', $field_value);
						$group->values[$tmp->id] = $tmp;
					}
				}
			}

			unset($group);
		}

		return $this->value;
	}


	public function getAttributes()
	{
		$filters = null;

		$cid = $this->form->getData()->get('catid');
		$level = $this->form->getData()->get('level');

		if (!empty($cid))
		{
			$db = JFactory::getDbo();
			$db->setQuery('SET SESSION group_concat_max_len = 1000000')->execute();

			$query = $db->getQuery(true);
			$query->select('`ag`.`id` AS `group_id`, `ag`.`title`, `ag`.`params`, `ag`.`alias`,'
				. ' GROUP_CONCAT(DISTINCT `a`.`id`, \'::\' , `a`.`title` SEPARATOR \'\\n\') as `values`')
				->from($db->qn('#__catalogue_attr_group_category', 'agc'))
				->where($db->qn('agc.cat_id') . ' = ' . $db->q( (int) $cid))
				->join(
					'LEFT',
					$db->qn('#__catalogue_attr_group', 'ag') .
					' ON ' . $db->qn('ag.id') . ' = ' . $db->qn('agc.group_id')
				)
				->join(
					'LEFT',
					$db->qn('#__catalogue_attr', 'a') .
					' ON ' . $db->qn('a.group_id') . ' = ' . $db->qn('ag.id')
				)
				->where($db->qn('a.state') . ' = ' . $db->q(1))
				->where($db->qn('ag.state') . ' = ' . $db->q(1))
				->group('`ag`.`id`')
				->order($db->qn('ag.ordering') . ' ASC');

			$db->setQuery($query);

			$filters = $db->loadObjectList('group_id');

			foreach ($filters as $group_id => &$group)
			{
				$group->params = new Registry($group->params);

				if (isset($group->values) && !empty($group->values))
				{
					$values = explode("\n", $group->values);
					$group->values = [];

					foreach ($values as $field_value)
					{
						if (strpos($field_value, '::') == false)
						{
							continue;
						}

						$tmp = new stdClass;
						@list($tmp->id, $tmp->label) = explode('::', $field_value);
						$tmp->val = '';
						$group->values[$tmp->id] = $tmp;
					}
				}
			}

			unset($group);
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
			$this->attributes = $this->getAttributes();
			$this->value = $this->getValue();
		}

		return $return;
	}

	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$extData = array(
			'attributes'        => $this->attributes
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
