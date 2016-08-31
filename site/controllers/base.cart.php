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
 * Controller tailored to suit most form-based admin operations.
 *
 * @since  12.2
 */
class CatalogueControllerBaseCart extends JControllerForm
{
	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   12.2
	 */
	public function save($key = 'id', $urlVar = 'cart_id')
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		/* @var $model CatalogueModelCart */
		$model = $this->getModel();
		$data  = $this->input->post->get('jform', [], 'array');
		$recordId = $this->input->getInt($urlVar);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Set redirect back to previous page.
		$this->goBack();

		// Attempt to save the data.
		return $model->save($data);
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   12.2
	 */
	public function edit($key = 'id', $urlVar = null)
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();

		$context = "$this->option.edit.$this->context";

		// Get the previous record id (if any) and the current record id.
		if (CatalogueCart::$id && CatalogueCart::$count)
		{
			$this->setRedirect(
				JRoute::_(
					CatalogueHelperRoute::getCartRoute()
				)
			);

			return true;
		}

		$this->goBack();

		return false;

	}

	/**
	 * Method to add to cart
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.0
	 */
	public function add()
	{
		$status = $this->save();

		if ($status !== false)
		{
			$msg = JText::_('COM_CATALOGUE_ADD_TO_CART_SUCCESS');
			$type = 'success';
		}
		else
		{
			$msg = JText::sprintf('COM_CATALOGUE_ADD_TO_CART_FAILED', $this->getModel()->getError());
			$type = 'error';
		}

		$this->setMessage($msg, $type);

		return $status;
	}

	/**
	 * Method to remove item from cart
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.0
	 */
	public function remove()
	{
		$input = $this->input->post;
		$data = $input->get('jform', [], 'array');

		if (!@is_array($data['items']))
		{
			return false;
		}

		foreach ($data['items'] as &$count)
		{
			$count = '-' . $count;
		}

		$input->set('jform', $data);

		$status = $this->save();

		if ($status !== false)
		{
			$msg = JText::_('COM_CATALOGUE_REMOVE_FROM_CART_SUCCESS');
			$type = 'success';
		}
		else
		{
			$msg = JText::sprintf('COM_CATALOGUE_REMOVE_FROM_CART_FAILED', $this->getModel()->getError());
			$type = 'error';
		}

		$this->setMessage($msg, $type);

		return $status;
	}

	/**
	 * Method to remove all items from cart
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.0
	 */
	public function clear()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Create data array with id only
		$data = ['id' => $this->input->getInt('cart_id')];

		// Set redirect to home page.
		$this->setRedirect(JUri::base());

		// Attempt to save the data.
		return $this->getModel()->clear($data);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	public function allowAdd($data = array())
	{
		return false;
	}

	/**
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return true;
	}

	/**
	 * Redirection to back page with message.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   string  $msg   Message text.
	 * @param   string  $type  Message type.
	 *
	 * @return  boolean
	 *
	 * @since   2.1
	 */
	protected function goBack($msg = null, $type = null)
	{
		$return = $this->input->server->get('HTTP_REFERER', JRoute::_('index.php'), 'string');
		$this->setRedirect($return, $msg, $type);
	}
}