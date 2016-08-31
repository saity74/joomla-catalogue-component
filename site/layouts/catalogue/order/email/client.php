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
$order_info = $displayData;

$delivery_types = [
	'0' => JText::_('COM_CATALOGUE_DELIVERY_TYPE_PICKUP'),
	'1' => JText::_('COM_CATALOGUE_DELIVERY_TYPE_POST'),
	'2' => JText::_('COM_CATALOGUE_DELIVERY_TYPE_TRANS'),
];
JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models/');
$payments_model = JModelLegacy::getInstance('Payments', 'CatalogueModel');

$payment_methods = [];

foreach ($payments_model->getItems() as $payment)
{
	$payment_methods[$payment->id] = $payment->title;
}

?>

<table>
	<tr>
		<td>Способ доставки</td>
		<td><?php echo $delivery_types[$order_info->delivery_type]; ?></td>
	</tr>
	<tr>
		<td>Адрес доставки</td>
		<td><?php echo $order_info->delivery_address; ?></td>
	</tr>
	<tr>
		<td>Способ оплаты</td>
		<td><?php echo $payment_methods[$order_info->payment_method]; ?></td>
	</tr>
	<tr>
		<td>Имя клиента</td>
		<td><?php echo $order_info->client_last_name . ' ' . $order_info->client_first_name; ?></td>
	</tr>
	<tr>
		<td>Почта клиента</td>
		<td><?php echo $order_info->client_mail; ?></td>
	</tr>
	<tr>
		<td>Телефон клиента</td>
		<td><?php echo $order_info->client_phone; ?></td>
	</tr>
</table>