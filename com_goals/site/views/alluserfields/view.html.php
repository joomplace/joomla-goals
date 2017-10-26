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

class GoalsViewAllUserfields extends JViewLegacy
{
	protected $state = null;
	protected $userfields = null;

       function display($tpl = null)
        {
        	$this->state	= $this->get('State');
			$this->userfields	= $userfields = $this->get('Items');
			$this->pagination	= $this->get('Pagination');
             $this->planuserfields = $this->get('PlanFields');


			if (count($errors = $this->get('Errors'))) { JError::raiseWarning(500, implode("\n", $errors));	return false;}
			if (!GoalsHelperFE::getSettings()->allow_userfields) { JError::raiseWarning(404, JText::_('COM_GOALS_RESTRICTED'));	return false;}

			JHTML::_('behavior.tooltip');

            $document = JFactory::getDocument();
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');

			parent::display($tpl);
        }
}
