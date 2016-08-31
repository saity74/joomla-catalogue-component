<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::register('CatalogueHelperItem', JPATH_ADMINISTRATOR . '/components/com_catalogue/helpers/item.php');

/**
 * Model class for handling lists of items.
 *
 * @since  12.2
 */
class CatalogueModelItem extends JModelItem
{
	public $_context = 'com_catalogue.item';

	protected $_extension = 'com_catalogue';

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $pk  The id of the article.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$user = JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('item.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select(
						$this->getState(
							'item.select', 'i.id, i.sku, i.asset_id, i.catid, i.title, i.alias, i.price, i.introtext, i.fulltext, ' .
							// If badcats is not null, this means that the article is inside an unpublished category
							// In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
							'CASE WHEN badcats.id is null THEN i.state ELSE 0 END AS state, ' .
							'i.catid, i.created, i.created_by, i.created_by_alias, ' .
							// Use created if modified is 0
							'CASE WHEN i.modified = ' . $db->quote($db->getNullDate()) . ' THEN i.created ELSE i.modified END as modified, ' .
							'i.modified_by, i.checked_out, i.checked_out_time, i.publish_up, i.publish_down, ' .
							'i.images, i.urls, i.attribs, i.version, i.ordering, ' .
							'i.metakey, i.metadesc, i.access, i.hits, i.metadata, i.language, i.xreference'
						)
					);
				$query->from('#__catalogue_item AS i');

				// Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
					->join('LEFT', '#__categories AS c on c.id = i.catid');

				// Join on user table.
				$query->select('u.name AS author')
					->join('LEFT', '#__users AS u on u.id = i.created_by');

				// Filter by language
				if ($this->getState('filter.language'))
				{
					$query->where('i.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}

				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias')
					->join('LEFT', '#__categories as parent ON parent.id = c.parent_id')

					->where('i.id = ' . (int) $pk);

				if ((!$user->authorise('core.edit.state', 'com_catalogue')) && (!$user->authorise('core.edit', 'com_catalogue')))
				{
					// Filter by start and end dates.
					$nullDate = $db->quote($db->getNullDate());
					$date = JFactory::getDate();

					$nowDate = $db->quote($date->toSql());

					$query->where('(i.publish_up = ' . $nullDate . ' OR i.publish_up <= ' . $nowDate . ')')
						->where('(i.publish_down = ' . $nullDate . ' OR i.publish_down >= ' . $nowDate . ')');
				}

				// Join to check for category published state in parent categories up the tree
				// If all categories are published, badcats.id will be null, and we just use the article state
				$subquery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
				$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
				$subquery .= 'WHERE parent.extension = ' . $db->quote('com_catalogue');
				$subquery .= ' AND parent.published <= 0 GROUP BY cat.id)';
				$query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');

				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');

				if (is_numeric($published))
				{
					$query->where('(i.state = ' . (int) $published . ' OR i.state =' . (int) $archived . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data))
				{
					return JError::raiseError(404, JText::_('COM_CATALOGUE_ERROR_ITEM_NOT_FOUND'));
				}

				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived)))
				{
					return JError::raiseError(404, JText::_('COM_CATALOGUE_ERROR_ITEM_NOT_FOUND'));
				}

				// Convert parameter fields to objects.
				$registry = new Registry($data->attribs);
				$data->params = clone $this->getState('params');
				$data->params->merge($registry);

				$data->attributes = CatalogueHelperAttributes::getAttributes($data->id, $data->catid);

				CatalogueHelperItem::decodeParams($data);

				if (!empty($data->id))
				{
					$data->tags = new JHelperTags;
					$data->tags->getTagIds($data->id, 'com_catalogue.item');
				}

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}


	/**
	 * Increment the hit counter for the article.
	 *
	 * @param   integer  $pk  Optional primary key of the article to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('item.id');

			$table = JTable::getInstance('Items', 'CatalogueTable');

			// Set attributes to avoid double query
			$table->set('attributes', []);
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}

	public function getChildren()
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('item.id');

		$table = $this->getTable('Items', 'CatalogueTable');
		$children = $table->getTree($pk);

		return $children;
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
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		$id = $app->input->getUInt('id');
		$this->setState('item.id', $id);

		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_catalogue')) && (!$user->authorise('core.edit', 'com_catalogue')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		// Load the parameters. Merge Global and Menu Item params into new object
		$params = JComponentHelper::getParams('com_catalogue');
		$menuParams = new Registry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		$this->setState('params', $mergedParams);

	}
}
