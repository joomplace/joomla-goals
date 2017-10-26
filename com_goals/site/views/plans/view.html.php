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

class GoalsViewPlans extends JViewLegacy
{
	protected $state = null;
	protected $plans = null;
	protected $milistones = null;

	function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->goals		= $plans = $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));	return false;
		}
        $settings = GoalsHelper::getSettings();
        $this->settings = $settings;

		if (sizeof($plans)) {
			foreach ($plans as $plan)	{
                $plan->stages 	 = GoalsHelper::getStages($plan->id);
                $plan->task_count = GoalsHelper::getPlanTasksCount($plan->id);
                $plan->task_count_complete = GoalsHelper::getPlanTasksCount($plan->id, 1);
				$plan->percent		 = 0;
				if ($plan->task_count) {
					$plan->percent 	= round($plan->task_count_complete/$plan->task_count*100);
				}
                $jdate = new JDate('now');
                $nowdate = $jdate->__toString();

                $tillleft = GoalsHelper::date_diff($nowdate, $plan->startup, 'start');
                $tillleftstr = GoalsHelper::getDateLeft($tillleft);
                $plan->tillleft = '('.$tillleftstr.' '.$tillleft['lateoraway'].')';

                $left = GoalsHelper::date_diff($nowdate, $plan->deadline);
                $leftstr = GoalsHelper::getDateLeft($left);
                $plan->left = '('.$leftstr.' '.$left['lateoraway'].')';

                $date_ahead = date('Y-m-d', strtotime($plan->deadline)-(int)$settings->n_days_ahed*24*60*60);
                $date_behind =  date('Y-m-d', strtotime($plan->deadline)+(int)$settings->m_days_behind*24*60*60);
                $now_date = date('Y-m-d', time());
                if ($plan->is_complete) {
                    $plan->status = $settings->complete_status_color;
                } else {
                    if ($now_date<$date_ahead) {
                        $plan->status = $settings->away_status_color;
                    } elseif ($now_date>$date_behind) {
                        $plan->status = $settings->late_status_color;
                    } else { $plan->status = $settings->justint_status_color;}
                }
			}
		}

		JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
		parent::display($tpl);
	}
}
