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
 * CatalogueControllerItems
 *
 * Controller (controllers are where you put all the actual code) Provides basic
 * functionality, such as rendering views (aka displaying templates).
 *
 * @since  12.2
 */
class CatalogueControllerItems extends JControllerAdmin
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = 'Item', $prefix = 'CatalogueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	public function nested()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids = $this->input->get('cid', array(), 'array');

		if ( count($ids) > 0 )
		{
			JFactory::getApplication()->setUserState('com_catalogue.items.filter.parent_id', $ids[0]);
		}

		$this->setRedirect(JRoute::_('index.php?option=com_catalogue', false));

		return true;
	}

	/**
	 * Method to reset parent_id filter variable and display a view.
	 *
	 * @return void
	 */
	public function reset()
	{
		JFactory::getApplication()->setUserState('com_catalogue.items.filter.parent_id', '');

		$this->setRedirect(JRoute::_('index.php?option=com_catalogue', false));
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return  bool  False on failure or error, true on success.
	 *
	 * @since   2.1
	 */
	public function rebuild()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->setRedirect(JRoute::_('index.php?option=com_catalogue', false));

		/** @var CategoriesModelCategory $model */
		$model = $this->getModel();

		if ($model->rebuild())
		{
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_CATALOGUE_REBUILD_SUCCESS'));

			return true;
		}

		// Rebuild failed.
		$this->setMessage(JText::_('COM_CATALOGUE_REBUILD_FAILURE'), 'error');

		return false;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $ids    The array of ids for items being deleted.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
}
