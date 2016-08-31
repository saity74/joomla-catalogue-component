<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$published = $this->state->get('filter.published');
?>

<div class="row-fluid">
	<div class="control-group span6">
		<div class="controls">
			<?php echo JLayoutHelper::render('html.batch.language', []); ?>
		</div>
	</div>
	<div class="control-group span6">
		<div class="controls">
			<?php echo JLayoutHelper::render('joomla.html.batch.access', []); ?>
		</div>
	</div>
</div>
<?php if ($published >= 0) : ?>
<div class="row-fluid">
	<?php echo JLayoutHelper::render('html.batch.attr', []); ?>
</div>
<?php endif; ?>
