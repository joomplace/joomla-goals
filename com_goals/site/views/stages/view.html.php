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

class GoalsViewStages extends JViewLegacy
{
	protected $state = null;
	protected $stages = null;
	protected $milistones = null;

       function display($tpl = null)
        {
        	JHTML::_('behavior.modal');
        	$this->state	= $this->get('State');
			$this->stages		= $stages = $this->get('Items');
			$this->pagination	= $this->get('Pagination');

			if (count($errors = $this->get('Errors'))) { JError::raiseWarning(500, implode("\n", $errors));	return false;}

            $settings = GoalsHelper::getSettings();
            $this->settings = $settings;

			$jdate = new JDate('now');
			$nowdate =  $jdate->__toString();

			if (sizeof($stages))
			{
				$lastmildate = null;

				foreach ( $stages as $stage )
				{
                    $stage->tasks = GoalsHelper::getStageTasks($stage->id);
					$left = GoalsHelper::date_diff($nowdate, $stage->duedate);
					$leftstr = GoalsHelper::getDateLeft($left);
                    $stage->left = '('.$leftstr.' '.$left['lateoraway'].')';
					//
					if ($left['lateoraway']=='away' && $stage->duedate>$nowdate)
					{
                        $stage->leftstatus = GoalsHelper::getStatusLeft($left);

					}else
					{
                        $stage->leftstatus = 4;
					}

					if ($stage->status==0)
					{
						if (!$lastmildate)
						{
							$lastmildate = $stage->duedate;
							$lastmilstatus = $stage->leftstatus;
						}
						else
						{
							if ($stage->duedate<$lastmildate)
							{
								$lastmildate=$stage->duedate;
								$lastmilstatus = $stage->leftstatus;
							}
						}
					}else if ($stage->status==1)
					{
                        $stage->leftstatus = 0;
					}

                    $date_ahead = date('Y-m-d', strtotime($stage->duedate)-(int)$settings->n_days_ahed*24*60*60);
                    $date_behind =  date('Y-m-d', strtotime($stage->duedate)+(int)$settings->m_days_behind*24*60*60);
                    $now_date = date('Y-m-d', time());
                    if ($stage->status) {
                        $stage->status_image = 'completed';
                    } else {
                        if ($now_date<$date_ahead) {
                            $stage->status_image = 'ahead_plan';
                        } elseif ($now_date>$date_behind) {
                            $stage->status_image = 'behind_plan';
                        } else { $stage->status_image = 'just_in_time';}
                    }
				}
			}


            $document = JFactory::getDocument();
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
            
			parent::display($tpl);
        }
}
