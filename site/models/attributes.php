<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Catalogue Component Attributes Model
 *
 * @since  1.0
 */
class CatalogueModelAttributes extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'type_id', 'a.type_id', 'type_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Get the master query for retrieving a list of articles subject to the model state.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select',
					'a.id, a.title, a.alias, a.description, a.params,
					 a.state, a.checked_out_time, a.ordering, a.access,
					 a.language'
				)
			)
			->from('#__catalogue_attr AS a');

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		// Add the list ordering clause.
		$query->order($this->getState('list.ordering', 'a.ordering') . ' ' . $this->getState('list.direction', 'ASC'));

		return $query;
	}

	/**
	 * Method to get a list of attributes.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		$items = parent::getItems();

		$attributes = [];

		foreach ($items as $item)
		{
			$attributes[$item->id] = $item;
		}

		return $attributes;
	}
}
