<?php
/**
 * @package     com_catalogue
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

list($listOrder, $listDirn) = $displayData;
$action_url = $_SERVER['REQUEST_URI'];
?>

<div class="m-ordering">
	<div class="m-ordering_item">
		<form action="<?php echo $action_url; ?>" class="m-ordering_form js-ordering-form" method="post">
			<input type="hidden" name="filter_order" value="itm.price" />

			<button type="submit"
					class="m-ordering_label js-sorting-reverse-btn"
					name="filter_order_Dir"
					data-sort="price"
					value="<?php echo $listDirn === 'DESC' ? 'ASC' : 'DESC'; ?>"
			>
				<span class="m-ordering_item_title">По цене</span>
			</button>

			<?php $is_active = ($listOrder === 'itm.price' && $listDirn === 'DESC') ? ' -active' : ''; ?>
			<button class="m-ordering_btn<?php echo $is_active; ?> js-sorting-btn" type="submit" name="filter_order_Dir" value="DESC" data-sort="price:desc">
				<i class="i-icon i-order-arrow-down"></i>
			</button>

			<?php $is_active = ($listOrder === 'itm.price' && $listDirn === 'ASC') ? ' -active' : ''; ?>
			<button class="m-ordering_btn<?php echo $is_active; ?> js-sorting-btn" type="submit" name="filter_order_Dir" value="ASC" data-sort="price:asc">
				<i class="i-icon i-order-arrow-up"></i>
			</button>
		</form>
	</div>

	<div class="m-ordering_item">
		<form action="<?php echo $action_url; ?>" class="m-ordering_form js-ordering-form" method="post">
			<input type="hidden" name="filter_order" value="itm.title" />

			<button type="submit"
					class="m-ordering_label js-sorting-reverse-btn"
					name="filter_order_Dir"
					data-sort="name"
					value="<?php echo $listDirn === 'DESC' ? 'ASC' : 'DESC'; ?>"
			>
				<span class="m-ordering_item_title hidden-xs">По алфавиту</span>
				<span class="m-ordering_item_title hidden-sm hidden-md hidden-lg">А-Я</span>
			</button>

			<?php $is_active = ($listOrder === 'itm.title' && $listDirn === 'DESC') ? ' -active' : ''; ?>
			<button class="m-ordering_btn<?php echo $is_active; ?> js-sorting-btn" type="submit" name="filter_order_Dir" value="DESC" data-sort="name:desc">
				<i class="i-icon i-order-arrow-down"></i>
			</button>

			<?php $is_active = ($listOrder === 'itm.title' && $listDirn === 'ASC') ? ' -active' : ''; ?>
			<button class="m-ordering_btn<?php echo $is_active; ?> js-sorting-btn" type="submit" name="filter_order_Dir" value="ASC" data-sort="name:asc">
				<i class="i-icon i-order-arrow-up"></i>
			</button>
		</form>
	</div>
</div>
