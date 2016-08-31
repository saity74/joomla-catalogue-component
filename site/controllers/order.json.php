<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controllers/base.order.php';

/**
 * Json Controller for validate order fields.
 *
 * @since  12.2
 */
class CatalogueControllerOrder extends CatalogueControllerBaseOrder
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

	/**
	 * Proceed with the order
	 */
	public function proceed()
	{
		if (parent::proceed())
		{
			return new JResponseJson([], $this->message);
		}
		else
		{
			throw new Exception($this->message);
		}
	}
}
