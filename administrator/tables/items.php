<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * CatalogueTableItems class
 *
 * @since  11.1
 */
class CatalogueTableItems extends JTableNested
{
	public $attributes;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   JDatabaseDriver  &$_db  JDatabaseDriver object.
	 *
	 * @since   2.1
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__catalogue_item', 'id', $_db);

		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_catalogue.item'));
		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_catalogue.item'));

		JLoader::import('item', __DIR__ . '/observer');
		CatalogueTableObserverItem::createObserver($this);

		$this->access = (int) JFactory::getConfig()->get('access');
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   2.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_catalogue.item.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   2.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   2.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$assetId = null;

		// This is a item under a category.

		if ($this->catid)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true)
				->select($this->_db->quoteName('asset_id'))
				->from($this->_db->quoteName('#__categories'))
				->where($this->_db->quoteName('id') . ' = ' . (int) $this->catid);

			// Get the asset id from the database.
			$this->_db->setQuery($query);

			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $array   An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTable/bind
	 * @since   3.2
	 * @throws  UnexpectedValueException
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['itemtext']))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $array['itemtext']);

			if ($tagPos == 0)
			{
				$this->introtext = $array['itemtext'];
				$this->fulltext = '';
			}
			else
			{
				list($this->introtext, $this->fulltext) = preg_split($pattern, $array['itemtext'], 2);
			}
		}

		if (isset($array['attribs']) && is_array($array['attribs']))
		{
			$registry = new Registry($array['attribs']);
			$array['attribs'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new Registry($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (isset($array['similar_items']) && is_array($array['similar_items']))
		{
			$registry = new Registry($array['similar_items']);
			$array['similar_items'] = (string) $registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTable/store
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			// Existing item
			$this->modified = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New item. A item created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		return parent::store($updateNulls);
	}

	/**
	 * Gets the default asset values for a component.
	 *
	 * @param   $string  $component  The component asset name to search for
	 *
	 * @return  JAccessRules  The JAccessRules object for the asset
	 */
	protected function getDefaultAssetValues($component)
	{
		// Need to find the asset id by the name of the component.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__assets'))
			->where($db->quoteName('name') . ' = ' . $db->quote($component));
		$db->setQuery($query);
		$assetId = (int) $db->loadResult();

		return JAccess::getAssetRules($assetId);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true)
			->update($this->_db->quoteName($this->_tbl))
			->set($this->_db->quoteName('state') . ' = ' . (int) $state)
			->where('(' . $where . ')' . $checkin);
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		$this->setError('');

		return true;
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     JTable::check()
	 * @since   11.1
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('COM_CATALOGUE_WARNING_PROVIDE_VALID_NAME'));

			return false;
		}

		if (trim($this->alias) == '')
		{
			$this->alias = $this->title;
		}

		$this->alias = JApplicationHelper::stringURLSafe($this->alias);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		if (trim(str_replace('&nbsp;', '', $this->fulltext)) == '')
		{
			$this->fulltext = '';
		}

		/**
		 * Ensure any new items have compulsory fields set. This is needed for things like
		 * frontend editing where we don't show all the fields or using some kind of API
		 */
		if (!$this->id)
		{
			// Images can be an empty json string
			if (!isset($this->images))
			{
				$this->images = '{}';
			}

			// URLs can be an empty json string
			if (!isset($this->urls))
			{
				$this->urls = '{}';
			}

			// Attributes (article params) can be an empty json string
			if (!isset($this->attribs))
			{
				$this->attribs = '{}';
			}

			// Metadata can be an empty json string
			if (!isset($this->metadata))
			{
				$this->metadata = '{}';
			}

			// If we don't have any access rules set at this point just use an empty JAccessRules class
			if (!$this->getRules())
			{
				$rules = $this->getDefaultAssetValues('com_catalogue');
				$this->setRules($rules);
			}
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey))
		{
			// Only process if not empty

			// Array of characters to remove
			$bad_characters = array("\n", "\r", "\"", "<", ">");

			// Remove bad characters
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);

			// Create array using commas as delimiter
			$keys = explode(',', $after_clean);

			$clean_keys = array();

			foreach ($keys as $key)
			{
				if (trim($key))
				{
					// Ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			// Put array back together delimited by ", "
			$this->metakey = implode(", ", $clean_keys);
		}

		return true;
	}
}
