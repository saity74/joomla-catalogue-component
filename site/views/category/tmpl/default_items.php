<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$viewName   = $this->getName();
$layout     = $this->getLayout();
$prefix     = "catalogue-$viewName-$layout-items";

$params     = $this->state->get('params');

$slice_desc = $params->get('slice_desc', 0);
$slice_len  = $params->get('short_desc_len', 150);

$img_width  = $params->get('img_width', 220);
$img_height = $params->get('img_height', 155);

$item_show_description = $params->get('item_show_description', 0);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$show_pagination = $params->get('show_pagination', 1);

?>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 j-catalogue-products_list m-products-list js-products-list-wrapper <?php echo $prefix ?>">
	<div class="row row-flex js-products-list">
		<?php foreach ($this->items as $item) : ?>
			<?php
				$item->link = JRoute::_(CatalogueHelperRoute::getItemRoute($item->id, $item->catid));
			?>
			<div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 js-product" data-name="<?php echo $item->title; ?>" data-price="<?php echo $item->price; ?>">
				<div class="j-catalogue_product m-product" itemscope itemtype="http://schema.org/Product">
					<?php echo JLayoutHelper::render('catalogue.category.item.tags', $item); ?>

					<div class="m-product_img js-gallery">
						<?php echo JLayoutHelper::render('catalogue.category.item.images', $item); ?>
					</div>

					<div class="m-product_desc">
						<?php echo JLayoutHelper::render('catalogue.category.item.name', $item); ?>

						<?php if ($params->get('show_short_desc', 0)) : ?>
							<div class="m-product_desc_text" itemprop="description">
								<?php echo JLayoutHelper::render('catalogue.category.item.description', $item); ?>
							</div>
						<?php endif; ?>

						<?php if (isset($item->sku) && !empty($item->sku)) : ?>
							<div class="m-product_attr">
								Артикул: <?php echo $item->sku; ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="m-product_footer">
						<?php echo JLayoutHelper::render('catalogue.category.item.price', $item); ?>
						<?php echo JLayoutHelper::render('catalogue.category.item.button', $item); ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
		echo JLayoutHelper::render('catalogue.category.pagination', [$this->pagination, $this->state->get('params')->get('show_pagination', 1)]);
	?>
</div>
