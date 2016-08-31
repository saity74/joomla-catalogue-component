<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var mixed $displayData */
list($item, $group_aliases) = is_array($displayData) ? $displayData : [$displayData, false];

if ($group_aliases !== false && !is_array($group_aliases))
{
	$group_aliases = [$group_aliases];
}
?>

<?php foreach ($item->attributes as $group_alias => $group) : ?>
	<?php
		if ($group_aliases && !in_array($group_alias, $group_aliases))
		{
			continue;
		}

		$group_title = $group->params->get('filter_title', $group->title);
		$group_filter_type = $group->params->get('filter_type');
	?>
	<?php if (!$group_filter_type && isset($group->values) && is_array($group->values)) : ?>
		<?php foreach ($group->values as $attribute) : ?>
			<?php if (!empty($attribute->val)) : ?>
				<div class="m-product-details_attr">
					<div class="m-product-details_attr_title">
						<?php echo $attribute->label; ?>:
					</div>
					<div class="m-product-details_attr_value">
						<?php echo $attribute->val; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endforeach; ?>
