<?php
/**
 * Catalogue Component Category Tree
 *
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


use \Joomla\Registry\Registry;
/**
 * Class CatalogueHelperAttributes
 */
class CatalogueHelperAttributes
{

	static public function getAttributes($id, $cid)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select(
				'`ag`.`id` AS `group_id`, `ag`.`title`, `ag`.`params`, `ag`.`alias` AS `group_alias`,'
				. ' GROUP_CONCAT('
				. ' DISTINCT `a`.`id` , \'::\' , `a`.`title`, \'::\','
				. ' COALESCE(`eav_b`.`value`, `eav_t`.`value`, `eav_i`.`value`, `eav_f`.`value`, \'\')'
				. ' ORDER BY `a`.`ordering`'
				. ' SEPARATOR \'\\n\') AS `values`')

			->from($db->qn('#__catalogue_attr', 'a'))

			->join('LEFT', $db->qn('#__catalogue_attr_group', 'ag')
				. ' ON ' . $db->qn('ag.id') . ' = ' . $db->qn('a.group_id'))

			->join('INNER', $db->qn('#__catalogue_attr_group_category', 'agc')
				. ' ON ' . $db->qn('agc.group_id') . ' = ' . $db->qn('ag.id'))

			->join('LEFT', $db->qn('#__catalogue_attr_value_bool', 'eav_b')
				. ' ON ' . $db->qn('eav_b.attr_id') . ' = ' . $db->qn('a.id'))

			->join('LEFT', $db->qn('#__catalogue_attr_value_text', 'eav_t')
				. ' ON ' . $db->qn('eav_t.attr_id') . ' = ' . $db->qn('a.id'))

			->join('LEFT', $db->qn('#__catalogue_attr_value_int', 'eav_i')
				. ' ON ' . $db->qn('eav_i.attr_id') . ' = ' . $db->qn('a.id'))

			->join('LEFT', $db->qn('#__catalogue_attr_value_float', 'eav_f')
				. ' ON ' . $db->qn('eav_f.attr_id') . ' = ' . $db->qn('a.id'))

			->where($db->qn('a.state') . ' = ' . $db->q(1))
			->where($db->qn('ag.state') . ' = ' . $db->q(1))
			->where($db->qn('agc.cat_id') . ' = ' . $db->q((int) $cid))

			->where(
				implode(' OR ', [
					'(' . $db->qn('eav_b.item_id') . ' = ' . $db->q((int) $id) . ' AND ' . $db->qn('eav_b.value') . ' IS NOT NULL)',
					'(' . $db->qn('eav_t.item_id') . ' = ' . $db->q((int) $id) . ' AND ' . $db->qn('eav_t.value') . ' IS NOT NULL)',
					'(' . $db->qn('eav_i.item_id') . ' = ' . $db->q((int) $id) . ' AND ' . $db->qn('eav_i.value') . ' IS NOT NULL)',
					'(' . $db->qn('eav_f.item_id') . ' = ' . $db->q((int) $id) . ' AND ' . $db->qn('eav_f.value') . ' IS NOT NULL)'
				])
			)

			->group($db->qn('ag.id'))
			->order($db->qn('ag.ordering') . ' ASC, ' . $db->qn('a.ordering') . ' ASC');

		$db->setQuery($query);
		try
		{
			$attrs = $db->loadObjectList('group_alias');

			if (!empty($attrs))
			{
				foreach ($attrs as $group_id => &$selected)
				{
					$selected->params = new Registry($selected->params);

					if (isset($selected->values) && !empty($selected->values))
					{
						$values = explode("\n", $selected->values);
						$selected->values = [];
						foreach ($values as $field_value)
						{
							if (strpos($field_value, '::') == false)
							{
								continue;
							}

							$tmp = new stdClass;
							@list($tmp->id, $tmp->label, $tmp->val) = explode('::', $field_value);
							$selected->values[$tmp->id] = $tmp;
						}
					}
				}

				unset($selected);
			}

			return $attrs;
		}
		catch (Exception $e)
		{
			echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br />';

			return false;
		}
	}
}
