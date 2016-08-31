<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Table observer for dealing with Attribute Groups to Categories mapping
 * TODO: add new groups from request
 *
 * @since  1.0
 */
class CatalogueTableObserverAttrgroup extends JTableObserver
{
	/**
	 * Override for postStoreProcess param newAttrGroups, Set by setNewAttrGroups, used by onAfterStore and onBeforeStore
	 *
	 * @var    array
	 * @since  3.1.2
	 */
	protected $newAttrGroups = false;

	/**
	 * Override for postStoreProcess param replaceAttrGroups. Set by setNewAttrGroups, used by onAfterStore
	 *
	 * @var    boolean
	 * @since  3.1.2
	 */
	protected $replaceAttrGroups = true;

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 * Creates the associated tags helper class instance
	 * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
	 *
	 * @param   JObservableInterface  $observableObject  The subject object to be observed
	 * @param   array                 $params            ( 'typeAlias' => $typeAlias )
	 *
	 * @return  CatalogueTableObserverAttrGroup
	 *
	 * @since   3.1.2
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);

		return $observer;
	}

	/**
	 * Pre-processor for $table->load($keys, $reset)
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function onBeforeLoad($keys, $reset)
	{
	}

	/**
	 * Post-processor for $table->load($keys, $reset)
	 *
	 * @param   boolean  &$result  The result of the load
	 * @param   array    $row      The loaded (and already binded to $this->table) row of the database table
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function onAfterLoad(&$result, $row)
	{
		if ($row['id'])
		{
			// Deleting old association for these items
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('cat_id')
				->from($db->qn('#__catalogue_attr_group_category'))
				->where($db->qn('group_id') . ' = ' . (int) $row['id']);
			$db->setQuery($query);

			try
			{
				$cat_ids = $db->loadColumn();
				$this->table->set('catid', (array) $cat_ids);
			}
			catch (Exception $e)
			{
				echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br />';
				$result = false;
			}
		}
	}

	/**
	 * Pre-processor for $table->store($updateNulls)
	 *
	 * @param   boolean  $updateNulls  The result of the load
	 * @param   string   $tableKey     The key of the table
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function onBeforeStore($updateNulls, $tableKey)
	{
	}

	/**
	 * Post-processor for $table->store($updateNulls)
	 *
	 * @param   boolean  &$result  The result of the load
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function onAfterStore(&$result)
	{
		if ($result === true)
		{
			$id = $this->table->get('id', false);
			$catids = $this->table->get('catid', false);

			if ( $id && $catids )
			{
				// Deleting old association for these items
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete($db->qn('#__catalogue_attr_group_category'))
					->where($db->qn('group_id') . ' = ' . (int) $id);
				$db->setQuery($query);
				$db->execute();

				if (is_array($catids))
				{
					$query = $db->getQuery(true)
						->insert('#__catalogue_attr_group_category');

					foreach ($catids as $cat_id)
					{
						$query->values($id . ',' . $cat_id);
					}

					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	/**
	 * Pre-processor for $table->delete($pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 * @throws  UnexpectedValueException
	 */
	public function onBeforeDelete($pk)
	{
		/*
		 * Due to MySQL schema there is no need to manual delete mapping records
		 * because of "ON DELETE CASCADE" they will be deleted by themselves.
		 */
	}
}
