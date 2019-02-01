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

class GoalsViewPlantasks extends JViewLegacy
{
	protected $state = null;
	protected $recs = null;
	protected $accordion = array('plans'=>false, 'stages'=>false);
	protected $active = array('plan'=>'', 'stage'=>'', 'task'=>'');

       function display($tpl = null)
        {
            $input = JFactory::getApplication()->input;
            $this->accordion['plans'] = $input->get('pid',false);

        	$this->state	= $this->get('State');
			$this->plans	= $this->get('Items');

            if($input->get('id',false)){
                $task = $this->get('TaskLegacy');
                if(!empty($task)) {
                    $this->active = array('plan' => $task->pid, 'stage' => $task->sid, 'task' => $task->id);
                }
            }
/*
			$settings = GoalsHelper::getSettings();
        	$this->settings = $settings;
*/
            foreach($this->plans as &$plan){
                $plan->percents = 1;
                $all = 0;
                $done = 0;
                foreach($plan->stages as &$stage){
                    $done+= $stage->tasks_count['done'] = GoalsHelper::getStageTasksCount($stage->id,true);
                    $all+= $stage->tasks_count['all']= count($stage->tasks);
                    if($stage->tasks){
                        foreach($stage->tasks as &$sta){
                            $sta->cfields = GoalsHelper::getCustomTaskFieldsPlans($plan->cid, $sta->id);
                            $sta->ufields = GoalsHelper::getCustomTaskUserFieldsPlans($plan->id, $sta->id);
                        }
                    }
                }
                if($all>0){
                    $plan->percents = floor($done/$all*100);
                }
            }

			if (count($errors = $this->get('Errors'))) { JError::raiseWarning(500, implode("\n", $errors));	return false;}

			JHTML::_('behavior.tooltip');

            $document =JFactory::getDocument();
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
			parent::display($tpl);
        }
}
