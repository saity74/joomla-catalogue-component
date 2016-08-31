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
	JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
);
?>
<div class="control-group span6">
	<div class="controls">
		<label id="batch-choose-action-lbl" for="batch-choose-action">
			<?php echo JText::_('JLIB_HTML_BATCH_MENU_LABEL'); ?>
		</label>
		<div id="batch-choose-action" class="control-group">
			<select name="batch[group_id]" class="inputbox" id="batch-group-id">
				<option value=""><?php echo JText::_('COM_CATALOGUE_HTML_BATCH_NO_ATTRGROUP'); ?></option>
				<?php echo JHtml::_('select.options', JHtml::_('catalogueadministrator.attrgroupsoptions')); ?>
			</select>
		</div>
		<div id="batch-copy-move" class="control-group radio">
			<?php echo JText::_('JLIB_HTML_BATCH_MOVE_QUESTION'); ?>
			<?php echo JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
		</div>
	</div>
</div>
<div class="control-group span6">
	<div class="controls">
		<label id="batch-choose-action-lbl" for="batch-choose-action">
			<?php echo JText::_('COM_CATALOGUE_HEADING_ATTRTYPE'); ?>
		</label>
		<select name="batch[type_id]" class="inputbox" id="batch-type-id">
			<option value=""><?php echo JText::_('COM_CATALOGUE_HTML_BATCH_NO_ATTRTYPE'); ?></option>
			<?php echo JHtml::_('select.options', JHtml::_('catalogueadministrator.attrtypesoptions')); ?>
		</select>
	</div>
</div>