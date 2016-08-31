<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

$params = JComponentHelper::getParams('com_catalogue');

/** @var mixed $displayData */
$attributes = $displayData[0];
?>

<?php if ($params->get('cart_show_item_thumb', true)) : ?>
	<td class="m-cart_heading_label">
		<?php echo JText::_('COM_CATALOGUE_CART_HEAD_PHOTO'); ?>
	</td>
<?php endif; ?>

<?php if ($params->get('cart_show_item_title', true)) : ?>
	<td class="m-cart_heading_label">
		<?php echo JText::_('COM_CATALOGUE_CART_HEAD_TITLE'); ?>
	</td>
<?php endif; ?>

<?php if ($params->get('cart_show_item_count', true)) : ?>
	<td class="m-cart_heading_label">
		<?php echo JText::_('COM_CATALOGUE_CART_HEAD_COUNT'); ?>
	</td>
<?php endif; ?>

<?php if ($params->get('cart_show_item_price', true)) : ?>
	<td class="m-cart_heading_label">
		<?php echo JText::_('COM_CATALOGUE_CART_HEAD_PRICE'); ?>
	</td>
<?php endif; ?>

<?php if ($attrs = $params->get('cart_show_custom_fields', [])) : ?>
	<?php foreach ($attrs as $attr) : ?>
		<?php $attr_id = @explode(':', $attr)[1]; ?>
		<?php if (isset($attributes[$attr_id])) : ?>
			<td class="m-cart_heading_label">
				<?php echo $attributes[$attr_id]->title; ?>
			</td>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ($params->get('cart_show_item_delete', true)) : ?>
	<td class="m-cart_heading_label">
		<?php echo JText::_('COM_CATALOGUE_CART_HEAD_DELETE'); ?>
	</td>
<?php endif; ?>