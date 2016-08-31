<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post">
	<button type="submit" class="m-cart_remove_all js-cart-page-remove-all i-icon i-circle-cross">Удалить все</button>
	<input type="hidden" name="task" value="cart.clear" />
	<input type="hidden" name="option" value="com_catalogue">

	<?php echo JHtml::_('form.token'); ?>
</form>