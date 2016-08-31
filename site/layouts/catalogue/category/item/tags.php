<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var mixed $displayData */
$item = $displayData;
?>

<?php foreach ($item->tags as $tag) : ?>
	<?php if (isset($tag->params['tag_link_class'])) : ?>
		<div class="m-label m-product_label">
			<i class="m-label_icon <?php echo $tag->params['tag_link_class'] ?>"></i>
		</div>
	<?php endif; ?>
<?php endforeach; ?>
