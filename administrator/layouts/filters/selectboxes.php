<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

extract($displayData);

$attr = !empty($class) ? ' class="' . $class . '"' : '';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';
$attr .= ' multiple';
$attr .= $required ? ' required aria-required="true"' : '';
$attr .= $autofocus ? ' autofocus' : '';

$selected = $value;
$html = [];

if (!empty($filters) && is_array($filters))
{
	foreach ($filters as $filter)
	{
		$filter_params = new \Joomla\Registry\Registry($filter->params);

		if (!$filter_params->get('filter_type'))
		{
			continue;
		}

		$values = explode("\n", $filter->values);
		$options = [];
		foreach ($values as $value)
		{
			if (strpos($value, '::') === false)
			{
				continue;
			}

			@list($id, $text) = explode('::', $value);
			$options[$id] = $text;
		}

		$filter_name = $name . '[' . $filter->alias . ']';

		if ($filter_params->get('multiple'))
		{
			$filter_name .= '[]';
		}

		$filter_selected = isset($selected[$filter->alias]) ? $selected[$filter->alias] : 0;

		$html[] = '<fieldset>';

		$html[] = '<div class="control-group">';

		$html[] = '<div class="control-label">';
		$html[] = $filter->title;
		$html[] = '</div>';

		$html[] = '<div class="controls">';
		$html[] = JHtml::_('select.genericlist', $options, $filter_name , trim($attr), 'id', 'text', $filter_selected);
		$html[] = '</div>';

		$html[] = '</div>';

		$html[] = '</fieldset>';
	}

	echo implode($html);
}
else
{
//	TODO: dafaq?
//	echo '<div class="alert alert-warning">' . JText::_('COM_CATALOGUE_NO_ATTRGROUPS_FOR_CATEGORY') . '</div>';
}
