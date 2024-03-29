<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class GoalsViewDashboard_item extends JViewLegacy
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
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $isNew = $this->item->id == 0;
        JToolBarHelper::title(JText::_('COM_GOALS').': '.JText::_('COM_GOALS_DASHBOARD_ITEM_EDITING'));
        $this->addToolBar();

        parent::display($tpl);
    }

    protected function addToolBar()
    {

        $user = JFactory::getUser();
        $canDo = GoalsHelper::getActions();
        if ($canDo->get('core.create')) {
                    JToolBarHelper::apply('dashboard_item.apply', 'JTOOLBAR_APPLY');
                    JToolBarHelper::save('dashboard_item.save', 'JTOOLBAR_SAVE');
                    JToolBarHelper::custom('dashboard_item.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                }
           
        JToolBarHelper::cancel('dashboard_item.cancel', 'JTOOLBAR_CANCEL');
    }
  
}
