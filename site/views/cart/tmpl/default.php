<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$params = $this->state->get('params');
$total = CatalogueCart::getInstance()->getProperty('total', 0)
?>

<div class="m-cart <?php if (!$total) echo '-empty'; ?>">
	<div class="m-cart_header">
		<div class="row">
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
				<?php if ($this->params->get('show_page_heading')) : ?>
					<h1 class="m-cart_title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
				<?php endif; ?>
			</div>

			<div class="col-xs-12 col-sm-9 col-md-9 col-lg-10">
				<?php if ($total = CatalogueCart::getInstance()->getProperty('total', 0)) : ?>
					<div class="m-cart_header_info">
						<div class="m-cart_header_info_col">
							<span class="m-cart_header_info_title">Изделий</span>
							<span class="m-cart_header_info_dash">-</span>
							<span class="m-cart_header_info_value js-cart-page-total"><?php echo $total ?></span>
						</div>


						<div class="m-cart_total m-cart_header_info_col">
							<span class="m-cart_header_info_title">На сумму</span>
							<span class="m-cart_header_info_dash">-</span>
							<span class="m-cart_header_info_value js-cart-page-amount">
								<?php echo number_format(CatalogueCart::getInstance()->getProperty('amount', 0), 0, '.', ' '); ?>
								<?php echo $params->get('catalogue_currency', 'руб.'); ?>
							</span>
						</div>

						<div class="m-cart_header_info_col">
							<?php echo JLayoutHelper::render('catalogue.cart.clear_btn'); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ($total) : ?>

	<div class="m-cart_items">
		<?php foreach ($this->items as $item) : ?>
			<?php echo JLayoutHelper::render('catalogue.cart.item', $item); ?>
		<?php endforeach; ?>
	</div>

	<div class="m-cart_order_btn_wrap">
		<a href="<?php echo JRoute::_(CatalogueHelperRoute::getOrderRoute()); ?>" class="m-cart_order_btn e-btn -lg">
			<?php echo JText::_('COM_CATALOGUE_CART_BTN_CHECKOUT') ?>
		</a>
	</div>
	<?php endif; ?>

	<div class="m-cart_empty_msg">
		<p><?php echo JText::_('COM_CATALOGUE_CART_EMPTY'); ?></p>
	</div>
</div>
