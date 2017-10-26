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

class GoalsViewAchievements extends JViewLegacy
{
	protected $state = null;
	protected $goals = null;
	protected $milistones = null;

	function display($tpl = null)
	{

		$this->state		= $this->get('State');
		$this->goals		= $this->get('GoalsList');
        $this->plans		= $this->get('PlansList');

        foreach ($this->plans as $plan) {
            $plan->task_count = GoalsHelper::getPlanTasksCount($plan->id,1);
            $plan->stages_count = count(GoalsHelper::getStages($plan->id));

        }

		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));	return false;
		}

		JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root().'components/com_goals/assets/css/progressbar.css');
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
		parent::display($tpl);
	}
}
