<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class GoalsViewHabits extends JViewLegacy
{
	function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');

		$this->habits = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->user = JFactory::getUser();

		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

        $this->addToolBar();

		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		$this->canDo = $canDo = GoalsHelper::getActions();
		JToolBarHelper::title(JText::_('COM_GOALS').': '.JText::_('COM_GOALS_MANAGER_HABITS'), 'habits');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('habit.add', 'JTOOLBAR_NEW');
            // JToolBarHelper::addNew('habit.addTemplate', 'COM_GOALS_NEW_FROM_TEMPLATE');
		}
		if ($canDo->get('core.edit') or $canDo->get('core.edit.own')) {
			JToolBarHelper::editList('habit.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'habits.delete', 'JTOOLBAR_DELETE');
		}
	}

}
