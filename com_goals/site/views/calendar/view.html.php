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



class GoalsViewCalendar extends JViewLegacy

{

    protected $state = null;

    public $_calendar = null;

    public $_calendar_date = 'now';



    function display($tpl = null)

    {

        $this->state = $this->get('State');



        if (count($errors = $this->get('Errors'))) {

            JError::raiseWarning(500, implode("\n", $errors));



            return false;

        }



        $action = JRequest::getVar('action', '');

        if ($action != 'refresh_calendar') {

            JHTML::_('behavior.tooltip');

            echo '<script language="javascript" src="' . JURI::root() . 'components/com_goals/assets/js/jquery.qtip.min.js"></script>';

            $document = JFactory::getDocument();

            $document->addStyleSheet(JURI::root() . "components/com_goals/assets/css/calendar.css");

            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');



        }





        $date = (JRequest::getInt('date')) ? JRequest::getInt('date') : time();

        $this->setCalendarDate($date);

        if ($action == 'refresh_calendar') $tpl = 'cal';

        parent::display($tpl);

        if ($action == 'refresh_calendar') die;

    }



    function getCalendarDate()

    {

        return $this->_calendar_date;

    }



    function setCalendarDate($date)

    {

        $this->_calendar_date = date('Y-m-d H:i:s', $date);

    }



    function &getCalendar()

    {

        if (!$this->_calendar) {

            $this->_createCalendar();

        }



        return $this->_calendar;

    }



    function _getEntries()

    {

        $db = JFactory::getDbo();

        $user = JFactory::getUser();

        $uid = (int)$user->id;

        $query = $db->getQuery(true);

        $query->select('t.*,"record" as type');

        $query->from('`#__goals_tasks` AS `t`');

        $query->join('LEFT', '`#__goals` AS `g` ON `g`.`id`=`t`.`gid` ');

        $query->where('`g`.`uid`=' . $uid . ' ');

        $query->order('`t`.`date` DESC');

        $db->setQuery($query);

        $dates = $db->loadObjectList();



        $temp_dates = array();

        if (!empty($dates)) {

            foreach ($dates as $date) {

                if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date->date, $match)) {

                    $temp_dates[$date->id][$date->type] = $match[0];

                }

            }

        }



        $query = $db->getQuery(true);

        $query->select('r.*, s.id as sid, s.title as stitle, "task" as type');

        $query->select('`g`.`title` AS `ptitle`, g.`metric` AS `pmetric`, `g`.id as `pid`');

        $query->from('`#__goals_plantasks` AS `r`');

        $query->join('LEFT', '`#__goals_stages` AS `s` ON `r`.`sid`=`s`.`id`');

        $query->join('LEFT', '`#__goals_plans` AS `g` ON `g`.`id`=`s`.`pid`');



        $query->where('`g`.`uid`=' . $user->id);

        $query->order('`r`.`date` DESC');

        $db->setQuery($query);

        $dates = $db->loadObjectList();

        if (!empty($dates)) {

            foreach ($dates as $date) {

                if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date->date, $match)) {

                    $temp_dates[$date->id][$date->type] = $match[0];

                }

            }

        }



        unset($dates);

        $dates = $temp_dates;

        return $dates;

    }



    function _getInfo($ids, $tids, $date)

    {

        $itemId = JRequest::getInt('Itemid');

        $itemId = $itemId ? ('&Itemid=' . $itemId) : '';

        $tmpl = JRequest::getVar('tmpl');

        if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';

        $db = JFactory::getDBO();

        $query = $db->getQuery(true);

        $html = "<div class='jbtip-in'><div class='jbtip-title'><small class='jbtip-title-small'>" . $date . "</small></div>";





        $search = (!empty($ids)) ? " t.id IN(" . implode(',', $ids) . ")" : "";

        $query->select('DISTINCT `t`.`title`, `t`.`id`,`t`.`date`, `t`.`gid`');

        $query->from('`#__goals_tasks` AS `t`');

        if($search){
        	$query->where($search);
    	}
    	
        $query->order('`t`.`date` DESC');

        $db->setQuery($query);

        $info = $db->loadObjectList();

        if (!empty($info)) {

            $html .= "<small class='amount-posts'>" . JText::_('COM_GOALS_THEREARE') . count($info) . JText::_('COM_GOALS_RECORD_AVAILABLE_ON_THIS_DATE') . "</small><ul class='jbtip-entries-list ulrest'>";

            foreach ($info as $i) {

                $html .= "<li><a href='" . JRoute::_('index.php?option=com_goals&view=record&id=' . $i->id . '&gid=' . $i->gid . $itemId . $tmpl) . "'>" . htmlspecialchars($i->title) . "</a></li>";

            }

            $html .= "</ul>";

        }

        $search = (!empty($tids)) ? " t.id IN(" . implode(',', $tids) . ")" : "";

        $info = new stdClass;
        if ($search){
            $query = $db->getQuery(true);

            $query->select('DISTINCT `t`.`title`, `t`.`id`,`t`.`date`, `t`.`sid`');

            $query->from('`#__goals_plantasks` AS `t`');

            $query->where($search);

            $db->setQuery($query);

            $info = $db->loadObjectList();
        }

        if (!empty($info)) {

            $html .= "<small class='amount-posts'>" . JText::_('COM_GOALS_THEREARE') . count($info) . JText::_('COM_GOALS_PLANTASKS_AVAILABLE_ON_THIS_DATE') . "</small><ul class='jbtip-entries-list ulrest'>";

            foreach ($info as $i) {

                $html .= "<li><a href='" . JRoute::_('index.php?option=com_goals&view=plantask&id=' . $i->id . '&sid=' . $i->sid . $itemId . $tmpl) . "'>" . htmlspecialchars($i->title) . "</a></li>";

            }

            $html .= "</ul>";

        }





        $html .= "</div>";



        return $html;



    }



    function _createCalendar()

    {



        // calendar

        //setlocale (LC_ALL, 'en_GB');



        $dates = $this->_getEntries();



        $date = $this->_calendar_date;



        $sundayfirst = false;



        $date = strtotime($date);



        $month = date('m', $date);

        $day = date('d', $date);

        $year = date('Y', $date);



        $wday = JDDayOfWeek(GregorianToJD($month, 1, $year), 0);



        if ($wday == 0)

            $wday = 7;

        $n = -($wday - ($sundayfirst ? 1 : 2));

        if ($sundayfirst && $n == -6)

            $n = 1;



        $cal = array();

        $nsat = ($sundayfirst ? 6 : 5);

        $nsun = ($sundayfirst ? 0 : 6);



        for ($y = 0; $y < 6; $y++) {

            $row = array();

            $notEmpty = false;

            for ($x = 0; $x < 7; $x++, $n++) {



                if (checkdate($month, $n, $year)) {



                    $day = $n;

                    $day = (intval($day) < 10) ? "0" . $day : $day;



                    $day_class = '';

                    $day_tip = '';

                    $day_style = '';

                    $day_html = $day;

                    $t_ids = $r_ids = array();

                    foreach ($dates as $key => $value) {



                        if (isset($value['record'])) {

                            if ($value['record'] == $year . '-' . $month . '-' . $day) {

                                $r_ids[] = $key;

                            }



                        } else {

                            if ($value['task'] == $year . '-' . $month . '-' . $day) {





                                $t_ids[] = $key;

                            }

                        }





                    }



                    if ($r_ids || $t_ids) {

                        $ids = array_keys($value, $year . '-' . $month . '-' . $day);

                        $timestamp = strtotime($year . '-' . $month . '-' . $day);

                        $ndate = date("d F Y", $timestamp);



                        $info = $this->_getInfo($r_ids, $t_ids, $ndate);

                        $day_class = !$day_class ? 'day yellow qtip' : $day_class;

                        $day_tip .= $info;

                    }



                    $day_class = !$day_class ? 'day' : $day_class;



                    $row[] = array('html'  => $day_html,

                                   'class' => $day_class,

                                   'style' => $day_style,

                                   'tip'   => ' title="' . $day_tip . '" '

                    );



                    $notEmpty = true;

                } else {

                    $row[] = array('html'  => '',

                                   'class' => 'day_empty',

                                   'style' => '',

                                   'tip'   => ''

                    );



                }



            }

            if (!$notEmpty)

                break;

            $cal[] = $row;

        }

        $this->_calendar = $cal;



        return;

    }

}

