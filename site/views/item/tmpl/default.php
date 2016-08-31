<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use \Joomla\Registry\Registry;

defined('_JEXEC') or die;

/** @noinspection PhpIncludeInspection */

$config     = JFactory::getConfig();
$params     = $this->state->get('params');
$currency   = $params->get('catalogue_currency', 'руб.');
$img_width  = $params->get('img_width', 300);
$img_height = $params->get('img_height', 320);

$viewName = $this->getName();
$layout   = $this->getLayout();

$item = $this->item;

$modules        = JModuleHelper::getModules('product_form');
$modal_modules  = JModuleHelper::getModules('product_modal');
$product_bottom = JModuleHelper::getModules('product_bottom');
$module_params  = array('style' => 'none');
?>

<div class="m-product-details" itemscope="" itemtype="http://schema.org/Product">
	<!-- Header.. -->
	<div class="m-product-details_header">
		<div class="row row-flex">
			<!-- Title.. -->
			<div class="col-xs-12 col-sm-7 col-md-8 col-lg-7">
				<div class="m-product-details_heading">
					<h1 class="m-product-details_title"><?php echo $item->title; ?></h1>
				</div>
			</div>
			<!-- ..Title -->

			<!-- Actions.. -->
			<div class="hidden-xs col-sm-5 col-md-4 col-lg-5">
				<div class="m-product-details_actions_wrap">
				<div class="m-product-details_actions">
					<div class="m-product-details_actions_item">
						<div class="ya-share2 m-product-details_share"
						     data-services="vkontakte,facebook,odnoklassniki,gplus" data-limit="0"></div>
					</div>
					<div class="m-product-details_actions_item">
						<a class="m-product-details_actions_link m-product-details_print i-icon i-print" href="javascript:window.print()">Распечатать</a>
					</div>
				</div>
				</div>
			</div>
			<!-- ..Actions -->
		</div>
	</div>
	<!-- ..Header -->

	<!-- Main.. -->
	<div class="m-product-details_main">
		<div class="row">

			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<!-- Desc.. -->
				<div class="m-product-details_desc">
					<div class="m-product-details_category">
						<?php echo $item->category_title; ?>
					</div>
					<div class="m-product-details_desc_text">
						<?php echo $item->introtext; ?>
					</div>
				</div>
				<!-- ..Desc -->

				<!-- Images.. -->
				<div class="m-product-details_images js-gallery visible-xs">
					<?php echo JLayoutHelper::render('catalogue.item.images', [$this->item, 'lg']); ?>
				</div>
				<!-- ..Images -->

				<!-- Attrs.. -->
				<div class="m-product-details_attrs">
					<div class="m-product-details_attr">
						<div class="m-product-details_attr_title">
							Камень:
						</div>
						<div class="m-product-details_attr_value">
							<form action="/catalogue/all" method="POST">
								<?php if (@$item->attributes['stone-type']->values) : ?>
									<?php
									$i           = 0;
									$types_count = count($item->attributes['stone-type']->values);
									?>
									<?php foreach ($item->attributes['stone-type']->values as $stone) : ?>
										<button name="filter[stone-type][]" type="submit"
										        value="<?php echo $stone->id; ?>">
											<?php echo $stone->label; ?>
										</button>
										<?php if (++$i !== $types_count) : ?>
											,
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</form>
						</div>
					</div>
					<?php echo JLayoutHelper::render('catalogue.item.attributes', [$item, 'base']); ?>
				</div>
				<!-- ..Attrs -->

				<!-- Price, order.. -->
				<div class="m-product-details_order">
					<div class="m-product-details_order_col">
						<div class="m-product-details_price">
							<?php if (($show_price = $params->get('show_price', 1)) && $item->price) : ?>
								<?php echo JLayoutHelper::render('catalogue.item.price', $this->item); ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="m-product-details_order_col">
						<?php echo JLayoutHelper::render('catalogue.item.button', $this->item); ?>
					</div>
				</div>
				<!-- ..Price, order -->
			</div>


			<!-- Images.. -->
			<div class="hidden-xs col-sm-6 col-md-6 col-lg-6">
				<div class="m-product-details_images js-gallery">
					<?php echo JLayoutHelper::render('catalogue.item.images', [$this->item, 'lg']); ?>
				</div>
			</div>
			<!-- ..Images -->
		</div>
	</div>
	<!-- ..Main -->
</div>

<?php echo $this->item->event->afterDisplayContent;
