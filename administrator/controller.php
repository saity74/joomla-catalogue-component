<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class CatalogueController
 *
 * @since  Joomla 1.5
 */
class CatalogueController extends JControllerLegacy
{

	protected $default_view = 'catalogue';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{

		/** @noinspection PhpIncludeInspection */
		require_once JPATH_COMPONENT . '/helpers/catalogue.php';

		$view = $this->input->get('view', 'catalogue');
		$layout = $this->input->get('layout', 'default');
		$id = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'item' && $layout == 'edit' && !$this->checkEditId('com_catalogue.edit.item', $id))
		{
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_catalogue&view=catalogue', false));

			return false;
		}

		if ($view == 'category' && $layout == 'edit' && !$this->checkEditId('com_catalogue.edit.category', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_catalogue&view=categories', false));

			return false;
		}

		if ($view == 'country' && $layout == 'edit' && !$this->checkEditId('com_catalogue.edit.country', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_catalogue&view=countries', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
