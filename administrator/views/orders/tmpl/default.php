<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app        = JFactory::getApplication();
$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$archived   = $this->state->get('filter.published') == 2 ? true : false;
$trashed    = $this->state->get('filter.published') == -2 ? true : false;
$saveOrder  = $listOrder == 'o.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_catalogue&task=orders.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'ordersList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_catalogue&view=orders'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif;?>
			<?php
			// Search tools bar
			echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>
			<?php if (empty($this->items)) : ?>
				<div class="alert alert-no-items">
					<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php else : ?>
				<table class="table table-striped" id="itemList">
					<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'o.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="10%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'o.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_CATALOGUE_HEADING_ORDER_ID', 'o.id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_CATALOGUE_HEADING_DELIVERY_TYPE', 'o.delivery_type', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_CATALOGUE_HEADING_DELIVERY_ADDRESS', 'o.delivery_address', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_CATALOGUE_HEADING_PAYMENT_METHOD', 'o.payment_method', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_CATALOGUE_HEADING_CLIENT_MAIL', 'o.client_mail', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_CATALOGUE_HEADING_CLIENT_NAME', 'o.client_name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_CATALOGUE_HEADING_CLIENT_PHONE', 'o.client_phone', $listDirn, $listOrder); ?>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($this->items as $i => $item) :
						$item->max_ordering = 0;
						$canCreate  = $user->authorise('core.create',     'com_catalogue.cart.' . $item->id);
						$canEdit    = $user->authorise('core.edit',       'com_catalogue.cart.' . $item->id);
						$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
						$canEditOwn = $user->authorise('core.edit.own',   'com_catalogue.cart.' . $item->id) && $item->created_by == $userId;
						$canChange  = $user->authorise('core.edit.state', 'com_catalogue.cart.' . $item->id) && $canCheckin;
						?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
							<td class="order nowrap center hidden-phone">
								<?php
								$iconClass = '';
								if (!$canChange)
								{
									$iconClass = ' inactive';
								}
								elseif (!$saveOrder)
								{
									$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
								}
								?>
								<span class="sortable-handler<?php echo $iconClass ?>">
									<span class="icon-menu"></span>
								</span>
								<?php if ($canChange && $saveOrder) : ?>
									<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
								<?php endif; ?>
							</td>
							<td class="center">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="center">
								<div class="btn-group">
									<?php echo JHtml::_('jgrid.published', $item->state, $i, 'cart.', $canChange, 'cb'); ?>
									<?php
									// Create dropdown items
									$action = $archived ? 'unarchive' : 'archive';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'cart');

									$action = $trashed ? 'untrash' : 'trash';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'cart');

									// Render dropdown list
									echo JHtml::_('actionsdropdown.render', $this->escape($item->id));
									?>
								</div>
							</td>
							<td class="has-context">
								<div class="pull-left break-word">
									<?php if ($item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'cart.', $canCheckin); ?>
									<?php endif; ?>

									<?php if ($canEdit || $canEditOwn) : ?>
										<a class="hasTooltip"
										   href="<?php echo JRoute::_('index.php?option=com_catalogue&task=order.edit&id=' . $item->id); ?>"
										   title="<?php echo JText::_('JACTION_EDIT'); ?>">
											<?php echo $this->escape($item->id); ?>
										</a>
									<?php else : ?>
										<?php echo $this->escape($item->id); ?>
									<?php endif; ?>
								</div>
							</td>

							<td>
								<?php echo $item->delivery_type; ?>
							</td>
							<td>
								<?php echo $item->delivery_address; ?>
							</td>
							<td>
								<?php echo $item->payment_method; ?>
							</td>
							<td>
								<?php echo $item->client_mail; ?>
							</td>
							<td>
								<?php echo $item->client_name; ?>
							</td>
							<td>
								<?php echo $item->client_phone; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php echo $this->pagination->getListFooter(); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>
