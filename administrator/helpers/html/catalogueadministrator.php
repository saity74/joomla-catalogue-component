<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('CatalogueHelper', JPATH_ADMINISTRATOR . '/components/com_catalogue/helpers/_catalogue.php');

/**
 * Content HTML helper
 *
 * @since  3.0
 */
abstract class JHtmlCatalogueAdministrator
{
	/**
	 * Render the list of associated items
	 *
	 * @param   int     $record_id   The attribute group item id
	 * @param   string  $table_name  SQL table name
	 * @param   string  $context     Current context
	 *
	 * @return string The language HTML
	 *
	 * @throws Exception
	 */
	public static function association($record_id, $table_name, $context)
	{
		// Defaults
		$html = '';

		// Get the associations
		$associations = JLanguageAssociations::getAssociations(
			'com_catalogue',
			'#__catalogue_' . $table_name,
			'com_catalogue.' . $context,
			$record_id,
			'id',
			'alias',
			false
		);

		if ($associations)
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated menu items
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('t.*')
				->select('l.sef as lang_sef')
				->from('#__catalogue_' . $table_name . ' as t')
				->where('t.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON t.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500);
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text = strtoupper($item->lang_sef);
					$url = JRoute::_('index.php?option=com_catalogue&task=' . $context . '.edit&id=' . (int) $item->id);
					$tooltipParts = array(
						JHtml::_('image', 'mod_languages/' . $item->image . '.gif',
							$item->language_title,
							array('title' => $item->language_title),
							true
						),
						$item->title
					);

					$item->link = JHtml::_(
						'tooltip',
						implode(' ', $tooltipParts),
						null,
						null,
						$text,
						$url,
						null,
						'hasTooltip label label-association label-' . $item->lang_sef
					);
				}
			}

			$html = JLayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}

	/**
	 * Build Attribute Groups options for select input
	 *
	 * @param   mixed  $element    Form element
	 * @param   int    $old_group  Old group id
	 *
	 * @return mixed
	 */
	public static function AttrGroupsOptions($element, $old_group = 0)
	{
		$options = [];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (JLanguageAssociations::isEnabled())
		{
			$query->select('DISTINCT g.id As value, CONCAT(g.title, " (", g.language, ")") As text, g.state');
		}
		else
		{
			$query->select('DISTINCT g.id As value, g.title As text, g.state');
		}

		$query
			->from('#__catalogue_attr_group AS g')
			->order('g.title');

		// Filter language
		if (!empty($element['language']))
		{
			$query->where('g.language = ' . $db->quote($element['language']));
		}

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Get the current user object.
		$user = JFactory::getUser();

		// For new items we want a list of attribute groups you are allowed to create in.
		if ($old_group == 0)
		{
			foreach ($options as $i => $option)
			{
				/* To take save or create in a attribute group you need to have create rights for that attribute group.
				 * Unset the option if the user isn't authorised for it.
				 */
				if ($user->authorise('core.create', 'com_catalogue.attrgroup.' . $option->value) !== true)
				{
					unset($options[$i]);
				}
			}
		}

		return $options;
	}

	/**
	 * Build Attribute Types options for select input
	 *
	 * @return mixed
	 */
	public static function AttrTypesOptions()
	{
		$options = [];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('DISTINCT id As value, alias, CONCAT("COM_CATALOGUE_ATTR_TYPE_", alias, "_TITLE") As text')
			->from('#__catalogue_attr_type')
			->where('id <> 1')
			->order('ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList('alias');

			array_map(
				function($e)
				{
					$e->text = JText::_($e->text);

					return $e;
				},
				$options
			);
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Get custom attributes types from item XML
		$attribute_types = JForm::getInstance('com_catalogue', 'item')->getFieldset('attributes_types');

		foreach ($attribute_types as $type => $field)
		{
			// We already have standard types, so we do not need them
			if (array_key_exists($type, $options))
			{
				continue;
			}

			$options[$type] = (object) [
				'value' => $field->name,
				'alias' => $type,
				'text'  => $field->title
				//'text'  => JText::_("COM_CATALOGUE_ATTR_TYPE_" . $field->name . "_TITLE")
			];
		}

		return $options;
	}

	/**
	 * Build Payment Methods radio buttons
	 *
	 * @return mixed
	 */
	public static function PaymentMethodsRadios()
	{
		$options = [];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('DISTINCT id As value, title As text')
			->from('#__catalogue_payment_method')
			->order('ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}
}
