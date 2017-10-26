<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class GoalsModelDashboard extends JModelList
{
    var $user;
    var $allowQuery = array( 'easysocial' );

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
		$settings	= GoalsHelper::getSettings();

		$l = $app->getCfg('list_limit');
		if (isset($settings->goals_dashboard_count)) $l = (int) $settings->goals_dashboard_count;
		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $l);
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
        $user = $this->getQueryUser();

        $query = GoalsHelper::getListQuery("goals",$db, null, array("is_complete" => 0, "featured" => 1));
        $query->select('"goal" as `type`');
		return $query;
	}

    public function getPlans() {
        // Create a new query object.
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $user = $this->getQueryUser();

        $query = GoalsHelper::getListQuery("plans",$db, null, array("is_complete" => 0, "featured" => 1));
        $query->select('"plan" as `type`');
        $db->setQuery($query);

        return $db->loadObjectList();
    }

	public function getRecords()
	{
		$settings	= GoalsHelper::getSettings();
		$db	= JFactory::getDbo();
        $user = $this->getQueryUser();
			$query	= $db->getQuery(true);
			$query->select('t.*, g.`title` AS `gtitle`, g.`metric` AS `gmetric`');
			$query->from('`#__goals_tasks` AS `t`');
			$query->join('LEFT','`#__goals` AS `g` ON `t`.`gid`=`g`.`id`');
			$query->where("DATE_FORMAT(`t`.`date`, '%d-%m-%y') = DATE_FORMAT( NOW(), '%d-%m-%y' )");
			$query->where('`g`.`uid`='.$user->id);
		$db->setQuery($query, 0, isset($settings->records_dashboard_count) ? $settings->records_dashboard_count : 0);
		return $db->loadObjectList();
	}

	public function getHabits()
	{
		$db	= $this->getDbo();
		$user = JFactory::getUser();

			$query	= $db->getQuery(true);
			$query->select('h.*');
			$query->from('`#__goals_habits` AS `h`');
			$query->where('`h`.`complete`=0');
			$query->where('`h`.`uid`='.$user->id);
			$query->order('`h`.`type` DESC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getHabitLog($id=0)
	{
		return GoalsHelper::getHabitLog($id);
	}

    public function setQueryUser($user_id){
        $GLOBALS["viewed_user"] = JFactory::getUser($user_id);
    }

    protected function getQueryUser(){

        return $GLOBALS["viewed_user"];
    }

    public function getQuery(){
        $jinput = JFactory::getApplication()->input;
        $option = $jinput->get('option');
        if(in_array($option, $this->allowQuery)) return $this->getListQuery();
        else return false;
    }
}