<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Layout variables
 * ---------------------
 *
 * @var  string   $extension The extension name
 */

extract($displayData);

// Create the copy/move options.
$options = array(
	JHtml::_('select.option', 'a', JText::_('COM_CATALOGUE_BATCH_ADD_CATEGORY')),
	JHtml::_('select.option', 'r', JText::_('COM_CATALOGUE_BATCH_REMOVE_CATEGORY'))
);
?>
<label id="batch-choose-action-lbl" for="batch-choose-action">
	<?php echo JText::_('COM_CATALOGUE_BATCH_CATEGORY_LABEL'); ?>
</label>
<div id="batch-choose-action" class="control-group">
	<select name="batch[category_id]" class="inputbox" id="batch-category-id">
		<option value=""><?php echo JText::_('COM_CATALOGUE_BATCH_CATEGORY_NOTHING'); ?></option>
		<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_catalogue')); ?>
	</select>
</div>
<div id="batch-copy-move" class="control-group radio">
	<?php echo JText::_('COM_CATALOGUE_BATCH_CATEGORY_QUESTION'); ?>
	<?php echo JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'a'); ?>
</div>
