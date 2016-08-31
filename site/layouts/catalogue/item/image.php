<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use \Joomla\Registry\Registry;

$params = JComponentHelper::getParams('com_catalogue');
$img_width = $params->get('img_width', 320);
$img_height = $params->get('img_height', 310);

/** @var mixed $displayData */
$item = $displayData;

$all_images = [];

foreach ($item->children as $child)
{
	if (!empty($child))
	{
		$images_data = json_decode($child->images, true);

		if (is_array($images_data) && !empty($images_data))
		{
			$all_images[$child->id] = $images_data;
		}
	}
}

$image_list = [];

if (is_array($all_images) && !empty($all_images))
{
	foreach ($all_images as $id => $images)
	{
		if (is_array($images))
		{
			foreach ($images as $image)
			{
				$name = basename($image['name']);
				$path = implode('/', ['images', $id, $name]);
				$path = JPath::clean($path);
				if (file_exists($path))
				{
					$img = new stdClass;
					$img = (object) $image;
					$img->src 	= CatalogueHelper::createThumb($id, $path, $img_width, $img_height, 'lg');
					$img->info 	= getimagesize(JPATH_SITE . '/' . $path);
					$image_list[] = $img;
				}
			}
		}
	}

	$color_codes = JArrayHelper::getColumn($image_list, 'color' );
	$color_names = JArrayHelper::getColumn($image_list, 'color_name' );

	$colors = array_combine($color_names, $color_codes);
	$colors = array_splice($colors, 0, 5);
}
?>

<?php if (!empty($image_list) && is_array($image_list)) : $first = array_shift($image_list); ?>

	<div class="j-catalogue-product-page_images js-popup-gallery" itemscope itemtype="http://schema.org/ImageGallery">
		<figure class="j-catalogue-product-page_image" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
			<a
				class="js-gallery-item"
				title="<?php echo $first->title; ?>"
				href="<?php echo $first->name; ?>"
				itemprop="contentUrl">
				<img
					src="<?php echo $first->src; ?>"
					width="<?php echo $img_width ?>"
					height="<?php echo $img_height ?>" />
				<figcaption itemprop="caption description"><?php echo $first->title; ?></figcaption>
			</a>
		</figure>

		<?php foreach($image_list as $image) : ?>
			<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
				<a
					class="js-gallery-item"
					title="<?php echo $image->title; ?>"
					href="<?php echo $image->name; ?>"
					itemprop="contentUrl"
					<figcaption itemprop="caption description"><?php echo $image->title; ?></figcaption>
				</a>
			</figure>
		<?php endforeach; ?>

		<?php if (is_array($colors) && count($colors) > 1) : ?>
		<div class="j-catalogue-product-page_colors j-catalogue_product_attr -inline">
			<span class="j-catalogue_product_attr_title -white">Цвета<span class="hidden-sm"> в наличии</span>:</span>
			<div class="j-catalogue_product_attr_box -inline">
				<?php foreach ($colors as $color_name => $color_code): ?>
					<div class="j-catalogue_product_attr_value j-catalogue_product_attr_color e-tooltip--top" data-hint="<?php echo $color_name; ?>" style="background: <?php echo $color_code; ?>"></div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>

<?php else: ?>
<div class="j-catalogue-product-page_images" itemscope itemtype="http://schema.org/ImageGallery">
	<div class="j-catalogue-product-page_images_image_not_found">
		<i class="i-icon i-camera"></i>
	</div>
</div>
<?php endif; ?>
