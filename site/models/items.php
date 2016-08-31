<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('CatalogueHelperItem', JPATH_ADMINISTRATOR . '/components/com_catalogue/helpers/item.php');

/**
 * Model class for handling lists of items.
 *
 * @since  12.2
 */
class CatalogueModelItems extends JModelList
{
	public $_context = 'com_catalogue.items';

	protected $_extension = 'com_catalogue';

	private $_items = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelList
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'itm.id',
				'title', 'itm.title',
				'price', 'itm.price',
				'alias', 'itm.alias',
				'state', 'itm.state',
				'ordering', 'itm.ordering'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Get all attributes with values
	 *
	 * @return bool|mixed
	 */
	public function getAttributes()
	{
		$db = $this->getDbo();
		$attr_query = '';

		$attr_types = ['text', 'int', 'float', 'bool', 'datetime'];

		// UNION select from all attribute tables for values
		foreach ($attr_types as $i => $type)
		{

			if ($i !== 0)
			{
				$attr_query .= ' UNION ';
			}

			$attr_query .= "SELECT a_$type.item_id, a_$type.value, aa.alias, aa.title, aa.id,
									aag.title as group_title, aag.alias as group_alias,
									aag.id as group_id
							FROM `#__catalogue_attr_value_$type` as a_$type
							LEFT JOIN `#__catalogue_attr` as aa ON a_$type.attr_id = aa.id
							LEFT JOIN `#__catalogue_attr_group` aag ON aa.group_id = aag.id
							WHERE aa.state = 1";
		}

		$db->setQuery($attr_query);

		try
		{
			return $db->loadObjectList();
		}
		catch (Exception $e)
		{
			echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br />';

			return false;
		}
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   12.2
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$categoryId = $this->getState('filter.category_id');

		$itemId = JFactory::getApplication()->input->get('Itemid', 0);

		if (is_numeric($categoryId))
		{
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'itm.catid ' . $type . (int) $categoryId;

			if ($includeSubcategories)
			{
				$levels = (int)$this->getState('filter.max_category_levels', '1');

				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true)
					->select('sub.id')
					->from('#__categories as sub')
					->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt')
					->where('this.id = ' . (int) $categoryId);

				if ($levels >= 0)
				{
					$subQuery->where('sub.level <= this.level + ' . $levels);
				}

				// Add the subquery to the main query
				$query->where('(' . $categoryEquals . ' OR itm.catid IN (' . $subQuery->__toString() . '))');

			}
			else
			{
				$query->where($categoryEquals);
			}
		}
		elseif (is_array($categoryId) && (count($categoryId) > 0))
		{
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);

			if (!empty($categoryId))
			{
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('itm.catid ' . $type . ' (' . $categoryId . ')');
			}
		}

		$params = $this->state->params;

		$params->get('catalogue_sort') == 1 ? $ordering = 'ordering' : $ordering = 'title';

		$query->select(
			$this->getState(
				'list.select',
				'itm.*, cat.title AS category_name, cat.description AS category_description'
			)
		)
			->from('`#__catalogue_item` AS `itm`')
			->join('LEFT', '`#__categories` as `cat` ON `itm`.`catid` = `cat`.`id`')
			->where('`itm`.`state` = 1 AND `itm`.`parent_id` = 1');

		// Price filter
		$price = $this->getState('filter.price');

		if (!empty($price))
		{
			if (strpos($price, '-') !== false)
			{
				@list($min, $max) = explode('-', $price);
				$query->where($this->_db->qn('itm.price') . ' BETWEEN ' . $this->_db->q((int)$min) . ' AND ' . $this->_db->q((int)$max));
			}
			else
			{
				$query->where($this->_db->qn('itm.price') . ' >= ' . $this->_db->q((int)$price));
			}
		}

		// Tags filter
		$tagIds = $this->getState('filter.tag', []);

		$query->select('`tagmap`.`tag_id`, `tags`.`title` AS `tag_title`, `tags`.`params` AS `tag_params`');

		if (is_array($tagIds) && !empty($tagIds))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' IN (' . implode(',', $tagIds) . ')');
		}

		$query->join(
			'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
			. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('itm.id')
			. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_catalogue.item')
		)->group('itm.id');

		$query->join('LEFT', $db->quoteName('#__tags', 'tags')
			. 'ON ' . $db->quoteName('tagmap.tag_id') . ' = ' . $db->quoteName('tags.id'));

		$filters = $this->getState('filter.' . $categoryId . ':' . $itemId);

		if (is_array($filters) && array_key_exists('sku', $filters))
		{
			$filter_sku = trim($filters['sku']);
			unset($filters['sku']);

			if (!empty($filter_sku))
			{
				$filter_sku = $db->quote('%' . str_replace(' ', '%', $db->escape($filter_sku, true) . '%'));
				$query->where('itm.sku LIKE ' . $filter_sku);
			}
		}

		if (is_array($filters) && !empty($filters))
		{
			$filters = array_filter(
				$filters,
				function ($val)
				{
					return !empty($val);
				}
			);

			// Filter number
			$fn = 1;

			foreach ($filters as $k => $filter_id)
			{
				$query->join('INNER',
					$db->qn('#__catalogue_attr_value_bool', 'eav' . $fn)
					. ' ON ' . $db->qn('eav' . $fn . '.item_id') . ' = ' . $db->qn('itm.id')
				);

				$where = "`eav$fn`.`attr_id` ";
				$where .= is_array($filter_id) ? 'IN (' . implode(',', $filter_id) . ')' : '= ' . $db->q((int) $filter_id);

				$query->where($where);

				$fn++;
			}
		}

		$query->order($this->getState('list.ordering', 'itm.ordering') . ' ' . $this->getState('list.direction', 'ASC'));

		return $query;
	}


	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItems()
	{
		JPluginHelper::importPlugin('content');

		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onCatalogueBeforeGetItems', array ('com_catalogue.category'));
		$items = parent::getItems();

		if (is_array($items) && !empty($items))
		{
			$attrs = $this->getAttributes();

			foreach ($items as $item)
			{
				CatalogueHelperItem::getTags($item);
				CatalogueHelperItem::decodeParams($item);

				$item->attributes = [];

				foreach ($attrs as $attr)
				{
					if ($item->id == $attr->item_id)
					{
						if ( !isset($item->attributes[$attr->group_alias]) )
						{
							$item->attributes[$attr->group_alias] = (object) [
								'title' => $attr->group_title,
								'attrs' => []
							];
						}

						$item->attributes[$attr->group_alias]->attrs[$attr->alias] = (object) [
							'title' => $attr->title,
							'value' => $attr->value
						];
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . serialize($this->getState('filter'));

		return parent::getStoreId($id);
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
		$app = JFactory::getApplication('site');

		// List state information
		$value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$orderCol = $app->input->get('filter_order', 'itm.ordering');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'itm.ordering';
		}

		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}

		$this->setState('list.direction', $listOrder);

		$catid = $app->input->getUInt('cid');
		$this->setState('filter.category_id', $catid);

		$id = $app->input->getUInt('id');
		$this->setState('item.id', $id);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}
}
