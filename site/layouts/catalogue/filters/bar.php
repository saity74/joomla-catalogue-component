<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;
// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');

$state = $data['view']->get('State');
$sku_filter = $state->get('filter.sku', '', 'string');
?>


<div class="m-filter">
	<form action="<?php echo $data['action']; ?>" method="post" id="catalogueFilterForm" class="j-catalogue-filter_form b-form js-catalogue-filter-form">
		<?php if ($filters) : ?>
			<?php foreach ($filters as $fieldName => $field) : ?>
				<?php
					$field_class = 'm-filter_control';

					switch ($field->type) {
						case 'List':
							$field_class .= ' js-selectbox m-filter_selectbox';
							break;
						case 'radio':
							$field_class .= ' m-filter_radio';
							break;
						case 'checkbox':
							$field_class .= ' m-filter_checkbox';
							break;
					}

					$field->class = $field->class . ' ' . $field_class;
				?>
				<div class="m-filter_item m-module">
					<div class="m-filter_item_heading m-module_heading">
						<h4 class="m-filter_item_title m-module_title">
							<?php echo $field->title; ?>
						</h4>
					</div>
					<div class="m-filter_item_box">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>

		<div class="m-filter_item m-module">
			<div class="m-filter_item_heading m-module_heading">
				<h4 class="m-filter_item_title m-module_title">
					Поиск по артикулу
				</h4>
			</div>
			<div class="m-filter_item_box">
				<div class="m-filter_input i-icon i-magnifier">
					<input type="text" name="filter[sku]" value="<?php echo $sku_filter; ?>" placeholder="Введите номер"/>
				</div>
			</div>
		</div>

		<div class="m-filter_reset">
			<button class="m-filter_reset_btn js-reset-form">Сбросить все фильтры</button>
		</div>

		<input type="hidden" name="filter_order" value="<?php echo $data['order']; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $data['dir']; ?>"/>
		<input type="hidden" name="option" value="com_catalogue"/>
		<input type="hidden" name="tmpl" value="component"/>
	</form>
</div>

<!--<div class="j-catalogue-filter-loader js-catalogue-filter-loader">-->
<!--	<div class="e-loader"></div>-->
<!--	<div class="j-catalogue-filter-loader_empty">-->
<!--		<div class="j-catalogue-filter-loader_empty_text">Нет подходящих товаров</div>-->
<!--		<button class="e-btn -sm js-catalogue-filter-reset">Сбросить фильтры</button>-->
<!--	</div>-->
<!--	<i class="js-catalogue-filter-loader-close i-icon i-cross"></i>-->
<!--</div>-->
