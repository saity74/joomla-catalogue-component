<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

/**
 * Table observer for dealing with Attribute Groups to Categories mapping
 * TODO: add new groups from request
 *
 * @since  1.0
 */
class CatalogueTableObserverItem extends JTableObserver
{
	/**
	 * Standard attributes types
	 *
	 * @var array
	 */
	private $standart_types = ['text', 'int', 'float', 'bool', 'datetime'];

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
	 *
	 * @param   JObservableInterface  $observableObject  The subject object to be observed
	 * @param   array                 $params            ( 'typeAlias' => $typeAlias )
	 *
	 * @return  CatalogueTableObserverItem
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);

		return $observer;
	}

	/**
	 * Post-processor for $table->load($keys, $reset)
	 *
	 * @param   boolean  &$result  The result of the load
	 * @param   array    $row      The loaded (and already binded to $this->table) row of the database table
	 *
	 * @return  void
	 */
	public function onAfterLoad(&$result, $row)
	{
		if ($result === true)
		{
			if ( ! $this->loadAttributes() || ! $this->loadImagesPath() )
			{
				$result = false;
			}
		}
	}

	/**
	 * If it was a new item, then in table images stored with '#IMAGESPATH#' prefix
	 * instead of real path. We need to convert them until second save.
	 *
	 * @return bool
	 */
	private function loadImagesPath()
	{
		if ( $this->table->id && isset($this->table->images) && $this->table->images !== '{}' )
		{
			$corrected_paths = str_replace('#IMAGESPATH#', '\/images\/' . $this->table->id, $this->table->images);

			if ( $corrected_paths !== null)
			{
				$this->table->images = $corrected_paths;
			}
		}

		return true;
	}

	/**
	 * Loads attributes for item
	 *
	 * @return bool
	 */
	private function loadAttributes()
	{
		if (is_null($this->table->attributes))
		{
			$id = $this->table->get('id');
			$cid = $this->table->get('catid');
			if ($id && $cid)
			{
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);

				$query
					->select(
						'`ag`.`id` AS `group_id`, `ag`.`title`, `ag`.`params`, `ag`.`alias`,'
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

					if ($attrs = $db->loadObjectList('group_id'))
					{
						$this->table->set('attributes', $attrs);
					}
				}
				catch (Exception $e)
				{
					echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br />';

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Pre-processor for $table->store($updateNulls)
	 *
	 * @param   boolean  $updateNulls  The result of the load
	 * @param   string   $tableKey     The key of the table
	 *
	 * @return  void
	 */
	public function onBeforeStore($updateNulls, $tableKey)
	{
	}

	/**
	 * Post-processor for $table->store($updateNulls)
	 *
	 * @param   boolean  &$result  The result of the load
	 *
	 * @return  void
	 */
	public function onAfterStore(&$result)
	{
		if ( $result === true )
		{
			$id = $this->table->get('id', false);

			if ( ! $id || ! $this->saveAttributes($id) || ! $this->moveImages($id) )
			{
				$result = false;
			}
		}
	}

	/**
	 * Move images from newly created item
	 *
	 * @param   string  $id  Item id
	 *
	 * @return bool
	 */
	private function moveImages($id)
	{
		jimport('joomla.filesystem.folder');

		$app = JFactory::getApplication();

		$images_folder = $app->getUserState('com_catalogue.edit.item.images_folder', '');
		$delete_folder = $app->getUserState('com_catalogue.edit.item.images_folder_delete', null);

		if ( $images_folder && ! empty($this->table->images) )
		{
			$images = new Registry;
			$images->loadString($this->table->images);

			$src_folder = JPATH_SITE . "/images/$images_folder";
			$dst_folder = JPATH_SITE . "/images/$id";

			if (!JFolder::exists($dst_folder))
			{
				JFolder::create($dst_folder);
			}

			foreach ($images as $image)
			{
				$src_filepath = str_replace('#IMAGESPATH#', $src_folder, $image->name);
				$dst_filepath = str_replace('#IMAGESPATH#', $dst_folder, $image->name);

				JFile::copy($src_filepath, $dst_filepath);
			}

			$app->setUserState('com_catalogue.edit.item.images_folder', '');
			$app->setUserState('com_catalogue.edit.item.images_folder_delete', null);

			if ($delete_folder === true)
			{
				JFolder::delete($src_folder);
			}
		}

		return true;
	}

	/**
	 * Saves attributes for item
	 *
	 * @param   string  $id  Saved item id
	 *
	 * @return bool
	 */
	private function saveAttributes($id)
	{

		$attrs_by_type = $this->table->get('attributes', false);

		if ($id && $attrs_by_type && is_array($attrs_by_type))
		{
			if ( (isset($attrs_by_type['list']) || isset($attrs_by_type['radio'])) && ! isset($attrs_by_type['bool']) )
			{
				$attrs_by_type['bool'] = [];
			}

			if (isset($attrs_by_type['list']))
			{
				JArrayHelper::toInteger($attrs_by_type['list']);
				$attrs_by_type['bool'] += array_fill_keys(array_keys(array_flip(array_filter($attrs_by_type['list']))), 1);
				unset($attrs_by_type['list']);
			}

			if (isset($attrs_by_type['radio']))
			{
				$attrs_by_type['bool'][$attrs_by_type['radio']] = 1;
				unset($attrs_by_type['radio']);
			}

			foreach ( $attrs_by_type as $type => $attrs)
			{

				// Dumb check if for some reason we don't have attr types in array
				if (is_int($type))
				{
					continue;
				}

				// Store attr in `#__catalogue_attr_value_text` table is this is custom attribute
				$type = in_array($type, $this->standart_types) ? $type : 'text';

				// Deleting old association for these items
				$db    = JFactory::getDbo();

				$table_name = $db->qn('#__catalogue_attr_value_' . $type);

				$query = $db->getQuery(true)
					->delete($table_name)
					->where($db->qn('item_id') . ' = ' . (int) $id);
				$db->setQuery($query);
				$db->execute();

				if (empty($attrs))
				{
					continue;
				}

				$query = $db->getQuery(true)->insert($table_name);

				foreach ($attrs as $attr_id => $value)
				{
					$query->values($id . ',' . $attr_id . ',' . $db->q($value, true));
				}

				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}

	/**
	 * Pre-processor for $table->delete($pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  void
	 */
	public function onBeforeDelete($pk)
	{
		/*
		 * Due to MySQL schema there is no need to manual delete mapping records
		 * because of "ON DELETE CASCADE" they will be deleted by themselves.
		 */
	}
}
