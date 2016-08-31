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
$img_width = $params->get('img_width', 154);
$img_height = $params->get('img_height', 70);

/** @var mixed $displayData */
$item = $displayData;

$image = '';

$images_data = json_decode($item->images, true);
if (is_array($images_data) && !empty($images_data) && isset($images_data[0]))
{
	$image = $images_data[0];
}

if (is_array($image) && !empty($image))
{
	$name = basename($image['name']);
	$path = implode('/', ['images', $item->id, $name]);
	$path = JPath::clean($path);
	if (file_exists($path))
	{
		$image['src'] 		= CatalogueHelper::createThumb($item->id, $path, $img_width, $img_height, 'tag');
		$image['info']  	= getimagesize(JPATH_SITE . '/' . $path);
	}
}
?>

<?php if ($image) : ?>

	<div class="j-catalogue_product_img">
		<a href="<?php echo CatalogueHelperRoute::getItemRoute($item->id, $item->catid); ?>" title="<?php echo $item->title; ?>">
			<img src="<?php echo $image['src']; ?>" alt="<?php echo $item->title; ?>" itemprop="image">
		</a>
	</div>
<?php endif; ?>
