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
 * None
 */

JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal'));

?>
<label id="batch-language-lbl" for="batch-language-id" class="modalTooltip" title="<?php echo JHtml::_('tooltipText', 'JLIB_HTML_BATCH_LANGUAGE_LABEL', 'JLIB_HTML_BATCH_LANGUAGE_LABEL_DESC'); ?>">
	<?php echo JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL'); ?>
</label>
<select name="batch[language_id]" class="inputbox" id="batch-language-id">
	<option value=""><?php echo JText::_('JLIB_HTML_BATCH_LANGUAGE_NOCHANGE'); ?></option>
	<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text'); ?>
</select>
