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
/** @var mixed $displayData */

/** @var array $images */
$tags = $displayData;
?>

<div class="j-catalogue-product-page_tags">
	<?php foreach ($tags as $k => $tag) : $tagParams = new Registry($tag->params) ?>

		<?php if ($k == 0) : ?>
			<div class="j-catalogue-product-page_tag -lead hidden-xs">
				<div class="j-catalogue-product-page_tag_text">
					<?php echo $tag->title; ?>
				</div>
			</div>
		<?php else: ?>
			<div class="j-catalogue-product-page_tag">
				<?php if ($tag_link_class = $tagParams->get('tag_link_class')) : ?>
					<i class="<?php echo $tag_link_class ?>"></i>
				<?php endif; ?>
				<div class="j-catalogue-product-page_tag_text">
					<?php echo $tag->title; ?>
				</div>
			</div>
		<?php endif; ?>
		
	<?php endforeach; ?>
</div>