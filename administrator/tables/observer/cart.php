<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Table class supporting modified pre-order tree traversal behavior.
 *
 * @link   https://docs.joomla.org/JTableObserver
 * @since  3.2
 */
class CatalogueTableCartObserver extends JTableObserver
{

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 *
	 * @param   JObservableInterface  $observableObject  The observable subject object
	 * @param   array                 $params            Params for this observer
	 *
	 * @return  JObserverInterface
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
	 * @return  bool
	 *
	 * @since   3.1.2
	 */
	public function onBeforeLoad($keys, $reset)
	{
		return true;
	}

	/**
	 * Pre-processor for $table->store($updateNulls)
	 *
	 * @param   boolean  $updateNulls  The result of the load
	 * @param   string   $tableKey     The key of the table
	 *
	 * @return  bool
	 *
	 * @since   3.1.2
	 */
	public function onBeforeStore($updateNulls, $tableKey)
	{
		$cartItems = json_decode($this->table->get('items'), true);

		if (!$cartItems)
		{
			return false;
		}

		$pk = array_keys($cartItems);

		if ($pk && !empty($pk))
		{
			$db = $this->table->getDbo();

			$query = $db->getQuery(true)
				->select('id, price')
				->from('#__catalogue_item')
				->where('id IN (' . implode(',', $pk) . ')');

			$prices = $db->setQuery($query)->loadObjectList('id');

			$amount = 0;
			array_map(
				function($count, $item) use (&$amount) {
					$amount += $item->price * (int) $count;
				},
				$cartItems, $prices
			);

			$this->table->set('amount', $amount);
		}

		return true;
	}
}
