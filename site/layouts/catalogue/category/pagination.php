<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

list($pagination, $show_pagination) = $displayData;
?>

<?php if ($show_pagination == 2) : ?>
	<?php if ($page_links = $pagination->getPagesLinks()): ?>
		<div class="m-pagination j-catalogue-pagination js-catalogue-pagination">
			<?php echo $page_links; ?>
		</div>

		<?php if ($next_page_link = $pagination->getData()->next->link) : ?>
			<div class="j-catalogue-loadmore">
				<a href="<?php echo $next_page_link; ?>" class="j-catalogue-loadmore_btn js-catalogue-loadmore" data-scroll="true">
					<span>Загрузить еще</span>
				</a>
				<div class="j-catalogue-loadmore_loader e-loader"></div>
			</div>
		<?php endif; ?>

	<?php endif; ?>

<?php elseif($show_pagination == 1) : ?>

	<div class="j-catalogue-pagination js-catalogue-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>

<?php endif; ?>
