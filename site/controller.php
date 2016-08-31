<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once(dirname(__FILE__) . DS . 'helper.php');

/**
 * Class CatalogueController
 */
class CatalogueController extends JControllerLegacy
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   12.2
	 */
	public function __construct(array $config)
	{

		$this->input = JFactory::getApplication('site')->input;

		parent::__construct($config);
	}

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

	public function display($cachable = false, $urlparams = array())
	{
		$vName = $this->input->get('view');

		if ($vName == 'order' && CatalogueCart::$isEmpty)
		{
			$this->setRedirect(JRoute::_(CatalogueHelperRoute::getCartRoute()), false);
		}

		parent::display();

		return $this;
	}
}
