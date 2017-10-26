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

class GoalsViewToday extends JViewLegacy
{
    protected $state = null;
    protected $goals = null;
    protected $milistones = null;
    public $_calendar = null;
    public $_calendar_date = 'now';

    function display($tpl = null)
    {
        $this->state		= $this->get('State');
        $this->goals		= $goals = $this->get('Items');
        $this->milistones	= $milistones = $this->get('Milistones');
        $this->records		= $records = $this->get('Records');
        $this->tasks		= $tasks = $this->get('Tasks');
        $this->habits		= $milistones = $this->get('Habits');
        $this->pagination	= $this->get('Pagination');
        $this->overdue_tasks = $this->get('OverdueTasks');

        $this->count_todayrecords = count($this->records);


        $model = $this->getModel();

        $jdate = new JDate('now');
        $nowdate =  $jdate->__toString();

        if (sizeof($goals)) {
            foreach ( $goals as $goal ) {
                $goal->milistones 	 = GoalsHelperFE::getMilistones($goal->id);
                $goal->records 	 	 = GoalsHelperFE::getRecords($goal->id);

                $goal->records_count = sizeof($goal->records);
                $goal->percent 		 = 0;
                if ($goal->records_count) {
                    $last_rec		= array_pop($goal->records);
                    $cur_value		= $last_rec->value;
                    $goal->percent 	= round( (abs(($cur_value - $goal->start))*100)/abs(($goal->finish - $goal->start)) );
                }

                //DAte away late
                $left = GoalsHelperFE::date_diff($nowdate, $goal->deadline);
                $leftstr = GoalsHelperFE::getDateLeft($left);
                $goal->left = '('.$leftstr.' '.$left['lateoraway'].')';
                //

                //Statuses
                if (sizeof($goal->milistones))
                {
                    $lastmildate = null;
                    $lastmilstatus = 4;

                    foreach ( $goal->milistones as $k=>$mil )
                    {
                        //Date away late
                        $left = GoalsHelperFE::date_diff($nowdate, $mil->duedate);
                        $leftstr = GoalsHelperFE::getDateLeft($left);
                        $mil->left = '('.$leftstr.' '.$left['lateoraway'].')';
                        //
                        if ($left['lateoraway']=='away' && $mil->duedate>$nowdate)
                        {
                            $mil->leftstatus = GoalsHelperFE::getStatusLeft($left);

                        }
                        else
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
                        }
                        else if ($mil->status==1)
                        {
                            $mil->leftstatus = 0;
                        }
                    }

                    if ($goal->deadline<$nowdate) $goal->status = 4;
                    else  if ($lastmilstatus)
                    {
                        $goal->status = $lastmilstatus;

                    }
                }else
                {
                    if ($left['lateoraway']=='away' && $goal->deadline>$nowdate)
                    {
                        $goal->status = GoalsHelperFE::getStatusLeft($left);
                    }else
                    {
                        $goal->status = 4;
                    }
                }
                if ($goal->is_complete) $goal->status = 5;
            }
        }

        //habits

        if (sizeof($this->habits))
        {

            $habits = array();
            $nw = $jdate->dayofweek;
            if ($nw==0) $nw=7;
            foreach ( $this->habits as $hab )
            {
                $hab->complete_count = 0;
                $hab->percent = 0;
                $hab->todaydid = false;
                if ($hab->days)
                {
                    $days = explode(',', $hab->days);
                    if (in_array($nw,$days))
                    {
                        $completes = $model->getHabitLog($hab->id);
                        if ($this->getTodayHabit($hab->id)) {
                             $hab->todaydid = true;
                        }

                        {
                            $hab->complete_count= $completes;
                            if ($hab->finish>0) $hab->percent = round(($completes/$hab->finish)*100);
                            if ($hab->percent>=100) $hab->complete = 1;
                        }

                        if (!$hab->complete) $habits[] = $hab;
                    }

                }

            }

            $this->habits=$habits;
        }
        //


        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));	return false;
        }

        JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
        parent::display($tpl);
    }

    public function getTodayHabit($id) {
        $day = date('Y-m-d');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__goals_habits_log');
        $query->where('hid='.$id);
        $query->where('date LIKE "'.$day.'%"');
        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result?1:0;
    }


}
