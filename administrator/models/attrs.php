<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Methods supporting a list of attr records.
 *
 * @since  1.6
 */
class CatalogueModelAttrs extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
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
				'group_id', 'a.group_id', 'group_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'published', 'a.published'
			);

			if (JLanguageAssociations::isEnabled())
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.id', $direction = 'desc')
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$groupId = $this->getUserStateFromRequest($this->context . '.filter.group_id', 'filter_group_id');
		$this->setState('filter.group_id', $groupId);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState($ordering, $direction);

		// Force a language
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.group_id');
		$id .= ':' . $this->getState('filter.type_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
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
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.type_id,
				 a.group_id, a.state, a.access, a.ordering, a.language, a.validation,
				 IF(at.id <> 1, at.alias, a.custom_type_alias) AS type_alias, at.validation AS type_validation'
			)
		);

		$query->from('#__catalogue_attr AS a');

		// Join over the attribute types
		$query->select(
			'IF(
				at.id <> 1,
				CONCAT("COM_CATALOGUE_ATTR_TYPE_", at.alias, "_TITLE"),
				CONCAT("COM_CATALOGUE_ATTR_TYPE_", a.custom_type_alias, "_TITLE")
			) AS type_title'
		)
			->join('LEFT', $db->quoteName('#__catalogue_attr_type') . ' AS at ON at.id = a.type_id');

		// Join over the attribute groups
		$query->select('g.title AS group_title')
			->join('LEFT', $db->quoteName('#__catalogue_attr_group') . ' AS g ON g.id = a.group_id');

		// Join over the language
		$query->select('l.title AS language_title, l.image AS language_image')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the associations.
		if (JLanguageAssociations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_catalogue.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id, l.title, l.image, uc.name, ag.title');
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state = 0 OR a.state = 1)');
		}

		// Filter by a single or set of groups.
		$groupId = $this->getState('filter.group_id');

		if ( is_numeric($groupId) )
		{
			$query->where('a.group_id = ' . (int) $groupId);
		}
		elseif ( is_array($groupId) )
		{
			JArrayHelper::toInteger($groupId);
			$query->where('a.group_id IN (' . implode(',', $groupId) . ')');
		}


		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'a.title ' . $orderDirn . ', a.ordering';
		}

		// SQL server change
		if ($orderCol == 'language')
		{
			$orderCol = 'l.title';
		}

		if ($orderCol == 'access_level')
		{
			$orderCol = 'ag.title';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
