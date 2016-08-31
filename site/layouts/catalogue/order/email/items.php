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
$items = $displayData;

$total_amount = 0;
$total_count = 0;

?>

<table>
	<thead>
		<tr>
			<th>Название</th>
			<th>Стоимость</th>
			<th>Количество</th>
			<th>Сумма</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($items as $item) : ?>
		<?php
			$total_amount += $item->amount;
			$total_count += $item->count;
		?>
			<tr>
				<td><?php echo $item->title; ?></td>
				<td><?php echo $item->price; ?></td>
				<td><?php echo $item->count; ?></td>
				<td><?php echo $item->amount; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='4'>
				Всего товаров: <?php echo $total_count; ?>, на сумму: <?php echo $total_amount; ?>
			</td>
		</tr>
	</tfoot>
</table>