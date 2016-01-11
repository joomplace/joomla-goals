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

class GoalsViewHabithistory extends JViewLegacy
{
	protected $state = null;
	protected $logs = null;

       function display($tpl = null)
        {
        	$this->state	= $this->get('State');
			$this->logs	= $logs = $this->get('Items');
			$this->pagination	= $this->get('Pagination');

			if (count($errors = $this->get('Errors'))) { JError::raiseWarning(500, implode("\n", $errors));	return false;}


            $document = JFactory::getDocument();
			JHTML::_('behavior.tooltip');
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
            $document->addScript('http://code.jquery.com/ui/1.9.1/jquery-ui.js');  //TODO: in settings
			parent::display($tpl);
        }
}
