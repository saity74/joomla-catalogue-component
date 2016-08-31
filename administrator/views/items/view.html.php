<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of items.
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  12.2
 */
class CatalogueViewItems extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * @var  JForm
	 */
	public $filterForm;

	protected $isModifications;

	public $ordering;

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
		if ($this->getLayout() !== 'modal')
		{
			CatalogueHelper::addSubmenu('catalogue');
		}

		JFactory::getDocument()
			->addScript('/administrator/components/com_catalogue/assets/js/admin-panel.js')
			->addStyleSheet('/administrator/components/com_catalogue/assets/css/admin-panel.css');

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->isModifications = ( ($this->state->get('filter.parent_id', 1) ?: 1) > 1);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		$this->f_levels = $options;

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();

			if (!$this->isModifications)
			{
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		if ($this->isModifications)
		{
			// Disable filters
			foreach (['category_id', 'level'] as $f)
			{
				if ($this->filterForm->getField($f, 'filter'))
				{
					$this->filterForm->setFieldAttribute($f, 'disabled', 'true', 'filter');
				}
			}
		}

		parent::display($tpl);
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
		$canDo = JHelperContent::getActions('com_catalogue', 'category', $this->state->get('filter.category_id'));
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		if ($this->isModifications)
		{
			JToolbarHelper::custom('items.reset', 'arrow-left-2.png', 'arrow-left-2.png', 'JTOOLBAR_BACK', false);
			JToolbarHelper::title(JText::_('COM_CATALOGUE_ITEM_MODIFICATIONS_TITLE'), 'stack item');
		}
		else
		{
			JToolbarHelper::title(JText::_('COM_CATALOGUE_ITEMS_TITLE'), 'stack item');
		}

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_catalogue', 'core.create'))) > 0 )
		{
			JToolbarHelper::addNew('item.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('item.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('items.archive');
			JToolbarHelper::checkin('items.checkin');
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_catalogue')
			&& $user->authorise('core.edit', 'com_catalogue')
			&& $user->authorise('core.edit.state', 'com_catalogue'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('items.trash');
		}

		if ( ! $this->isModifications )
		{
			if ($user->authorise('core.admin', 'com_catalogue'))
			{
				JToolbarHelper::custom('items.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
			}

			if ($user->authorise('core.admin', 'com_catalogue') || $user->authorise('core.options', 'com_catalogue'))
			{
				JToolbarHelper::preferences('com_catalogue');
			}
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
			'mf.manufacturer_name'  => JText::_('COM_CATALOGUE_HEADING_MANUFACTURER'),
			'i.price'               => JText::_('COM_CATALOGUE_HEADING_PRICE'),
			'i.ordering'            => JText::_('JGRID_HEADING_ORDERING'),
			'i.state'               => JText::_('JSTATUS'),
			'i.title'               => JText::_('JGLOBAL_TITLE'),
			'category_title'        => JText::_('JCATEGORY'),
			'access_level'          => JText::_('JGRID_HEADING_ACCESS'),
			'i.created_by'          => JText::_('JAUTHOR'),
			'language'              => JText::_('JGRID_HEADING_LANGUAGE'),
			'i.created'             => JText::_('JDATE'),
			'i.id'                  => JText::_('JGRID_HEADING_ID'),
		);
	}
}
