<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\Utilities\ArrayHelper;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */
class JFormFieldCategoriesList extends JFormFieldList
{
	/**
	 * A flexible categories list that respects access controls
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'CategoriesList';

	/**
	 * Method to get a list of categories that respects access controls and can be used for
	 * either category assignment or parent category assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = [];
		$published = $this->element['published'] ? $this->element['published'] : array(0, 1);

		$extension = 'com_catalogue';

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT a.id AS value, a.title AS text, a.level, a.published, a.lft');
		$subQuery = $db->getQuery(true)
			->select('id,title,level,published,parent_id,extension,lft,rgt')
			->where('(extension = ' . $db->quote($extension) . ')')
			->from('#__categories');

		// Filter language
		if (!empty($this->element['language']))
		{
			$subQuery->where('language = ' . $db->quote($this->element['language']));
		}

		// Filter on the published state
		if (is_numeric($published))
		{
			$subQuery->where('published = ' . (int) $published);
		}
		elseif (is_array($published))
		{
			$subQuery->where('published IN (' . implode(',', ArrayHelper::toInteger($published)) . ')');
		}

		$query->from('(' . (string) $subQuery . ') AS a')
			->join('LEFT', $db->quoteName('#__categories') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Get the current user object.
		$user = JFactory::getUser();

		// For new items we want a list of categories you are allowed to create in.
		foreach ($options as $i => $option)
		{
			/* To take save or create in a category you need to have create rights for that category
			 * unless the item is already in that category.
			 * Unset the option if the user isn't authorised for it. In this field assets are always categories.
			 */
			if ($user->authorise('core.create', $extension . '.category.' . $option->value) != true && $option->level != 0)
			{
				unset($options[$i]);
			}
		}

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
	}

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html = '<div class="chzn-buttons">
					<div class="btn-group">
						<a id="chznSelectAll" href="#"
							class="btn btn-small btn-success hasTooltip"
							data-toggle="tooltip" title="' . JText::_("JGLOBAL_SELECTION_ALL") . '"
						>
							<i class="icon-checkmark"></i>
						</a>
						<a id="chznSelectNone" href="#"
							class="btn btn-small btn-danger hasTooltip"
							data-toggle="tooltip" title="' . JText::_("JGLOBAL_SELECTION_NONE") . '"
						>
							<i class="icon-remove"></i>
						</a>
					</div>
				</div>';

		$html .= parent::getInput();

		$js = 'jQuery(function($) {
			var $select  = $("#' . $this->id . '"),
				$chzn    = $("#' . $this->id . '_chzn");
				$allBtn  = $("#chznSelectAll"),
				$noneBtn = $("#chznSelectNone");

			$allBtn.click(function(e) {
				console.log($select.find("option"));
				e.preventDefault();
				$select
					.find("option")
						.prop("selected", true)
					.end()
					.trigger("liszt:updated");
			});

			$noneBtn.click(function(e) {
				console.log($select.find("option"));
				e.preventDefault();
				$select
					.find("option")
						.prop("selected", false)
					.end()
					.trigger("liszt:updated");
			});
		});';

		$styles = '
			.chzn-buttons {
				margin-bottom: 10px;
			}
		';

		JFactory::getDocument()
			->addScriptDeclaration($js)
			->addStyleDeclaration($styles);

		return $html;
	}
}
