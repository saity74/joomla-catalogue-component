<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$form = $displayData->getForm();

echo $form->renderFieldset('imagemodal');
