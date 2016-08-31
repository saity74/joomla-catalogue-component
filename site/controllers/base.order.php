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
 * Class CatalogueControllerOrder
 *
 * @since  1.0
 */
class CatalogueControllerBaseOrder extends JControllerForm
{
	/**
	 * Method to checkout record
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function checkout()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		if (!CatalogueCart::$isEmpty && CatalogueCart::$amount > 0)
		{
			$app = JFactory::getApplication('site');

			$model = $this->getModel();
			$data = [];

			if (CatalogueCart::$id)
			{
				$data['cart_id'] = CatalogueCart::$id;

				$order = $model->getItem(['cart_id' => CatalogueCart::$id]);

				if (isset($order->id))
				{
					$data['id'] = $order->id;
				}
			}

			// Set model state
			$model->setState('cart.id', CatalogueCart::$id);

			// Attempt to save the data.
			if (!$model->save($data))
			{
				// Redirect back to the edit screen.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
				$this->goBack();

				return false;
			}

			$context = "$this->option.edit.$this->context";

			$orderId = $model->getState('order.id');

			$table = $model->getTable();

			$checkin = property_exists($table, 'checked_out');

			// Attempt to check-out the new record for editing and redirect.
			if ($checkin && !$model->checkout($orderId))
			{
				// Check-out failed, display a notice but allow the user to see the record.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');
				$this->goBack();

				return false;
			}
			else
			{
				// Check-out succeeded, push the new record id into the session.
				$this->holdEditId($context, $orderId);
				$app->setUserState($context . '.data', null);

				$this->setRedirect(JRoute::_('index.php?option=com_catalogue'));

				return true;
			}
		}

		return false;
	}

	/**
	 * Proceed with the order
	 *
	 * @return bool
	 */
	public function proceed()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Check for request forgeries
		if (!JSession::checkToken())
		{
			$app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			$this->goBack();

			return;
		}

		$input = $this->input->post;
		$data = $input->get('jform', [], 'array');

		if (empty($data))
		{
			$app->enqueueMessage(JText::_('COM_CATALOGUE_ERROR_EMPTY_ORDER_INFO'), 'error');
			$this->goBack();

			return;
		}

		$catalogue_params = JComponentHelper::getParams('com_catalogue');
		$cart_model = JModelLegacy::getInstance('Cart', 'CatalogueModel');
		$cart_items = $cart_model->getItems();

		if (   !$this->sendOrderEmails($catalogue_params->toObject(), $cart_items, (object) $data)
			|| !$this->cartToOrder($data))
		{
			$error_msg = $params->get('order_fail_text', JText::_('COM_CATALOGUE_ORDER_FAIL_MSG'));
			$this->setMessage($error_msg, 'error');

			$this->goBack();

			return false;
		}

		$success_msg = $params->get('order_success_text', JText::_('COM_CATALOGUE_ORDER_SUCCESS_MSG'));
		$this->setMessage($success_msg, 'success');

		$redirect_page = JFactory::getApplication()->getMenu()->getItem($catalogue_params->get('order_redirect_success'))->route;
		$this->setRedirect($redirect_page);

		return true;
	}

	/**
	 * Add order information for current cart
	 *
	 * @param  array  $data  Data to save
	 *
	 * @return bool
	 */
	private function cartToOrder($data = array())
	{
		if (!CatalogueCart::$isEmpty && CatalogueCart::$amount > 0)
		{
			$model = $this->getModel();

			if (CatalogueCart::$id)
			{
				$data['cart_id'] = CatalogueCart::$id;
			}

			if (!array_key_exists('client_name', $data))
			{
				$data['client_name'] = '';

				if (array_key_exists('client_first_name', $data))
				{
					$data['client_name'] .= $data['client_first_name'] . ' ';
				}

				if (array_key_exists('client_last_name', $data))
				{
					$data['client_name'] .= $data['client_last_name'];
				}
			}

			// Attempt to save the data.
			if (!$model->save($data))
			{
				// Redirect back to the edit screen.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');

				return false;
			}

			// Remove cart info from cookies
			JFactory::getApplication('site')->input->cookie->set('_ti', null, time() - 1);

			return true;
		}

		return false;
	}

	/**
	 * Send emails to the client and manager with order details
	 *
	 * @param  object   $c_params    Catalogue params object
	 * @param  array    $items       Ordered items
	 * @param  object   $order_info  Information about order
	 *
	 * @return bool
	 */
	private function sendOrderEmails($c_params, $items, $order_info)
	{
		$app = JFactory::getApplication();
		$vars = [];

		$site_name = $app->get('sitename');

		$vars['{%site_name%}'] = $site_name;
		$vars['{%order_items%}'] = JLayoutHelper::render('catalogue.order.email.items', $items);
		$vars['{%user_info%}'] = JLayoutHelper::render('catalogue.order.email.client', $order_info);
		$vars['{%comment%}'] = $order_info->comment;

		$client_mail_html = strtr($c_params->order_text_client, $vars);
		$manager_mail_html = strtr($c_params->order_text_manager, $vars);

		$status = JFactory::getMailer()->sendMail(
			$app->get('mailfrom'),
			$app->get('fromname'),
			$order_info->client_mail,
			'Заказ на сайте ' . $site_name,
			$client_mail_html,
			true
		);

		if ($status !== true)
		{
			$app->enqueueMessage((string) $status, 'error');

			return false;
		}

		$status = JFactory::getMailer()->sendMail(
			$app->get('mailfrom'),
			$app->get('fromname'),
			$c_params->order_manager_address,
			'Заказ на сайте ' . $site_name,
			$manager_mail_html,
			true
		);

		if ($status !== true)
		{
			$app->enqueueMessage((string) $status, 'error');

			return false;
		}

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
