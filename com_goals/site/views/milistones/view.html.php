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

class GoalsViewMilistones extends JViewLegacy
{
	protected $state = null;
	protected $mils = null;
	protected $milistones = null;

       function display($tpl = null)
        {
        	JHTML::_('behavior.modal');
        	$this->state	= $this->get('State');
			$this->mils		= $mils = $this->get('Items');
			$this->pagination	= $this->get('Pagination');

			$settings = GoalsHelper::getSettings();
        	$this->settings = $settings;

			if (count($errors = $this->get('Errors'))) { JError::raiseWarning(500, implode("\n", $errors));	return false;}
            $settings = GoalsHelper::getSettings();
			$jdate = new JDate('now');
			$nowdate =  $jdate->__toString();

			if (sizeof($mils))
			{
				$lastmildate = null;

				foreach ( $mils as $mil )
				{
					$left = GoalsHelper::date_diff($nowdate, $mil->duedate);
					$leftstr = GoalsHelper::getDateLeft($left);
					$mil->left = '('.$leftstr.' '.$left['lateoraway'].')';
					//
					if ($left['lateoraway']=='away' && $mil->duedate>$nowdate)
					{
						$mil->leftstatus = GoalsHelper::getStatusLeft($left);

					}else
					{
						$mil->leftstatus = 4;
					}

					if ($mil->status==0)
					{
						if (!$lastmildate)
						{
							$lastmildate = $mil->duedate;
							$lastmilstatus = $mil->leftstatus;
						}
						else
						{
							if ($mil->duedate<$lastmildate)
							{
								$lastmildate=$mil->duedate;
								$lastmilstatus = $mil->leftstatus;
							}
						}
					}else if ($mil->status==1)
					{
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


            $document = JFactory::getDocument();
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
            //TODO: in settings
			parent::display($tpl);
        }
}
