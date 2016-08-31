<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_catalogue');
$img_width = $params->get('img_width', 320);
$img_height = $params->get('img_height', 310);

/** @var mixed $displayData */
list($item, $thumb_size) = (is_array($displayData)) ? $displayData : [$displayData, 'cat'];

$image_list = [];

if (is_array($item->images) && !empty($item->images))
{
	foreach ($item->images as $image)
	{
		$name = basename($image['name']);
		$path = JPath::clean($image['url']);

		if (file_exists(JPATH_SITE . $path))
		{
			$img = new stdClass;
			$img = (object) $image;
			$img->src 	= CatalogueHelper::createThumb($item->id, $path, $img_width, $img_height, $thumb_size);
			$img->info 	= getimagesize(JPATH_SITE . $path);
			$image_list[] = $img;
		}
	}

	$first_image = array_shift($image_list);
}

?>

<?php if (isset($first_image)) : ?>
	<a class="m-product-details_images_item js-gallery-item" title="<?php echo $first_image->title; ?>" href="<?php echo $first_image->url; ?>">
		<img src="<?php echo $first_image->src; ?>"/>
		<?php if ($image_list): ?>
		<span class="m-product-details_images_counter">
			Еще <?php echo count($image_list); ?> фото
		</span>
		<?php endif; ?>
	</a>
<?php else: ?>
	<div class="m-product_img_placeholder m-product-details_img_placeholder e-image-not-found">
		<i class="i-icon i-picture"></i>
	</div>
<?php endif; ?>

<?php if ($image_list) : ?>
	<?php foreach($image_list as $image) : ?>
		<a class="m-product-details_images_item_hidden js-gallery-item" title="<?php echo $image->title; ?>" href="<?php echo $image->url; ?>"></a>
	<?php endforeach; ?>
<?php endif; ?>
