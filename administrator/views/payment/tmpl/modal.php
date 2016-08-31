<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');


JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$this->configFieldsets  = array('editorConfig');
$this->hiddenFieldsets  = array('basic-limited');
$this->ignore_fieldsets = array('jmetadata', 'item_associations');

// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;
$assoc = JLanguageAssociations::isEnabled();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$params = json_decode($params);

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "payment.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			' . $this->form->getField('description')->save() . '
			if (window.opener && (task == "payment.save" || task == "payment.cancel"))
			{
				window.opener.document.closeEditWindow = self;
				window.opener.setTimeout("window.document.closeEditWindow.close()", 1000);
			}

		Joomla.submitform(task, document.getElementById("item-form"));
		}
	};
');

?>

<div class="container-popup">

<div class="pull-right">
	<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('payment.apply');"><?php echo JText::_('JTOOLBAR_APPLY') ?></button>
	<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('payment.save');"><?php echo JText::_('JTOOLBAR_SAVE') ?></button>
	<button class="btn" type="button" onclick="Joomla.submitbutton('payment.cancel');"><?php echo JText::_('JCANCEL') ?></button>
</div>

<div class="clearfix"> </div>
<hr class="hr-condensed" />

<form action="<?php echo JRoute::_('index.php?option=com_catalogue&layout=modal&tmpl=component&id=' . (int) $this->item->id); ?>" method="post"
	  name="adminForm" id="item-form" class="form-validate">
<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_CATALOGUE_PAYMENT_GENERAL', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('description'); ?>
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				<fieldset class="form-vertical">
					<?php echo $this->form->renderField('id'); ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if (isset($assoc)) : ?>
			<div class="hidden"><?php echo $this->loadTemplate('associations'); ?></div>
		<?php endif; ?>

		<?php if ($this->canDo->get('core.admin')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_CATALOGUE_FIELDSET_RULES', true)); ?>
				<?php echo $this->form->getInput('rules'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
