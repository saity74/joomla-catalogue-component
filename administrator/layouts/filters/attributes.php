<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use \Joomla\Registry\Registry;

extract($displayData);

$attr = !empty($class) ? ' class="' . $class . '"' : '';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';
$attr .= ' multiple';
$attr .= $required ? ' required aria-required="true"' : '';

if (is_array($value) && !empty($value))
{
	$data_attributes = $value;
}

if (!empty($attributes) && is_array($attributes))
{
	foreach ($attributes as &$field)
	{
		$field->data = isset($data_attributes[$field->group_id]) && isset($data_attributes[$field->group_id]->values) ? $data_attributes[$field->group_id]->values : [];

		if (!$field->params->get('filter_type'))
		{
			$html[] = '<h4>' . $field->title . '</h4>';

			if (isset($field->values) && is_array($field->values) && !empty($field->values))
			{

				foreach ($field->values as $input)
				{
					$val = '';
					if (isset($field->data[$input->id]) && isset($field->data[$input->id]->val))
					{
						$val = $field->data[$input->id]->val;
					}

					$html[] = '<div class="control-group">';
					$html[] = '<div class="control-label">';
					$html[] = $input->label;
					$html[] = '</div>';
					$html[] = '<div class="controls">';
					$html[] = '<input type="text" name="' . $name . '[text][' . $input->id . ']" value="' . $val . '" />';
					$html[] = '</div>';
					$html[] = '</div>';
				}
			}
			$html[] = '<hr/>';
			continue;
		}
		else
		{
			$field->type_alias = 'list';
		}

		$field_name = $name . '[' . $field->type_alias . ']';

		if ($field->params->get('multiple', 1))
		{
			$field_name .= '[]';
		}


		$html[] = '<div class="control-group">';

		$html[] = '<div class="control-label">';
		$html[] = $field->title;
		$html[] = '</div>';

		$html[] = '<div class="controls">';
		$html[] = JHtml::_('select.genericlist', $field->values, $field_name , trim($attr), 'id', 'label', $field->data);

		if ($field->params->get('multiple', 1))
		{
			// Dummy field to clear multiselect, if it is empty
			$html[] = '<input type="hidden" name="' . $field_name . '" value=""/>';
		}

		$html[] = '</div>';

		$html[] = '</div>';

	}

	echo implode($html);
}
else
{
	echo '<div class="alert alert-warning">' . JText::_('COM_CATALOGUE_NO_ATTRGROUPS_FOR_CATEGORY') . '</div>';
}
