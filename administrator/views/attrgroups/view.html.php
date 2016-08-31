<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of attribute groups.
 *
 * @since  1.6
 */
class CatalogueViewAttrgroups extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			CatalogueHelper::addSubmenu('attrgroups');
		}

		JFactory::getDocument()
			->addScript('/administrator/components/com_catalogue/assets/js/admin-panel.js')
			->addStyleSheet('/administrator/components/com_catalogue/assets/css/admin-panel.css');

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo = JHelperContent::getActions('com_catalogue', 'attrgroup', $this->state->get('filter.category_id'));
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_CATALOGUE_ATTRGROUPS_MANAGER_TITLE'), 'grid-2');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_catalogue', 'core.create'))) > 0 )
		{
			JToolbarHelper::addNew('attrgroup.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('attrgroup.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('attrgroups.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('attrgroups.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('attrgroups.checkin');
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_catalogue')
			&& $user->authorise('core.edit', 'com_catalogue')
			&& $user->authorise('core.edit.state', 'com_catalogue'))
		{
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'attrgroups.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('attrgroups.trash');
		}

		if ($user->authorise('core.admin', 'com_catalogue') || $user->authorise('core.options', 'com_catalogue'))
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
			'g.ordering'     => JText::_('JGRID_HEADING_ORDERING'),
			'g.state'        => JText::_('JSTATUS'),
			'g.title'        => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'group_title'    => JText::_('COM_CATALOGUE_ATTR_GROUP_FILTER_TITLE'),
			'type_title'     => JText::_('COM_CATALOGUE_ATTR_TYPE_FILTER_TITLE'),
			'access_level'   => JText::_('JGRID_HEADING_ACCESS'),
			'g.created_by'   => JText::_('JAUTHOR'),
			'language'       => JText::_('JGRID_HEADING_LANGUAGE'),
			'g.created'      => JText::_('JDATE'),
			'g.id'           => JText::_('JGRID_HEADING_ID'),
		);
	}
}
