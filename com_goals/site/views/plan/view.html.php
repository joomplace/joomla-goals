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

class GoalsViewPlan extends JViewLegacy
{
	protected $state = null;
	protected $plan = null;
    protected $user = null;
	protected $milistones = null;

	function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->plan		= $plan = $this->get('Items');
		$this->user 	= $user = JFactory::getUser(JFactory::getApplication()->input->get('user', null));

        if ($this->user->id!=$this->plan[0]->uid) {
            JError::raiseWarning(403, JText::_('COM_GOALS_ERROR_ACCESS'));
            return null;
        }

        $settings = GoalsHelper::getSettings();
        $this->settings = $settings;
        
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));	return false;
		}

		$jdate = new JDate('now');
		$nowdate =  $jdate->__toString();

		if (sizeof($plan)) {
			$this->plan			 = $plan = $plan[0];
			$plan->stages 	 = GoalsHelper::getStages($plan->id);


			$plan->records 	  	 = GoalsHelper::getPlanTasksCount($plan->id);
            $plan->records_done = count(GoalsHelper::getPlanTasksCount($plan->id, 1));
			$plan->records_count = $plan->records;
			$plan->percent 		 = 0;

            if ($plan->records_count) {

                $plan->percent = round(($plan->records_done / $plan->records_count) * 100);
            }

			//Date away late
			$left = GoalsHelper::date_diff($nowdate, $plan->deadline);
			$leftstr = GoalsHelper::getDateLeft($left);
			$plan->left = '('.$leftstr.' '.$left['lateoraway'].')';

			//Statuses
			if (sizeof($plan->stages)) {
				$lastmildate = null;

				foreach ( $plan->stages as $mil ) {
					//Date away late
					$left = GoalsHelper::date_diff($nowdate, $mil->duedate);
					$leftstr = GoalsHelper::getDateLeft($left);
					$mil->left = '('.$leftstr.' '.$left['lateoraway'].')';

					if ($left['lateoraway'] == 'away' && $mil->duedate > $nowdate) {
						$mil->leftstatus = GoalsHelper::getStatusLeft($left);
					} else {
						$mil->leftstatus = 4;
					}

					if ($mil->status == 0) {
						if (!$lastmildate) {
							$lastmildate = $mil->duedate;
						} else {
							if ($mil->duedate < $lastmildate) {
								$lastmildate=$mil->duedate;

							}
						}
					} else if ($mil->status == 1) {
						$mil->leftstatus = 0;
					}

                    $date_ahead = date('Y-m-d', strtotime($mil->duedate)-(int)$settings->n_days_ahed*24*60*60);
                    $date_behind =  date('Y-m-d', strtotime($mil->duedate)+(int)$settings->m_days_behind*24*60*60);

                    $now_date = date('Y-m-d', time());
                    if ($mil->status) {
                        $mil->status_image = 'completed';
                    } else {
                        if ($now_date<$date_ahead) {
                            $mil->status_image = 'ahead_plan';
                        } elseif ($now_date>$date_behind) {
                            $mil->status_image = 'behind_plan';
                        } else { $mil->status_image = 'just_in_time';}
                    }

				}
			}

            $plan->status = $plan->is_complete;
            $date_ahead = date('Y-m-d', strtotime($plan->deadline)-(int)$settings->n_days_ahed*24*60*60);
            $date_behind =  date('Y-m-d', strtotime($plan->deadline)+(int)$settings->m_days_behind*24*60*60);

            $now_date = date('Y-m-d', time());
            if ($plan->status) {
                $plan->status_image = 'completed';
            } else {
                if ($now_date<$date_ahead) {
                    $plan->status_image = 'ahead_plan';
                } elseif ($now_date>$date_behind) {
                    $plan->status_image = 'behind_plan';
                } else { $plan->status_image = 'just_in_time';}
            }

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

		JHTML::_('behavior.tooltip');

		$document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
        parent::display($tpl);
	}
}
