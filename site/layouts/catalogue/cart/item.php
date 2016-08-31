<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var mixed $displayData */
$item = $displayData;
$params = JComponentHelper::getParams('com_catalogue');
$cart = CatalogueCart::getInstance();
?>

<div class="m-cart_item js-cart-page-product"
     data-id="<?php echo $item->id; ?>"
     data-title="<?php echo $item->title; ?>"
     data-sku="<?php echo $item->sku; ?>"
     data-price="<?php echo $item->price; ?>"
     data-count="<?php echo $cart->get($item->id, 1); ?>">
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="m-cart_item_info">
				<?php if ($params->get('cart_show_item_thumb', true)) : ?>
					<div class="m-cart_item_img">
						<?php echo JLayoutHelper::render('catalogue.cart.thumb', $item); ?>
					</div>
				<?php endif; ?>

				<div class="m-cart_item_desc">
					<div class="m-cart_item_desc_inner">
						<?php if ($params->get('cart_show_item_title', true)) : ?>
							<div class="m-cart_item_title">
								<span class="item-name"><?php echo $item->title; ?></span>
							</div>
						<?php endif; ?>

						<div class="m-cart_item_attrs">
							<?php if (isset($item->sku) && !empty($item->sku)) : ?>
								<div class="m-cart_item_attr">
									Артикул: <?php echo $item->sku; ?>
								</div>
							<?php endif; ?>

							<?php if ($attrs = $params->get('cart_show_custom_fields', [])) : ?>
								<?php foreach ($attrs as $attr_id): ?>
									<div class="m-cart_item_attr">
										<?php echo $item->attributes[$attr_id]->value; ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>

						<?php if ($params->get('cart_show_item_price', true)) : ?>
							<div class="m-cart_item_price">
								<?php echo JLayoutHelper::render('catalogue.item.price', $item); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="m-cart_item_controls">
				<?php if ($params->get('cart_show_item_count', true)) : ?>
					<div class="m-cart_item_controls_item">
						<div class="m-cart_item_count">
							<?php echo JLayoutHelper::render('catalogue.item.counter', $item); ?>
						</div>
					</div>
				<?php endif; ?>


				<?php if ($params->get('cart_show_item_amount', true)) : ?>
					<div class="m-cart_item_controls_item">
						<div class="m-cart_item_amount">
							<?php echo JLayoutHelper::render('catalogue.cart.item_amount', $item); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($params->get('cart_show_item_delete', true)) : ?>
					<div class="m-cart_item_controls_item">
						<div class="m-cart_item_remove">
							<form class="js-cart-page-product-remove-form" action="<?php echo JRoute::_('index.php'); ?>" method="post">
								<button type="submit" class="m-cart_item_remove_btn i-icon i-circle-cross js-cart-page-product-remove-btn"></button>
								<input type="hidden" name="task" value="cart.remove">
								<input type="hidden"
									   name="jform[items][<?php echo $item->id; ?>]"
									   value="<?php echo $cart->get($item->id, 1); ?>"
									   class="js-cart-page-product-count-input"
								/>
								<input type="hidden" name="option" value="com_catalogue">
								<input type="hidden" name="return" value="" />
								<?php echo JHtml::_('form.token'); ?>
							</form>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
