<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once('thumbnail.php');

/**
 * Class CatalogueHelper
 */
class CatalogueHelper
{

	/**
	 * Create image thumb function
	 *
	 * @param   int     $id      Catalogue item ID
	 * @param   string  $image   Image relative path
	 * @param   int     $width   Width of image
	 * @param   int     $height  Height if image
	 * @param   string  $suffix  Additional suffix for image filename
	 *
	 * @return  bool|string
	 */
	public static function createThumb($id, $image, $width, $height, $suffix = 'min')
	{
		$resized_folder = 'resized';

		// Check for the existence of source image
		$abs_source_img = JPATH_BASE . '/' . $image;

		if (!file_exists($abs_source_img))
		{
			return false;
		}

		$images_dir = dirname($image);

		$abs_images_dir = JPATH_BASE . '/' . $images_dir;

		$abs_new_img_dir = $abs_images_dir . '/' . $resized_folder . '/' . $id . '/' . $suffix;

		if (!file_exists($abs_images_dir . '/' . $resized_folder))
		{
			mkdir($abs_images_dir . '/' . $resized_folder, 0777);
		}

		if (!file_exists($abs_images_dir . '/' . $resized_folder . '/' . $id))
		{
			mkdir($abs_images_dir . '/' . $resized_folder . '/' . $id, 0777);
		}

		if (!file_exists($abs_new_img_dir))
		{
			mkdir($abs_new_img_dir, 0777);
		}

		$sizeOptions = array(
			'width'  => (int) $width,
			'height' => (int) $height,
			'method' => THUMBNAIL_METHOD_SCALE_MIN,
		);

		$p = str_replace('.jpg', '-' . $suffix . '.jpg', $abs_new_img_dir . '/' . basename($image));

		if (!file_exists($p))
		{
			$thumb = new Thumbnail;

			$resizeImage = $thumb->render($abs_source_img, $sizeOptions);
			@imageJpeg($resizeImage, $p, 90);
			@imagedestroy($resizeImage);
		}

		return $images_dir . '/' . $resized_folder . '/' . $id . '/' . $suffix . '/' . str_replace('.jpg', '-' . $suffix . '.jpg', basename($image));
	}

}
