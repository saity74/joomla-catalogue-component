<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var mixed $displayData */
$item = $displayData;

$cart = CatalogueCart::getInstance();

?>

<form class="js-cart-page-product-count-form" action="<?php echo JRoute::_('index.php'); ?>" method="post">
	<div class="m-cart_item_count_inc">
		<button class="m-cart_item_count_inc_icon js-cart-page-product-count-inc" type="submit" name="jform[items][<?php echo $item->id; ?>]" value="1">plus</button>
	</div>
	<div class="m-cart_item_count_value">
		<span class="js-cart-item-count-text js-cart-page-product-count"><?php echo $cart->get($item->id, 1); ?></span> шт.
	</div>
	<div class="m-cart_item_count_dec">
		<button class="m-cart_item_count_dec_icon js-cart-page-product-count-dec" type="submit" name="jform[items][<?php echo $item->id; ?>]" value="-1">minus</button>
	</div>


	<input type="hidden" name="task" value="cart.save" />
	<input type="hidden" name="option" value="com_catalogue">
	<?php echo JHtml::_('form.token'); ?>
</form>
