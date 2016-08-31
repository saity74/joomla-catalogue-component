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

$img_width = $params->get('category_img_width', 280);
$img_height = $params->get('category_img_height', 210);

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
<a href="<?php echo JRoute::_(CatalogueHelperRoute::getItemRoute($item->id, $item->catid)); ?>" class="m-product_img_link">
<?php if ($first_image) : ?>
	<img src="<?php echo $first_image->src; ?>"/>
	<a class="m-product_img_gallery_icon i-icon i-move js-gallery-item" title="<?php echo $first_image->title; ?>" href="<?php echo $first_image->url; ?>"></a>
<?php else: ?>
	<div class="m-product_img_placeholder e-image-not-found">
		<i class="i-icon i-picture"></i>
	</div>
<?php endif; ?>
</a>

<?php if ($image_list) : ?>
	<?php foreach($image_list as $image) : ?>
		<a class="m-product_gallery_img_link js-gallery-item" title="<?php echo $image->title; ?>" href="<?php echo $image->url; ?>"></a>
	<?php endforeach; ?>
<?php endif; ?>
