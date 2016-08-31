<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$params = $this->state->get('params');
$input_groups = [
	array('client_last_name', 'client_first_name'),
	array('client_phone', 'client_mail'),
	array('delivery_address'),
	array('comment')
];
?>

<div class="catalogue-order-default">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_title')); ?> </h1>
		</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php') ?>" class="m-form m-order-form js-form" id="orderForm" method="post" data-ajax="true" data-empty-cart="true" data-format="json">
		<div class="m-order-form_box -large-bottom-offset">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
					<div class="m-order-form_control">
						<div class="m-order-form_control_label">
							<?php echo $this->form->getLabel('delivery_type'); ?>
						</div>
						<div class="m-order-form_control_fields">
							<?php echo $this->form->getInput('delivery_type'); ?>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
					<div class="m-order-form_control">
						<div class="m-order-form_control_label">
							<?php echo $this->form->getLabel('payment_method'); ?>
						</div>
						<div class="m-order-form_control_fields">
							<?php echo $this->form->getInput('payment_method'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php foreach ($input_groups as $group): ?>
			<div class="m-order-form_box">
				<div class="row">
					<?php foreach ($group as $input): ?>
					<?php $col_size = 12 / count($group); ?>
					<div class="<?php echo 'col-xs-12 col-sm-'.$col_size.' col-md-'.$col_size.' col-lg-'.$col_size; ?>">
						<div class="m-order-form_control">
							<div class="m-order-form_control_label -with-top-offset">
								<?php echo $this->form->getLabel($input); ?>
							</div>
							<div class="m-order-form_control_fields">
								<?php echo $this->form->getInput($input); ?>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<input type="hidden" name="option" value="com_catalogue" />
		<input type="hidden" name="task" value="order.proceed" />

		<?php echo JHtml::_('form.token'); ?>

		<div class="m-order-form_submit m-form_submit">
			<button type="submit" class="m-order-form_submit_btn m-form_submit_btn e-btn -lg">
				<?php echo JText::_('COM_CATALOGUE_CART_BTN_CHECKOUT') ?>
			</button>
			<div class="m-form_loader"></div>
		</div>
	</form>
</div>
