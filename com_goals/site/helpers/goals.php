<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die;

class GoalsHelper
{
    public function getHabitLog($id = 0)
    {
        $db = JFactory::getDBO();
        /*
         * Get main info about habit
         */
        $query = $db->getQuery(true);
        $query->select('type');
        $query->from('`#__goals_habits`');
        $query->where('`id`=' . $id);
        $db->setQuery($query);
        $type = $db->loadResult();
        $query = $db->getQuery(true);
        $query->select('date');
        $query->from('`#__goals_habits`');
        $query->where('`id`=' . $id . ' AND `date`!="0000-00-00"');
        $db->setQuery($query);
        $date = $db->loadResult();
        $query = $db->getQuery(true);
        $query->select('days');
        $query->from('`#__goals_habits`');
        $query->where('`id`=' . $id);
        $db->setQuery($query);
        $days = $db->loadResult();
        if ($type == '+') { // Positive habit
            $dateToday = date_create(date('Y-m-d', time()));
            $dateCreateHabit = date_create($date);

            //calculate days interval
            $interval = date_diff($dateCreateHabit, $dateToday);
            $daysCounter = $interval->days;
            $datesRequired = array();
            $habitDays = explode(',', $days);
            for ($i = 0; $i <= $daysCounter; $i++) {
                // Start object creating, that contains all required dates
                if ($i) {
                    //start increment date
                    $dateCreateHabit->modify('+ 1 day');
                }
                // what day of week is
                $resDateDayNumber = date("w", $dateCreateHabit->getTimestamp());
                //Check necessity of this habit at that day
                if (in_array($resDateDayNumber, $habitDays)) {
                    //Add to array this date
                    $datesRequired[] = $dateCreateHabit->format('Y-m-d 00:00:00');
                }
            }
            $query = $db->getQuery(true);
            $query->select('l.date');
            $query->from('`#__goals_habits_log` AS `l`');
            $query->where('`l`.`hid`=' . $id);
            $query->where('result=1');
            $query->order('`l`.`date` ASC');
            $db->setQuery($query);
            $checkedDates = $db->loadColumn();
            //Init of check counter
            $counter = 0;
            foreach ($datesRequired as $dateReq) {
                if (in_array($dateReq, $checkedDates)) {
                    $counter++;
                } else {
                    $counter = 0;
                }
            }

            //Return @int counter
            return $counter;
        } else {
            $query = $db->getQuery(true);
            $query->select('l.*');
            $query->from('`#__goals_habits_log` AS `l`');
            $query->where('`l`.`hid`=' . $id);
            $query->where('result=1');
            $query->order('`l`.`date` DESC LIMIT 0,1');
            $db->setQuery($query);
            $res = $db->loadObject();
            $datetime1 = date_create(date('Y-m-d', time()));
            if (!isset($res->date)) {
                $datetime2 = date_create($date);
            } else {
                $datetime2 = date_create($res->date);
            }
            $interval = date_diff($datetime1, $datetime2);
            if ($interval->days > 0 && $datetime1 > $datetime2) {
                if (!isset($res->date)) {
                    $jdate = new JDate($date);
                } else {
                    $jdate = new JDate($res->date);
                }
                $resDateDayNumber = (int)$jdate->dayofweek;
                $habitDays = explode(',', $days);
                $days_counter = 0;
                for ($i = 1; $i <= $interval->days; $i++) {
                    if (in_array($resDateDayNumber, $habitDays)) {
                        $days_counter++;
                    }
                    $resDateDayNumber++;
                    if ($resDateDayNumber > 7) {
                        $resDateDayNumber = 1;
                    }
                }
                return $days_counter;
            } else {
                return 0;
            }
        }
    }

    public function dayToStr($day, $abbr = false)
    {
        switch ($day) {
            case 0:
                return $abbr ? JText::_('SUN') : JText::_('SUNDAY');
            case 1:
                return $abbr ? JText::_('MON') : JText::_('MONDAY');
            case 2:
                return $abbr ? JText::_('TUE') : JText::_('TUESDAY');
            case 3:
                return $abbr ? JText::_('WED') : JText::_('WEDNESDAY');
            case 4:
                return $abbr ? JText::_('THU') : JText::_('THURSDAY');
            case 5:
                return $abbr ? JText::_('FRI') : JText::_('FRIDAY');
            case 6:
                return $abbr ? JText::_('SAT') : JText::_('SATURDAY');
        }
    }

    public static function getSettings()
    {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT `params` FROM #__extensions WHERE element="com_goals"');
        $params = json_decode($db->loadResult());
        return $params;
    }

    public function calculateGoal($data = array(), $start = 0, $finish = 0){
        $response = new stdClass();
        $response->summary = $start;
        $response->dinamic = array();
        $response->dinamic[] = array($start,'');
        $response->percents = 0;

        foreach($data as $in){
            if($in->result_mode == 0){
                $response->summary = $in->value;
            }else{
                $response->summary += $in->value;
            }
            $response->dinamic[] = array(($response->summary + $in->value), $in->date);
        }

        if($start == $finish || $response->summary == $finish) $response->percents = 100;
        else $response->percents = round(100 * (($response->summary - $start) / ($finish - $start)));

        //$response->dinamic = array($finish,'');
        return $response;
    }

    public function showDashHeader($featuredActive = 'active', $goalsActive = '', $plansActive = '', $achievementsActive = '', $relativeLinks = false)
    {
        $jinput = JFactory::getApplication()->input;

        if ($jinput->get('tmpl') == 'component') return '';
        $tmpl = $jinput->get('tmpl');
        $db = JFactory::getDbo();

        $query = GoalsHelper::getListQuery("goals",$db, null, array("is_complete" => 0, "featured" => 1));
        $db->setQuery($query);
        $featured_goals =  count($db->loadObjectList());
        $query = GoalsHelper::getListQuery("plans",$db, null, array("is_complete" => 0, "featured" => 1));
        $db->setQuery($query);
        $featured_plans =  count($db->loadObjectList());

        $query = GoalsHelper::getListQuery("goals",$db);
        $db->setQuery($query);
        $goals_count = count($db->loadObjectList());
        $query = GoalsHelper::getListQuery("plans",$db);
        $db->setQuery($query);
        $plans_count = count($db->loadObjectList());
        $query = GoalsHelper::getListQuery("goals",$db, null, array("is_complete" => 1));
        $db->setQuery($query);
        $done_goals =  count($db->loadObjectList());
        $query = GoalsHelper::getListQuery("plans",$db, null, array("is_complete" => 1));
        $db->setQuery($query);
        $done_plans =  count($db->loadObjectList());

        /*
         * Count of goals templates
         */
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__goalstemplates');
        $db->setQuery($query);
        $goalstemplates_count = (int)$db->loadResult();
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__goals_planstemplates');
        $db->setQuery($query);
        $planstemplates_count = (int)$db->loadResult();
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__goals_stages');
        $db->setQuery($query);
        $stages_count = (int)$db->loadResult();

        if($GLOBALS["viewed_user"]->id == $GLOBALS["viewer_user"]->id)
            $can_manage = true;

        if($jinput->get('option')=='easysocial')
            $relativeLinks = true;

        GoalsHelper::renderDashHeader($relativeLinks, $can_manage, $tmpl, $featured_goals, $featured_plans, $goals_count, $plans_count, $stages_count, $goalstemplates_count, $planstemplates_count, $done_goals, $done_plans, $featuredActive, $goalsActive, $plansActive, $achievementsActive);
    }

    protected function makeDashLink($vars=array(), $relativeLinks='false'){

        $router = JRouter::getInstance('site');
		
        $query = $router->getVars();

        if($relativeLinks){
            if($query['option']=='com_easysocial'){
                $vars['easyview'] = $vars['view'];
                unset($vars['view']);
            }
        }
        $path = array_merge($query, $vars);

        if(!array_key_exists('Itemid',$path)) $path['Itemid'] = GoalsHelperRoute::getClosesItemId(array('view' => $path['view']));
        else if(!$path['Itemid']) $path['Itemid'] = GoalsHelperRoute::getClosesItemId(array('view' => $path['view']));

        $link = '';
        $segments = array();
        foreach($vars as $var => $val){
            if($val) $segments[] = $var.'='.$val;
        }

        $link = implode('&',$segments);

        if($link) $link = 'index.php?'.$link;
        else $link = 'index.php';

        return $link;
    }

    protected function renderDashHeader($relativeLinks, $can_manage = false, $tmpl, $featured_goals, $featured_plans, $goals_count, $plans_count, $stages_count, $goalstemplates_count, $planstemplates_count, $done_goals, $done_plans, $featuredActive = 'active', $goalsActive = '', $plansActive = '', $achievementsActive = ''){

        $settings = GoalsHelper::getSettings();
        ?>
        <div class="navbar">
            <div class="navbar-inner">
                <a class="brand" href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'dashboard'))); ?>"><?php echo JFactory::getDocument()->getTitle(); ?></a>
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="nav-collapse collapse navbar-responsive-collapse">
                    <ul class="nav pull-right">
                        <?php if($can_manage){ ?>
                            <li class="dropdown">
                                <a href="#" title="<?php echo JText::_('COM_GOALS_TOPPANEL_ADD')?>" class="dropdown-toggle"
                                   data-toggle="dropdown"><span class="icon-plus"></span></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'editgoal', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_GOAL')?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'editplan', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_PLAN')?></a>
                                    </li>

                                    <li>
                                        <hr style="margin:0 2px"/>
                                    </li>

                                    <?php if ($goals_count) { ?>
                                        <li>
                                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'editmilistone', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_ADD_MILESTONE')?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($plans_count) { ?>
                                        <li>
                                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'editstage', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_ADD_STAGE')?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($goals_count || $plans_count) { ?>
                                        <li>
                                            <hr style="margin:0 2px"/>
                                        </li>
                                    <?php } ?>
                                    <?php if ($goals_count) { ?>
                                        <li>
                                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'editrecord', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_RECORD')?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($stages_count) { ?>
                                        <li>
                                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'editplantask', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_PLANTASK')?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($goals_count || $stages_count) { ?>
                                        <li>
                                            <hr style="margin:0 2px"/>
                                        </li>
                                    <?php } ?>
                                    <?php if ($goalstemplates_count) { ?>
                                        <li>
                                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'goalstemplate', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_GOAL_TEMPLATE')?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($planstemplates_count) { ?>
                                        <li>
                                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'planstemplate', 'tmpl' => $tmpl))); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_PLAN_TEMPLATE')?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php if($settings->allow_userfields){ ?>
                                <li class="">
                                    <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'alluserfields', 'tmpl' => $tmpl))); ?>"
                                       title="<?php echo JText::_('COM_GOALS_TOPPANEL_SETTINGS')?>"><span class="icon-options"></span></a>
                                </li>
                            <?php } ?>
                            <li class=""><a
                                    href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'calendar', 'tmpl' => $tmpl))); ?>"
                                    title="<?php echo JText::_('COM_GOALS_CALENDAR')?>"><span class="icon-calendar"></span></a>
                            </li>
                        <?php } ?>
                        <li class="<?php echo $goalsActive?>">
                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'goals', 'tmpl' => $tmpl))); ?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_GOALS')?></span><span
                                    class="badge badge-info badge-large"><?php echo $goals_count ?></span></a></li>
                        <li class="<?php echo $plansActive?>">
                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'plans', 'tmpl' => $tmpl))); ?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_PLANS')?></span><span
                                    class="badge badge-info badge-large"><?php echo $plans_count ?></span></a></li>
                        <li class="<?php echo $achievementsActive?>">
                            <a href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'achievements', 'tmpl' => $tmpl))); ?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_ACHIEVEMENTS')?></span><span
                                    class="badge badge-warning badge-large"><?php echo $done_goals + $done_plans?></span></a>
                        </li>
                        <li class="<?php echo $featuredActive?>"><a
                                href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'dashboard', 'tmpl' => $tmpl))); ?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_FEATURED')?></span><span
                                    class="badge badge-info badge-large"><?php echo $featured_goals + $featured_plans?></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Goals Header -->
        <div class="goals-head">
            <!-- Goals Toolbar Navigation -->
            <div class="tools">
            </div>
        </div>

        <?php

    }

    public function showHabitHeader($featuredActive = 'active', $allActive = '', $formedActive = '')
    {
        JFactory::getDocument()->addScriptDeclaration('
    window.onload = setScreenClass;
	window.onresize = setScreenClass;

	//  Following transition classes will be declared:

	//

	//	classname		  container width

	//	------------------------------------------

	//	mobile  		  320px

	//	screen_ultralow	  320px -  550px

	//	screen_low		  550px -  800px

	//	screen_med		  800px - 1024px

	//	screen_hi		 1024px - 1280px

	//	screen_wide				> 1280px



	function setScreenClass(){

		var fmt = document.getElementById(\'goals-wrap\').clientWidth;

		var cls = (fmt<=320)?\'mobile screen_ultralow\':(fmt>320&&fmt<=550)?\'screen_ultralow\':(fmt>550&&fmt<=800)?\'screen_low\':(fmt>800&&fmt<=1024)?\'screen_med\':(fmt>1024&&fmt<=1280)?\'screen_high\':\'screen_wide\';

		document.getElementById(\'goals-wrap\').className=cls;

	};');

        ?>



    <?php



        $db = JFactory::getDbo();

        $query = $db->getQuery(true);



        $query->select('*');

        $query->from('#__goals_habits');

        $query->where('uid=' . JFactory::getUser()->id);



        $db->setQuery($query);



        $habits = $db->loadObjectList();



        $featured = 0;

        $formed = 0;





        foreach ($habits as $habit) {

            $log = GoalsHelper::getHabitLog($habit->id);

            if ($log) {

                $habit->complete_count = $log;

                if ($habit->finish > 0) $habit->percent = round(($log / $habit->finish) * 100);

                if ($habit->percent >= 100) {

                    $formed++;

                } else {

                    if ($habit->featured) {

                        $featured++;



                    }

                }

            } else {

                if ($habit->featured) {

                    $featured++;



                }

            }

        }



        ?>



    <!-- Goals Header -->

    <div class="goals-head">

        <!-- Goals Toolbar Navigation -->

        <div class="tools">

            <ul class="goals-toolbar clearfix">

                <li class="toolbar-feature tool-left <?php echo $featuredActive ?>"><a

                        href="<?php echo JRoute::_('index.php?option=com_goals&view=habits'); ?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_FEATURED')?></span><span class="badge badge-info badge-large"><?php echo $featured ?></span></a></li>





                <li class="toolbar-add-item dropdown">

                    <a href="#" title="<?php echo JText::_('COM_GOALS_TOPPANEL_ADD')?>" class="dropdown-toggle"

                       data-toggle="dropdown"><span><?php echo JText::_('COM_GOALS_TOPPANEL_ADD')?></span></a>

                    <ul class="dropdown-menu">

                        <li>

                            <a href="<?php echo JRoute::_('index.php?option=com_goals&view=edithabit'); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_HABIT')?></a>

                        </li>

                        <li>

                            <a href="<?php echo JRoute::_('index.php?option=com_goals&view=habitstemplate'); ?>"><?php echo JText::_('COM_GOALS_TOPPANEL_ADD_HABIT_TEMPLATE')?></a>

                        </li>

                    </ul>

                </li>





                <li class="toolbar-history"><a

                        href="<?php echo JRoute::_('index.php?option=com_goals&view=habithistory'); ?>"

                        title="<?php echo JText::_('COM_GOALS_TOPPANEL_HISTORY')?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_HISTORY')?></span></a>

                </li>

                <li class="<?php echo $allActive ?>">

                    <a href="<?php echo JRoute::_('index.php?option=com_goals&view=allhabits'); ?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_ALL_HABITS')?></span><span class="badge badge-info badge-large"><?php echo count($habits) - $formed ?></span></a></li>

                <li class="<?php echo $formedActive ?>">

                    <a href="<?php echo JRoute::_('index.php?option=com_goals&view=formedhabits'); ?>"><span><?php echo JText::_('COM_GOALS_TOPPANEL_FORMED_HABITS')?></span><span class="badge badge-warning badge-large"><?php echo $formed ?></span></a></li>

            </ul>

        </div>

    </div>

    <?php

    }



    public function getCustomTaskFields($cid = 0, $tid = 0)

    {

        //if (!$cid) return null;

        $db = JFactory::getDBO();

        $cfields = array();

        $query = $db->getQuery(true);

        $query->select('f.*');

        $query->from('`#__goals_custom_fields` AS `f`');

        //$query->select('`x`.`values` AS `inp_values`');

        $query->leftJoin('`#__goals_xref` AS `x` ON `x`.`fid`=`f`.`id`');

        $query->order('f.`title` ASC');

        $db->setQuery($query);



        $fields = $db->loadObjectList();


        try{
            $query = $db->getQuery(true);

            $query->select('`fid`');

            $query->from('`#__goals_categories_xref`');

            $query->where('`cid`=' . $cid);

            $db->setQuery($query);

            $selected = $db->loadColumn();
        }
        catch(Exception $e){}


        if (sizeof($fields)) {

            foreach ($fields as $field) {

                if (sizeof($selected))

                    if (in_array($field->id, $selected)) {

                        $field->inp_values = null;

                        if ($tid) {

                            $query = $db->getQuery(true);

                            $query->select('`values`');

                            $query->from('#__goals_tasks_xref');

                            $query->where('`fid`=' . (int)$field->id);

                            $query->where('`tid`=' . (int)$tid);

                            $db->setQuery($query);

                            $field->inp_values = $db->loadResult();

                        }

                        $cfields[] = $field;



                    }

            }

        }



        return $cfields;

    }



    public function getCustomTaskUserFields($id = 0, $tid = 0)

    {

        if (!$id) return null;

        $db = JFactory::getDBO();

        $cfields = array();



        $query = $db->getQuery(true);

        $query->select('`cf`.*,`x`.fid');

        //$query->select('`x`.`values` AS `inp_values`');

        $query->from('`#__goals_xref` AS `x`');

        $query->join('LEFT', '`#__goals_custom_fields` AS `cf` ON `cf`.`id`=`x`.`fid`');

        $query->where('`gid`=' . $id);

        $db->setQuery($query);

        $cfields = $db->loadObjectList();

        if (sizeof($cfields)) {

            foreach ($cfields as $field) {

                $field->inp_values = null;

                if ($tid) {

                    $query = $db->getQuery(true);

                    $query->select('`values`');

                    $query->from('#__goals_tasks_xref');

                    $query->where('`fid`=' . (int)$field->id);

                    $query->where('`tid`=' . (int)$tid);

                    $db->setQuery($query);

                    $field->inp_values = $db->loadResult();

                }

            }

        }



        return $cfields;

    }





    public function getCustomTaskFieldsPlans($cid = 0, $tid = 0)

    {



        //if (!$cid) return null;

        $db = JFactory::getDBO();

        $cfields = array();

        $query = $db->getQuery(true);

        $query->select('f.*');

        $query->from('`#__goals_custom_fields` AS `f`');

        //$query->select('`x`.`values` AS `inp_values`');

        $query->leftJoin('`#__goals_xref` AS `x` ON `x`.`fid`=`f`.`id`');

        $query->order('f.`title` ASC');

        $db->setQuery($query);



        $fields = $db->loadObjectList();


        try{
            $query = $db->getQuery(true);

            $query->select('`fid`');

            $query->from('`#__goals_categories_xref`');

            $query->where('`cid`=' . $cid);

            $db->setQuery($query);

            $selected = $db->loadColumn();
        }
        catch(Exception $e){}


        if (sizeof($fields)) {

            foreach ($fields as $field) {

                if (sizeof($selected))

                    if (in_array($field->id, $selected)) {

                        $field->inp_values = null;

                        if ($tid) {

                            $query = $db->getQuery(true);

                            $query->select('`values`');

                            $query->from('#__goals_plantasks_xref');

                            $query->where('`fid`=' . (int)$field->id);

                            $query->where('`tid`=' . (int)$tid);

                            $db->setQuery($query);

                            $field->inp_values = $db->loadResult();

                        }

                        $cfields[] = $field;



                    }

            }

        }



        return $cfields;

    }



    public function getCustomTaskUserFieldsPlans($id = 0, $tid = 0)

    {

        if (!$id) return null;

        $db = JFactory::getDBO();

        $cfields = array();



        $query = $db->getQuery(true);

        $query->select('`cf`.*,`x`.fid');

        //$query->select('`x`.`values` AS `inp_values`');

        $query->from('`#__goals_plans_xref` AS `x`');

        $query->join('LEFT', '`#__goals_plan_custom_fields` AS `cf` ON `cf`.`id`=`x`.`fid`');

        $query->where('`pid`=' . $id);

        $db->setQuery($query);

        $cfields = $db->loadObjectList();

        if (sizeof($cfields)) {

            foreach ($cfields as $field) {

                $field->inp_values = null;

                if ($tid) {

                    $query = $db->getQuery(true);

                    $query->select('`values`');

                    $query->from('#__goals_plantasks_xref');

                    $query->where('`fid`=' . (int)$field->id);

                    $query->where('`tid`=' . (int)$tid);

                    $db->setQuery($query);

                    $field->inp_values = $db->loadResult();

                }

            }

        }



        return $cfields;

    }





    public static function getMilistones($gid = 0)

    {

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('m.*');

        $query->from('`#__goals_milistones` AS `m`');

        $query->where('`m`.`gid` = ' . $gid);

        $query->order('`m`.`duedate` DESC');

        $db->setQuery($query);



        return $db->loadObjectList();

    }

    // ="( it`s no mvc

    public static function getPlans($recursive = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('p.*');
        $query->from('`#__goals_plans` AS `p`');
        $db->setQuery($query);
        $plans = $db->loadObjectList('id');
        if($recursive){
            foreach($plans as &$plan){
                $plan->stages = self::getStages($plan->id, $recursive);
            }
        }
        return $plans;
    }

    public static function getPlan($id, $recursive = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('p.*');
        $query->from('`#__goals_plans` AS `p`');
        if($id) $query->where('`p`.`id` = ' . (int)$id);
        $db->setQuery($query);
        $plan = $db->loadObject();
        if($recursive){
            $plan->stages = self::getStages($plan->id, $recursive);
        }
        return $plan;
    }

    public static function getStages($pid = 0, $recursive = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('m.*');
        $query->from('`#__goals_stages` AS `m`');
        if($pid) $query->where('`m`.`pid` = ' . $pid);
        //$query->order('`m`.`duedate` DESC');
        $db->setQuery($query);
        $stages = $db->loadObjectList('id');
        if($recursive){
            foreach($stages as &$stage){
                $stage->tasks = self::getStageTasks($stage->id, $recursive);
            }
        }
        return $stages;
    }

    public static function getStageTasks($sid = 0, $done = 0)
    {
        if ($sid) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__goals_plantasks');
            $query->where('`sid` = '.$db->quote($sid));
            if ($done) {
                $query->where('`status`=1');
                $db->setQuery($query);
                return $db->loadObjectList();
            } else {
                $db->setQuery($query);
                return $db->loadObjectList();
            }
        } else {
            return null;
        }
    }




    public function getRecords($gid = 0, $limit = 0)

    {

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('t.*');

        $query->from('`#__goals_tasks` AS `t`');

        $query->where('`t`.`gid` = ' . $gid);

        $query->order('`t`.`date` ASC');

        if (!$limit) $db->setQuery($query); else $db->setQuery($query, 0, $limit);



        return $db->loadObjectList();

    }


    public static function getPlanTasksCount($pid = 0, $done = 0)
    {
        $stages = GoalsHelper::getStages($pid);
        if (count($stages)) {
            $str = '';
            foreach ($stages as $stage) {
                $str .= $stage->id . ',';
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('count(*)');
            $query->from('#__goals_plantasks');
            $query->where('`sid` IN (' . substr_replace($str, '.', strrpos($str, ',')) . ')');
            if ($done) {
                $query->where('`status`=1');
                $db->setQuery($query);
                return $db->loadResult();
            } else {
                $db->setQuery($query);
                return $db->loadResult();
            }
        } else {
            return null;
        }

    }

    public static function getPlanTasks($pid = 0, $done = 0)
    {
        $stages = GoalsHelper::getStages($pid);
        if (count($stages)) {
            $str = '';
            foreach ($stages as $stage) {
                $str .= $stage->id . ',';
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__goals_plantasks');
            $query->where('`sid` IN (' . substr_replace($str, '.', strrpos($str, ',')) . ')');
            if ($done) {
                $query->where('`status`=1');
                $db->setQuery($query);
                return $db->loadObjectList();
            } else {
                $db->setQuery($query);
                return $db->loadObjectList();
            }
        } else {
            return null;
        }

    }

    public static function getStageTasksCount($sid = 0, $done = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__goals_plantasks');
        $query->where('`sid` = "'.$sid.'"');
        if ($done) $query->where('`status`=1');
        $db->setQuery($query);
        return $db->loadResult();
    }



    public function getStatusLeft($left)

    {

        if ($left['w'] > 0 || $left['o'] > 0 || $left['y'] > 0) $status = 1;



        if ($left['o'] == 0 && $left['y'] == 0 && $left['w'] == 0) {

            if ($left['d'] > 2) $status = 2;

            if ($left['d'] < 3) $status = 3;

        }



        return $status;

    }





    public static function date_diff($date1, $date2)

    {

        $result = array();

        $result['lateoraway'] = 'late';

        $result['s'] = 0;

        $result['m'] = 0;

        $result['h'] = 0;

        $result['d'] = 0;

        $result['w'] = 0;

        $result['o'] = 0;

        $result['y'] = 0;



        $diff = strtotime($date2) - strtotime($date1);



        if ($diff > 0) $result['lateoraway'] = 'away';



        $diff = abs($diff);



        if ($diff < 60) {

            $result['s'] = $diff;

        } else

            if ($diff >= 60 && $diff < 3600) {

                $diff_in_min = ($diff / 60);

                $result['m'] = $diff_in_min;

            } else

                if ($diff >= 3600 && $diff <= 86400) {

                    $diff_in_hours = ($diff / 3600);

                    $ost = ($diff_in_hours - floor($diff_in_hours)) * 3600;

                    $diff_in_min = ($ost / 60);

                    $result['m'] = $diff_in_min;

                    $result['h'] = $diff_in_hours;

                }

        if ($diff > 86400 && $diff <= 604800) {

            $diff_in_days = ($diff / 86400);

            $ost = ($diff_in_days - floor($diff_in_days)) * 86400;

            $diff_in_hours = ($ost / 3600);

            $result['h'] = $diff_in_hours;

            $result['d'] = $diff_in_days;

        }

        if ($diff > 604800 && $diff <= 2629743) {

            $diff_in_weeks = ($diff / 604800); //������� � �������

            $ost = ($diff_in_weeks - floor($diff_in_weeks)) * 604800;

            $diff_in_days = ($ost / 86400);

            $result['d'] = $diff_in_days;

            $result['w'] = $diff_in_weeks;

        }

        if ($diff > 2629743 && $diff <= 31556926) {

            $diff_in_month = ($diff / 2629743); //������� � �������

            $ost = ($diff_in_month - floor($diff_in_month)) * 2629743;

            $diff_in_weeks = ($ost / 604800);

            $result['w'] = $diff_in_weeks;

            $result['o'] = $diff_in_month;

        }

        if ($diff > 31556926) {

            $diff_in_years = ($diff / 31556926); //������� � �����

            $ost = ($diff_in_years - floor($diff_in_years)) * 31556926;

            $diff_in_month = ($ost / 2629743);

            ;

            $result['o'] = $diff_in_month;

            $result['y'] = $diff_in_years;

        }

        $result['s'] = floor($result['s']);

        $result['m'] = floor($result['m']);

        $result['h'] = floor($result['h']);

        $result['d'] = floor($result['d']);

        $result['w'] = floor($result['w']);

        $result['o'] = floor($result['o']);

        $result['y'] = floor($result['y']);



        return $result;

    }



    public static function getDateLeft($left)

    {

        $leftstr = '';

        if ($left['y']) {

            $leftstr = $left['y'] . ' ' . ($left['y'] == 1 ? JText::_('COM_GOAL_DATE_YEAR') : JText::_('COM_GOAL_DATE_YEARS'));

        }

        if ($left['o']) {

            $leftstr .= ' ' . $left['o'] . ' ' . ($left['o'] == 1 ? JText::_('COM_GOAL_DATE_MONTH') : JText::_('COM_GOAL_DATE_MONTHS'));

        }

        if ($left['w']) {

            $leftstr .= ' ' . $left['w'] . ' ' . ($left['w'] == 1 ? JText::_('COM_GOAL_DATE_WEEK') : JText::_('COM_GOAL_DATE_WEEKS'));

        }

        if ($left['d']) {

            $leftstr .= ' ' . $left['d'] . ' ' . ($left['d'] == 1 ? JText::_('COM_GOAL_DATE_DAY') : JText::_('COM_GOAL_DATE_DAYS'));

        }

        if ($left['h']) {

            $leftstr .= ' ' . $left['h'] . ' ' . ($left['h'] == 1 ? JText::_('COM_GOAL_DATE_HOUR') : JText::_('COM_GOAL_DATE_HOURS'));

        }

        if ($left['m']) {

            $leftstr .= ' ' . $left['m'] . ' ' . ($left['m'] == 1 ? JText::_('COM_GOAL_DATE_MINUTE') : JText::_('COM_GOAL_DATE_MINUTES'));

        }

        if ($left['s']) {

            $leftstr .= ' ' . $left['s'] . ' ' . ($left['s'] == 1 ? JText::_('COM_GOAL_DATE_SECOND') : JText::_('COM_GOAL_DATE_SECONDS'));

        }



        return trim($leftstr);

    }



    function showMilistones($milistones)

    {
        $settings = GoalsHelper::getSettings();
        $date_format = JText::_('DATE_FORMAT_LC3');
        
        $tmpl = JRequest::getVar('tmpl');

        //if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';
        $tmpl = '';

        ?>



    <?php if (sizeof($milistones)) { ?> <h4><?php echo JText::_('COM_GOALS_ACTIVE_MILISTONES'); ?>:</h4> <?php

    } else {

        echo '<div class="gl_msntf">' . JText::_('COM_GOALS_MILISTONES_NOT_FOUND') . '</div>';



        return;

    }

        ?>



    <?php if (sizeof($milistones)) {

        for ($i = 0, $n = sizeof($milistones); $i < $n; $i++) {

            $milistone = $milistones[$i];

            ?>

        <div class="gl_mil_item">

            <div class="gl_goal_status_left_part">

                <?php

                $status = $milistone->leftstatus;

                $status_style = $milistone->status_image;

                ?>

                <div class="gl_goal_<?php echo $status_style;?>">&nbsp;</div>

            </div>

            <div class="gl_goal_left_part">

                <div class="gl_goal_title">

                    <?php echo $milistone->title;?>

                </div>

                <div class="gl_goal_short_details">

                    <?php

                    if (isset($milistone->duedate))

                        if ($milistone->duedate != '0000-00-00 00:00:00') {

                            echo JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $milistone->duedate, $date_format);

                        }

                    ?>

                    <span class="gl_left_count"><br/>

                        <?php

                        if (isset($milistone->left)) {

                            echo $milistone->left;

                        }

                        ?>

												</span>

                </div>

                <div style="clear:both"></div>

            </div>

            <div><?php echo $milistone->description; ?></div>

        </div>

        <div style="clear:both"></div>

        <?php } ?>

    <?php

    }

    }





    public static function showStages($stages)

    {

        $settings = GoalsHelper::getSettings();
        $date_format = JText::_('DATE_FORMAT_LC3');

        $tmpl = JRequest::getVar('tmpl');

        //if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';
        $tmpl = '';

        ?>



    <?php if (sizeof($stages)) { ?> <h4><?php echo JText::_('COM_GOALS_ACTIVE_STAGES'); ?>:</h4> <?php

    } else {

        echo '<div class="gl_msntf">' . JText::_('COM_GOALS_STAGES_NOT_FOUND') . '</div>';



        return;

    }

        ?>



    <?php if (sizeof($stages)) {

        foreach ($stages as $stage) {



            ?>

        <div class="gl_mil_item">

            <div class="gl_goal_status_left_part">

                <?php

                $status = $stage->leftstatus;

                $status_style = $stage->status_image;

                ?>

                <div class="gl_goal_<?php echo $status_style;?>">&nbsp;</div>

            </div>

            <div class="gl_goal_left_part">

                <div class="gl_goal_title">

                    <?php echo $stage->title;?>

                </div>

                <div class="gl_goal_short_details">

                    <?php

                    if (isset($stage->duedate))

                        if ($stage->duedate != '0000-00-00 00:00:00') {

                            echo JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $stage->duedate, $date_format);

                        }

                    ?>

                    <span class="gl_left_count"><br/>

                        <?php

                        if (isset($stage->left)) {

                            echo $stage->left;

                        }

                        ?>

												</span>

                </div>

                <div style="clear:both"></div>

            </div>

            <div><?php echo $stage->description; ?></div>

        </div>

        <div style="clear:both"></div>

        <?php } ?>

    <?php

    }

    }



    function showUserFieldsValues($custom_fields = null, $negative = false)

    {

        ob_start();

        ?>

                <div class="goalfieldslist form-horizontal">

					<?php

        $emchecks = $urlchecks = array();

        if (sizeof($custom_fields)) {

            ?>

            <?php

            foreach ($custom_fields as $field) {

                $type = $field->type;

                $params = json_decode($field->values);

                if ($field->type != 'pc') $ivals = json_decode($field->inp_values); else $ivals = $field->inp_values;

                $id = $field->id;

                ?>

                <div class="control-group">

                    <div class="control-label">

                        <label for="cf_<?php echo $id;?>">

                            <?php echo $field->title;?>

                        </label>

                    </div>

                    <div class="controls">

                        <?php

                        $default_text = '';

                        switch ($type) {

                            case 'tf':

                                $tf_max = 70;

                                if ($ivals) $default_text = $ivals;

                                else if (isset($params->tf_default)) $default_text = $params->tf_default;

                                if (isset($params->tf_max)) $tf_max = $params->tf_max;

                                ?>

                                <input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text;?>" maxlength="<?php echo $tf_max;?>"/>

                                <?php

                                break;

                            case 'ta':

                                $ta_rows = 30;

                                $ta_colls = 10;

                                $text = '';

                                if (isset($params->ta_rows)) $ta_rows = $params->ta_rows;

                                if (isset($params->ta_colls)) $ta_colls = $params->ta_colls;

                                if ($ivals) $text = $ivals;

                                ?>

                                <textarea id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                          rows="<?php echo $ta_rows;?>" cols="<?php echo $ta_colls;?>" wrap="off">

                                    <?php echo $text;?>

                                </textarea>

                                <?php

                                break;



                            case 'hc':

                                $text = '';

                                if ($ivals) $text = $ivals;

                                $editor = JFactory::getEditor('codemirror');

                                $params = array();

                                echo $editor->display('f_' . $id, $text, '250', '250', '60', '20', false, $params);

                                break;



                            case 'em':

                                if ($ivals) $default_text = $ivals;

                                $emchecks[] = 'cf_' . $id;

                                ?>

                                <input id="cf_<?php echo $id;?>" type="text" class="text" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text;?>" maxlength="70"/>

                                <?php

                                break;



                            case 'wu':

                                $urlchecks[] = "cf_" . $id;

                                if ($ivals) $default_text = $ivals;

                                ?>

                                <input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text; ?>" maxlength=""/>

                                <?php

                                break;

                            case 'pc':

                                if ($ivals) {

                                    $ivals = str_replace('"', '', $ivals);
                                    
                                    $ivals = str_replace('\\', '/', $ivals);

                                    if (file_exists(JPATH_SITE . $ivals) && is_file(JPATH_SITE . $ivals)) {

                                        
                                        $default_text = '<img src="' . JURI::root() . $ivals . '" alt="" />';

                                    }

                                }

                                echo $default_text;

                                ?>

                                <input type="file" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"/>

                                <?php

                                break;

                            case 'in':

                                if ($ivals) $default_text = $ivals;

                                ?>
									<?php if($negative=0){ ?>
									<small class="pre-negative-int">
									<?php echo JText::_('COM_GOALS_NEGATIVE_INPUT_NOTE'); ?>
									</small>
									<?php } ?>
                                <input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text; ?>" maxlength=""/>

                                <?php

                                break;

                            case 'sl':

                                $list = array();

                                $selected = NULL;

                                if (isset($ivals)) $selected = $ivals;

                                if (isset($params->sl_elmts)) {

                                    if (sizeof($params->sl_elmts)) {

                                        foreach ($params->sl_elmts as $el) {

                                            $list[] = JHTML::_('select.option', $el, $el);

                                        }

                                    }

                                }

                                echo JHTML::_('select.genericlist', $list, $name = 'f_' . $id, null, 'value', 'text', $selected, false, false);

                                break;

                            case 'ml':

                                $list = array();

                                $selected = NULL;

                                if (isset($ivals)) $selected = $ivals;

                                if (isset($params->ms_elmts)) {

                                    if (sizeof($params->ms_elmts)) {

                                        foreach ($params->ms_elmts as $el) {

                                            $list[] = JHTML::_('select.option', $el, $el);

                                        }

                                    }

                                }

                                echo JHTML::_('select.genericlist', $list, $name = 'f_' . $id . '[]', ' multiple="multiple" size="3"', 'value', 'text', $selected, false, false);

                                break;

                            case 'ch':

                                $selected = array();

                                if (isset($ivals)) $selected = $ivals;

                                if (!is_array($selected)) $selected = array();

                                if (isset($params->ch_elmts)) {

                                    if (sizeof($params->ch_elmts)) {

                                        foreach ($params->ch_elmts as $k => $el) {

                                            ?>

                                            <div>

                                                <input id="cf_ch_<?php echo $k;?>" type="checkbox" class="text"

                                                       value="<?php echo $el;?>" <?php if (in_array($el, $selected)) echo 'checked="checked"';?>

                                                       name="f_<?php echo $id;?>[]"/>

                                                <label for="cf_ch_<?php echo $k;?>"><?php echo $el;?></label>

                                            </div>

                                            <?php

                                        }

                                    }

                                }

                                break;

                            case 'rd':

                                $selected = null;

                                if (isset($ivals)) $selected = $ivals;

                                if (isset($params->rb_elmts)) {

                                    if (sizeof($params->rb_elmts)) {

                                        foreach ($params->rb_elmts as $k => $el) {

                                            ?>

                                            <div>

                                                <input id="cf_rb_<?php echo $k;?>" type="radio" class="text"

                                                       value="<?php echo $el;?>"

                                                       name="f_<?php echo $id;?>"  <?php if ($el == $selected) echo 'checked="checked"';?> />

                                                <label for="cf_rb_<?php echo $k;?>"><?php echo $el;?></label>

                                            </div>

                                            <?php

                                        }

                                    }

                                }

                                break;

                            default:

                                break;

                        }

                        ?>

                        <div class="clr"></div>

                    </div>

                </div>

                <?php } ?>

            </div>

					 	 				<?php



        }

        ?>

    <script type="text/javascript">

        function isValidUserURLs() {

            var RegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;

            var tmpurl = '';

            <?php if (sizeof($urlchecks)) {

                foreach ($urlchecks as $urlid) {



                    ?>

                    tmpurl = $('<?php echo $urlid;?>').get('value');

                    if (tmpurl != '') {

                        if (RegExp.test(tmpurl)) {

                        }

                        else {

                            $('<?php echo $urlid;?>').focus();

                            alert('Invalid url');

                            $('<?php echo $urlid;?>').set('class', 'invalid');

                            return false;

                        }

                    }

                    $('<?php echo $urlid;?>').set('class', 'text');

                    <?php

                }

            } ?>

            return true;

        }

    </script>

    <?php





        ?>

    <script type="text/javascript">

        function isValidUserEmails() {

            var RegExp = /^((([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+(\.([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+)*)@((((([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.))*([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.)[\w]{2,4}|(((([0-9]){1,3}\.){3}([0-9]){1,3}))|(\[((([0-9]){1,3}\.){3}([0-9]){1,3})\])))$/

            <?php  if (sizeof($emchecks)) {

                foreach ($emchecks as $emlid) {

                    ?>

                    if ($('<?php echo $emlid;?>').get('value') != '') {



                        if (RegExp.test($('<?php echo $emlid;?>').get('value'))) {

                        }

                        else {

                            $('<?php echo $emlid;?>').focus();

                            alert('Invalid Email');

                            $('<?php echo $emlid;?>').set('class', 'invalid');

                            return false;

                        }

                    }

                    $('<?php echo $emlid;?>').set('class', 'text');

                    <?php

                }

            } ?>

            return true;

        }

    </script>

    <div class="clr"></div>

    <?php

        $content = ob_get_contents();

        ob_get_clean();

        if (sizeof($custom_fields)) echo $content;



        return $content;

    }



    function showCustoGroupFieldsValues($custom_fields = null, $negative= false)

    {
        ?>

                <div class="goalfieldslist form-horizontal">

					<?php

        // echo JHtml::_('tabs.panel',JText::_('COM_GOAL_CATEGORY_FIELDS'), 'gl-category-fields');
		
        $cfs = $custom_fields;

        $emchecks = $urlchecks = array();

        if (sizeof($cfs)) {

            ?>



            <?php

            foreach ($cfs as $field) {

                $type = $field->type;

                $params = json_decode($field->values);

                if ($field->type != 'pc') $ivals = json_decode($field->inp_values); else $ivals = $field->inp_values;

                $id = $field->id;

                ?>

                <div class="control-group">

                    <div class="control-label">

                        <label for="cf_<?php echo $id;?>">

                            <?php echo $field->title;?>

                        </label>

                    </div>

                    <div class="controls">

                        <?php

                        $default_text = '';

                        switch ($type) {

                            case 'tf':

                                $tf_max = 70;

                                if ($ivals) $default_text = $ivals;

                                else if (isset($params->tf_default)) $default_text = $params->tf_default;

                                if (isset($params->tf_max)) $tf_max = $params->tf_max;

                                ?>

                                <input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text;?>" maxlength="<?php echo $tf_max;?>"/>

                                <?php

                                break;

                            case 'ta':

                                $ta_rows = 30;

                                $ta_colls = 10;

                                $text = '';

                                if (isset($params->ta_rows)) $ta_rows = $params->ta_rows;

                                if (isset($params->ta_colls)) $ta_colls = $params->ta_colls;

                                if ($ivals) $text = $ivals;

                                ?>

                                <textarea id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                          rows="<?php echo $ta_rows;?>" cols="<?php echo $ta_colls;?>" wrap="off">

                                    <?php echo $text;?>

                                </textarea>

                                <?php

                                break;



                            case 'hc':

                                $text = '';

                                if ($ivals) $text = $ivals;

                                $editor = JFactory::getEditor('codemirror');

                                $params = array();

                                echo $editor->display('f_' . $id, $text, '250', '250', '60', '20', false, $params);

                                break;



                            case 'em':

                                $emchecks[] = 'cf_' . $id;

                                if ($ivals) $default_text = $ivals;

                                ?>

                                <input id="cf_<?php echo $id;?>" type="text" class="text" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text;?>" maxlength="70"/>

                                <?php

                                break;

                            case 'wu':

                                $urlchecks[] = "cf_" . $id;

                                if ($ivals) $default_text = $ivals;

                                ?>

                                <input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text; ?>" maxlength=""/>

                                <?php

                                break;

                            case 'pc':

                                if ($ivals) {

                                    $ivals = str_replace('"', '', $ivals);



                                    if (file_exists(JPATH_SITE . $ivals) && is_file(JPATH_SITE . $ivals)) {

                                        $ivals = str_replace(DS, '/', $ivals);

                                        $default_text = '<img src="' . JURI::root() . $ivals . '" alt="" />';

                                    }

                                }

                                echo $default_text;

                                ?>

                                <input type="file" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"/>

                                <?php

                                break;

                            case 'in':

                                if ($ivals) $default_text = $ivals;

                                ?>
									<?php if($negative=0){ ?>
									<small class="pre-negative-int">
									<?php echo JText::_('COM_GOALS_NEGATIVE_INPUT_NOTE'); ?>
									</small>
									<?php } ?>

                                <input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>"

                                       value="<?php echo $default_text; ?>" maxlength=""/>

                                <?php

                                break;

                            case 'sl':

                                $list = array();

                                $selected = NULL;

                                if (isset($ivals)) $selected = $ivals;

                                if (isset($params->sl_elmts)) {

                                    if (sizeof($params->sl_elmts)) {

                                        foreach ($params->sl_elmts as $el) {

                                            $list[] = JHTML::_('select.option', $el, $el);

                                        }

                                    }

                                }

                                echo JHTML::_('select.genericlist', $list, $name = 'f_' . $id, null, 'value', 'text', $selected, false, false);

                                break;

                            case 'ml':

                                $list = array();

                                $selected = NULL;

                                if (isset($ivals)) $selected = $ivals;

                                if (isset($params->ms_elmts)) {

                                    if (sizeof($params->ms_elmts)) {

                                        foreach ($params->ms_elmts as $el) {

                                            $list[] = JHTML::_('select.option', $el, $el);

                                        }

                                    }

                                }

                                echo JHTML::_('select.genericlist', $list, $name = 'f_' . $id . '[]', ' multiple="multiple" size="3" ', 'value', 'text', $selected, false, false);

                                break;

                            case 'ch':

                                $selected = array();

                                if (isset($ivals)) $selected = $ivals;

                                if (!is_array($selected)) $selected = array();

                                if (isset($params->ch_elmts)) {

                                    if (sizeof($params->ch_elmts)) {

                                        foreach ($params->ch_elmts as $k => $el) {

                                            ?>

                                            <div>

                                                <label for="cf_ch_<?php echo $k;?>" class="checkbox"><input

                                                        id="cf_ch_<?php echo $k;?>" type="checkbox" class="text"

                                                        value="<?php echo $el;?>" <?php if (in_array($el, $selected)) echo 'checked="checked"';?>

                                                        name="f_<?php echo $id;?>[]"/>

                                                    <?php echo $el;?></label>

                                            </div>

                                            <?php

                                        }

                                    }

                                }

                                break;

                            case 'rd':

                                $selected = null;

                                if (isset($ivals)) $selected = $ivals;

                                if (isset($params->rb_elmts)) {

                                    if (sizeof($params->rb_elmts)) {

                                        foreach ($params->rb_elmts as $k => $el) {

                                            ?>

                                            <div>

                                                <label for="cf_rb_<?php echo $k;?>" class="checkbox"><input

                                                        id="cf_rb_<?php echo $k;?>" type="radio" class="text"

                                                        value="<?php echo $el;?>"

                                                        name="f_<?php echo $id;?>"  <?php if ($el == $selected) echo 'checked="checked"';?> />

                                                    <?php echo $el;?></label>

                                            </div>

                                            <?php

                                        }

                                    }

                                }

                                break;

                            default:

                                break;

                        }

                        ?>

                        <div class="clr"></div>

                    </div>

                </div>

                <?php } ?>



            </div>

					 	 				<?php



        }

        ?>

    <script type="text/javascript">

        function isValidURLs() {

            var RegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;

            var tmpurl = '';

            <?php if (sizeof($urlchecks)) {

                foreach ($urlchecks as $urlid) {



                    ?>

                    tmpurl = $('<?php echo $urlid;?>').get('value');

                    if (tmpurl != '') {

                        if (RegExp.test(tmpurl)) {

                        }

                        else {

                            $('<?php echo $urlid;?>').focus();

                            alert('Invalid url');

                            $('<?php echo $urlid;?>').set('class', 'invalid');

                            return false;

                        }

                    }

                    $('<?php echo $urlid;?>').set('class', 'text');

                    <?php

                }

            } ?>

            return true;

        }

    </script>

    <script type="text/javascript">

        function isValidEmails() {

            var RegExp = /^((([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+(\.([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+)*)@((((([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.))*([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.)[\w]{2,4}|(((([0-9]){1,3}\.){3}([0-9]){1,3}))|(\[((([0-9]){1,3}\.){3}([0-9]){1,3})\])))$/

            <?php  if (sizeof($emchecks)) {

                foreach ($emchecks as $emlid) {

                    ?>

                    if ($('<?php echo $emlid;?>').get('value') != '') {



                        if (RegExp.test($('<?php echo $emlid;?>').get('value'))) {

                        }

                        else {

                            $('<?php echo $emlid;?>').focus();

                            alert('Invalid Email');

                            $('<?php echo $emlid;?>').set('class', 'invalid');

                            return false;

                        }

                    }

                    $('<?php echo $emlid;?>').set('class', 'text');

                    <?php

                }

            } ?>

            return true;

        }

    </script>

    <div class="clr"></div>

    <?php

    }



    public static function getHabitsDaysHeader($day_start)

    {

        if ($day_start == 'm') {

            return array(

                JText::_('COM_GOALS_HABITS_MONDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_TUESDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_WEDNESDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_THURSDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_FRIDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_SATURDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_SUNDAY_LABEL')

            );

        } else {

            return array(

                JText::_('COM_GOALS_HABITS_SUNDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_MONDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_TUESDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_WEDNESDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_THURSDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_FRIDAY_LABEL'),

                JText::_('COM_GOALS_HABITS_SATURDAY_LABEL')



            );

        }





    }



    public function getJsCssIncluded()

    {

        $document = JFactory::getDocument();

        $settings = GoalsHelper::getSettings();

        if ($settings->include_jquery) {

            if ($settings->include_jquery == 1) {

                $document->addScript(JURI::root() . 'components/com_goals/assets/js/jquery.min.js');

            } else {

                $document->addScript('http://code.jquery.com/jquery-latest.min.js');



            }

            $document->addScriptDeclaration('jQuery.noConflict();');





        }

        if ($settings->include_bootstrap) {

            $document->addScript(JURI::root() . 'components/com_goals/assets/js/bootstrap.min.js');

        }

        // we have local_bootstrap already so need to be excluded
        if ($settings->include_bootstrap_css) {
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/bootstrap.css');
        }



    }



    public static function getCheckedHabit($id, $day)

    {

        //return 'checked="checked"';



        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('id');

        $query->from('#__goals_habits_log');

        $query->where('hid=' . $id);

        $query->where('date LIKE "' . $day . '%"');

        $db->setQuery($query);

        $result = $db->loadObjectList();



        if ($result) {

            return 'checked="checked"';

        } else {

            return '';

        }



    }



    public static function getRequiredDayClass($id, $date)

    {



        $db = JFactory::getDbo();



        $query = $db->getQuery(true);

        $query->select('days');

        $query->from('#__goals_habits');

        $query->where('id=' . $id);

        $db->setQuery($query);

        $daysArr = $db->loadResult();

        $jdate = new JDate($date);

        $nw = (int)$jdate->dayofweek;

        if ($nw == 0) $nw = 7;



        if ($daysArr) {



            $days = explode(',', $daysArr);

            if (!in_array($nw, $days)) {

                return ' dimmed';

            } else {

                return '';

            }

        } else {

            return '';

        }





    }



    public static function isDisabled($dateCreated, $dateCheck)

    {

        if ($dateCreated > $dateCheck) {

            return 'style="visibility:hidden;"';

        } else {

            return null;

        }

    }

    public static function getJoomla3Vesion() {

        $arr = explode('.',JVERSION);

        if ($arr[0]==='3') {

            return true;

        } else {

            return false;

        }

    }
	
	// retrieves $object 
	
    public static function getPercents($goal) {
		$negative = false;
		//if($goal->finish < $goal->start) $negative = true;
		$sum_val = $goal->start;
		foreach($goal->records as $rec){
			if($rec->result_mode==0){
				$sum_val = $rec->value;
			}else{
				if($negative) $sum_val -= $rec->value;
				else $sum_val += $rec->value;
			}
			// uncomment for goal achieved stop
			// if($sum_val>=$goal->finish) break;
		}
		if($goal->finish == $goal->start || $sum_val == $goal->finish) $goal->percent = 100;
		else $goal->percent = round( ((($sum_val - $goal->start))/($goal->finish - $goal->start))*100 );
		$goal->negative = $negative;
		
        return $goal;
    }


    public static function getReturnURL($defaultItemId = null, $params = array())
    {
        $app	= JFactory::getApplication();
        $router = $app->getRouter();
        $url = null;

        if ($itemid = $defaultItemId)
        {
            $db		= JFactory::getDbo();
            $query	= $db->getQuery(true)
                ->select($db->quoteName('link'))
                ->from($db->quoteName('#__menu'))
                ->where($db->quoteName('published') . '=1')
                ->where($db->quoteName('id') . '=' . $db->quote($itemid));

            $db->setQuery($query);

            if ($link = $db->loadResult())
            {
                if ($router->getMode() == JROUTER_MODE_SEF)
                {
                    $url = 'index.php?Itemid=' . $itemid;
                }
                else
                {
                    $url = $link . '&Itemid=' . $itemid;
                }
            }
        }

        if (!$url)
        {
            // Stay on the same page
            $uri = clone JUri::getInstance();
            $vars = $router->parse($uri);
            foreach($params as $key => $value){
                $vars[$key]=$value;
            }
            unset($vars['lang']);

            if ($router->getMode() == JROUTER_MODE_SEF)
            {
                if (isset($vars['Itemid']))
                {
                    $itemid = $vars['Itemid'];
                    $menu = $app->getMenu();
                    $item = $menu->getItem($itemid);
                    unset($vars['Itemid']);

                    if (isset($item) && $vars == $item->query)
                    {
                        $url = 'index.php?Itemid=' . $itemid;
                    }
                    else
                    {
                        $url = 'index.php?' . JUri::buildQuery($vars) . '&Itemid=' . $itemid;
                    }
                }
                else
                {
                    $url = 'index.php?' . JUri::buildQuery($vars);
                }
            }
            else
            {
                $url = 'index.php?' . JUri::buildQuery($vars);
            }
        }

        return base64_encode($url);
    }

    public static function getListQuery($type, $db, $user = null, $params = array("is_complete"=>0)){
        if(!$user) $user = $GLOBALS["viewed_user"];
        switch($type){
            case "goals":
                $query	= $db->getQuery(true)
                    ->select('g.*')
                /*
                    ->select('(SELECT COUNT(`t`.`id`) FROM `#__goals_tasks` AS `t` WHERE `t`.`gid`=`g`.`id` LIMIT 1) AS `task_count`')
                    ->select('(SELECT COUNT(`m`.`id`) FROM `#__goals_milistones` AS `m` WHERE `m`.`gid`=`g`.`id` LIMIT 1) AS `milistones_count`')
                    ->select('(SELECT COUNT(`mc`.`id`) FROM `#__goals_milistones` AS `mc` WHERE `mc`.`gid`=`g`.`id` AND `mc`.`status`=1 LIMIT 1) AS `milistones_count_complete`')
                    ->select('`c`.`title` AS `catname`')
                    ->join('LEFT','`#__goals_categories` AS `c` ON `c`.`id`=`g`.`cid`')
                */
                    ->from('`#__goals` AS `g`')
                    ->where('`g`.`uid`='.$user->id)
                    ->order('`g`.`is_complete` DESC,`g`.`deadline` DESC')
                    ->group('g.id');
                break;
            case "plans":
                $query	= $db->getQuery(true)
                    ->select('g.*')
                /*
                    ->select('count(m.id) as stages_count')
                    ->select('`c`.`title` AS `catname`')
                    ->join('LEFT','`#__goals_categories` AS `c` ON `c`.`id`=`g`.`cid`')
                    ->join('LEFT','`#__goals_stages` AS `m` ON `g`.`id`=`m`.`pid`')
                */
                    ->from('`#__goals_plans` AS `g`')
                    ->where('`g`.`uid`='.$user->id)
                    ->order('`g`.`is_complete` DESC,`g`.`deadline` DESC')
                    ->group('g.id');
                break;
            default:
                return false;
        }
        if($params)
            foreach($params as $param => $value){
                $query->where('`'.$param.'`="'.$value.'"');
            }

        if($params['is_complete']!=1){
            if(!JFactory::getUser()->authorise('core.see_upcoming', 'com_goals') && !array_key_exists('show_startup',$params)) $query->where('`startup` <= NOW()');
            if(!JFactory::getUser()->authorise('core.show_expired', 'com_goals') && !array_key_exists('show_deadline',$params)) $query->where('`deadline` >= NOW()');
        }

        return $query;
    }

}