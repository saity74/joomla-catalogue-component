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
					$image_list[] = $img;
				}
			}
		}
	}

	$color_codes = JArrayHelper::getColumn($image_list, 'color');
	$color_names = JArrayHelper::getColumn($image_list, 'color_name');

	$colors = array_combine($color_names, $color_codes);
}
?>

<?php if (!empty($colors) && is_array($colors)) : ?>
	<div class="j-catalogue_product_attr -bordered -inline">
		<span class="j-catalogue_product_attr_title">Цвета в наличии:</span>
		<div class="j-catalogue_product_attr_box -inline">
			<?php foreach ($colors as $color_name => $color_code): ?>
				<div class="j-catalogue_product_attr_value j-catalogue_product_attr_color e-tooltip--top" data-hint="<?php echo $color_name; ?>" style="background: <?php echo $color_code; ?>"></div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif;
