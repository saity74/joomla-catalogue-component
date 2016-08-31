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

$item = $displayData;
$params = new Registry($item->params);

if (!class_exists('CatalogueHelperRoute'))
{
	require_once JPATH_SITE . '/components/com_catalogue/helpers/route.php';
	if (!is_callable('CatalogueHelperRoute::getCartRoute'))
	{
		JFactory::getApplication()->enqueueMessage('Класс CatalogueHelperRoute не найден', 'warning');
	}
}

// Params

$item_cart_btn_text 	= $params->get('item_cart_btn_text', JText::_('COM_CATALOGUE_CATEGORY_CART_BTN_TEXT_DEFAULT'));
$item_cart_btn_class 	= $params->get('item_cart_btn_class', '');
$item_cart_btn_id 		= $params->get('item_cart_btn_id', '');
$item_cart_btn_onclick 	= $params->get('item_cart_btn_onclick', '');

$cart_button_attrs = [
	'id'      => [ 'itemid-' . $item->id, $item_cart_btn_id ],
	'name'    => 'task',
	'value'   => 'cart.add',
	'class'   => [ 'js-add-to-cart m-product_order_btn i-icon i-add-to-cart', $item_cart_btn_class ],
	'onclick' => $item_cart_btn_onclick
];

// Cart data
if (!class_exists('CatalogueCart'))
{
	JFactory::getApplication()->enqueueMessage('Класс CatalogueCart не найден, установите корзину', 'error');
	return;
}

$cart = CatalogueCart::getInstance();
$count = $cart->get($item->id);
$in_cart = $count > 0;
$count_value = $in_cart ? (int) $count : 1;

// If item in cart
if ($in_cart)
{
	$cart_button_attrs['class'][] = '-in-cart';
	$cart_button_attrs['value'] = 'cart.remove';

	$item_cart_btn_text = 'В корзине';
}

$form_action = $count ? CatalogueHelperRoute::getCartRoute() : 'index.php';

$args = [];
foreach($cart_button_attrs as $attr_key => $attr_value)
{
	if (!empty($attr_value))
	{
		$values = is_array($attr_value) ? implode(' ', $attr_value) : $attr_value;
		$args[] = $attr_key . '="' . $values . '"';
	}
}

?>

<?php if ($params->get('item_show_cart_btn', '1') == '1') : ?>
	<form class="m-product_form js-add-to-cart-form" action="<?php echo JRoute::_($form_action); ?>" method="post" enctype="multipart/form-data" >
		<button <?php echo implode(' ', $args); ?> >
			<?php echo $item_cart_btn_text; ?>
		</button>
		<input type="hidden" name="jform[items][<?php echo $item->id; ?>]" value="<?php echo $count_value; ?>" />
		<input type="hidden" name="option" value="com_catalogue">
		<input type="hidden" name="return" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
<?php endif;
