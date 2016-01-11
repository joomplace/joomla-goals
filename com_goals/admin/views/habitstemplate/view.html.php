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

class GoalsViewHabitsTemplate extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null) 
	{
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		 $document = JFactory::getDocument();
        	 $document->addStyleSheet(JURI::root().'administrator/components/com_goals/assets/css/js_color_picker.css');
        	 $document->addScript(JURI::root().'administrator/components/com_goals/assets/js/js_color_picker.js');

		$this->addToolBar();

		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		$user = JFactory::getUser();
		$isNew = $this->item->id == 0;
		$canDo = GoalsHelper::getActions();
		JToolBarHelper::title($isNew ? (JText::_('COM_GOALS').': '.JText::_('COM_GOALS_HABITSTEMPLATE_CREATING')) : (JText::_('COM_GOALS').': '.JText::_('COM_GOALS_HABITSTEMPLATE_EDITING')), 'habits');
		if ($isNew) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('habitstemplate.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('habitstemplate.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('habitstemplate.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		} else {
			if ($canDo->get('core.edit') or ($canDo->get('core.edit.own') and ($this->item->user_id == $user->get('id')))) {
				JToolBarHelper::apply('habitstemplate.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('habitstemplate.save', 'JTOOLBAR_SAVE');
				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('task.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
		}
		JToolBarHelper::cancel('habitstemplate.cancel', 'JTOOLBAR_CANCEL');
	}

}
