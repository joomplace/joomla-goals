<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

class GoalsControllerHabit extends JControllerForm
{
	public function getModel($name = 'Edithabit', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
	{
		if (empty($name)) {
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	public function save($key = null, $urlVar = null)
	{
		$app		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";
		$app->setUserState($context.'.id',JRequest::getInt('id'));
		return parent::save();
	}

	public function delete()
	{
		// Get items to remove from the request.
		$id	= JRequest::getVar('id', array(), '', 'array');
		$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
		if (!is_array($id) || count($id) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($id);
			// Remove the items.
			if ($model->delete($id)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_DELETED', count($id)));
			} else {
				$this->setMessage($model->getError());
			}
		}
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$tmpl, false));
	}

	public function cancel($key = null)
	{
		$mainframe = JFactory::getApplication();
		$id = JRequest::getInt('gid');
		$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
		$url =  JRoute::_('index.php?option=com_goals&view=allhabits'.$tmpl, false);
		$this->setRedirect($url,false);
	}

	public function changestatus()
	{
		$img='process_none';
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id	= JRequest::getInt('hid', 0);
		$query->select('l.`status`, h.type');
		$query->from('`#__goals_habits_log` AS `l`');
		$query->join('LEFT','`#__goals_habits` AS `h` ON h.id=l.hid ');
		$query->where('`l`.`id`='.$id);
		$query->group('`l`.`id`');
		$db->setQuery($query);
		$h = $db->loadObject();
		if ($h->status==0) $status=1; else $status=0;
		$query	= $db->getQuery(true);
		$query->update('`#__goals_habits_log`');
		$query->set('`status`='.$status);
		$query->where('`id`='.$id);
		$db->setQuery($query);
		$db->query();
		if ($h->type=='-')
		{
			if ($status==0) {
			}
			else
			{
				$img='process_bad';
			}
		}
		else
		{
			if ($status==1) {
				$img='process_good';
			}
		}
		echo '<a href="javascript:void(0);" onclick="javascript:gl_change_status('.$id.');"><img src="'.JURI::root().'components/com_goals/assets/images/'.$img.'.png" alt="'.JText::_('COM_GOALS_CHANGE_STATUS').'" /></a>';
		die;
	}

	public function addstatus()
	{
		$hid	= JRequest::getInt('hid', 0);
		$t		= JRequest::getInt('t', 0);
		$date	= date('Y-m-d 00:00:00');
        $day_date = date('Y-m-d');
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('h.*');
		$query->from('`#__goals_habits` AS `h`');
		$query->where('`h`.`id`='.$hid);
		$db->setQuery($query);
		$habit = $db->loadObject();
		$query	= $db->getQuery(true);
        /* if status has changed today */
        $query->select('id');
        $query->from('#__goals_habits_log');
        $query->where('`hid`='.$hid);
        $query->where('`date` LIKE "%'.$day_date.'%"');
        $db->setQuery($query);
        $isTodayChanged=$db->loadResult();
        $jdate = new JDate('now');
        $nw = $jdate->dayofweek;
        if ($nw==0) $nw=7;
        if ($habit->days)
        {
            $days = explode(',', $habit->days);
            if (in_array($nw,$days))
            {
                $habit->result = 1;
            } else {
                $habit->result = 0;
            }
        }
        if ($isTodayChanged) {
            $query = $db->getQuery(true);
            $query->delete('`#__goals_habits_log`');
            $query->where('`id`='.$isTodayChanged);
            $db->setQuery($query);
            $db->query();
        } else {
            $query = $db->getQuery(true);
            $query->insert('`#__goals_habits_log`');
            $query->set('`hid`='.$hid);
            $query->set('`date`="'.$date.'"');
            $query->set('`status`='.$t);
            $query->set('`result`='.$habit->result);
            $db->setQuery($query);
            $db->query();
        }
        $jdate = new JDate('now');
        $nw = $jdate->dayofweek;
        if ($nw==0) $nw=7;
        if ($habit->days)
        {
            $days = explode(',', $habit->days);
            if (in_array($nw,$days))
            {
                $completes = $this->getModel('Today')->getHabitLog($habit->id);
                if ($habit->finish>0) $habit->percent = round(($completes/$habit->finish)*100);
                if ($habit->percent>=100) $habit->complete = 1;
                $habit->complete_count = $completes;
            }
        }
       echo $habit->complete_count.';'.$habit->percent;
		exit();
	}

	public function deletelog()
	{
		$id	= JRequest::getInt('id', 0);
		$db		= JFactory::getDbo();
		$mainframe = JFactory::getApplication();
		$query	= $db->getQuery(true);
		$query->delete('`#__goals_habits_log`');
		$query->where('`id`='.$id);
		$db->setQuery($query);
		$msg = JText::sprintf('COM_GOALS_N_ITEMS_DELETED',1);
		if (!$db->query()) {
			//$msg=JText::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $db->getErrorMsg());
			$msg="Delete is failed.";
			$this->setMessage($msg,'notice');
		} else $this->setMessage($msg);
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view=habithistory'.$this->getRedirectToListAppend(), false));
	}

	public function showallGraph()
	{
		error_reporting(E_ALL & ~E_NOTICE);
		$user 		= JFactory::getUser();
		$uid 		= JRequest::getInt('u',0);
		$cal_start 	= JRequest::getVar('stc');
		$cal_end 	= JRequest::getVar('ecl');
		$format 	= GoalsHelper::getSettings()->chart_date_format;
		$format_s 	= $format ? $format : '%d-%m-%y'; // format_s - old variable with notice
		$format 	= '%d-%m-%Y';
		$db 		= JFactory::getDbo();
		if (!$uid) $uid = $user->id;
		if (!$uid) return;
		$query = $db->getQuery(true)
					->select('DISTINCT(DATE_FORMAT(l.date, "'.$format.'"))')
					->from('`#__goals_habits_log` AS `l`')
					->leftJoin('`#__goals_habits` AS `h` ON h.id=l.hid')
					->where('`h`.`uid` ='.$uid)
					->order('`l`.`date` ASC');
		$db->setQuery($query);
		$dates = $db->loadColumn();
		$values = array();
		if ($cal_start) {
			$jdate 		= new JDate($cal_start);
			$cal_start 	= $jdate->format($format);
			$cal_start	= strtotime($cal_start);
		}
		if ($cal_end) {
			$jdate 		= new JDate($cal_end);
			$cal_end 	= $jdate->format($format);
			$cal_end 	= strtotime($cal_end);
		}
		foreach ( $dates as $k=>$date ) {
			if ($cal_start) {
				if (strtotime($date) < $cal_start) {
					unset($dates[$k]);
					continue;
				}
			}
			if ($cal_end && ($dates[$k])) {
				if (strtotime($date) > $cal_end) {
					unset($dates[$k]);
					continue;
				}
			}
			$query = $db->getQuery(true)
						->select('h.`weight`,h.type, l.result')
						->from('`#__goals_habits_log` AS `l`')
						->leftJoin('`#__goals_habits` AS `h` ON h.id=l.hid')
						->where('`h`.`uid` ='.$uid)
						->where('"'.$date.'"=DATE_FORMAT(l.date, "'.$format.'")')
						->order('`l`.`date` ASC');
			$db->setQuery($query);
			$habs = $db->loadObjectList();
			$val = 0;
			$p = 0;
			$n = 0;
			if (!empty($habs)) {
				foreach ( $habs as $hab ) {
					if ($hab->type == '+') {
						$val += $hab->weight;
						$pos[$dates[$k]] = $p + $hab->weight;
                        $p+=$hab->weight;
					} elseif ($hab->type == '-') {
						$val -= $hab->weight;
						$neg[$dates[$k]] = $n - $hab->weight;
					} else {
						$pos[$dates[$k]] = 0;
						$neg[$dates[$k]] = 0;
					}
                    if (!isset($pos[$dates[$k]])) {
                        $pos[$dates[$k]] = 0;
                    }
                    if (!isset($neg[$dates[$k]])) {
                        $neg[$dates[$k]] = 0;
                    }
				}
			}
			if ($dates[$k]) {
				$jdate 		= new JDate($dates[$k]);
                if (GoalsHelper::getJoomla3Vesion()) {
                  $format_s = str_replace('%','',$format_s);
                }
				$dates[$k] 	= JHtml::_('date', $dates[$k], JText::_('DATE_FORMAT_LC3'));
				$values[]	= (int)$val;
			}
		}
		
        if(count($values)>=10){
			$every = floor(count($values)/10)+2;
			for($i=0; $i<=count($values); $i++){
				if($i%$every!=0) $dates[$i]='';
			}
        }

		/* pChart library inclusions */
		require_once (JPATH_COMPONENT."/helpers/pChart/class/pData.class.php");
		require_once (JPATH_COMPONENT."/helpers/pChart/class/pDraw.class.php");
		require_once (JPATH_COMPONENT."/helpers/pChart/class/pImage.class.php");

		/* Create and populate the pData object */
		$MyData = new pData();
		$MyData->addPoints(!empty($pos) ? $pos : array(VOID,VOID,VOID,VOID,VOID),"Positive");
		$MyData->addPoints(!empty($neg) ? $neg : array(VOID,VOID,VOID,VOID,VOID),"Negative");
		$MyData->addPoints(!empty($values) ? $values : array(VOID,VOID,VOID,VOID,VOID),"Spline");
		$MyData->setAxisName(0, JText::_("Weight"));
		$MyData->addPoints(!empty($dates) ? $dates : array(VOID,VOID,VOID,VOID,VOID),"Labels");
		$MyData->setSerieDescription("Labels","Dates");
		$MyData->setAbscissa("Labels");
		$MyData->setAxisDisplay(0,AXIS_FORMAT_CUSTOM,"YAxisFormat");

		/* Create the pChart object */
		$myPicture = new pImage(700,230,$MyData);

		/* Set the default font properties */
		$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>JPATH_COMPONENT."/helpers/pChart/fonts/tahoma.ttf","FontSize"=>8));
		if (empty($dates)) {
			$myPicture->drawText(370, 118, JText::_('COM_GOALS_CHART_NO_DATA'), array("FontSize"=>16, "Align"=>TEXT_ALIGN_BOTTOMMIDDLE, "R"=>206, "G"=>206, "B"=>206));
		}

		/* Draw the scale and the chart */
		$myPicture->setGraphArea(60,20,680,190);
		$Settings = array(
				"Pos" 			 	=> SCALE_POS_LEFTRIGHT,
				"Mode" 			 	=> SCALE_MODE_ADDALL,
				"LabelingMethod" 	=> LABELING_ALL,
				"GridR" 		 	=> 214,
				"GridG" 		 	=> 212,
				"GridB" 		 	=> 212,
				"GridAlpha" 	 	=> 50,
				"TickR" 		 	=> 130,
				"TickG" 		 	=> 121,
				"TickB" 		 	=> 121,
				"TickAlpha" 	 	=> 50,
				"LabelRotation"		=> 0,
				"CycleBackground" 	=> 1,
				"DrawYLines" 		=> ALL
		);
		$myPicture->drawScale($Settings);

		//$myPicture->drawThreshold();
		$MyData->setSerieDrawable("Positive",	TRUE);
		$MyData->setSerieDrawable("Negative",	TRUE);
		$MyData->setSerieDrawable("Spline",		FALSE);
		$myPicture->setShadow(FALSE);
		$myPicture->drawStackedBarChart(array("DisplayValues"=>FALSE,"DisplayColor"=>DISPLAY_AUTO,"Gradient"=>FALSE,"Surrounding"=>-20,"InnerSurrounding"=>20));

		/* Draw the line and plot chart */
		$MyData->setSerieDrawable("Positive",	FALSE);
		$MyData->setSerieDrawable("Negative",	FALSE);
		$MyData->setSerieDrawable("Spline",		TRUE);
		$myPicture->setShadow(TRUE, array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$myPicture->drawSplineChart();
		$myPicture->setShadow(FALSE);
		$myPicture->drawPlotChart(array("DisplayValues"=>TRUE,"PlotSize"=>3,"PlotBorder"=>TRUE,"BorderSize"=>3,"BorderAlpha"=>20));

		/* Make sure all series are drawable before writing the scale */
		$MyData->drawAll();
		$myPicture->stroke();
		die;
	}

    public function switchstatus() {
        $hid	= JRequest::getInt('hid', 0);
        $day		= JRequest::getVar('day', 0);
        $date	= date('Y-m-d H:i:s');
        $day_date = date('Y-m-d');
        $db		= JFactory::getDbo();
        $query	= $db->getQuery(true);
        $query->select('h.*');
        $query->from('`#__goals_habits` AS `h`');
        $query->where('`h`.`id`='.$hid);
        $db->setQuery($query);
        $habit = $db->loadObject();
        $query	= $db->getQuery(true);
        /* if status has changed today */
        $query->select('id');
        $query->from('#__goals_habits_log');
        $query->where('`hid`='.$hid);
        $query->where('`date` LIKE "%'.$day.'%"');
        $jdate = new JDate($day);
        $nw = $jdate->dayofweek;
        if ($nw==0) $nw=7;
        if ($habit->days)
        {
            $days = explode(',', $habit->days);
            if (in_array($nw,$days))
            {
                $habit->result = 1;
            } else {
                $habit->result = 0;
            }
        }
        $db->setQuery($query);
        $isTodayChanged=$db->loadResult();
        if ($isTodayChanged) {
            $query = $db->getQuery(true);
            $query->delete('`#__goals_habits_log`');
            $query->where('`id`='.$isTodayChanged);
            $db->setQuery($query);
            $db->query();
        } else {
            $query = $db->getQuery(true);
            $query->insert('`#__goals_habits_log`');
            $query->set('`hid`='.$hid);
            $query->set('`date`="'.$day.'"');
            $query->set('`status`=1');
            $query->set('`result`='.$habit->result);
            $db->setQuery($query);
            $db->query();
        }
        if ($habit->days)
        {
            $days = explode(',', $habit->days);
            if (in_array($nw,$days))
            {
                $completes = $this->getModel('Today')->getHabitLog($habit->id);
                if ($habit->finish>0) $habit->percent = round(($completes/$habit->finish)*100);
                if ($habit->percent>=100) $habit->complete = 1;
                $habit->complete_count = $completes;
            echo $habit->complete_count.';'.$habit->percent;
            } else {
                exit();
            }
        }
        exit();
    }

    public function sortingFeatured()
    {
        $post = JRequest::get('post');
        $items = $post['ids'];
        $items_array = explode(',', $items);
        $i = 1;
        foreach ($items_array as $item) {
            $table = explode('_', $item);
            $id = $table[1];
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->update('#__goals_habits');
            $query->set('ordering=' . $i);
            $query->where('id = ' . $id);
            $db->setQuery($query);
            $db->query();
            $i++;
        }
        die;
    }

    public function featuredOnOff()
    {
        $id = JRequest::getVar('id');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('featured');
        $query->from('#__goals_habits');
        $query->where('`id`='.$id);
        $db->setQuery($query);
        $featured = (int)$db->loadResult();
        $query = $db->getQuery(true);
        $query->update('#__goals_habits');
        $query->set('featured='.($featured?'0':'1'));
        $query->where('id='.$id);
        $db->setQuery($query)->query();
        $this->setRedirect(JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'habits', 'tmpl' => $tmpl)), false));
        return;
    }
}
