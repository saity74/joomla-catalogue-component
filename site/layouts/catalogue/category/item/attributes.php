<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_catalogue');

/** @var mixed $displayData */
list($item, $attr_to_show) = is_array($displayData) ? $displayData : [$displayData, false];

if ($attr_to_show !== false && !is_array($attr_to_show))
{
	$attr_to_show = [$attr_to_show => ['*']];
}
?>

<?php foreach ($item->attributes as $group_alias => $attr_group) : ?>
	<?php
		if ($attr_to_show && !in_array($group_alias, array_keys($attr_to_show)))
		{
			continue;
		}
	?>
	<?php foreach ($attr_group->attrs as $attr_alias => $attr) : ?>
		<?php if ( in_array('*', $attr_to_show[$group_alias]) || in_array($attr_alias, $attr_to_show[$group_alias])) : ?>
			<?php if ($attr->value) : ?>
			<div class="m-product_attr">
				<?php echo $attr->title; ?>: <?php echo $attr->value; ?>
			</div>
			<?php endif; ?>
		<?php endif ?>
	<?php endforeach; ?>
<?php endforeach; ?>
