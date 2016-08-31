<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$form      = $displayData->getForm();

$fields = $displayData->get('fields') ?: [
	'price',
	'sale',
	'count',
	'sticker',
	'sku',
	['parent', 'parent_id'],
	['published', 'state', 'enabled'],
	['category', 'catid'],
	'access',
	'language',
	'tags',
	'note',
	'version_note'
];

$hiddenFields = $displayData->get('hidden_fields') ?: ['parent_id'];

$saveHistory = JComponentHelper::getParams('com_catalogue')->get('save_history', 0);

if (!$saveHistory)
{
	$hiddenFields[] = 'version_note';
}

$html   = array();
$html[] = '<fieldset class="form-vertical">';

foreach ($fields as $field)
{
	$field = is_array($field) ? $field : array($field);

	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			$html[] = $form->renderField($f);
			break;
		}
	}
}

$html[] = '</fieldset>';

echo implode('', $html);
