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

class GoalsViewGoal extends JViewLegacy
{
	protected $state = null;
	protected $goal = null;
    protected $user = null;
	protected $milistones = null;

	function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->goal		= $goal = $this->get('Items');
		$this->user 	= $user = JFactory::getUser(JFactory::getApplication()->input->get('user',null));
        $this->return   = GoalsHelper::getReturnURL();

        if ($this->user->id!=$this->goal[0]->uid) {
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

		if (sizeof($goal)) {
			$this->goal			 = $goal = $goal[0];
			$goal->milistones 	 = GoalsHelper::getMilistones($goal->id);
			$goal->records 	  	 = GoalsHelper::getRecords($goal->id);
            $goal->summary       = GoalsHelper::calculateGoal($goal->records, $goal->start, $goal->finish)->summary;
			$goal->records_count = sizeof($goal->records);
			$goal->percent 		 = 0;
			if ($goal->records_count) {
				$goal = GoalsHelper::getPercents($goal);
			}

			//Date away late
			$left = GoalsHelper::date_diff($nowdate, $goal->deadline);
			$leftstr = GoalsHelper::getDateLeft($left);
			$goal->left = '('.$leftstr.' '.$left['lateoraway'].')';

			//Statuses
			if (sizeof($goal->milistones)) {
				$lastmildate = null;

				foreach ( $goal->milistones as $mil ) {
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
			
			$this->goal->fields		= $fields = $this->get('Fields');

            $goal->status = $goal->is_complete;
            $date_ahead = date('Y-m-d', strtotime($goal->deadline)-(int)$settings->n_days_ahed*24*60*60);
            $date_behind =  date('Y-m-d', strtotime($goal->deadline)+(int)$settings->m_days_behind*24*60*60);

            $now_date = date('Y-m-d', time());
            if ($goal->status) {
                $goal->status_image = 'completed';
            } else {
                if ($now_date<$date_ahead) {
                    $goal->status_image = 'ahead_plan';
                } elseif ($now_date>$date_behind) {
                    $goal->status_image = 'behind_plan';
                } else { $goal->status_image = 'just_in_time';}
            }
            if ($goal->is_complete) {
                $goal->status = $settings->complete_status_color;
            } else {
                if ($now_date<$date_ahead) {
                    $goal->status = $settings->away_status_color;
                } elseif ($now_date>$date_behind) {
                    $goal->status = $settings->late_status_color;
                } else { $goal->status = $settings->justint_status_color;}
            }
		}

		JHTML::_('behavior.tooltip');

		$document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
        parent::display($tpl);
	}
}
