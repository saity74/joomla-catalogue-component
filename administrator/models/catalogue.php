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
 * CatalogueModelCatalogue model.
 *
 * @since  12.2
 */
class CatalogueModelCatalogue extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'c.id',
				'category_id', 'c.category_id',
				'item_name', 'c.item_name',
				'section_name', 's.section_name',
				'manufacturer_name', 'mf.manufacturer_name',
				'country_name', 'ct.country_name',
				'section_id', 'c.section_id',
				'manufacturer_id', 'c.manufacturer_id',
				'country_id', 'mf.country_id',
				'price', 'c.price',
				'published', 'c.published',
				'ordering', 'c.ordering',
				'created_by', 'c.created_by'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getTable($type = 'Catalogue', $prefix = 'CatalogueTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = null, $direction = null)
	{

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$manufacturerId = $this->getUserStateFromRequest($this->context . '.filter.manufacturer_id', 'filter_manufacturer_id', '');
		$this->setState('filter.manufacturer_id', $manufacturerId);

		$countryId = $this->getUserStateFromRequest($this->context . '.filter.country_id', 'filter_country_id', '');
		$this->setState('filter.country_id', $countryId);

		$id = $this->getUserStateFromRequest($this->context . '.item.id', 'id', 0, 'int');
		$this->setState('item.id', $id);

		$params = JComponentHelper::getParams('com_catalogue');
		$this->setState('params', $params);

		parent::populateState('c.item_name', 'asc');
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   12.2
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'c.*'
			)
		);
		$query->from($db->quoteName('#__catalogue_item') . ' AS c');

		// Join over the categories.
		$query->select('cat.title AS category_name')
			->join('LEFT', '#__categories AS cat ON cat.id = c.category_id');

		$query->select('mf.manufacturer_name')
			->join('LEFT', '#__catalogue_manufacturer AS mf ON mf.id = c.manufacturer_id');

		$query->select('ct.country_name')
			->join('LEFT', '#__catalogue_country AS ct ON ct.id = mf.country_id');

		$query->select('u.name AS username')
			->join('LEFT', '#__users AS u ON u.id = c.created_by');

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state))
		{
			$query->where('c.state = ' . (int) $state);
		}
		elseif ($state === '')
		{
			$query->where('(c.state IN (0, 1))');
		}

		// Filter by category.

		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId))
		{
			// Create a subquery for the subcategory list
			$subQuery = $db->getQuery(true)
				->select('sub.id')
				->from('#__categories as sub')
				->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt')
				->where('this.id = ' . (int) $categoryId);

			// Add the subquery to the main query
			$query->where('(c.category_id IN (' . $subQuery->__toString() . ') OR c.category_id = ' . (int) $categoryId . ')');
		}

		// Filter by manufacturer.
		$manufacturerId = $this->getState('filter.manufacturer_id');
		if (is_numeric($manufacturerId))
		{
			$query->where('c.manufacturer_id = ' . (int) $manufacturerId);
		}

		// Filter by country.
		$countryId = $this->getState('filter.country_id');
		if (is_numeric($countryId))
		{
			$query->where('mf.country_id = ' . (int) $countryId);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('c.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(c.published = 0 OR c.published = 1)');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('c.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(c.item_name LIKE ' . $search . ' OR c.item_description LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'ordering');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		if ($orderCol == 'ordering' || $orderCol == 'category_name')
		{
			$orderCol = 'c.item_name ' . $orderDirn . ', c.ordering';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.2
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.manufacturer_id');
		$id .= ':' . $this->getState('filter.country_id');

		return parent::getStoreId($id);
	}

}
