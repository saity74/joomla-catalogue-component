<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('CatalogueHelper', JPATH_COMPONENT . '/helpers/catalogue.php');
/**
 * CatalogueViewCatalogue View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  12.2
 */
class CatalogueViewCatalogue extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $categories;

	protected $filterForm;

	protected $activeFilters;

	protected $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   12.2
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->categories = $this->get('Categories');

		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		/** @noinspection PhpUndefinedClassInspection */
		CatalogueHelper::addSubmenu('catalogue');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);

		return true;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		/** @noinspection PhpIncludeInspection */
		require_once JPATH_COMPONENT . '/helpers/catalogue.php';

		/** @noinspection PhpUndefinedClassInspection */
		$canDo = CatalogueHelper::getActions();

		JToolbarHelper::title(JText::_('COM_CATALOGUE_MANAGER'), 'component.png');
		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('item.add');
		}

		if (($canDo->get('core.edit')))
		{
			JToolbarHelper::editList('item.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				JToolbarHelper::publish('item.publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('item.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1)
			{
				if ($this->state->get('filter.published') != 2)
				{
					JToolbarHelper::archiveList('item.archive');
				}
				elseif ($this->state->get('filter.state') == 2)
				{
					JToolbarHelper::unarchiveList('item.publish');
				}
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::checkin('item.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'catalogue.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('catalogue.trash');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_catalogue');
		}

	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'c.item_name' => JText::_('COM_CATALOGUE_HEADING_NAME'),
			'mf.manufacturer_name' => JText::_('COM_CATALOGUE_HEADING_MANUFACTURER'),
			'category_title' => JText::_('JCATEGORY'),
			'c.price' => JText::_('COM_CATALOGUE_HEADING_PRICE'),
			'c.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'c.published' => JText::_('JSTATUS'),
			'c.id' => JText::_('JGRID_HEADING_ID')
		);
	}

}
