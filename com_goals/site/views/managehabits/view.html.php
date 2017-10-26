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
jimport( 'joomla.utilities.date' );

class GoalsViewManageHabits extends JViewLegacy
{
	protected $state = null;
	protected $habits = null;
    protected $pagination = null;
    protected $params = null;

       function display($tpl = null)
        {
            $jinput = JFactory::getApplication()->input;
            $week = $jinput->get('week', 0, 'int');
        	$this->state	= $this->get('State');
			$this->habits	= $habits = $this->get('Items');
			$this->pagination	= $this->get('Pagination');
            $this->params = GoalsHelperFE::getSettings();
            $this->startDate = JFactory::getDate()->toUnix()+$week*604800;
            $this->hrefBack = JRoute::_('index.php?option=com_goals&view=managehabits&tmpl=component&week='.(string)($week-1), false);
            $this->hrefForward = JRoute::_('index.php?option=com_goals&view=managehabits&tmpl=component&week='.(string)($week+1),false);
            if (sizeof($this->habits))
            {
                $habits = array();
                foreach ( $this->habits as $hab )
                {
                    $hab->complete_count = 0;
                    $hab->percent = 0;
                    $hab->todaydid = false;
                    $completes = GoalsHelperFE::getHabitLog($hab->id);
                    if ($completes)
                    {
                        if ($hab->finish>0) $hab->percent = round(($completes/$hab->finish)*100);
                        if ($hab->percent>=100) $hab->complete = 1;
                    }
                    $habits[] = $hab;
                }
                $this->habits=$habits;
            }
            $document = JFactory::getDocument();
			JHTML::_('behavior.tooltip');
            JHtml::_('jquery.framework');
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
			parent::display($tpl);
        }
}
