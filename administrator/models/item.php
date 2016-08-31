<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

JLoader::register('CatalogueHelper', JPATH_ADMINISTRATOR . '/components/com_catalogue/helpers/_catalogue.php');
JLoader::register('CatalogueHelperItem', JPATH_ADMINISTRATOR . '/components/com_catalogue/helpers/item.php');

/**
 * Item Model for an Item.
 */
class CatalogueModelItem extends JModelAdmin
{
	/**
	 * @var        string    The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_CATALOGUE';

	/**
	 * The type alias for this content type (for example, 'com_catalogue.item').
	 *
	 * @var      string
	 */
	public $typeAlias = 'com_catalogue.item';

	/**
	 * The context used for the associations table
	 *
	 * @var      string
	 */
	protected $associationsContext = 'com_catalogue.item';

	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		$config = array_merge(
			[
				'event_after_delete'  => 'onCatalogueAfterDeleteItem',
				'event_after_save'    => 'onCatalogueAfterSaveItem',
				'event_before_delete' => 'onCatalogueBeforeDeleteItem',
				'event_before_save'   => 'onCatalogueBeforeSaveItem',
				'events_map'          => [
					'delete' => 'catalogue',
					'save' => 'catalogue',
					'change_state' => 'catalogue'
				]
			], $config
		);

		parent::__construct($config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * @note Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$pk = $app->input->getInt('id');
		$this->setState($this->getName() . '.id', $pk);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_catalogue');
		$this->setState('params', $params);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return false;
			}

			$user = JFactory::getUser();

			return $user->authorise('core.delete', 'com_catalogue.item.' . (int) $record->id);
		}

		return false;
	}

	/**
	 * Method rebuild the entire nested set tree.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   1.6
	 */
	public function rebuild()
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->rebuild())
		{
			$this->setError($table->getError());

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		// Check for existing item.
		if (!empty($record->id))
		{
			$assetname = 'com_catalogue.item.' . (int) $record->id;
		}
		// New item, so check against the category.
		elseif (!empty($record->catid))
		{
			$assetname = 'com_catalogue.category.' . (int) $record->catid;
		}
		// Default to component settings if neither item nor category known.
		else
		{
			$assetname = 'com_catalogue';
		}

		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', $assetname);
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable($table)
	{
		// Set the publish date to now
		$db = $this->getDbo();

		if ($table->state == 1 && (int) $table->publish_up == 0)
		{
			$table->publish_up = JFactory::getDate()->toSql();
		}

		if ($table->state == 1 && intval($table->publish_down) == 0)
		{
			$table->publish_down = $db->getNullDate();
		}

		// Increment the content version number.
		$table->version++;

		// Reorder the items within the category so the new item is first
		if (empty($table->id))
		{
			$table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0');
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Items', $prefix = 'CatalogueTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);

		$item = ArrayHelper::toObject($properties, 'JObject', false);

		$item->itemtext = trim($item->fulltext) != ''
			? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext
			: $item->introtext;

		CatalogueHelperItem::decodeParams($item);

		if (!empty($item->id))
		{
			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->id, 'com_catalogue.item');
		}

		// Load associated catalogue items
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_catalogue', '#__catalogue_item', 'com_catalogue.item', $item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}


		return $item;
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_catalogue.item', 'item', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}

		// Determine correct permissions to check.
		if ($this->getState('item.id'))
		{
			$id = $this->getState('item.id');

			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');

			// Existing record. Can only edit own items in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing item.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_catalogue.item.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_catalogue')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an item you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}
		// Prevent messing with item language and category when editing existing item with associations
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		// Check if item is associated
		if ($this->getState('item.id') && $app->isSite() && $assoc)
		{
			$associations = JLanguageAssociations::getAssociations('com_catalogue', '#__catalogue_item', 'com_catalogue.item', $id);

			// Make fields read only
			if ($associations)
			{
				$form->setFieldAttribute('language', 'readonly', 'true');
				$form->setFieldAttribute('catid', 'readonly', 'true');
				$form->setFieldAttribute('language', 'filter', 'unset');
				$form->setFieldAttribute('catid', 'filter', 'unset');
			}
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_catalogue.edit.item.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('item.id') == 0)
			{
				$filters = (array) $app->getUserState('com_catalogue.items.filter');

				$filterCatId = isset($filters['category_id']) ? $filters['category_id'] : null;
				$data->set('catid', $app->input->getInt('catid', $filterCatId));
				$filterParentId = isset($filters['parent_id']) ? $filters['parent_id'] : null;
				$data->set('parent_id', $app->input->getInt('parent_id', $filterParentId));
			}
		}

		$this->preprocessData('com_catalogue.item', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 */
	public function save($data)
	{
		jimport('joomla.filesystem.folder');

		$dispatcher = JEventDispatcher::getInstance();
		$app        = JFactory::getApplication();
		$input      = $app->input;
		$table      = $this->getTable();
		$filter     = JFilterInput::getInstance();
		$pk         = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('item.id');
		$isNew      = true;
		$context    = 'com_catalogue.item';

		if ( ! empty($data['tags']) && $data['tags'][0] != '' )
		{
			$table->newTags = $data['tags'];
		}

		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
		}

		if (isset($data['created_by_alias']))
		{
			$data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
		}

		if (isset($data['images']) && is_array($data['images']))
		{
			$root = JUri::root(true);

			// Restruct images data
			$images = array_map(
				function($name, $size, $alt, $color, $title, $color_name) use ($root)
				{
					// This will be replaced to real path in item observer after table->load()
					$name = '#IMAGESPATH#' . '/' . JFile::makeSafe($name);

					return [
						'name'    => $name,
						'size'    => $size,
						'alt'     => $alt,
						'color'   => $color,
						'title'   => $title,
						'color_name'   => $color_name
					];
				},
				$data['images']['name'],
				$data['images']['size'],
				$data['images']['alt'],
				$data['images']['color'],
				$data['images']['title'],
				$data['images']['color_name']
			);
			$registry = new Registry;
			$registry->loadArray($images);
			$data['images'] = (string) $registry;
		}
		else
		{
			$data['images'] = '{}';
		}

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		// Load the row if saving an existing item.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;
		}

		if ( ! isset($data['parent_id']) )
		{
			$data['parent_id'] = 1;
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->parent_id != $data['parent_id'] || (int) $data['id'] === 0)
		{
			$table->setLocation($data['parent_id'], 'last-child');
		}

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$old_item_id = $input->getInt('id');

			$orig_table = clone $this->getTable();
			$orig_table->load($old_item_id);

			if ($data['images'] !== '{}')
			{
				$app->setUserState('com_catalogue.edit.item.images_folder', $old_item_id);
				$app->setUserState('com_catalogue.edit.item.images_folder_delete', false);
			}

			if ($data['title'] == $orig_table->title)
			{
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $orig_table->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['state'] = 0;
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (!isset($data['id']) || (int) $data['id'] === 0))
		{
			if ($data['alias'] == null)
			{
				if (JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
				}
				else
				{
					$data['alias'] = JFilterOutput::stringURLSafe($data['title']);
				}

				if ($this->getTable()->load(array('alias' => $data['alias'])))
				{
					$msg = JText::_('COM_CATALOGUE_SAVE_WARNING_ALIAS_EXISTS');
				}

				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['alias'] = $alias;

				if (isset($msg))
				{
					JFactory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}
		
		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Bind the rules.
		if (isset($data['rules']))
		{
			$rules = new JAccessRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the before save event.
		$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		$assoc = CatalogueHelper::getAssociations($pk);

		if ($assoc)
		{
			// Adding self to the association
			$associations = $data['associations'];

			// Unset any invalid associations
			$associations = Joomla\Utilities\ArrayHelper::toInteger($associations);

			foreach ($associations as $tag => $id)
			{
				if (!$id)
				{
					unset($associations[$tag]);
				}
			}

			// Detecting all item menus
			$all_language = $table->language == '*';

			if ($all_language && !empty($associations))
			{
				JError::raiseNotice(403, JText::_('COM_CATEGORIES_ERROR_ALL_LANGUAGE_ASSOCIATED'));
			}

			$associations[$table->language] = $table->id;

			// Deleting old association for these items
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->delete('#__associations')
				->where($db->quoteName('context') . ' = ' . $db->quote($this->associationsContext))
				->where($db->quoteName('id') . ' IN (' . implode(',', $associations) . ')');
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

			if (!$all_language && count($associations))
			{
				// Adding new association for these items
				$key = md5(json_encode($associations));
				$query->clear()
					->insert('#__associations');

				foreach ($associations as $id)
				{
					$query->values(((int) $id) . ',' . $db->quote($this->associationsContext) . ',' . $db->quote($key));
				}

				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					$this->setError($e->getMessage());

					return false;
				}
			}
		}

		// Trigger the after save event.
		$dispatcher->trigger($this->event_after_save, array($context, &$table, $isNew));

		// Rebuild the path for the tag:
		if (!$table->rebuildPath($table->id))
		{
			$this->setError($table->getError());

			return false;
		}

		// Rebuild the paths of the tag's children:
		if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path))
		{
			$this->setError($table->getError());

			return false;
		}

		$this->setState($this->getName() . '.id', $table->id);

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Association content items
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_CATALOGUE_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;

			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_item');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}

			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Custom clean the cache of com_catalogue and content modules
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_catalogue');
	}
}
