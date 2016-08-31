<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('CatalogueHelperItem', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/item.php');

require_once JPATH_COMPONENT . '/controllers/base.cart.php';

/**
 * Class CatalogueControllerCart
 *
 */
class CatalogueControllerCart extends CatalogueControllerBaseCart
{
	/**
	 * Execute a task by triggering a method in the derived class.
	 *
	 * @param   string  $task  The task to perform. If no matching task is found, the '__default' task is executed, if defined.
	 *
	 * @return  mixed   The value returned by the called method, false in error case.
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function execute($task)
	{
		header("Content-type: application/json; charset=UTF-8");

		try
		{
			$this->task = $task;

			$task = strtolower($task);

			if (isset($this->taskMap[$task]))
			{
				$doTask = $this->taskMap[$task];
			}
			elseif (isset($this->taskMap['__default']))
			{
				$doTask = $this->taskMap['__default'];
			}
			else
			{
				throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
			}

			// Record the actual task being fired
			$this->doTask = $doTask;

			echo $this->$doTask();
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}

		JFactory::getApplication()->close();
	}

	private static function buildItemForResponse($item, $count)
	{
		return (object) [
			'id' => $item->id,
			'title' => $item->title,
			'images' => $item->images,
			'price' => $item->price,
			'formatted_price' => CatalogueHelperItem::formatPrice($item->price),
			'sku' => $item->sku,
			'count' => $count
		];
	}

	/**
	 * Create response object
	 *
	 * @return object
	 */
	private function buildResposeJson()
	{
		/* @var $table CatalogueTableCart */
		$table = $this->getModel()->getTable();
		$recordId = $this->input->getInt('cart_id');
		$data  = $this->input->post->get('jform', array(), 'array');

		$table->load($recordId);

		$item_model = JModelLegacy::getInstance('Item', 'CatalogueModel');

		$response = new stdClass;

		// Added or removed items
		$response->modified = [];

		if (array_key_exists('items', $data) && is_array($data['items']))
		{
			foreach ($data['items'] as $item_id => $count)
			{
				$item = $item_model->getItem($item_id);

				$response->modified[] = self::buildItemForResponse($item, $count);
			}
		}

		// Items in cart
		$response->cart = new stdClass;
		$response->cart->items = [];

		$cart_items = json_decode($table->get('items', null), true) ?: [];

		foreach ($cart_items as $item_id => $count)
		{
			$item = $item_model->getItem($item_id);

			$response->cart->items[] = self::buildItemForResponse($item, $count);
		}

		$response->cart->total = $table->get('total', 0);
		$response->cart->amount = CatalogueHelperItem::formatPrice($table->get('amount', 0));

		return $response;
	}

	/**
	 * Method to add to cart
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @throws  Exception
	 * @since   1.0
	 */
	public function add()
	{
		if (parent::add())
		{
			$response = $this->buildResposeJson();
			$response->action = 'add';

			return new JResponseJson($response, $this->message);
		}
		else
		{
			throw new Exception($this->message);
		}
	}

	/**
	 * Method to remove item from cart
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @throws  Exception
	 * @since   12.2
	 */
	public function remove()
	{
		if (parent::remove())
		{
			$response = $this->buildResposeJson();
			$response->action = 'remove';

			return new JResponseJson($response, $this->message);
		}
		else
		{
			throw new Exception($this->message);
		}
	}
}