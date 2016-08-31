<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('behavior.caption');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

$app        = JFactory::getApplication();
$jinput     = $app->input;
$view       = $this->getName();
$layout     = $this->getLayout();
$menu       = $this->menu;
$category   = $this->category;

jimport('joomla.application.module.helper');
$modules = JModuleHelper::getModules('left');
$modal_modules = JModuleHelper::getModules('category_modal');
$module_params = ['style' => 'default'];

$filter_form_action = JRoute::_(CatalogueHelperRoute::getCategoryRoute($this->state->get('category.id')));
//$filter_form_action = $_SERVER['REQUEST_URI'];
$filters_html = JLayoutHelper::render(
	'catalogue.filters.bar',
	[
		'view' => $this,
		'action' => $filter_form_action,
		'order' => $listOrder,
		'dir' => $listDirn
	]
);

$mobile_filters_html = JLayoutHelper::render(
	'catalogue.filters.mobile_bar',
	[
		'view' => $this,
		'action' => $filter_form_action,
		'order' => $listOrder,
		'dir' => $listDirn
	]
);
$prefix = "catalogue-$view-$layout";
?>

<div class="j-catalogue-category <?php echo $prefix ?>">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-9">
		<?php
			if ($category->params->get('show_page_heading'))
			{
				echo JLayoutHelper::render('catalogue.page.title', $category);
			}
		?>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
			<div class="m-horizontal-filter-bar js-mobile-filters-toggle">
				<div class="m-mobile-filter-toggler js-mobile-filters-open i-icon i-controls hidden-lg">
					<span class="hidden-xs">Фильтровать по параметрам</span>
					<span class="hidden-sm hidden-md">Фильтр</span>
				</div>
				<?php
					if ($category->params->get('show_ordering', 1))
					{
						echo JLayoutHelper::render('catalogue.filters.ordering', [$listOrder, $listDirn]);
					}
				?>
			</div>
		</div>
	</div>
	<div class="row">
		<?php if ($modules) : ?>
				<div class="visible-lg col-lg-3">
					<div class="l-sidebar l-left-sidebar">
						<?php
							foreach ($modules as $module)
							{
								echo JModuleHelper::renderModule($module, $module_params);
							}
						?>

						<?php echo $filters_html; ?>
					</div>
				</div>
		<?php endif; ?>

		<?php

			echo JLayoutHelper::render('catalogue.category.title', $category);

			if ($category->params->get('show_subcategories_list', 0))
			{
				echo $this->loadTemplate('subcategories');
			}
			// TODO: Add check category description length TEXT
			if ($category->params->get('show_description', 0) && $category->description)
			{
				echo JLayoutHelper::render('catalogue.category.description', $category);
			}
		?>

		<?php if (!empty($this->items)): ?>
			<?php echo $this->loadTemplate('items'); ?>
		<?php else: ?>

		<?php endif; ?>
	</div>

	<div id="categoryModal" class="e-hide">
	<?php
	foreach ($modal_modules as $module)
	{
		echo JModuleHelper::renderModule($module, $module_params);
	}
	?>
	</div>
</div>

<?php if ($modules) : ?>
	<div class="l-mobile-filters js-mobile-filters">
		<i class="i-icon i-cross l-mobile-filters_close js-mobile-filters-close"></i>
		<div class="l-mobile-filters_header">Каталог изделий</div>
		<?php
		foreach ($modules as $i => $module)
		{
			echo JModuleHelper::renderModule($module, ['style' => 'mfc', 'opened' => ($i == 0)]);
		}
		?>

		<?php echo $mobile_filters_html; ?>
	</div>
	<div class="l-mobile-filters-backdrop js-mobile-filters-backdrop"></div>
<?php endif; ?>
