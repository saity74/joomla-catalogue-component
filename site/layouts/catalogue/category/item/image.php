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

$img_width = $params->get('category_img_width', '280');
$img_height = $params->get('category_img_height', '210');

/** @var mixed $displayData */
$item = $displayData;

$all_images = [];

if (isset($item->children))
{
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
}
else
{
	$images_data = $item->images;

	if (is_array($images_data) && !empty($images_data))
	{
		$all_images[$item->id] = $images_data;
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
					$img->src 	= CatalogueHelper::createThumb($id, $path, $img_width, $img_height, 'cat');
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
	<a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>">
		<?php if(!empty($first->src)): ?>
			<img src="<?php echo $first->src; ?>" alt="<?php echo $item->title; ?>" itemprop="image">
		<?php else : ?>
			<div class="m-product_img_placeholder e-image-not-found">
				<i class="i-icon i-picture"></i>
			</div>
		<?php endif; ?>
	</a>
<?php else: ?>
	<div class="m-product_img_placeholder e-image-not-found">
		<i class="i-icon i-picture"></i>
	</div>
<?php endif; ?>
