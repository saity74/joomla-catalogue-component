<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Attrs list controller class.
 *
 * @since  1.6
 */
class CatalogueControllerAttrs extends JControllerAdmin
{

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Attr', $prefix = 'CatalogueModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
