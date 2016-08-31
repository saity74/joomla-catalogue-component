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
 * Table observer for dealing with Attributes
 * TODO: add new attributes from request
 *
 * @since  1.0
 */
class CatalogueTableObserverAttr extends JTableObserver
{
	/**
	 * Override for postStoreProcess param newAttr, Set by setNewAttr, used by onAfterStore and onBeforeStore
	 *
	 * @var    array
	 * @since  3.1.2
	 */
	protected $newAttr = false;

	/**
	 * Override for postStoreProcess param replaceAttr. Set by setNewAttr, used by onAfterStore
	 *
	 * @var    boolean
	 * @since  3.1.2
	 */
	protected $replaceAttr = true;

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 * Creates the associated tags helper class instance
	 * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
	 *
	 * @param   JObservableInterface  $observableObject  The subject object to be observed
	 * @param   array                 $params            ( 'typeAlias' => $typeAlias )
	 *
	 * @return  CatalogueTableObserverAttr
	 *
	 * @since   3.1.2
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);

		return $observer;
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
		if ($row['type_id'] === '1' && isset($row['custom_type_alias']))
		{
			$this->table->type_id = $row['custom_type_alias'];
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
		if ( ! is_numeric($this->table->type_id) )
		{
			$this->table->custom_type_alias = $this->table->type_id;
			$this->table->type_id = '1';
		}
		else
		{
			$this->table->custom_type_alias = '';
		}
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
	}
}
